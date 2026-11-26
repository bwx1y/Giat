<?php
// perbaiki routing
$eventSearch = $_GET['search'] ?? '';
$eventStatus = $_GET['status'] ?? '';

$_GET['page'] = $_GET['page'] ?? 1;
$_GET['size'] = $_GET['size'] ?? 50;
require __DIR__ . '/../admin/event/index.php';
$eventsDataList = $result;

$contentFile = __DIR__ . '/content.php';
require __DIR__ . '/../components/layout.php';
