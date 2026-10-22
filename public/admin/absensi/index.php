<?php
$applicationDb = require_once '../../../config/database.php';

$page = $_GET['page'] ?? 1;
$size = $_GET['size'] ?? 10;

