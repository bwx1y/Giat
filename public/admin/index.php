<?php
global $applicationDb;

$eventStmt = $applicationDb->query("SELECT count(*) FROM events WHERE deleted_at IS NULL");
$eventCount = $eventStmt->fetchColumn();
//var_dump($eventCount);

$subEventStmt = $applicationDb->query("SELECT count(*) FROM meetings WHERE deleted_at IS NULL");
$subEventCount = $subEventStmt->fetchColumn();
//var_dump($subEventCount);

$userStmt = $applicationDb->query("SELECT count(*) FROM users WHERE deleted_at IS NULL");
$userCount = $userStmt->fetchColumn();
//var_dump($userCount);

$userActiveStmt = $applicationDb->query("SELECT count(*) FROM users WHERE deleted_at IS NULL AND active = true");
$userActive = $userActiveStmt->fetchColumn();
//var_dump($userActive);

$avgAttendanceStmt = queryBuilder(
    table: 'vw_absent',
    columns: ['ROUND(AVG(status) * 100, 1) AS avg_attendance'],
    limit: 1
);
$avgAttendanceResult = floatval($avgAttendanceStmt->fetch(PDO::FETCH_ASSOC)['avg_attendance'] ?? 0);
//var_dump($avgAttendanceResult);

$attendanceTrendStmt = queryBuilder(
    table: 'mv_attendance_trend_6_months',
    filters: [
        [
            'key' => 'has_attendance',
            'query' => 'total_attendance > 0',
            'value' => '1',
        ],
    ],
    limit: 6
);
$attendanceTrendResult = $attendanceTrendStmt->fetchAll(PDO::FETCH_ASSOC);
//var_dump($attendanceTrendResult);

$leaderboardStmt = $applicationDb->query("SELECT 
    v.user_id,
    v.member_name,
    SUM(v.status) AS total_attended,
    (COUNT(*) - SUM(v.status)) AS total_absent,
    (SUM(v.status) * 3) - ((COUNT(*) - SUM(v.status)) * 1) AS total_points
FROM vw_absent v
JOIN users u ON u.id = v.user_id
WHERE u.active = true
GROUP BY v.user_id, v.member_name
ORDER BY total_points DESC, v.member_name ASC
LIMIT 5");
$leaderboardResult = $leaderboardStmt->fetchAll(PDO::FETCH_ASSOC);
//var_dump($leaderboardResult);

$search = $_GET['search'] ?? null;
$status = $_GET['status'] ?? null;

$eventFilters = [
    [
        'key' => 'search',
        'query' => 'title ILIKE :search',
        'value' => $search ? "%$search%" : null,
    ],
    [
        'key' => 'status',
        'query' => 'status = :status',
        'value' => $status,
    ],
];

$eventsStmt = queryBuilder(
    table: 'vw_event',
    filters: $eventFilters,
    size: 50,
    page: 1
);
$eventsResult = $eventsStmt->fetchAll(PDO::FETCH_ASSOC);
//var_dump($eventsResult);