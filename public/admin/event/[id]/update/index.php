<?php

$id = $_GET['id'] ?? null;
if (empty($id)) {
    header('Location: /admin/event/');
}

$stmt = queryBuilder(
    table: 'events',
    filters: [
        [
            'key' => 'id',
            'query' => 'id = :id',
            'value' => $id,
        ]
    ],
    limit: 1
);

$event = $stmt->fetch(); 