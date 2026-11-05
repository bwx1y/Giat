<?php

$page = $_GET['page'] ?? 1;
$size = $_GET['size'] ?? 10;

$startDate = !empty($_GET['start_date']) ? $_GET['start_date'] . ' 00:00:00' : null;
$endDate   = !empty($_GET['end_date'])   ? $_GET['end_date'] . ' 23:59:59' : null;

$currentUser = current_user();

$filter = [
    [
        'key' => 'user_id',
        'query' => 'user_id = :user_id',
        'value' => $currentUser['id'],
    ],
    [
        'key' => 'start_date',
        'query' => '(:start_date IS NULL OR raw_date >= :start_date::timestamp)',
        'value' => $startDate,
    ],
    [
        'key' => 'end_date',
        'query' => '(:end_date IS NULL OR raw_date <= :end_date::timestamp)',
        'value' => $endDate,
    ],
];

$stmt = queryBuilder(
    'vw_user_attendance_history',
    filters: $filter,
    size: $size,
    page: $page,
);
$result = $stmt->fetch();
//var_dump($result);