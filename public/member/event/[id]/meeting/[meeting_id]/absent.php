<?php

global $applicationDb;

$meetingId = (int) ($_GET['meeting_id'] ?? 0);
$eventId   = (int) ($_GET['id'] ?? 0);

if ($eventId === 0 || $meetingId === 0) {
    header('Location: /member');
    exit;
}

$eventStmt = $applicationDb->prepare("SELECT * FROM vw_event WHERE id = :id");
$eventStmt->bindValue(':id', $eventId, PDO::PARAM_INT);
$eventStmt->execute();
$event = $eventStmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    header('Location: /member');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $validation = validate([
        "user_id" => "required",
    ]);

    $userId = (int) $validation["user_id"];

    $checkStmt = $applicationDb->prepare("
        SELECT id 
        FROM user_meetings 
        WHERE user_id = :user_id 
          AND meeting_id = :meeting_id 
          AND deleted_at IS NULL
    ");
    $checkStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $checkStmt->bindValue(':meeting_id', $meetingId, PDO::PARAM_INT);
    $checkStmt->execute();
    $existingRecord = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if ($existingRecord) {
        $deleteStmt = $applicationDb->prepare("
            UPDATE user_meetings 
            SET deleted_at = NOW() 
            WHERE id = :id
        ");
        $deleteStmt->bindValue(':id', $existingRecord['id'], PDO::PARAM_INT);
        $deleteStmt->execute();

    } else {
        $insertStmt = $applicationDb->prepare("
            INSERT INTO user_meetings (user_id, meeting_id, attendance_method) 
            VALUES (:user_id, :meeting_id, :method)
        ");
        $insertStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $insertStmt->bindValue(':meeting_id', $meetingId, PDO::PARAM_INT);
        $insertStmt->bindValue(':method', 'Manual', PDO::PARAM_STR);
        $insertStmt->execute();
    }

    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}