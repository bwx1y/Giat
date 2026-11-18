<?php

require_login();

$currentUser = current_user();
$currentUserId = $currentUser['id'] ?? null;

$page = $_GET['page'] ?? 1;
$size = $_GET['size'] ?? 10;

$search = $_GET['search'] ?? null;
$status = $_GET['status'] ?? null;

$filters = [
    [
        'key' => 'user_id',
        'query' => 'ue.user_id = :user_id',
        'value' => $currentUserId,
    ],
    [
        'key' => 'search',
        'query' => 've.title ILIKE :search',
        'value' => $search ? "%$search%" : null,
    ],
    [
        'key' => 'status',
        'query' => 've.status = :status',
        'value' => $status,
    ],
    [
        'key' => 'active_ue',
        'query' => 'ue.deleted_at IS NULL',
        'value' => true,
    ],
];

$joins = [
    [
        'type' => 'INNER',
        'table' => 'vw_event ve',
        'on' => 've.id = ue.event_id',
    ],
];

$columns = [
    've.*',
    'ue.role AS my_role',
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
