<?php

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/validation.php';

require_login();

$currentUser = current_user();
if (($currentUser['role']?? '') !== 'Admin') {
    die("Akses ditolak: Hanya Admin yang dapat menambahkan pengguna");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$redirectTo = $_SERVER['HTTP_REFERER'] ?? 'index.php';

$data = validate([
    'username' => 'required|min:3|max:100',
    'full_name' => 'required|min:3|max:255',
    'password' => 'required|min:6',
    'role' => 'required|in:Admin,Member', 
], $redirectTo);

$username = trim($data['username']);
$fullName = trim($data['full_name']);
$password = $data['password'];
$role = $data['role'];

$stmtCheck = $applicationDb->prepare("SELECT id FROM users WHERE username = :username AND deleted_at IS NULL LIMIT 1");
$stmtCheck->execute([':username' => $username]);

if ($stmtCheck->fetch()) {
    $_SESSION['field_errors'] ['username'] [] = 'Username sudah digunakan oleh akun lain.';
    $_SESSION['old_input'] =$data;
    header("Location: " . $redirectTo);
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $applicationDb->prepare("
    INSERT INTO users (username, full_name, password, role, active)
    VALUES (:username, :full_name, :password, :role, TRUE)
");

$stmt->execute([
    ':username' => $username,
    ':full_name' => $fullName,
    ':password' => $hashedPassword,
    ':role' => $role
]);

header("Location: index.php?success=Pengguna berhasil ditambahkan");
exit;
