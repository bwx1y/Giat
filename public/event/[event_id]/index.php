<?php
// perbaiki routing
$memberEventRouteId = filter_var(
    $route_params['event_id'] ?? $_GET['event_id'] ?? null,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if (!$memberEventRouteId) {
    http_response_code(404);
    exit('Event not found');
}

$memberEventRole       = user_role_event((int) $memberEventRouteId);
$canManageEventMembers = has_role('Admin') || ($memberEventRole['role'] ?? null) === 'Admin';

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && ($_POST['meeting_action'] ?? '') === 'create'
) {
    if (($memberEventRole['role'] ?? null) !== 'Admin') {
        http_response_code(403);
        exit('Forbidden');
    }

    $_GET['id'] = (string) $memberEventRouteId;
    $handlerLocation    = '/admin/event/' . $memberEventRouteId . '/meeting';
    $eventDetailLocation = '/event/' . $memberEventRouteId;

    header_register_callback(static function () use ($handlerLocation, $eventDetailLocation): void {
        foreach (headers_list() as $header) {
            if (preg_match('/^Location:\s*' . preg_quote($handlerLocation, '/') . '$/i', $header)) {
                header('Location: ' . $eventDetailLocation, true, 303);
                break;
            }
        }
    });

    require __DIR__ . '/../../member/event/[id]/meeting/create/handler.php';
    exit;
}

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && ($_POST['member_action'] ?? '') === 'add'
) {
    if (!$canManageEventMembers) {
        http_response_code(403);
        exit('Forbidden');
    }

    $_GET['id'] = (string) $memberEventRouteId;
    require __DIR__ . '/../../admin/event/[id]/member/create/handler.php';
    header_remove('Location');
    header('Location: /event/' . $memberEventRouteId . '#event-members', true, 303);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (!$canManageEventMembers) {
        http_response_code(403);
        exit('Forbidden');
    }

    parse_str(file_get_contents('php://input'), $deleteInput);
    $memberId = filter_var(
        $deleteInput['member_id'] ?? null,
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );
    if (!$memberId) {
        http_response_code(400);
        exit('Invalid member ID');
    }

    $_GET['id']        = (string) $memberEventRouteId;
    $_GET['member_id'] = (string) $memberId;
    require __DIR__ . '/../../admin/event/[id]/member/[member_id]/delete.php';
    exit;
}

$_detailEventId = $memberEventRouteId;

$memberAddedToastName = null;
$memberAddedFlash     = $_SESSION['event_member_added'] ?? null;

if (
    is_array($memberAddedFlash)
    && (int) ($memberAddedFlash['event_id'] ?? 0) === $_detailEventId
    && is_string($memberAddedFlash['name'] ?? null)
) {
    $memberAddedToastName = $memberAddedFlash['name'];
    unset($_SESSION['event_member_added']);
}

$_dbEventDetail           = null;
$_dbEventMeetings         = [];
$_dbEventMemberCandidates = [];

$_isSuperAdmin  = has_role('Admin');
$_userEventRole = user_role_event($_detailEventId);

$_isEventAdminLocal = false;
$_currentUserId = null;
if (is_logged_in()) {
    $_currentUserId = current_user()['id'] ?? null;
}
if (!$_isSuperAdmin && $_currentUserId !== null) {
    global $applicationDb;
    $eaStmt = $applicationDb->prepare(
        'SELECT DISTINCT event_id FROM user_events WHERE user_id = :user_id AND deleted_at IS NULL'
    );
    $eaStmt->execute(['user_id' => $_currentUserId]);
    foreach ($eaStmt->fetchAll(PDO::FETCH_COLUMN) as $eaId) {
        if ((user_role_event((int) $eaId)['role'] ?? null) === 'Admin') {
            $_isEventAdminLocal = true;
            break;
        }
    }
}

$_isAdminBiasa = !$_isSuperAdmin && (
    ($memberEventRole['role'] ?? null) === 'Admin' || $_isEventAdminLocal
);

$_GET['id'] = (string) $_detailEventId;

require __DIR__ . '/../../admin/event/[id]/update/index.php';
$_dbEventDetail = $event ?? null;

require __DIR__ . '/../../admin/event/[id]/member/create/index.php';
$_dbEventMemberCandidates = $userList ?? [];

global $applicationDb;
$_mtStmt = $applicationDb->prepare(
    'SELECT m.id, m.title, m.location, m.start_date, m.end_date, m.status,
            COALESCE(s.total_peserta, 0)        AS total_peserta,
            COALESCE(s.hadir, 0)                AS hadir,
            COALESCE(s.tidak_hadir, 0)          AS tidak_hadir,
            COALESCE(s.persentase_kehadiran, 0) AS persentase_kehadiran
     FROM meetings m
     LEFT JOIN vw_meeting_stats s ON s.meeting_id = m.id
     WHERE m.event_id = :event_id AND m.deleted_at IS NULL
     ORDER BY m.start_date ASC, m.id ASC'
);
$_mtStmt->bindValue(':event_id', $_detailEventId, PDO::PARAM_INT);
$_mtStmt->execute();
$_dbEventMeetings = $_mtStmt->fetchAll(PDO::FETCH_ASSOC);

$isEventAdmin = $_isEventAdminLocal;

$contentFile = __DIR__ . '/content.php';
require __DIR__ . '/../../components/layout.php';
