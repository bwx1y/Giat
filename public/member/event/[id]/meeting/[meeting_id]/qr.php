<?php

global $applicationDb;
require_once dirname(__DIR__, 6) . '/config/mail.php';

$eventId   = (int) ($_GET['id'] ?? 0);
$meetingId = (int) ($_GET['meeting_id'] ?? 0);

if (!function_exists('qr_data_dir')) {
    function qr_data_dir(): string
    {
        $dir = dirname(__DIR__, 6) . '/data';
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        return $dir;
    }

    function qr_code_path(int $meetingId): string
    {
        return qr_data_dir() . '/qr_' . $meetingId . '.txt';
    }

    function qr_used_path(): string
    {
        return qr_data_dir() . '/used_codes.txt';
    }

    function qr_used_codes(): array
    {
        $fh = @fopen(qr_used_path(), 'r');
        if ($fh === false) {
            return [];
        }
        flock($fh, LOCK_SH);
        $contents = (string) stream_get_contents($fh);
        flock($fh, LOCK_UN);
        fclose($fh);

        $codes = [];
        foreach (explode("\n", $contents) as $line) {
            $line = trim($line);
            if ($line !== '') {
                $codes[$line] = true;
            }
        }
        return $codes;
    }

    function qr_current_code(int $meetingId): string
    {
        $fh = @fopen(qr_code_path($meetingId), 'r');
        if ($fh === false) {
            return '';
        }
        flock($fh, LOCK_SH);
        $code = trim((string) stream_get_contents($fh));
        flock($fh, LOCK_UN);
        fclose($fh);
        return $code;
    }

    function qr_generate_code(int $meetingId): string
    {
        $used    = qr_used_codes();
        $current = qr_current_code($meetingId);
        $path    = qr_code_path($meetingId);

        for ($attempt = 0; $attempt < 10; $attempt++) {
            $code = bin2hex(random_bytes(16)); // 128-bit, 32 karakter hex
            if (isset($used[$code]) || $code === $current) {
                continue;
            }
            file_put_contents($path, $code, LOCK_EX);
            return $code;
        }

        throw new RuntimeException('Gagal membuat kode QR unik');
    }

    function qr_mark_used(string $code): void
    {
        file_put_contents(qr_used_path(), $code . "\n", FILE_APPEND | LOCK_EX);
    }
        function qr_otp_path(int $meetingId, int $userId): string
    {
        return qr_data_dir() . '/qr_otp_' . $meetingId . '_' . $userId . '.json';
    }

    function qr_json(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($payload);
        exit;
    }
}

if (!is_logged_in()) {
    qr_json(['status' => 'error', 'message' => 'Login diperlukan'], 401);
}

if ($eventId <= 0 || $meetingId <= 0) {
    qr_json(['status' => 'error', 'message' => 'Event atau meeting tidak valid'], 400);
}

$meetingStmt = $applicationDb->prepare("
    SELECT id FROM meetings WHERE id = :meeting_id AND event_id = :event_id
");
$meetingStmt->execute(['meeting_id' => $meetingId, 'event_id' => $eventId]);
if (!$meetingStmt->fetchColumn()) {
    qr_json(['status' => 'error', 'message' => 'Meeting tidak ditemukan pada event ini'], 404);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Release the session lock before keeping the SSE connection open.
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }

    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header('Connection: keep-alive');
    header('X-Accel-Buffering: no');

    set_time_limit(0);

    while (ob_get_level() > 0) {
        ob_end_flush();
    }

    echo "retry: 3000\n\n";
    flush();

    $lastCode = '';

    while (true) {
        if (connection_aborted()) {
            break;
        }

        try {
            $code = qr_current_code($meetingId);
            if ($code === '' || isset(qr_used_codes()[$code])) {
                $code = qr_generate_code($meetingId);
            }
        } catch (Throwable $e) {
            echo "event: qr_error\n";
            echo "data: " . json_encode(['message' => $e->getMessage()]) . "\n\n";
            flush();
            sleep(2);
            continue;
        }

        if ($code !== $lastCode) {
            $lastCode = $code;
            echo "event: qr_code\n";
            echo "data: " . json_encode([
                'code'       => $code,
                'meeting_id' => $meetingId,
                'event_id'   => $eventId,
            ]) . "\n\n";
            flush();
        }

        // Heartbeat: tanpa write, PHP tak pernah mendeteksi klien putus
        echo ": ping\n\n";
        flush();

        sleep(2);
    }

    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawBody = trim((string) file_get_contents('php://input'));
    if ($rawBody !== '' && empty($_POST)) {
        $data = json_decode($rawBody, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            qr_json(['status' => 'error', 'message' => 'Format JSON tidak valid'], 400);
        }
    } else {
        $data = $_POST;
    }

    $userId = (int) ($_SESSION['user_id'] ?? 0);
    if ($userId <= 0) {
        qr_json(['status' => 'error', 'message' => 'Login diperlukan'], 401);
    }

    $action   = strtolower(trim((string) ($data['action'] ?? '')));
    $otpInput = trim((string) ($data['otp'] ?? $data['verification_code'] ?? $data['pin'] ?? ''));
    $rawCode  = trim((string) ($data['code'] ?? ''));

    // Deteksi Tahap 2 (Verifikasi 5 Angka dari Email)
    $isVerifyStep = ($action === 'verify')
        || ($otpInput !== '')
        || ($action === '' && preg_match('/^\d{5}$/', $rawCode) === 1);

    if ($isVerifyStep) {
        $pin = $otpInput !== '' ? $otpInput : $rawCode;
        if (preg_match('/^\d{5}$/', $pin) !== 1) {
            qr_json(['status' => 'error', 'message' => 'Kode verifikasi harus terdiri dari 5 angka'], 400);
        }

        $existingStmt = $applicationDb->prepare("
            SELECT id FROM user_meetings
            WHERE user_id = :user_id AND meeting_id = :meeting_id AND deleted_at IS NULL
            LIMIT 1
        ");
        $existingStmt->execute(['user_id' => $userId, 'meeting_id' => $meetingId]);
        if ($existingStmt->fetchColumn()) {
            @unlink(qr_otp_path($meetingId, $userId));
            qr_json(['status' => 'error', 'message' => 'Anda sudah tercatat presensi pada meeting ini'], 409);
        }

        $otpPath = qr_otp_path($meetingId, $userId);
        if (!is_file($otpPath)) {
            qr_json(['status' => 'error', 'message' => 'Belum ada sesi scan QR aktif. Silakan scan QR terlebih dahulu'], 404);
        }

        $otpHandle = @fopen($otpPath, 'r');
        if ($otpHandle === false) {
            qr_json(['status' => 'error', 'message' => 'Gagal membaca data kode verifikasi'], 500);
        }

        flock($otpHandle, LOCK_SH);
        $otpRaw = (string) stream_get_contents($otpHandle);
        flock($otpHandle, LOCK_UN);
        fclose($otpHandle);

        $otpState = json_decode($otpRaw, true);
        if (!is_array($otpState) || empty($otpState['otp'])) {
            @unlink($otpPath);
            qr_json(['status' => 'error', 'message' => 'Sesi verifikasi tidak valid. Silakan scan QR ulang'], 400);
        }

        if (time() > (int) ($otpState['expires_at'] ?? 0)) {
            @unlink($otpPath);
            qr_json(['status' => 'error', 'message' => 'Kode verifikasi sudah kedaluwarsa. Silakan scan QR ulang'], 410);
        }

        if (!hash_equals((string) $otpState['otp'], $pin)) {
            qr_json(['status' => 'error', 'message' => 'Kode verifikasi 5 angka tidak sesuai. Silakan cek kembali email Anda'], 422);
        }

        try {
            $insertStmt = $applicationDb->prepare("
                INSERT INTO user_meetings (user_id, meeting_id, attendance_method, code)
                VALUES (:user_id, :meeting_id, 'Qr', :code)
                RETURNING id
            ");
            $insertStmt->execute([
                'user_id'    => $userId,
                'meeting_id' => $meetingId,
                'code'       => $pin,
            ]);
            $attendanceId = (int) $insertStmt->fetchColumn();
        } catch (PDOException $e) {
            if ($e->getCode() === '23505') {
                @unlink($otpPath);
                qr_json(['status' => 'error', 'message' => 'Anda sudah tercatat presensi pada meeting ini'], 409);
            }

            write_log('Gagal menyimpan presensi QR: ' . $e->getMessage(), 'ERROR', [
                'user_id'    => $userId,
                'meeting_id' => $meetingId,
            ]);
            qr_json(['status' => 'error', 'message' => 'Gagal menyimpan presensi'], 500);
        }

        @unlink($otpPath);

        qr_json([
            'status'  => 'success',
            'message' => 'Presensi berhasil diverifikasi',
            'data'    => [
                'attendance_id'     => $attendanceId,
                'user_id'           => $userId,
                'meeting_id'        => $meetingId,
                'attendance_method' => 'Qr',
            ],
        ], 201);
    }

    // Tahap 1: Scan QR & Kirim Kode 5 Angka ke Email Member
    if ($rawCode === '') {
        qr_json(['status' => 'error', 'message' => 'Field code wajib diisi'], 400);
    }

    $handle = fopen(qr_code_path($meetingId), 'c+');
    if ($handle === false) {
        qr_json(['status' => 'error', 'message' => 'Gagal membaca kode QR'], 500);
    }

    flock($handle, LOCK_EX);
    $current = trim((string) stream_get_contents($handle));

    $reject = static function () use ($handle): void {
        flock($handle, LOCK_UN);
        fclose($handle);
    };

    if ($current === '' || !hash_equals($current, $rawCode)) {
        $reject();
        qr_json(['status' => 'error', 'message' => 'Kode QR tidak valid atau sudah digunakan'], 409);
    }

    if (isset(qr_used_codes()[$rawCode])) {
        $reject();
        qr_json(['status' => 'error', 'message' => 'Kode QR tidak valid atau sudah digunakan'], 409);
    }

    $existingStmt = $applicationDb->prepare("
        SELECT id FROM user_meetings
        WHERE user_id = :user_id AND meeting_id = :meeting_id AND deleted_at IS NULL
        LIMIT 1
    ");
    $existingStmt->execute(['user_id' => $userId, 'meeting_id' => $meetingId]);
    if ($existingStmt->fetchColumn()) {
        $reject();
        qr_json(['status' => 'error', 'message' => 'Anda sudah tercatat presensi pada meeting ini'], 409);
    }

    $userStmt = $applicationDb->prepare("
        SELECT full_name, username, email
        FROM users
        WHERE id = :id AND deleted_at IS NULL
        LIMIT 1
    ");
    $userStmt->execute(['id' => $userId]);
    $member = $userStmt->fetch(PDO::FETCH_ASSOC);
    if (!$member) {
        $reject();
        qr_json(['status' => 'error', 'message' => 'Data pengguna tidak ditemukan'], 404);
    }

    $recipientEmail = trim((string) ($member['email'] ?? ''));
    if ($recipientEmail === '') {
        $recipientEmail = $member['username'] . '@giat.test';
    }

    $meetingInfoStmt = $applicationDb->prepare("SELECT title FROM meetings WHERE id = :id LIMIT 1");
    $meetingInfoStmt->execute(['id' => $meetingId]);
    $meetingTitle = (string) ($meetingInfoStmt->fetchColumn() ?: 'Sub Event GIAT');

    // Generate kode random 5 angka (00000 - 99999)
    $otpCode = str_pad((string) random_int(0, 99999), 5, '0', STR_PAD_LEFT);
    $otpPath = qr_otp_path($meetingId, $userId);
    $otpPayload = [
        'otp'        => $otpCode,
        'qr_code'    => $rawCode,
        'user_id'    => $userId,
        'meeting_id' => $meetingId,
        'event_id'   => $eventId,
        'created_at' => time(),
        'expires_at' => time() + 600,
    ];

    if (file_put_contents($otpPath, json_encode($otpPayload), LOCK_EX) === false) {
        $reject();
        qr_json(['status' => 'error', 'message' => 'Gagal menyimpan kode verifikasi sementara'], 500);
    }

    $safeName    = htmlspecialchars((string) ($member['full_name'] ?? $member['username']), ENT_QUOTES, 'UTF-8');
    $safeMeeting = htmlspecialchars($meetingTitle, ENT_QUOTES, 'UTF-8');
    $emailSubject = "Kode Verifikasi Presensi GIAT - {$meetingTitle}";
    $emailHtml = "
        <div style='font-family:Segoe UI,Arial,sans-serif;max-width:480px;margin:0 auto;padding:24px;border:1px solid #e2e8f0;border-radius:12px;'>
            <h2 style='color:#0f172a;margin-top:0;'>Verifikasi Presensi GIAT</h2>
            <p style='color:#334155;'>Halo <b>{$safeName}</b>,</p>
            <p style='color:#334155;'>Masukkan kode 5 angka berikut pada perangkat Anda untuk menyelesaikan presensi <b>{$safeMeeting}</b>:</p>
            <div style='background:#f1f5f9;border-radius:8px;padding:16px;text-align:center;font-size:28px;font-weight:700;letter-spacing:8px;color:#1d4ed8;margin:20px 0;'>
                {$otpCode}
            </div>
            <p style='color:#64748b;font-size:12px;'>Kode ini berlaku selama 10 menit dan hanya dapat digunakan satu kali.</p>
        </div>
    ";

    try {
        kirimEmailMailpit(
            to: $recipientEmail,
            subject: $emailSubject,
            htmlBody: $emailHtml
        );
    } catch (Throwable $e) {
        @unlink($otpPath);
        $reject();
        write_log('Gagal mengirim email OTP presensi: ' . $e->getMessage(), 'ERROR', [
            'user_id'    => $userId,
            'meeting_id' => $meetingId,
            'email'      => $recipientEmail,
        ]);
        qr_json(['status' => 'error', 'message' => 'Gagal mengirim kode verifikasi ke email: ' . $e->getMessage()], 500);
    }

    // Sekali pakai global: kosongkan kode QR aktif agar layar PIC berganti QR baru
    ftruncate($handle, 0);
    fflush($handle);
    flock($handle, LOCK_UN);
    fclose($handle);
    qr_mark_used($rawCode);

    qr_json([
        'status'  => 'pending_verification',
        'step'    => 'verify_otp',
        'message' => 'Kode verifikasi 5 angka telah dikirim ke email Anda',
        'data'    => [
            'email'      => $recipientEmail,
            'user_id'    => $userId,
            'meeting_id' => $meetingId,
            'event_id'   => $eventId,
            'expires_in' => 600,
        ],
    ], 200);
}

qr_json(['status' => 'error', 'message' => 'Method tidak didukung'], 405);