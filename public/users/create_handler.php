<?php

require_once __DIR__ . '/../../config/session.php';

require_login();

$currentUser = current_user();
if (($currentUser['role']?? '') !== 'Admin') {
    die("Akses ditolak: Hanya Admin yang dapat menambahkan pengguna");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$username   = trim($_POST['username'] ?? '');
$fullName   = trim($_POST['full_name'] ?? '');
$password   = ($_POST['password'] ?? '');
$role       = ($_POST['role'] ?? 'Member');

if (empty($username) || empty($fullName) || empty($password)) {
    header("Location: index.php?error=Semua bidang wajib diisi");
    exit;
}

if (!in_array($role, ['Admin', 'Member'])) {
    $role = 'Member';
}

$stmtCheck = $applicationDb->prepare("SELECT id FROM users WHERE username = :username AND deleted_at IS NULL LIMIT 1");
$stmtCheck->execute([':username' => $username]);

if($stmtCheck->fetch()) {
    header("Location: index.php?error=Username sudah digunakan oleh akun lain");
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
