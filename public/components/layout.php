<?php
// perbaiki routing

if (!isset($route_params)) {
    $route_params = [];
}

$isLoggedIn  = is_logged_in();
$currentUser = $isLoggedIn ? current_user() : null;

if (!isset($isEventAdmin)) {
    $isEventAdmin = false;

    if ($isLoggedIn && ($currentUser['role'] ?? null) !== 'Admin' && isset($currentUser['id'])) {
        global $applicationDb;

        $eventStmt = $applicationDb->prepare(
            'SELECT DISTINCT event_id FROM user_events WHERE user_id = :user_id AND deleted_at IS NULL'
        );
        $eventStmt->execute(['user_id' => $currentUser['id']]);

        foreach ($eventStmt->fetchAll(PDO::FETCH_COLUMN) as $eventId) {
            $eventRole = user_role_event((int) $eventId);
            if (($eventRole['role'] ?? null) === 'Admin') {
                $isEventAdmin = true;
                break;
            }
        }
    }
}

$memberEventRouteId = filter_var(
    $route_params['event_id'] ?? null,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);
$memberEventRole       = $memberEventRouteId ? user_role_event((int) $memberEventRouteId) : null;
$canManageEventMembers = $memberEventRouteId
    && (has_role('Admin') || ($memberEventRole['role'] ?? null) === 'Admin');

require_once __DIR__ . '/header.php';

if (!$isLoggedIn):
    require_once __DIR__ . '/login.php';
else:
?>
<div id="app-shell" class="d-flex min-vh-100 w-100">
  <div id="sidebar-overlay" onclick="closeSidebar()"></div>
  <?php require_once __DIR__ . '/sidebar.php'; ?>
  <div class="d-flex flex-column flex-fill min-w-0" id="main-wrapper">
    <?php require_once __DIR__ . '/topbar.php'; ?>
    <main id="main-content" class="flex-fill w-100 min-w-0">
      <?php require $contentFile; ?>
    </main>
  </div>
</div>
<?php
require_once __DIR__ . '/modals.php';
require_once __DIR__ . '/toast.php';
?>
<script>
  window.currentUser             = <?= json_encode($currentUser) ?>;
  window.isEventAdmin            = <?= json_encode($isEventAdmin) ?>;
  window.canManageEventMembers   = <?= json_encode((bool) $canManageEventMembers) ?>;
  window.routeParams             = <?= json_encode($route_params ?? []) ?>;
</script>
<?php
endif;
require_once __DIR__ . '/footer.php';
