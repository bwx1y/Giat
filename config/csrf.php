<?php

use Random\RandomException;

ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_httponly', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (RandomException $e) {

        }
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function verify_csrf(): bool
{
    $token = $_POST['csrf_token'] ?? '';
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        return false;
    }
    return true;
}