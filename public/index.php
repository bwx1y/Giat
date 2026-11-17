<?php
require_once __DIR__ . '/../config/init.php';

$isLoggedIn = is_logged_in();
$currentUser = $isLoggedIn ? current_user() : null;
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

$memberEventRouteId = filter_var(
    $route_params['event_id'] ?? null,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);
$memberEventRole = $memberEventRouteId ? user_role_event((int) $memberEventRouteId) : null;
$canManageEventMembers = $memberEventRouteId
    && (has_role('Admin') || ($memberEventRole['role'] ?? null) === 'Admin');

if ($memberEventRouteId && $_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['member_action'] ?? '') === 'add') {
    if (!$canManageEventMembers) {
        http_response_code(403);
        exit('Forbidden');
    }

    $_GET['id'] = (string) $memberEventRouteId;
    require __DIR__ . '/admin/event/[id]/member/create/handler.php';
    header_remove('Location');
    header('Location: /event/' . $memberEventRouteId . '#event-members', true, 303);
    exit;
}

if ($memberEventRouteId && $_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (!$canManageEventMembers) {
        http_response_code(403);
        exit('Forbidden');
    }

    parse_str(file_get_contents('php://input'), $deleteInput);
    $memberId = filter_var(
        $deleteInput['member_id'] ?? null,
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );
    if (!$memberId) {
        http_response_code(400);
        exit('Invalid member ID');
    }

    $_GET['id'] = (string) $memberEventRouteId;
    $_GET['member_id'] = (string) $memberId;
    require __DIR__ . '/admin/event/[id]/member/[member_id]/delete.php';
    exit;
}

require_once __DIR__ . '/components/header.php';

if (!$isLoggedIn):
    require_once __DIR__ . '/components/login.php';
else:
?>
<div id="app-shell" class="d-flex min-vh-100 w-100">
   <div id="sidebar-overlay" onclick="closeSidebar()"></div>
  <?php require_once __DIR__ . '/components/sidebar.php'; ?>
  <div class="d-flex flex-column flex-fill min-w-0" id="main-wrapper">
    <?php require_once __DIR__ . '/components/topbar.php'; ?>
    <main id="main-content" class="flex-fill w-100 min-w-0">
      <?php
      require_once __DIR__ . '/components/pages/dashboard.php';
      require_once __DIR__ . '/components/pages/anggota.php';
      require_once __DIR__ . '/components/pages/event.php';
      require_once __DIR__ . '/components/pages/event_detail.php';
      require_once __DIR__ . '/components/pages/subevent_detail.php';
      require_once __DIR__ . '/components/pages/admin_dashboard.php';
      require_once __DIR__ . '/components/pages/absensi.php';
      require_once __DIR__ . '/components/pages/statistik.php';
      require_once __DIR__ . '/components/pages/user_dashboard.php';
      require_once __DIR__ . '/components/pages/user_history.php';
      ?>
    </main>
  </div>
</div>
<?php
require_once __DIR__ . '/components/modals.php';
require_once __DIR__ . '/components/toast.php';
?>
<script>
  window.currentUser = <?= json_encode($currentUser) ?>;
  window.isEventAdmin = <?= json_encode($isEventAdmin) ?>;
  window.canManageEventMembers = <?= json_encode((bool) $canManageEventMembers) ?>;
  window.routeParams = <?= json_encode($route_params ?? []) ?>;
</script>
<?php
endif;
require_once __DIR__ . '/components/footer.php';
