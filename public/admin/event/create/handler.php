<?php

global $applicationDb;

$body = validate([
    'title' => 'required|max:255',
    'description' => 'max:5000',
    'location' => 'max:255',
    'start_date' => 'required',
    'end_date' => 'required'
], "/admin/event/create");

$sql = "INSERT INTO events (title, description, location, start_date, end_date, status) 
        VALUES (:title, :description, :location, :start_date, :end_date, 'Not started')";

$stmt = $applicationDb->prepare($sql);

$stmt->execute([
    'title'       => $body['title'],
    'description' => $body['description'] ?? null,
    'location'    => $body['location'] ?? null,
    'start_date'  => $body['start_date'],
    'end_date'    => $body['end_date'],
]);

$newEventId = $stmt->fetchColumn();

header('Location: /admin/event/' . $newEventId . '/update');
