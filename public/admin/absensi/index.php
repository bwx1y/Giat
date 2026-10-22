<?php
require_once "../../../config/query-builder.php";

$page = $_GET['page'] ?? 1;
$size = $_GET['size'] ?? 10;
$offside = ($page - 1) * $size;

$search = $_GET['search'] ?? null;
$event = $_GET['event'] ?? null;
$status = $_GET['status'] ?? null;

$filters = [
    [
        'key' => 'search',
        'query' => 'member_name ILIKE :search',
        'value' => $search ? "%$search%" : null,
    ],
    [
        'key' => 'event',
        'query' => 'event_id = :event',
        'value' => $event,
    ],
    [
        'key' => 'status',
        'query' => 'status = :status',
        'value' => $status,
    ]
];

$stmtResult = queryBuilder(
    table: 'vw_absent',
    filters: $filters,
    size: $size,
    page: $page,
);

$stmtEvent = queryBuilder(
    table: 'event',
    filters: [
        'key' => 'delete_at',
        'query' => 'event.delete_at :delete_at',
        'value' => 'NOT NULL',
    ],
);

$stmtEvent->execute();
$stmtResult->execute();

$eventList = $stmtResult->fetchAll();
$result = $stmtResult->fetchAll();




