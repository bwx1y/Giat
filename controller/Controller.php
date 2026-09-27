<?php

namespace controller;

use JetBrains\PhpStorm\NoReturn;

abstract class Controller
{
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
                "error" => $data['error']
            ]);
            exit;
        }

        echo json_encode(["status" => $statusCode, "data" => $data]);
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
    protected function getRequestBody(): array
    {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }

    /**
     * Handle incoming HTTP requests and dispatch them based on the HTTP method.
     */
    #[NoReturn]
    public function handleRequest(?string $id = null): void
    {
        $method = $_SERVER['REQUEST_METHOD'];

        switch ($method) {
            case 'GET':
                $id ? $this->show($id) : $this->index();
                break;
            case 'POST':
                $this->store();
                break;
            case 'PUT':
            case 'PATCH':
                if (!$id) {
                    $this->json(['error' => 'ID parameter is required to update data'], 400);
                }
                $this->update($id);
                break;
            case 'DELETE':
                if (!$id) {
                    $this->json(['error' => 'ID parameter is required to delete data'], 400);
                }
                $this->destroy($id);
                break;
            default:
                $this->json(['error' => 'Method Not Allowed'], 405);
                break;
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
        $this->json(['error' => 'Endpoint not found'], 404);
    }

    #[NoReturn]
    protected function store(): void
    {
        $this->json(['error' => 'Endpoint not found'], 404);
    }

    #[NoReturn]
    protected function update(string $id): void
    {
        $this->json(['error' => 'Endpoint not found'], 404);
    }

    #[NoReturn]
    protected function destroy(string $id): void
    {
        $this->json(['error' => 'Endpoint not found'], 404);
    }
}