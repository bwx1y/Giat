# Developer & Agent Guidelines

## System Requirements & Runtime
- **PHP Version**: Requires **PHP 8.5+** (uses the pipe operator `|>` in `index.php` and `src/JwtHelper.php`). Older PHP versions will throw parse errors.
- **Zero Dependencies**: Pure native PHP micro-framework. No Composer, no `vendor/`, no external package manager.
- **Autoloading**: `spl_autoload_register` in `index.php` maps namespaces (`controller\`, `core\`, `model\`, `config\`) directly to directory paths.
- **Database**: PostgreSQL with PDO (`pdo_pgsql`). Local dev server connects via settings in `.env` (`DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`).
- **Environment**: `.env` is parsed manually in `index.php` without third-party libraries. Required keys: database credentials and `JWT_SECRET`.

## Commands
- **Run dev server**:
  ```bash
  php -S localhost:8080 index.php
  ```
- **Syntax check all PHP files**:
  ```bash
  find . -name "*.php" -not -path "*/.*" -exec php -l {} +
  ```
- **Manual API verification**:
  Use `testin.http` (VS Code REST Client / IntelliJ HTTP Client) or `curl` against `http://localhost:8080/api/...`.

## Architecture & Routing Conventions
- **API Prefix**: All API routes must start with `/api/` (e.g., `/api/auth/login`). Root `/api` returns health status.
- **Controller Resolution**:
  - URL segments map directly to filesystem subdirectories under `controller/`.
  - The final segment becomes `ucfirst($segment) . 'Controller'`.
  - Trailing numeric or string ID is passed as `$idParam` and populated into `$_GET['id']`.
  - Examples:
    - `/api/auth/login` -> `controller\auth\LoginController`
    - `/api/profile` -> `controller\ProfileController`
    - `/api/events/1` -> `controller\EventsController` with `$idParam = "1"`
- **Request Lifecycle (`controller\Controller`)**:
  - Subclasses must extend `controller\Controller`.
  - Dispatches methods automatically based on HTTP verb:
    - `GET` without ID -> `index()`
    - `GET` with ID -> `show(string $id)`
    - `POST` -> `store(Request $request)`
    - `PUT` / `PATCH` -> `update(Request $request, string $id)` (400 if ID omitted)
    - `DELETE` -> `destroy(string $id)` (400 if ID omitted)
  - Unimplemented verbs return 404 by default.
  - `$this->json($data, $statusCode)` sends JSON and immediately terminates script execution (`exit`).
- **Validation**:
  - `$request->validate(['field' => 'required|min:8|email'])` terminates with HTTP 400 on error.
- **Authentication & RBAC**:
  - Define `protected array|null $authorization` on controllers:
    - `null`: Public endpoint (no auth required).
    - `['*']`: Any authenticated user with valid JWT bearer token. Populates `$this->user`.
    - `['Admin']`, `['Member']`: Restricts access to specific roles. Returns 401 on missing/invalid token, 403 on role mismatch.
  - Header format: `Authorization: Bearer <token>`.

## Database Schema & Enums (PostgreSQL)
- **Tables**: `users`, `events`, `meetings`, `user_events`, `user_meetings`.
- **Enums**:
  - `user_role`: `'Admin'`, `'Member'`
  - `attendance_method`: `'Qr'`, `'Manually'`
  - `event_status`: `'Not started'`, `'in progess'`, `'done'` *(note: `'in progess'` is spelled with one 's' in the database enum)*

## Git & Notion Workflow
- **Commit Message Format**:
  Commit messages must start with the Notion Task ID prefix:
  `<task_id> - <message>` or `<task_id>: <message>` (e.g. `8 - create login route`).
- **Branches**:
  - `main-backend`, `main-frontend`: Protected deployment branches.
  - Work on feature branches. Pushes trigger `.github/workflows/main-notion.yml` to set task status to "In progress". Merged PRs set task status to "Done".
