<?php

require_once __DIR__ . '/../../../../config/validation.php';
global $applicationDb;
if (!$applicationDb instanceof PDO) {
    $applicationDb = require __DIR__ . "/../../../../config/database.php";
}

$idx = $_POST['id'];

$body = validate([
    'id' => 'required|numeric',
    'title' => 'required|max:255',
    'description' => 'max:5000',
    'location' => 'max:255',
    'start_date' => 'required',
    'end_date' => 'required',
    'status' => 'in:Not started,In progress,Completed,Cancelled'
], "/admin/event/update?id=" . $idx);

$sql = "UPDATE events 
        SET title = :title, 
            description = :description, 
            location = :location, 
            start_date = :start_date, 
            end_date = :end_date, 
            status = :status 
        WHERE id = :id";

$stmt = $applicationDb->prepare($sql);

$stmt->execute([
    'id' => $body['id'],
    'title' => $body['title'],
    'description' => $body['description'] ?? null,
    'location' => $body['location'] ?? null,
    'start_date' => $body['start_date'],
    'end_date' => $body['end_date'],
    'status' => $body['status'] ?? 'Not started',
]);

header('Location: /admin/event/update?id=' . $body['id']);
