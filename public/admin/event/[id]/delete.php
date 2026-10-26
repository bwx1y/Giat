<?php

global $applicationDb;

$idx = $_GET['id'] ?? null;
if (!$idx) {
    http_response_code(400);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $stmt = $applicationDb->prepare("UPDATE events SET deleted_at = CURRENT_TIMESTAMP WHERE id = :id AND deleted_at IS NULL");

    $stmt->bindParam(':id', $idx);
    $stmt->execute();

    http_response_code(204);
    exit();
}

http_response_code(404);
exit();
