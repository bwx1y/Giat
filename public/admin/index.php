<?php
global $applicationDb;

$eventStmt = $applicationDb->query("SELECT count(*) FROM events WHERE deleted_at IS NULL");
$eventCount = $eventStmt->fetchColumn();
//var_dump($eventCount);

$subEventStmt = $applicationDb->query("SELECT count(*) FROM meetings WHERE deleted_at IS NULL");
$subEventCount = $subEventStmt->fetchColumn();
//var_dump($eventCount);

$userStmt = $applicationDb->query("SELECT count(*) FROM users WHERE deleted_at IS NULL");
$userCount = $userStmt->fetchColumn();
//var_dump($eventCount);

$userActiveStmt = $applicationDb->query("SELECT count(*) FROM users WHERE deleted_at IS NULL AND active = true");
$userActive = $userActiveStmt->fetchColumn();
//var_dump($userActive);

$attendanceTrendStmt = queryBuilder(
    'mv_attendance_trend_6_months'
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