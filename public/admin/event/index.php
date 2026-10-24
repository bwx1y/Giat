<?php

$page = $_GET['page'] ?? 1;
$size = $_GET['size'] ?? 10;

$search = $_GET['search'] ?? null;
$status = $_GET['status'] ?? null;

$filter = [
    [
        'key' => 'search',
        'query' => 'title ILIKE :search',
        'value' => $search ? "$search%" : null,
    ],
    [
        'key' => 'status',
        'query' => 'title = :status',
        'value' => $status,
    ],
];

$stmt = queryBuilder(
    table: 'vw_event',
    filters: $filter,
    size: $size,
    page: $page,
);

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
//var_dump($result);