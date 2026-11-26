<?php
// perbaiki routing
$mSearch = $_GET['m_search'] ?? '';
$mRole   = $_GET['m_role']   ?? '';

$membersDataList = [];

if (has_role('Admin')) {
    require __DIR__ . '/../admin/users/index.php';
    $membersDataList = $users ?? [];
}

global $applicationDb;
$memberStats = [];
try {
    $statsStmt = $applicationDb->query(
        "SELECT user_id,
                SUM(status)                                   AS hadir,
                SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END)  AS absen,
                (SUM(status) * 3 - SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END)) AS point
         FROM vw_absent
         GROUP BY user_id"
    );
    foreach ($statsStmt->fetchAll(PDO::FETCH_ASSOC) as $s) {
        $memberStats[$s['user_id']] = $s;
    }
} catch (\Throwable $e) {}

foreach ($membersDataList as &$m) {
    $stats = $memberStats[$m['id']] ?? ['hadir' => 0, 'absen' => 0, 'point' => 0];
    $m['hadir'] = (int) $stats['hadir'];
    $m['absen'] = (int) $stats['absen'];
    $m['point'] = (int) $stats['point'];
}
unset($m);

$contentFile = __DIR__ . '/content.php';
require __DIR__ . '/../components/layout.php';
