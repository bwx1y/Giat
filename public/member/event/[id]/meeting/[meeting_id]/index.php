<?php

global $applicationDb;

$eventId = (int) ($_GET['id'] ?? 0);
$meetingId = (int) ($_GET['meeting_id'] ?? 0);

if ($eventId == 0 || $meetingId == 0) {
    header('Location: /member');
}

$eventStmt = $applicationDb->prepare("SELECT * FROM vw_event WHERE id = :id");
$eventStmt->bindValue(':id', $eventId, PDO::PARAM_INT);
$eventStmt->execute();
$event = $eventStmt->fetch(PDO::FETCH_ASSOC);
//var_dump($event);

$statsStmt = $applicationDb->prepare("SELECT * FROM vw_meeting_stats WHERE meeting_id = :meeting_id");
$statsStmt->bindValue(':meeting_id', $meetingId, PDO::PARAM_INT);
$statsStmt->execute();
$stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
//var_dump($stats);