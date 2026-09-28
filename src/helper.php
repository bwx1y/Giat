<?php

include_once "JwtHelper.php";

if (!function_exists('dd')) {
    /**
     * Dump response and die (JSON-friendly for API).
     */
    function dd(...$vars): void
    {
        http_response_code(500);

        header('Content-Type: application/json; charset=utf-8');

        $dumps = [];
        foreach ($vars as $var) {
            $dumps[] = $var;
        }

        echo json_encode([
            '__DD__' => count($dumps) === 1 ? $dumps[0] : $dumps
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        exit;
    }
}

if (!function_exists('dump')) {
    /**
     * Dump response without stopping execution.
     */
    function dump(...$vars): void
    {
        foreach ($vars as $var) {
            echo "<pre>";
            var_dump($var);
            echo "</pre>";
        }
    }
}