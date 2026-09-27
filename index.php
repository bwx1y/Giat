<?php
// ==========================================
// 1. LOAD ENV
// ==========================================
// Load environment variables from .env file
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        if (str_contains($line, '=')) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value, " \t\n\r\0\x0B\"'");
            putenv("$name=$value");
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// set header
header('Content-Type: application/json');

// ==========================================
// 2. STATIC FILES (Ignore if not /api request)
// ==========================================
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/');
if ($uri === '') $uri = '/';

$filePath = __DIR__ . $uri;
if ($uri !== '/' && file_exists($filePath) && !is_dir($filePath)) {
    return false;
}

// load file
if (file_exists(__DIR__ . '/src/helper.php')) require_once __DIR__ . '/src/helper.php';
if (file_exists(__DIR__ . '/src/functions.php')) require_once __DIR__ . '/src/function.php';

spl_autoload_register(function ($class) {
    $path = str_replace('\\', '/', $class);
    $file = __DIR__ . '/' . $path . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// ==========================================
// 3. VALIDATE /api PREFIX
// ==========================================
$segments = explode('/', $uri)
        |> array_filter(...)
        |> array_values(...);

// Check if URL starts with 'api'
if (empty($segments) || $segments[0] !== 'api') {
    http_response_code(404);
    echo json_encode([
        'status' => 404,
        'message' => 'Endpoint not found. Use /api/ prefix (example: /api/user)'
    ]);
    exit;
}

// Remove 'api' segment from URL segments
array_shift($segments);

// If accessing /api without an endpoint
if (empty($segments)) {
    echo json_encode([
        'status' => 200,
        'message' => 'API server is online'
    ]);
    exit;
}

// ==========================================
// 4. ROUTE MAPPER TO CONTROLLER
// ==========================================
// First segment after /api/ is the resource name (e.g., /api/user -> UserController)
$resource = ucfirst($segments[0]);
$controllerName = "controller\\" . $resource . "Controller";
$idParam = $segments[1] ?? null;

// Check controller availability
if (!class_exists($controllerName)) {
    http_response_code(404);
    echo json_encode([
        'status' => 404,
        'message' => 'Endpoint not found'
    ]);
    exit;
}

// Store ID parameter in $_GET if present
if ($idParam !== null) {
    $_GET['id'] = $idParam;
}

// Execute controller
$controller = new $controllerName();

if ($controller instanceof \controller\Controller) {
    $controller->handleRequest($idParam);
} else {
    http_response_code(500);
    echo json_encode([
        'status' => 500,
        'message' => "Class $controllerName must extend the base Controller class"
    ]);
}