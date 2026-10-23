<?php

require_once __DIR__ . "/../config/session.php";
require_once __DIR__ . "/../config/validation.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /index.php");
    exit;
}

$redirectTo = $_SERVER['HTTP_REFERER'] ?? '/index.php';

$data = validate([
    'username' => 'required|string',
    'password' => 'required|string',
], $redirectTo);

global $applicationDb;
if (!$applicationDb instanceof PDO) {
    $applicationDb = require __DIR__ . "/../config/database.php";
}

$stmt = $applicationDb->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
$stmt->bindParam(':username', $data['username']);
$stmt->execute();

$entity = $stmt->fetch();

if ($entity && password_verify($data['password'], $entity['password'])) {
    if (isset($entity['active']) && !$entity['active']) {
        $_SESSION['field_errors']['username'][] = 'Akun dinonaktifkan.';
        $_SESSION['old_input'] = ['username' => $data['username']];
        header("Location: " . $redirectTo);
        exit;
    }

    set_user_session($entity);
    header("Location: /index.php");
    exit;
}

$_SESSION['field_errors']['username'][] = 'Username atau password salah.';
$_SESSION['old_input'] = ['username' => $data['username']];
header("Location: " . $redirectTo);
exit;