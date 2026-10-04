<?php

namespace controller;

use core\Pagination;
use core\Request;
use JetBrains\PhpStorm\NoReturn;
use JwtHelper;
use Throwable;

abstract class Controller
{
    /**
     * Role-based authorization configuration for this component/controller.
     *
     * - `['*']`      : Authorizes ALL roles (public/unrestricted access within the auth context).
     * - `['Admin']`  : Restricts authorization strictly to the specified role(s).
     * - `null`       : Disables authorization check or applies default global fallback behavior.
     *
     * @var array<string>|null
     */
    protected array|null $authorization = null;
    protected array $user = [];
    protected Pagination|null $pagination = null {
        get {
            $method = $_SERVER['REQUEST_METHOD'];
            if ($method === 'GET') {
                $page = $this->getQuery('page', 1);
                $limit = $this->getQuery('limit', 10);

                $this->pagination = new Pagination($page, $limit);

                return $this->pagination;
            }

            $this->json([
                'error' => "Invalid request method not allow get pagination in this method",
            ], 500);
        }
    }

    /**
     * Send a JSON response with an HTTP status code.
     */
    #[NoReturn]
    protected function json(mixed $data, $statusCode = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);

        if (is_array($data) && isset($data['error'])) {
            echo json_encode([
                "status" => $statusCode,
                "message" => $data['error']
            ]);
            exit;
        }

        $response = [["status" => $statusCode, "data" => $data]];
        if ($this->pagination != null) {
            $meta = $this->pagination->getMeta();

            if (empty($meta)) {
                http_response_code(500);
                echo json_encode([
                    "status" => 500,
                    "message" => "Pagination is empty"
                ]);
            }

            $response['meta'] = $this->pagination->getMeta();
        }

        echo json_encode($response);
        exit;
    }

    /**
     * Retrieve query parameters from the URL ($_GET).
     */
    protected function getQuery(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $_GET;
        }

        return $_GET[$key] ?? $default;
    }

    /**
     * Parse and retrieve JSON payload from the request body.
     */
    private function getRequestBody(): array
    {
        $rawInput = $_SERVER['RAW_REQUEST_BODY'] ?? file_get_contents('php://input');

        if (empty($rawInput)) {
            return $_POST;
        }

        $contentType = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'application/json')) {
            $data = json_decode($rawInput, true);
            return is_array($data) ? $data : [];
        }

        if (in_array($_SERVER['REQUEST_METHOD'], ['PUT', 'PATCH', 'DELETE'])) {
            parse_str($rawInput, $parsedData);
            return is_array($parsedData) ? $parsedData : [];
        }

        return $_POST;
    }

    /**
     * validate authorization
     */

    private function authorize(): void
    {
        $headers = null;

        if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
            $headers = trim($_SERVER['HTTP_AUTHORIZATION']);
        } elseif (!empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $headers = trim($_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
        } elseif (!empty($_SERVER['Authorization'])) {
            $headers = trim($_SERVER['Authorization']);
        } elseif (function_exists('getallheaders')) {
            $allHeaders = getallheaders();
            if (!empty($allHeaders['Authorization'])) {
                $headers = trim($allHeaders['Authorization']);
            } elseif (!empty($allHeaders['authorization'])) {
                $headers = trim($allHeaders['authorization']);
            }
        }

        if (empty($headers)) {
            $this->json(['error' => 'Missing authorization header'], 401);
        }

        $token = null;
        if (preg_match('/Bearer\s+(\S+)/i', $headers, $matches)) {
            $token = $matches[1];
        }

        if (empty($token)) {
            $this->json(['error' => 'Invalid or missing Bearer token'], 401);
        }

        try {
            $entity = JwtHelper::validateToken($token);
        } catch (Throwable $e) {
            $this->json(['error' => 'Unauthorized: ' . $e->getMessage()], 401);
        }

        if (!$entity) {
            $this->json(['error' => 'Unauthorized: Token is invalid or expired'], 401);
        }

        if (in_array('*', $this->authorization, true)) {
            $this->user = $entity;
            return;
        }

        $userRole = $entity['role'] ?? null;
        if (!in_array($userRole, $this->authorization, true)) {
            $this->json(['error' => 'Forbidden: You do not have permission to access this resource'], 403);
        }

        $this->user = $entity;
    }

    /**
     * Handle incoming HTTP requests and dispatch them based on the HTTP method.
     */
    #[NoReturn]
    public function handleRequest(?string $id = null): void
    {
        $method = $_SERVER['REQUEST_METHOD'];

        $body = $this->getRequestBody();
        $request = new Request($body);

        if ($this->authorization) {
            $this->authorize();
        }

        switch ($method) {
            case 'GET':
                $id ? $this->show($id) : $this->index();
            case 'POST':
                $this->store($request);
            case 'PUT':
            case 'PATCH':
                if (!$id) {
                    $this->json(['error' => 'ID parameter is required to update data'], 400);
                }
                $this->update($request, $id);
            case 'DELETE':
                if (!$id) {
                    $this->json(['error' => 'ID parameter is required to delete data'], 400);
                }
                $this->destroy($id);
            default:
                $this->json(['error' => 'Method Not Allowed'], 405);
        }
    }

    // Default CRUD handlers (can be overridden by child controllers)
    #[NoReturn]
    protected function index(): void
    {
        $this->json(['error' => 'Endpoint not found'], 404);
    }

    #[NoReturn]
    protected function show(string $id): void
    {
        (void)$id;

        $this->json(['error' => 'Endpoint not found'], 404);
    }

    #[NoReturn]
    protected function store(Request $request): void
    {
        (void)$request;
        $this->json(['error' => 'Endpoint not found'], 404);
    }

    #[NoReturn]
    protected function update(Request $request, string $id): void
    {
        (void)$request;
        (void)$id;

        $this->json(['error' => 'Endpoint not found'], 404);
    }


    #[NoReturn]
    protected function destroy(string $id): void
    {
        (void)$id;

        $this->json(['error' => 'Endpoint not found'], 404);
    }
}