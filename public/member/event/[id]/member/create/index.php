<?php
global $applicationDb;

$idx = $_GET['id'] ?? null;

if (empty($idx)) {
    header("Location: /admin/event");
}

$stmt = $applicationDb->prepare('WITH registered_users AS (SELECT DISTINCT user_id
                          FROM user_events
                          WHERE event_id = :event_id
                            AND deleted_at IS NULL)
SELECT users.id, users.full_name
FROM users LEFT JOIN registered_users ON registered_users.user_id = users.id
WHERE users.deleted_at IS NULL
  AND registered_users.user_id IS NULL
ORDER BY users.full_name;');

$stmt->execute(['event_id' => $idx]);

$userList = $stmt->fetchAll();

//var_dump($userList);