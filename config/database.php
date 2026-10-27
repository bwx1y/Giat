<?php
include_once __DIR__ . "/load-env.php";

global $applicationDb;
if ($applicationDb instanceof PDO) {
    return $applicationDb;
}

$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '5432';
$db = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');

$dsn = "pgsql:host=$host;port=$port;dbname=$db";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

/**
 * Establishes and returns a secure PostgreSQL database connection via PDO.
 *
 * This script retrieves database credentials from environment variables
 * and configures the PDO instance with secure defaults (exception error mode,
 * associative array fetching, and disabled emulated prepares).
 *
 * @return PDO Returns an active PDO database connection instance.
 *
 * @throws PDOException If the connection fails or authentication is invalid (triggers HTTP 500 and terminates execution).
 */
try {
    $applicationDb = new PDO($dsn, $user, $pass, $options);
    return $applicationDb;
} catch (PDOException $e) {
    if (function_exists('write_log')) {
        write_log("Database connection failed: " . $e->getMessage(), 'CRITICAL');
    }
    http_response_code(500);

    die("Error database connection: " . $e->getMessage());
}