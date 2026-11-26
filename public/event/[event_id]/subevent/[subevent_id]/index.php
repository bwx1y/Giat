<?php
// perbaiki routing
$_subDetailEventId   = filter_var($route_params['event_id']   ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$_subDetailMeetingId = filter_var($route_params['subevent_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if (!$_subDetailEventId || !$_subDetailMeetingId) {
    http_response_code(404);
    exit('Not found');
}

$_subSearch = $_GET['q'] ?? $_GET['search'] ?? '';

$_dbSubEventDetail   = null;
$_dbSubMeetingStats  = null;
$_dbSubMeetingDetail = null;
$_dbSubParticipants  = [];

$_GET['id']         = (string) $_subDetailEventId;
$_GET['meeting_id'] = (string) $_subDetailMeetingId;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require __DIR__ . '/../../../../member/event/[id]/meeting/[meeting_id]/absent.php';
}

require __DIR__ . '/../../../../member/event/[id]/meeting/[meeting_id]/index.php';
$_dbSubEventDetail  = $event ?? null;
$_dbSubMeetingStats = $stats ?? null;

require __DIR__ . '/../../../../admin/event/[id]/meeting/[meeting_id]/update/index.php';
$_dbSubMeetingDetail = $meeting ?? null;

global $applicationDb;
$pQuery  = 'SELECT v.user_id, v.member_name AS name, v.method, v.status,
                   um.created_at AS attended_at,
                   CASE WHEN v.status = 1 THEN 3 ELSE 0 END AS point
            FROM vw_absent v
            LEFT JOIN user_meetings um ON um.user_id = v.user_id AND um.meeting_id = v.meeting_id AND um.deleted_at IS NULL
            WHERE v.meeting_id = :meeting_id';
$pParams = [':meeting_id' => $_subDetailMeetingId];

if (!empty($_subSearch)) {
    $pQuery .= ' AND v.member_name ILIKE :search';
    $pParams[':search'] = '%' . $_subSearch . '%';
}

$pQuery .= ' ORDER BY v.status DESC, v.member_name ASC';

$pStmt = $applicationDb->prepare($pQuery);
$pStmt->execute($pParams);
$_dbSubParticipants = $pStmt->fetchAll(PDO::FETCH_ASSOC);

$contentFile = __DIR__ . '/content.php';
require __DIR__ . '/../../../../components/layout.php';
