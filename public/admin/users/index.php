<?php

require_once __DIR__ . '/../../config/session.php';

require_login();

$currentUser = current_user();
if (($currentUser['role']?? '') !== 'Admin') {
    die("Akses ditolak: Halaman ini hanya dapat diakses oleh Admin.");
}

$search = trim($_GET['search'] ?? $_GET['q'] ?? '');
$role   = trim($_GET['role'] ?? '');

$sql = "SELECT id, username, full_name, role, active, created_at FROM users WHERE deleted_at IS NULL";
$params = [];

if (in_array($role, ['Admin', 'Member'])) {
    $sql .= " AND role = :role";
    $params[':role'] = $role;
}
if ($search !== '') {
    $sql .= " AND (username ILIKE :search OR full_name ILIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

$sql .= " ORDER BY id ASC";

$stmt = $applicationDb->prepare($sql);
$stmt->execute($params);

$users = $stmt->fetchAll();
