<?php

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/validation.php';

require_login();

$currentUser = current_user();
if (($currentUser['role'] ?? '') !== 'Admin') {
    die("Akses ditolak: Hanya Admin yang dapat memperbarui pengguna.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$redirectTo = $_SERVER['HTTP_REFERER'] ?? 'index.php';

$data = validate([
    'id'        => 'required|numeric',
    'full_name' => 'required|min:3|max:255',
    'role'      => 'required|in:Admin,Member',
    'password'  => 'min:6',
], $redirectTo);

$id       = (int) $data['id'];
$fullName = trim($data['full_name']);
$role     = $data['role'];
$password = $data['password'] ?? '';

$stmtCheck = $applicationDb->prepare("SELECT id FROM users WHERE id = :id AND deleted_at IS NULL LIMIT 1");
$stmtCheck->execute([':id' => $id]);

if (!$stmtCheck->fetch()) {
    $_SESSION['field_errors']['id'][] = 'Pengguna tidak ditemukan.';
    header("Location: " . $redirectTo);
    exit;
}

if (!empty($password)) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $applicationDb->prepare("
        UPDATE users
        SET full_name = :full_name, role = :role, password = :password
        WHERE id = :id AND deleted_at IS NULL
    ");
    $stmt->execute([
        ':full_name' => $fullName,
        ':role'      => $role,
        ':password'  => $hashedPassword,
        ':id'        => $id,
    ]);
} else {
    $stmt = $applicationDb->prepare("
        UPDATE users
        SET full_name = :full_name, role = :role
        WHERE id = :id AND deleted_at IS NULL
    ");
    $stmt->execute([
        ':full_name' => $fullName,
        ':role'      => $role,
        ':id'        => $id,
    ]);
}

header("Location: index.php?success=Data pengguna berhasil diperbarui");
exit;