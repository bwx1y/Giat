<?php

require_once __DIR__ . '/../../config/session.php';

require_login();

$currentUser = current_user();
if (($currentUser['role']?? '') !== 'Admin') {
    die("Akses ditolak: Halaman ini hanya dapat diakses oleh Admin.");
}
$stmt = $applicationDb->query("SELECT id, username, full_name, role, active, created_at FROM 
users WHERE deleted_at IS NULL ORDER BY id ASC");

$users = $stmt->fetchAll();

