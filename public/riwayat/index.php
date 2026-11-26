<?php
// perbaiki routing
$userHistoryData = [];
if (has_role('Member')) {
    require __DIR__ . '/../member/history/index.php';
    if ($result) {
        $userHistoryData[] = $result;
    }
}

$contentFile = __DIR__ . '/content.php';
require __DIR__ . '/../components/layout.php';
