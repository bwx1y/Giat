<?php
require_once __DIR__ . '/components/header.php';
require_once __DIR__ . '/components/login.php';
?>
<div id="app-shell" class="d-flex min-vh-100 w-100" style="display:none;">
  <div id="sidebar-overlay" onclick="closeSidebar()" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:99;"></div>
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
require_once __DIR__ . '/components/footer.php';
