<?php
// perbaiki routing
$_GET['page'] = $_GET['page'] ?? 1;
$_GET['size'] = $_GET['size'] ?? 1000;

$_absiIsEventAdmin = false;
if (is_logged_in()) {
    $_absiCurrentUser = current_user();
    if (($_absiCurrentUser['role'] ?? null) !== 'Admin' && isset($_absiCurrentUser['id'])) {
        global $applicationDb;
        $eaStmt = $applicationDb->prepare(
            'SELECT DISTINCT event_id FROM user_events WHERE user_id = :user_id AND deleted_at IS NULL'
        );
        $eaStmt->execute(['user_id' => $_absiCurrentUser['id']]);
        foreach ($eaStmt->fetchAll(PDO::FETCH_COLUMN) as $eaId) {
            if ((user_role_event((int) $eaId)['role'] ?? null) === 'Admin') {
                $_absiIsEventAdmin = true;
                break;
            }
        }
    }
}

if ($_absiIsEventAdmin) {
    ob_start();
    require __DIR__ . '/../member/absensi/index.php';
    ob_end_clean();

    $result = $results;
    $eventList = [];
    foreach ($result as $attendance) {
        $eventId = (string) ($attendance['event_id'] ?? '');
        if ($eventId !== '' && !isset($eventList[$eventId])) {
            $eventList[$eventId] = [
                'id'    => $eventId,
                'title' => $attendance['event'] ?? '',
            ];
        }
    }
    $eventList = array_values($eventList);
} else {
    require __DIR__ . '/../admin/absensi/index.php';
}

$isEventAdmin = $_absiIsEventAdmin;

$contentFile = __DIR__ . '/content.php';
require __DIR__ . '/../components/layout.php';
