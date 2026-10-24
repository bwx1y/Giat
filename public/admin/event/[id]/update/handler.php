<?php

global $applicationDb;

$idx = $_GET['id'];

$body = validate([
    'title' => 'required|max:255',
    'description' => 'max:5000',
    'location' => 'max:255',
    'start_date' => 'required',
    'end_date' => 'required',
    'status' => 'in:Not started,In progress,Completed,Cancelled'
], '/admin/event/' . $idx . '/update');

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
    'id' => $idx,
    'title' => $body['title'],
    'description' => $body['description'] ?? null,
    'location' => $body['location'] ?? null,
    'start_date' => $body['start_date'],
    'end_date' => $body['end_date'],
    'status' => $body['status'] ?? 'Not started',
]);

header('Location: /admin/event/' . $idx . '/update');
