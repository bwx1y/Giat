<?php
// perbaiki routing

require_once __DIR__ . '/config/init.php';

// CORS support (allow all origins, handles SSE withCredentials & preflight OPTIONS)
$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
header("Access-Control-Allow-Origin: $origin");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-CSRF-Token");
header("Access-Control-Max-Age: 86400");

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

$file = __DIR__ . '/public' . $uri;
if ($uri !== '/' && file_exists($file) && !is_dir($file) && pathinfo($file, PATHINFO_EXTENSION) !== 'php') {
    $mimes = [
        'css' => 'text/css',
        'js' => 'text/javascript',
        'json' => 'application/json',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
        'map' => 'application/json',
    ];
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $mime = $mimes[$ext] ?? (function_exists('mime_content_type') ? mime_content_type($file) : 'application/octet-stream');
    header("Content-Type: $mime");
    header("Content-Length: " . filesize($file));
    readfile($file);
    exit;
}

if (!function_exists('resolveRoute')) {
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
}

// Semua UI route sudah file-based via resolveRoute:
// /dashboard                => public/dashboard/index.php
// /users                    => public/users/index.php
// /anggota                  => public/anggota/index.php
// /event                    => public/event/index.php
// /event/{id}               => public/event/[event_id]/index.php
// /event/{id}/subevent/{id} => public/event/[event_id]/subevent/[subevent_id]/index.php
// /absensi                  => public/absensi/index.php
// /statistik                => public/statistik/index.php
// /riwayat                  => public/riwayat/index.php
$resolved = resolveRoute(__DIR__ . '/public', $uri);

$ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$target = $resolved !== null ? str_replace(__DIR__ . '/', '', $resolved['file']) : '404';
write_log("$ip $method $uri -> $target", 'INFO');

$cleanPath = '/' . trim($uri, '/');

if ($cleanPath === '/login' && is_logged_in()) {
    header("Location: " . (has_role('Admin') ? '/admin/event' : '/'));
    exit;
}

if ($cleanPath === '/admin' || str_starts_with($cleanPath, '/admin/')) {
    require_role('Admin');
}

if ($cleanPath === '/member' || str_starts_with($cleanPath, '/member/')) {
    require_role('Member');
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
