<?php

global $applicationDb;

$idx = $_GET['id'] ?? null;

if (empty($idx)) {
    header("Location: /admin/event");
}

$body = validate([
    'user_id' => 'required|integer',
    'role' => 'in:Admin,Member'
], '/admin/event/' . $idx . '/member/create');

$nameStmt = $applicationDb->prepare(
    'SELECT full_name FROM users WHERE id = :user_id AND deleted_at IS NULL'
);
$nameStmt->execute(['user_id' => $body['user_id']]);
$memberName = $nameStmt->fetchColumn();

if ($memberName === false) {
    http_response_code(404);
    exit('Member not found');
}

$stmt = $applicationDb->prepare('INSERT INTO user_events (user_id, event_id, role) VALUES (:user_id, :event_id, :role)');

$stmt->bindParam(':event_id', $idx);
$stmt->bindParam(':user_id', $body['user_id']);
$stmt->bindParam(':role', $body['role']);

$stmt->execute();

$_SESSION['event_member_added'] = [
    'event_id' => (int) $idx,
    'name' => $memberName,
];

header('Location: /event/' . $idx, true, 303);
exit;