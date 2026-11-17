<?php

global $applicationDb;

$size = $_GET['size'] ?? 10;
$page = $_GET['page'] ?? 1;
$offside = ($page - 1) * $size;

$search = $_GET['search'] ?? null;
$event = $_GET['event'] ?? null;
$status = $_GET['status'] ?? null;

$current_user = current_user();

$whereConditions = [
    'v.user_id != :logged_in_user'
];
$params = [
    ':logged_in_user' => $current_user["id"]
];

if (!empty($search)) {
    $whereConditions[] = 'v.member_name ILIKE :search';
    $params[':search'] = "%{$search}%";
}

if (!empty($event)) {
    $whereConditions[] = 'v.event_id = :event';
    $params[':event'] = $event;
}

if ($status !== null && $status !== '') {
    $whereConditions[] = 'v.status = :status';
    $params[':status'] = $status;
}

$whereClause = implode(' AND ', $whereConditions);

$limit = (int) ($size ?? 10);
$page = (int) ($page ?? 1);
$offset = ($page - 1) * $limit;

$sql = "
    WITH user_my_events AS (
        SELECT event_id 
        FROM user_events 
        WHERE user_id = :logged_in_user AND deleted_at IS NULL
    )
    SELECT v.* 
    FROM vw_absent v
    JOIN user_my_events ume ON v.event_id = ume.event_id
    WHERE {$whereClause}
    ORDER BY v.date DESC
    LIMIT :limit OFFSET :offset
";

$stmt = $applicationDb->prepare($sql);

foreach ($params as $param => $value) {
    $stmt->bindValue($param, $value);
}

$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
var_dump($results);