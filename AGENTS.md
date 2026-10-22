# Developer & Agent Guidelines

## Runtime & Stack
- **PHP**: Pure native PHP without Composer or external package dependencies.
- **Database**: PostgreSQL via PDO (`pdo_pgsql`).
- **Environment**: Custom `.env` parser in `config/load-env.php`. Required keys in `.env` (see `.env.example`):
  - `DB_HOST` (defaults to `127.0.0.1` if empty)
  - `DB_PORT` (defaults to `5432` if empty)
  - `DB_NAME`, `DB_USER`, `DB_PASS`

## Architecture & Conventions (Current Codebase)
- **Environment Loading (`config/load-env.php`)**:
  - Automatically loads `../.env` when included.
  - Populates `putenv()`, `$_ENV`, and `$_SERVER`.
  - Throws `Exception` if `.env` does not exist.
- **Database Connection (`config/database.php`)**:
  - Includes `load-env.php` and returns an active `PDO` instance.
  - Sets `ERRMODE_EXCEPTION`, `FETCH_ASSOC`, and disables `EMULATE_PREPARES`.
  - Throws 500 HTTP response and halts script on connection failure.
- **Session & Auth Helpers (`config/session.php`)**:
  - Initializes native PHP session with `session.use_strict_mode` and `session.cookie_httponly`.
  - Exposes global helpers:
    - `set_user_session($user)`: Regenerates session ID, stores `user_logged_in`, `user_id`, `username`.
    - `is_logged_in()`: Returns boolean authentication status.
    - `require_login()`: Guards protected pages; redirects unauthenticated requests to `auth/login.php`.
    - `current_user()`: Queries `users` table via PDO for the active `$_SESSION['user_id']` (fallback to session array).
    - `logout_user()`: Clears `$_SESSION`, deletes session cookie, and destroys session.
- **CSRF & Validation (`config/csrf.php`, `config/validation.php`)**:
  - `csrf_token()`, `csrf_field()`, `verify_csrf()` for CSRF protection.
  - `validate(array $rules, ?string $fallbackUrl)` for input validation, flash errors (`error()`, `has_error()`, `old()`).
- **Database Migrations (`migration`, `migrations/`)**:
  - CLI runner: `php migration <command>` or `./migration <command>`.
  - Migration files live in `migrations/` named `migration_YYYYMMDD_His_<name>.php`.
  - Migration class must implement `up(PDO $pdo): void` and `down(PDO $pdo): void`.
  - Applied migrations are recorded in PostgreSQL `migrations` table.

## Git & Notion Workflow
- **Commit Message Format**:
  - Commits MUST start with the Notion Task ID prefix: `<id> - <message>` or `<id>: <message>` (e.g., `16 - Update data` or `8: create login page`).
  - `.github/workflows/main-notion.yml` parses `^(\d+)\s*[-:]?` on push to transition tasks to "In progress".

## Developer Commands
- **Database Migrations**:
  ```bash
  php migration up            # Run pending migrations
  php migration down          # Rollback last migration
  php migration create <name> # Scaffold new migration file
  ```
- **Syntax check all PHP files**:
  ```bash
  find . -name "*.php" -not -path "*/.*" -exec php -l {} +
  ```
- **Run dev server**:
  ```bash
  php -S localhost:8080 -t public/
  ```
