<?php

ignore_user_abort(true);

$handler = static function () {
    // 1. Refresh superglobals agar $_GET, $_POST, $_SERVER selalu membawa data request terbaru
    $rawBody = '';
    if (function_exists('frankenphp_get_request_body')) {
        $rawBody = frankenphp_get_request_body() ?? '';
    } else {
        $rawBody = file_get_contents('php://input') ?: '';
    }

    // Fallback jika array superglobal belum terinisialisasi
    $_GET = $_GET ?? [];
    $_POST = $_POST ?? [];
    $_COOKIE = $_COOKIE ?? [];
    $_FILES = $_FILES ?? [];
    $_REQUEST = array_merge($_GET, $_POST);

    // 2. Tangkap output dari index.php
    ob_start();

    try {
        // Gunakan include (BUKAN require) agar file index.php dieksekusi ulang di setiap request
        include __DIR__ . '/index.php';
    } catch (Throwable $e) {
        http_response_code(500);
        echo 'Internal Server Error: ' . htmlspecialchars($e->getMessage());
    }

    // 3. Kirim output ke client
    $output = ob_get_clean();
    echo $output;
};

$maxRequests = (int)($_SERVER['MAX_REQUESTS'] ?? 0);
for ($nbRequests = 0; !$maxRequests || $nbRequests < $maxRequests; ++$nbRequests) {
    $keepRunning = frankenphp_handle_request($handler);

    // Garbage collection untuk mencegah memory leak
    gc_collect_cycles();

    if (!$keepRunning) {
        break;
    }
}