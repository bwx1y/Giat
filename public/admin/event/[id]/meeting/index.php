<?php

$id = $_GET['id'] ?? null;
if (empty($id)) {
    header('Location: /admin/event/');
    exit;
}

$page = $_GET['page'] ?? 1;
$size = $_GET['size'] ?? 20;
$search = $_GET['search'] ?? null;
$status = $_GET['status'] ?? null;

$filters = [
    [
        'key' => 'event_id',
        'query' => 'event_id = :event_id',
        'value' => $id,
    ],
    [
        'key' => 'deleted_at',
        'query' => 'deleted_at IS NULL',
        'value' => null,
    ],
    [
        'key' => 'search',
        'query' => 'title ILIKE :search',
        'value' => $search ? "%$search%" : null,
    ],
    [
        'key' => 'status',
        'query' => 'status = :status',
        'value' => $status,
    ]
];

$stmt = queryBuilder(
    table: 'meetings',
    filters: $filters,
    size: $size,
    page: $page,
);

$meetings = $stmt->fetchAll();
