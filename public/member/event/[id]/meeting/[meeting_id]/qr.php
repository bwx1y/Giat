<?php

global $applicationDb;

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
    $data = json_decode((string) file_get_contents('php://input'), true);

    if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
        qr_json(['status' => 'error', 'message' => 'Format JSON tidak valid'], 400);
    }

    $code = $data['code'] ?? '';
    if (!is_string($code) || trim($code) === '') {
        qr_json(['status' => 'error', 'message' => 'Field code wajib diisi'], 400);
    }
    $code = trim($code);

    $userId = (int) ($_SESSION['user_id'] ?? 0);
    if ($userId <= 0) {
        qr_json(['status' => 'error', 'message' => 'Login diperlukan'], 401);
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

    if ($current === '' || !hash_equals($current, $code)) {
        $reject();
        qr_json(['status' => 'error', 'message' => 'Kode QR tidak valid atau sudah digunakan'], 409);
    }

    if (isset(qr_used_codes()[$code])) {
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
        // Kode TIDAK dibakar: anggota lain masih boleh memakainya
        $reject();
        qr_json(['status' => 'error', 'message' => 'Anda sudah tercatat presensi pada meeting ini'], 409);
    }

    try {
        $insertStmt = $applicationDb->prepare("
            INSERT INTO user_meetings (user_id, meeting_id, attendance_method)
            VALUES (:user_id, :meeting_id, 'Qr')
            RETURNING id
        ");
        $insertStmt->execute(['user_id' => $userId, 'meeting_id' => $meetingId]);
        $attendanceId = (int) $insertStmt->fetchColumn();
    } catch (PDOException $e) {
        $reject();

        if ($e->getCode() === '23505') {
            qr_json(['status' => 'error', 'message' => 'Anda sudah tercatat presensi pada meeting ini'], 409);
        }

        write_log('Gagal menyimpan presensi QR: ' . $e->getMessage(), 'ERROR', [
            'user_id'    => $userId,
            'meeting_id' => $meetingId,
        ]);
        qr_json(['status' => 'error', 'message' => 'Gagal menyimpan presensi'], 500);
    }

    // Sekali pakai global: kosongkan kode aktif lalu catat di ledger
    ftruncate($handle, 0);
    fflush($handle);
    flock($handle, LOCK_UN);
    fclose($handle);
    qr_mark_used($code);

    qr_json([
        'status'  => 'success',
        'message' => 'Presensi berhasil',
        'data'    => [
            'attendance_id'     => $attendanceId,
            'user_id'           => $userId,
            'meeting_id'        => $meetingId,
            'attendance_method' => 'Qr',
        ],
    ], 201);
}

qr_json(['status' => 'error', 'message' => 'Method tidak didukung'], 405);