<?php

$id = $_GET['id'] ?? null;
if (empty($id)) {
    header('Location: /admin/event/');
    exit;
}
