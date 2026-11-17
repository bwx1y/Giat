<?php
$id = $_GET['id'] ?? null;
header('Location: /?page_view=event' . ($id ? '&created_id=' . $id : ''));
exit;
