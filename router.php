<?php

// 1. Auto load semua file config & helper
require_once __DIR__ . '/config/init.php';

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// 2. Jika request ke file statis (CSS, JS, Gambar), sajikan langsung
$file = __DIR__ . '/public' . $uri;
if ($uri !== '/' && file_exists($file) && !is_dir($file)) {
    return false;
}

// 3. Routing ke file PHP di dalam folder public/
$script = __DIR__ . '/public' . $uri;
if (is_dir($script)) {
    $script = rtrim($script, '/') . '/index.php';
}

if (file_exists($script)) {
    require $script;
} else {
    http_response_code(404);
    echo "404 Not Found";
}