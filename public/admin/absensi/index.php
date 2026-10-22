<?php
require_once __DIR__ . "/../../../config/query-builder.php";

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

$result = $stmtResult->fetchAll(PDO::FETCH_ASSOC);
//var_dump($result);

$stmtEvent = queryBuilder(
    table: 'events',
    filters: [
        [
            'key' => 'deleted_at_check',
            'query' => 'events.deleted_at IS NULL',
            'value' => 'bypass',
        ]
    ],
);

$eventList = $stmtEvent->fetchAll(PDO::FETCH_ASSOC);
//var_dump($eventList);