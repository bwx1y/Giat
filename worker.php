<?php

ignore_user_abort(true);

// Bootstrap config sekali per worker
require_once __DIR__ . '/config/init.php';

$handler = static function () {
    require __DIR__ . '/router.php';
};

$maxRequests = (int) ($_SERVER['MAX_REQUESTS'] ?? 500);

// Fallback jika dijalankan di luar FrankenPHP
if (!function_exists('frankenphp_handle_request')) {
    $handler();
    return;
}

for ($nbRequests = 0; !$maxRequests || $nbRequests < $maxRequests; ++$nbRequests) {
    $keepRunning = \frankenphp_handle_request($handler);
    gc_collect_cycles();

    if (!$keepRunning) {
        break;
    }
}
