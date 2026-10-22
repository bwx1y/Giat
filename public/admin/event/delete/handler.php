<?php

require_once __DIR__ . '/../../../../config/validation.php';
$applicationDb = require_once __DIR__ . "/../../../../config/database.php";

$id = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = validate([
        'id' => 'required|numeric',
    ], '/admin/event/');
    $id = $body['id'];
} elseif (!empty($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
} else {
    header('Location: /admin/event/');
    exit;
}

$sql = "UPDATE events SET deleted_at = CURRENT_TIMESTAMP WHERE id = :id AND deleted_at IS NULL";
$stmt = $applicationDb->prepare($sql);
$stmt->execute(['id' => $id]);

header('Location: /admin/event/');
exit;
