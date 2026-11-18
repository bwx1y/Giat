<?php

global $applicationDb;

$eventId = $_GET['id'] ?? null;
$meetingId = $_GET['meeting_id'] ?? null;

if (empty($eventId) || empty($meetingId)) {
    header('Location: /admin/event');
    exit;
}

$body = validate([
    'title' => 'required|max:255',
    'location' => 'max:255',
    'start_date' => 'required',
    'end_date' => 'required',
    'status' => 'in:Not started,In progress,Completed,Cancelled'
], '/admin/event/' . $eventId . '/meeting/' . $meetingId . '/update');

$sql = "UPDATE meetings 
        SET title = :title, 
            location = :location, 
            start_date = :start_date, 
            end_date = :end_date, 
            status = :status 
        WHERE id = :id AND event_id = :event_id AND deleted_at IS NULL";

$stmt = $applicationDb->prepare($sql);
$stmt->execute([
    'id' => $meetingId,
    'event_id' => $eventId,
    'title' => $body['title'],
    'location' => $body['location'] ?? null,
    'start_date' => $body['start_date'],
    'end_date' => $body['end_date'],
    'status' => $body['status'] ?? 'Not started',
]);

header('Location: /admin/event/' . $eventId . '/meeting/' . $meetingId . '/update');
exit;
