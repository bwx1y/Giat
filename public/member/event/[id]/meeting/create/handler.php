<?php

global $applicationDb;

$eventId = $_GET['id'] ?? null;
if (empty($eventId)) {
    header('Location: /admin/event');
    exit;
}

$body = validate([
    'title' => 'required|max:255',
    'location' => 'max:255',
    'start_date' => 'required',
    'end_date' => 'required',
    'status' => 'in:Not started,In progress,Completed,Cancelled'
], '/admin/event/' . $eventId . '/meeting/create');

$stmt = $applicationDb->prepare('
    INSERT INTO meetings (event_id, title, location, start_date, end_date, status) 
    VALUES (:event_id, :title, :location, :start_date, :end_date, :status)
');

$stmt->execute([
    'event_id' => $eventId,
    'title' => $body['title'],
    'location' => $body['location'] ?? null,
    'start_date' => $body['start_date'],
    'end_date' => $body['end_date'],
    'status' => $body['status'] ?? 'Not started',
]);

header('Location: /admin/event/' . $eventId . '/meeting');
exit;
