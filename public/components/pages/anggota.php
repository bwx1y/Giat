<?php
$mSearch = $_GET['m_search'] ?? '';
$mRole = $_GET['m_role'] ?? '';

$membersDataList = [];
if (has_role('Admin')) {
    require __DIR__ . '/../../admin/users/index.php';
    $membersDataList = $users;
}

global $applicationDb;
$memberStats = [];
try {
    $statsStmt = $applicationDb->query("SELECT user_id, SUM(status) AS hadir, SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) AS absen, (SUM(status) * 3 - SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END)) AS point FROM vw_absent GROUP BY user_id");
    foreach ($statsStmt->fetchAll(PDO::FETCH_ASSOC) as $s) {
        $memberStats[$s['user_id']] = $s;
    }
} catch (\Throwable $e) {}

foreach ($membersDataList as &$m) {
    $stats = $memberStats[$m['id']] ?? ['hadir' => 0, 'absen' => 0, 'point' => 0];
    $m['hadir'] = intval($stats['hadir']);
    $m['absen'] = intval($stats['absen']);
    $m['point'] = intval($stats['point']);
}
unset($m);
?>
<div id="page-anggota" class="page">
        <div class="card">
          <div class="card-header">
            <div>
              <span class="card-title">Manajemen Data Anggota</span>
              <div class="card-subtitle">Daftar pengguna terdaftar dalam sistem</div>
            </div>
            <button class="btn btn-blue" onclick="openModal('modal-add-member')">
              <i class="fa-solid fa-user-plus"></i> Tambah Anggota
            </button>
          </div>
          <form method="GET" action="/users" class="toolbar">
            <div class="search-wrap search-wrap-sm">
              <i class="fa-solid fa-magnifying-glass"></i>
              <input type="text" name="m_search" id="member-search" placeholder="Cari nama atau username…" value="<?= htmlspecialchars($mSearch) ?>" />
            </div>
            <select name="m_role" class="filter-select" id="member-filter-role" onchange="this.form.submit()">
              <option value="">Semua Role</option>
              <option value="Admin" <?= $mRole === 'Admin' ? 'selected' : '' ?>>Admin</option>
              <option value="Member" <?= $mRole === 'Member' ? 'selected' : '' ?>>Member</option>
            </select>
            <button type="submit" class="btn btn-blue btn-sm">Cari</button>
            <?php if (!empty($mSearch) || !empty($mRole)): ?>
              <a href="/users" class="btn btn-outline btn-sm">Reset</a>
            <?php endif; ?>
          </form>
          <div class="table-wrap">
            <table id="member-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nama Anggota</th>
                  <th>Username</th>
                  <th>Role</th>
                  <th>Hadir</th>
                  <th>Absen</th>
                  <th>Total Point</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody id="member-tbody"></tbody>
            </table>
          </div>
        </div>
      </div>
<script>
  window.dbMembers = <?= json_encode($membersDataList) ?>;
</script>
