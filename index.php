<?php
require_once __DIR__ . '/src/autoload.php';
require_once __DIR__ . '/src/cors.php';

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

if (!empty($segments) && $segments[0] === 'docs') {
    if ($_ENV['MODE'] === 'development') {

        if (isset($segments[1]) && $segments[1] === 'schema.yaml') {
            $schemaPath = __DIR__ . '/document/schema.yaml';
            if (file_exists($schemaPath)) {
                header('Content-Type: text/yaml');
                readfile($schemaPath);
                exit;
            }
        }

        $indexPath = __DIR__ . '/document/index.html';
        if (file_exists($indexPath)) {
            header('Content-Type: text/html; charset=utf-8');
            readfile($indexPath);
            exit;
        }
    }

    http_response_code(404);
    echo json_encode([
        'status' => 404,
        'message' => 'Endpoint not found.'
    ]);
    exit;
}

// Check if URL starts with 'api'
if (empty($segments) || $segments[0] !== 'api') {
    http_response_code(404);
    echo json_encode([
        'status' => 404,
        'message' => 'Endpoint not found.'
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
$baseDir = __DIR__ . '/controller';
$namespaceParts = ['controller'];
$remainingSegments = $segments;
$idParam = null;

// Check controller availability
while (!empty($remainingSegments)) {
    $current = $remainingSegments[0];

    $testPath = $baseDir . '/' . $current;

    if (is_dir($testPath)) {
        $namespaceParts[] = $current;
        $baseDir = $testPath;
        array_shift($remainingSegments);
    } else {
        break;
    }
}

if (!empty($remainingSegments)) {
    $controllerSegment = array_shift($remainingSegments);
    $namespaceParts[] = ucfirst($controllerSegment) . 'Controller';
} else {
    $namespaceParts[] = 'IndexController';
}

$controllerName = implode('\\', $namespaceParts);

if (!empty($remainingSegments)) {
    $idParam = array_shift($remainingSegments);
    $_GET['id'] = $idParam;
}

$classFilePath = __DIR__ . '/' . str_replace('\\', '/', $controllerName) . '.php';

if (file_exists($classFilePath)) {
    require_once $classFilePath;
}

if (!class_exists($controllerName)) {
    http_response_code(404);
    echo json_encode([
        'status' => 404,
        'message' => "Endpoint not found: Class $controllerName standard file not found"
    ]);
    exit;
}

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