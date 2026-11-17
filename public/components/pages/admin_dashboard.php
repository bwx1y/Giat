<?php
$adminEventSearch = $_GET['search'] ?? '';
$adminEventStatus = $_GET['status'] ?? '';

$_GET['page'] = $_GET['page'] ?? 1;
$_GET['size'] = $_GET['size'] ?? 50;

require __DIR__ . '/../../admin/event/index.php';
$adminEventsList = $result ?? [];
?>
<div id="page-admin-dashboard" class="page">
  <div class="page-header">
    <div>
      <h1 class="page-header-title">Event & Sub Event yang Saya Kelola</h1>
      <p class="page-header-desc">
        Anda ditugaskan sebagai PIC untuk Sub Event di bawah ini. Anda dapat membuat sub event baru, membuka sesi,
        mencetak QR Code, dan mengelola absensi.
      </p>
    </div>
    <button class="btn btn-blue" onclick="openAddSubEventModal()">
      <i class="fa-solid fa-plus"></i> Tambah Sub Event
    </button>
  </div>

  <form method="GET" action="/dashboard" class="toolbar">
    <div class="search-wrap search-wrap-sm">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input type="text" name="search" id="search-admin-event" placeholder="Cari Event…" value="<?= htmlspecialchars($adminEventSearch) ?>" />
    </div>
    <select name="status" class="filter-select" id="filter-admin-event-status" onchange="this.form.submit()">
      <option value="">Semua Status</option>
      <option value="Not started" <?= $adminEventStatus === 'Not started' ? 'selected' : '' ?>>Not started</option>
      <option value="in progress" <?= $adminEventStatus === 'in progress' ? 'selected' : '' ?>>in progress</option>
      <option value="done" <?= $adminEventStatus === 'done' ? 'selected' : '' ?>>done</option>
    </select>
    <button type="submit" class="btn btn-blue btn-sm">Cari</button>
    <?php if (!empty($adminEventSearch) || !empty($adminEventStatus)): ?>
      <a href="/dashboard" class="btn btn-outline btn-sm">Reset</a>
    <?php endif; ?>
  </form>

  <div id="admin-managed-container">

  </div>
</div>

<script>
  window.dbAdminEvents = <?= json_encode($adminEventsList) ?>;
</script>
