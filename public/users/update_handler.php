<?php

require_once __DIR__ . '/../../config/session.php';

require_login();

$currentUser = current_user();
if (($currentUser['role']?? '') !== 'Admin') {
    die("Akses ditolak: Hanya Admin yang dapat memperbarui pengguna.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}
$id         = (int) ($_POST['id'] ?? 0);
$fullName   = trim($_POST['full_name'] ?? '');
$role       = ($_POST['role'] ?? 'Member');
$password   = $_POST['password'] ?? '';

if (!$id || empty($fullName)) {
    header("Location: index.php?error= ID dan Nama Lengkap wajib diisi");
    exit;
}
if (!in_array($role, ['Admin', 'Member'])) {
    $role = 'Member';
}

$stmtCheck = $applicationDb->prepare("SELECT id FROM users WHERE id = :id AND deleted_at IS NULL LIMIT 1");
$stmtCheck->execute([':id' => $id]);

if (!$stmtCheck->fetch()) {
    header("Location: index.php?error=ID pengguna tidak ditemukan");
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
        ':role' => $role,
        ':password' => $hashedPassword,
        ':id' => $id
    ]);
} else {
    $stmt = $applicationDb->prepare("
    UPDATE users
    SET full_name = :full_name, role = :role
    WHERE id = :id AND deleted_at IS NULL
    ");
    $stmt->execute([
        ':full_name' => $fullName,
        ':role' => $role,
        ':id' => $id
    ]);
}
header("Location: index.php?success=Data pengguna berhasil diperbarui");
exit;