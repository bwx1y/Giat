<?php

// 1. Auto load semua file config & helper
require_once __DIR__ . '/config/init.php';

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// 2. Jika request ke file statis (CSS, JS, Gambar), sajikan langsung
$file = __DIR__ . '/public' . $uri;
if ($uri !== '/' && file_exists($file) && !is_dir($file) && pathinfo($file, PATHINFO_EXTENSION) !== 'php') {
    return false;
}

// 3. Resolver untuk clean URL dan dynamic path parameter [param]
function resolveRoute(string $baseDir, string $uri): ?array {
    $path = trim($uri, '/');
    $baseDir = rtrim($baseDir, '/');

    if ($path === '') {
        $index = "$baseDir/index.php";
        return is_file($index) ? ['file' => $index, 'params' => []] : null;
    }

    $segments = explode('/', $path);
    $curr = $baseDir;
    $params = [];

    foreach ($segments as $i => $seg) {
        $isLast = ($i === count($segments) - 1);

        if ($isLast) {
            if (is_file("$curr/$seg")) {
                return ['file' => "$curr/$seg", 'params' => $params];
            }
            if (is_file("$curr/$seg.php")) {
                return ['file' => "$curr/$seg.php", 'params' => $params];
            }
            if (is_dir("$curr/$seg") && is_file("$curr/$seg/index.php")) {
                return ['file' => "$curr/$seg/index.php", 'params' => $params];
            }
        } else {
            if (is_dir("$curr/$seg")) {
                $curr .= "/$seg";
                continue;
            }
        }

        $entries = is_dir($curr) ? scandir($curr) : false;
        if ($entries === false) {
            return null;
        }

        $matched = false;

        if ($isLast) {
            foreach ($entries as $entry) {
                if (preg_match('/^\[([a-zA-Z0-9_]+)\]\.php$/', $entry, $m)) {
                    $params[$m[1]] = $seg;
                    return ['file' => "$curr/$entry", 'params' => $params];
                }
            }
            foreach ($entries as $entry) {
                if ($entry === '.' || $entry === '..') continue;
                if (is_dir("$curr/$entry") && preg_match('/^\[([a-zA-Z0-9_]+)\]$/', $entry, $m)) {
                    if (is_file("$curr/$entry/index.php")) {
                        $params[$m[1]] = $seg;
                        return ['file' => "$curr/$entry/index.php", 'params' => $params];
                    }
                }
            }
        }

        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') continue;
            if (is_dir("$curr/$entry") && preg_match('/^\[([a-zA-Z0-9_]+)\]$/', $entry, $m)) {
                $params[$m[1]] = $seg;
                $curr .= "/$entry";
                $matched = true;
                break;
            }
        }

        if (!$matched) {
            return null;
        }
    }

    if (is_dir($curr) && is_file("$curr/index.php")) {
        return ['file' => "$curr/index.php", 'params' => $params];
    }

    return null;
}

// 4. Resolver & Access Logger
$resolved = resolveRoute(__DIR__ . '/public', $uri);
$ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$target = $resolved !== null ? str_replace(__DIR__ . '/', '', $resolved['file']) : '404';
write_log("$ip $method $uri -> $target", 'INFO');

// 5. Role & Auth Guard
$cleanPath = '/' . trim($uri, '/');

if ($cleanPath === '/login' && is_logged_in()) {
    header("Location: " . (has_role('Admin') ? '/admin/event' : '/'));
    exit;
}

if ($cleanPath === '/admin' || str_starts_with($cleanPath, '/admin/')) {
    require_role('Admin');
}

if ($resolved !== null) {
    foreach ($resolved['params'] as $key => $value) {
        $_GET[$key] = $value;
    }
    $route_params = $resolved['params'];

    require $resolved['file'];
} else {
    http_response_code(404);
    echo "404 Not Found";
}