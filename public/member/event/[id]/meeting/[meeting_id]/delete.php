<?php

global $applicationDb;

$eventId = $_GET['id'] ?? null;
$meetingId = $_GET['meeting_id'] ?? null;

if (!$eventId || !$meetingId) {
    http_response_code(404);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $stmt = $applicationDb->prepare('
        UPDATE meetings 
        SET deleted_at = CURRENT_TIMESTAMP 
        WHERE id = :id AND event_id = :event_id AND deleted_at IS NULL
    ');

    $stmt->execute([
        'id' => $meetingId,
        'event_id' => $eventId,
    ]);

    http_response_code(204);
    exit;
}

http_response_code(404);
exit;
