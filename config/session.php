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
    $_SESSION['role'] = $user['role'] ?? 'Member';
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
        header("Location: /?error=Login is required");
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
        'username' => $_SESSION['username'],
        'role' => $_SESSION['role'] ?? 'Member',
    ];
}

/**
 * 6. Get the currently active user role
 */
function current_role(): ?string
{
    if (!is_logged_in()) {
        return null;
    }
    return $_SESSION['role'] ?? current_user()['role'] ?? null;
}

/**
 * 7. Check if active user has one of the specified roles
 */
function has_role(string|array $roles): bool
{
    $role = current_role();
    if (!$role) {
        return false;
    }
    if (is_array($roles)) {
        foreach ($roles as $r) {
            if (strcasecmp($role, $r) === 0) {
                return true;
            }
        }
        return false;
    }
    return strcasecmp($role, $roles) === 0;
}

/**
 * 8. Protect page with specific role
 */
function require_role(string|array $roles): void
{
    require_login();
    if (!has_role($roles)) {
        http_response_code(403);
        echo "403 Forbidden - Akses Ditolak";
        exit;
    }
}

/**
 * 9. Handle user logout and clear session data
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

/**
 * Retrieve the event record for the currently logged-in user.
 *
 * Checks if the user is authenticated, then queries the database for an active
 * (non-deleted) record matching both the logged-in user ID and the specified event ID.
 *
 * @global PDO $applicationDb Database connection instance.
 *
 * @param int $eventId The ID of the event to check against.
 *
 * @return array|null Returns an associative array of the user event record if found,
 *                    or null if the user is not logged in or no record exists.
 */
function user_role_event(int $eventId): ?array
{
    if (!is_logged_in()) {
        return null;
    }

    $userId = $_SESSION['user_id'];
    global $applicationDb;

    $stmt = $applicationDb->prepare("SELECT * FROM user_events where user_id = :user AND event_id = :event AND deleted_at IS NULL LIMIT 1");

    $stmt->execute(['user' => $userId, 'event' => $eventId]);
    $userEvent = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($userEvent) {
        return $userEvent;
    }

    return null;
}