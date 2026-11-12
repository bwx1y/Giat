<?php
global $applicationDb;

$idx = $_GET['id'] ?? null;
if (!$idx) {
    http_response_code(404);
    exit();
}

$idx_member = $_GET['member_id'] ?? null;
if (!$idx_member) {
    http_response_code(404);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $stmt = $applicationDb->prepare("UPDATE user_events SET deleted_at = CURRENT_TIMESTAMP WHERE event_id = :event_id AND user_id = :user_id AND deleted_at IS NULL");

    $stmt->execute(['event_id' => $idx, 'user_id' => $idx_member]);

    http_response_code(204);
    exit();
}

http_response_code(404);
exit();