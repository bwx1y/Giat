<?php
// perbaiki routing
$isLoggedIn  = is_logged_in();
$currentUser = $isLoggedIn ? current_user() : null;
$isEventAdmin = false;

if ($isLoggedIn && ($currentUser['role'] ?? null) !== 'Admin' && isset($currentUser['id'])) {
    global $applicationDb;

    $eventStmt = $applicationDb->prepare(
        'SELECT DISTINCT event_id FROM user_events WHERE user_id = :user_id AND deleted_at IS NULL'
    );
    $eventStmt->execute(['user_id' => $currentUser['id']]);

    foreach ($eventStmt->fetchAll(PDO::FETCH_COLUMN) as $eventId) {
        $eventRole = user_role_event((int) $eventId);
        if (($eventRole['role'] ?? null) === 'Admin') {
            $isEventAdmin = true;
            break;
        }
    }
}

if (has_role('Admin')) {
    $contentFile = __DIR__ . '/content-admin.php';
} elseif ($isEventAdmin) {
    $contentFile = __DIR__ . '/content-event-admin.php';
} else {
    $contentFile = __DIR__ . '/content-member.php';
}

require __DIR__ . '/../components/layout.php';
