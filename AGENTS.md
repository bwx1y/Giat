# Developer & Agent Guidelines

## Runtime & Stack
- **PHP**: Pure native PHP without Composer or external package dependencies.
- **Database**: PostgreSQL via PDO (`pdo_pgsql`).
- **Environment**: Custom `.env` parser in `config/load-env.php`. Required keys in `.env` (see `.env.example`):
  - `DB_HOST` (defaults to `127.0.0.1` if empty)
  - `DB_PORT` (defaults to `5432` if empty)
  - `DB_NAME`, `DB_USER`, `DB_PASS`

## Architecture & Conventions (Current Codebase)
- **Bootstrap Initialization (`config/init.php`)**:
  - Central loader for config and core helpers.
  - Load order:
    1. `config/load-env.php`
    2. `config/logger.php`
    3. `config/session.php`
    4. `config/csrf.php`
    5. `config/validation.php`
    6. `config/database.php`
    7. `config/query-builder.php`
- **Routing & Clean URLs (`router.php`)**:
  - Custom file-based router executing targets within `public/`.
  - Static assets (CSS, JS, images, fonts) disajikan langsung.
  - Clean URL: request path dipetakan ke target `.php` tanpa ekstensi di URL.
  - Dynamic path parameters:
    - `public/.../[param].php` untuk segmen file dinamis terakhir.
    - `public/.../[param]/` untuk segmen direktori dinamis.
  - Parameter otomatis disuntikkan ke `$_GET['param']` dan variabel global `$route_params['param']`.
  - Rute statis/exact selalu diprioritaskan sebelum fallback dynamic match.
  - Role & Auth Guard:
    - `/login`: Redirect otomatis jika sudah login (`/admin/event` untuk Admin, `/` untuk Member).
    - `/admin` & `/admin/*`: Diproteksi via `require_role('Admin')` (redirect ke `/login` jika belum login, HTTP 403 jika role tidak sesuai).
- **Environment Loading (`config/load-env.php`)**:
  - Automatically loads `../.env` when included.
  - Populates `putenv()`, `$_ENV`, and `$_SERVER`.
  - Throws `Exception` if `.env` does not exist.
- **Database Connection (`config/database.php`)**:
  - Includes `load-env.php` and returns an active `PDO` instance (cached via global `$applicationDb`).
  - Sets `ERRMODE_EXCEPTION`, `FETCH_ASSOC`, and disables `EMULATE_PREPARES`.
  - Throws 500 HTTP response and halts script on connection failure.
- **Query Builder (`config/query-builder.php`)**:
  - Provides `queryBuilder($table, $columns, $filters, $size, $page, $joins, $limit): PDOStatement`.
  - Reuses global `$applicationDb` PDO instance.
- **Session & Auth Helpers (`config/session.php`)**:
  - Initializes native PHP session with `session.use_strict_mode` and `session.cookie_httponly`.
  - Exposes global helpers:
    - `set_user_session($user)`: Regenerates session ID, stores `user_logged_in`, `user_id`, `username`, `role`.
    - `is_logged_in()`: Returns boolean authentication status.
    - `require_login()`: Guards protected pages; redirects unauthenticated requests to `/login?error=Login is required`.
    - `current_user()`: Queries `users` table via PDO for the active `$_SESSION['user_id']` (fallback to session array).
    - `current_role()`: Returns active user role (`string|null`).
    - `has_role(string|array $roles)`: Checks if current user matches given role(s).
    - `require_role(string|array $roles)`: Guards page by role; redirects unauthenticated to login or halts with 403 Forbidden.
    - `logout_user()`: Clears `$_SESSION`, deletes session cookie, and destroys session.
- **Logger (`config/logger.php`)**:
  - `write_log($message, $level = 'INFO', $context = [])` dan `logger(...)`.
  - Simpan ke `log/app.log` dengan rotasi file (`<epoch>.log`) jika melebihi `LOG_MAX_SIZE` (default `5M`).
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
  php migration down          # Rollback migrations (newest to oldest)
  php migration create <name> # Scaffold new migration file
  php migration seed          # Seed dummy data (20 records per table)
  ```
- **Syntax check all PHP files**:
  ```bash
  find . -name "*.php" -not -path "*/.*" -exec php -l {} +
  ```
- **Run dev server**:
  ```bash
  php -S localhost:8080 router.php
  # atau tanpa custom router: php -S localhost:8080 -t public/
  ```
