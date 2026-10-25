<?php

use Random\RandomException;

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_httponly', 1);
    session_start();
}

/**
 * Generates or retrieves the CSRF token stored in the current user session.
 *
 * If a token does not exist in the session, a cryptographically secure
 * random 32-byte token is generated and stored.
 *
 * @return string Returns the active session CSRF token string (hexadecimal).
 *
 * @throws RandomException If it fails to generate cryptographically secure bytes.
 */
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (RandomException $e) {

        }
    }
    return $_SESSION['csrf_token'];
}

/**
 * Generates a hidden HTML input field containing the active CSRF token
 * to be embedded inside HTML forms.
 *
 * @return string Returns an HTML string of a hidden input element with the token value.
 */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

/**
 * Verifies whether the incoming POST CSRF token matches the token stored in the session.
 *
 * Uses timing attack safe string comparison (`hash_equals`) to validate the token.
 *
 * @return bool Returns true if the token is valid and matches the session, false otherwise.
 */
function verify_csrf(): bool
{
    $token = $_POST['csrf_token'] ?? '';
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        return false;
    }
    return true;
}