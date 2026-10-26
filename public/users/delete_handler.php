<?php

require_once __DIR__ . '/../../config/session.php';

require_login();
$currentUser = current_user();
if (($currentUser['role']?? '') !== 'Admin') {
    die("Akses ditolak: Hanya Admin yang dapat meghapus pengguna.");
}
if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$id = $_POST['id']?? null;

if(!$id || !is_numeric($id)) {
    header("Location: index.php?error=ID pengguna tidak ditemukan");
    exit;
}
$id = (int)$id;

if ($id === (int) $currentUser['id']) {
    die("Aksi ditolak: Anda tidak dapat menghapus akun Anda sendiri");
}
$stmt = $applicationDb->prepare("UPDATE users SET deleted_at = NOW() WHERE id = :id AND deleted_at IS NULL");
$stmt->execute([':id' => $id]);
header("Location: index.php?success=Pengguna berhasil dihapus");
exit;