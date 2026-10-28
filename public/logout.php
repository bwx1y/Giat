<?php

require_once __DIR__ . '/../config/init.php';

logout_user();
header("Location: /");
exit;
