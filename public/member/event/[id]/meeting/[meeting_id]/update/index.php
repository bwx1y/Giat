<?php

$eventId = $_GET['id'] ?? null;
$meetingId = $_GET['meeting_id'] ?? null;

if (empty($eventId) || empty($meetingId)) {
    header('Location: /admin/event/');
    exit;
}

$stmt = queryBuilder(
    table: 'meetings',
    filters: [
        [
            'key' => 'id',
            'query' => 'id = :id',
            'value' => $meetingId,
        ],
        [
            'key' => 'event_id',
            'query' => 'event_id = :event_id',
            'value' => $eventId,
        ],
        [
            'key' => 'deleted_at',
            'query' => 'deleted_at IS NULL',
            'value' => null,
        ]
    ],
    limit: 1
);

$meeting = $stmt->fetch();
if (!$meeting) {
    http_response_code(404);
    exit;
}
