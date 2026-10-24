<?php

global $applicationDb;
if (!$applicationDb instanceof PDO) {
    $applicationDb = require_once __DIR__ . "/database.php";
}

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_httponly', 1);
    session_start();
}
/**
 * 2. Register user session upon successful login
 */
function set_user_session($user): void
{
    session_regenerate_id(true);

    $_SESSION['user_logged_in'] = true;
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
}

/**
 * 3. Check if the user is currently logged in
 */
function is_logged_in(): bool
{
    return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
}

/**
 * 4. Protect a page (redirect to log in if not authenticated)
 */
function require_login(): void
{
    if (!is_logged_in()) {
        header("Location: auth\login.php?error=Login is required");
        exit;
    }
}

/**
 * 5. Get the currently active user data
 * Optionally accepts a PDO database connection to fetch fresh data directly from the database.
 */
function current_user()
{
    if (!is_logged_in()) {
        return null;
    }

    global $applicationDb;
    if ($applicationDb instanceof PDO) {
        try {
            $stmt = $applicationDb->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->execute(['id' => $_SESSION['user_id']]);
            $user = $stmt->fetch();

            if ($user) {
                return $user;
            }
        } catch (PDOException $e) {
        }
    }

    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username']
    ];
}

/**
 * 6. Handle user logout and clear session data
 */
function logout_user(): void
{
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}