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

if (
    $memberEventRouteId
    && $_SERVER['REQUEST_METHOD'] === 'POST'
    && ($_POST['meeting_action'] ?? '') === 'create'
) {
    if (($memberEventRole['role'] ?? null) !== 'Admin') {
        http_response_code(403);
        exit('Forbidden');
    }

    $_GET['id'] = (string) $memberEventRouteId;
    $handlerLocation = '/admin/event/' . $memberEventRouteId . '/meeting';
    $eventDetailLocation = '/event/' . $memberEventRouteId;

    header_register_callback(static function () use ($handlerLocation, $eventDetailLocation): void {
        foreach (headers_list() as $header) {
            if (preg_match('/^Location:\s*' . preg_quote($handlerLocation, '/') . '$/i', $header)) {
                header('Location: ' . $eventDetailLocation, true, 303);
                break;
            }
        }
    });

    require __DIR__ . '/member/event/[id]/meeting/create/handler.php';
    exit;
}

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

$pageView = $_GET['page_view'] ?? 'dashboard';

$pageComponentMap = [
    'dashboard'       => ['dashboard.php'],
    'anggota'         => ['anggota.php'],
    'event'           => ['event.php'],
    'event-detail'    => ['event_detail.php'],
    'subevent-detail' => ['subevent_detail.php'],
    'admin-dashboard' => ['admin_dashboard.php'],
    'absensi'         => ['absensi.php'],
    'statistik'       => ['statistik.php'],
    'user-dashboard'  => ['user_dashboard.php'],
    'user-history'    => ['user_history.php'],
];

$roleBasedPages = [
    'dashboard' => ['dashboard.php', 'admin_dashboard.php', 'user_dashboard.php'],
];

$pagesToLoad = $roleBasedPages[$pageView]
    ?? $pageComponentMap[$pageView]
    ?? [$pageView . '.php'];

$pagesDir = __DIR__ . '/components/pages/';

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
      foreach ($pagesToLoad as $pageFile) {
          $fullPath = $pagesDir . $pageFile;
          if (is_file($fullPath)) {
              require_once $fullPath;
          }
      }
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
