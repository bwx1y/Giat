<?php

$id = $_GET['id'];
if (empty($id)) {
    header('Location: /admin/event/');
    exit;
}

$page = $_GET['page'] ?? 1;
$size = $_GET['size'] ?? 20;
$search = $_GET['search'] ?? null;
$role = $_GET['role'] ?? null;

$filters = [
    [
        'key' => 'search',
        'query' => 'full_name LIKE :search',
        'value' => $search ? "$search%" : null,
    ],
    [
        'key' => 'event_id',
        'query' => 'event_id = :event_id',
        'value' => $id,
    ],
    [
        'key' => 'role',
        'query' => 'role = :role',
        'value' => $role,
    ]
];

$stmt = queryBuilder(
    table: 'vw_member_event',
    filters: $filters,
    size: $size,
    page: $page,
);

$result = $stmt->fetchAll();
var_dump($result);