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

$stmt = $applicationDb->prepare('INSERT INTO user_events (user_id, event_id, role) VALUES (:user_id, :event_id, :role)');

$stmt->bindParam(':event_id', $idx);
$stmt->bindParam(':user_id', $body['user_id']);
$stmt->bindParam(':role', $body['role']);

$stmt->execute();

header("Location: /admin/event" . $idx . '/member');