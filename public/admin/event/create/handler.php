<?php

require_once __DIR__ . '/../../../../config/validation.php';
$applicationDb = require_once __DIR__ . "/../../../../config/database.php";

$body = validate([
    'title' => 'required|max:255',
    'description' => 'max:5000',
    'location' => 'max:255',
    'start_date' => 'required',
    'end_date' => 'required',
    'status' => 'in:Not started,In progress,Completed,Cancelled'
], "/admin/event/create");

$sql = "INSERT INTO events (title, description, location, start_date, end_date, status) 
        VALUES (:title, :description, :location, :start_date, :end_date, :status)";

$stmt = $applicationDb->prepare($sql);

$stmt->execute([
    'title' => $body['title'],
    'description' => $body['description'] ?? null,
    'location' => $body['location'] ?? null,
    'start_date' => $body['start_date'],
    'end_date' => $body['end_date'],
    'status' => $body['status'] ?? 'Not started',
]);

header('Location: /admin/event/update?id=' . $stmt->lastInsertId());