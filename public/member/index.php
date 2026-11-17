<?php

global $applicationDb;

$userEventRole = user_role_event(1);
//var_dump($userEventRole);

// this for refresh materialized view
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $applicationDb->query("REFRESH MATERIALIZED VIEW CONCURRENTLY mv_user_dashboard_member;");
}

$currentUser = current_user();
$statchStmt = queryBuilder(
    table: "mv_user_dashboard_member",
    filters: [
        [
            'key' => 'user_id',
            'query' => 'user_id = :user_id',
            'value' => $currentUser['id'],
        ],
    ]
);
$statch = $statchStmt->fetchAll();
//var_dump($statch);

$eventStmt = queryBuilder(
    table: "vw_user_event_progress",
    filters: [
        [
            'key' => 'user_id',
            'query' => 'user_id = :user_id',
            'value' => $currentUser['id'],
        ],
    ]
);
$event = $eventStmt->fetchAll();
//var_dump($event);