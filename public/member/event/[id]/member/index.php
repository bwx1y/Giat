<?php

require_login();

$id = $_GET['id'] ?? null;
if (empty($id)) {
    header('Location: /member/event');
    exit;
}

$currentUser = current_user();
$currentUserId = $currentUser['id'] ?? null;

$page = $_GET['page'] ?? 1;
$size = $_GET['size'] ?? 20;
$search = $_GET['search'] ?? null;
$role = $_GET['role'] ?? null;

$filters = [
    [
        'key' => 'event_id',
        'query' => 'ue.event_id = :event_id',
        'value' => $id,
    ],
    [
        'key' => 'user_id',
        'query' => 'ue.user_id = :user_id',
        'value' => $currentUserId,
    ],
    [
        'key' => 'search',
        'query' => 'u.full_name ILIKE :search',
        'value' => $search ? "%$search%" : null,
    ],
    [
        'key' => 'role',
        'query' => 'ue.role = :role',
        'value' => $role,
    ],
    [
        'key' => 'active_ue',
        'query' => 'ue.deleted_at IS NULL',
        'value' => true,
    ],
    [
        'key' => 'active_u',
        'query' => 'u.deleted_at IS NULL',
        'value' => true,
    ]
];

$joins = [
    [
        'type' => 'INNER',
        'table' => 'users u',
        'on' => 'u.id = ue.user_id',
    ]
];

$columns = [
    'ue.id',
    'ue.role',
    'ue.event_id',
    'u.id AS user_id',
    'u.full_name',
    'u.username',
];

$stmt = queryBuilder(
    table: 'user_events ue',
    columns: $columns,
    filters: $filters,
    size: $size,
    page: $page,
    joins: $joins,
);

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
var_dump($result);
