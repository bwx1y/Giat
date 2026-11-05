<?php
global $applicationDb;

$dashSearch = $_GET['dash_search'] ?? '';
$dashStatus = $_GET['dash_status'] ?? '';

$dashFilters = [];
if (!empty($dashSearch)) {
    $dashFilters[] = [
        'key' => 'search',
        'query' => 'title ILIKE :search',
        'value' => "%$dashSearch%",
    ];
}
if (!empty($dashStatus)) {
    $dashFilters[] = [
        'key' => 'status',
        'query' => 'status = :status',
        'value' => $dashStatus,
    ];
}

$dashEvents = [];
try {
    $eventStmt = queryBuilder(table: 'vw_event', filters: $dashFilters, size: 50, page: 1);
    if ($eventStmt) {
        $dashEvents = $eventStmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (\Throwable $e) {
    $dashEvents = [];
}

$dashTotalEvents = count($dashEvents);
$dashTotalSubEvents = 0;
foreach ($dashEvents as $ev) {
    $dashTotalSubEvents += intval($ev['sub_event_count'] ?? 0);
}

$dashTotalMembers = 0;
try {
    $userStmt = queryBuilder(table: 'users', columns: ['COUNT(*) AS total'], filters: [
        ['key' => 'active', 'query' => 'active = true', 'value' => '1'],
        ['key' => 'deleted', 'query' => 'deleted_at IS NULL', 'value' => '1'],
    ], limit: 1);
    $dashTotalMembers = intval($userStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
} catch (\Throwable $e) {}

$dashAvgAttendance = 0;
try {
    $avgStmt = queryBuilder(table: 'vw_absent', columns: ['ROUND(AVG(status) * 100, 1) AS avg_attendance'], limit: 1);
    $dashAvgAttendance = floatval($avgStmt->fetch(PDO::FETCH_ASSOC)['avg_attendance'] ?? 0);
} catch (\Throwable $e) {}

$dashTrend = [];
try {
    $trendStmt = queryBuilder(table: 'mv_attendance_trend_6_months');
    $dashTrend = $trendStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (\Throwable $e) {}

$dashLeaderboard = [];
try {
    $lbStmt = $applicationDb->query("SELECT member_name, SUM(status) AS hadir, SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) AS absen, (SUM(status) * 3 - SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END)) AS point FROM vw_absent GROUP BY member_name ORDER BY point DESC LIMIT 5");
    $dashLeaderboard = $lbStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (\Throwable $e) {}

$dashboardData = [
    'stats' => [
        'totalEvents' => $dashTotalEvents,
        'totalSubEvents' => $dashTotalSubEvents,
        'totalMembers' => $dashTotalMembers,
        'avgAttendance' => $dashAvgAttendance,
    ],
    'events' => $dashEvents,
    'trend' => $dashTrend,
    'leaderboard' => $dashLeaderboard,
];

// echo '<pre>';
// var_dump($dashboardData);
// echo '</pre>';
// exit;

?>
<div id="page-dashboard" class="page">
        <div class="summary-grid">
          <div class="summary-card">
            <div class="summary-icon icon-blue"><i class="fa-solid fa-calendar-days"></i></div>
            <div class="summary-info">
              <div class="label">Total Event Utama</div>
              <div class="value" id="stat-total-events"><?= $dashTotalEvents ?></div>
            </div>
          </div>
          <div class="summary-card">
            <div class="summary-icon icon-purple"><i class="fa-solid fa-sitemap"></i></div>
            <div class="summary-info">
              <div class="label">Total Sub Event</div>
              <div class="value" id="stat-total-subevents"><?= $dashTotalSubEvents ?></div>
            </div>
          </div>
          <div class="summary-card">
            <div class="summary-icon icon-green"><i class="fa-solid fa-users"></i></div>
            <div class="summary-info">
              <div class="label">Total Anggota</div>
              <div class="value" id="stat-total-members"><?= $dashTotalMembers ?></div>
            </div>
          </div>
          <div class="summary-card">
            <div class="summary-icon icon-amber"><i class="fa-solid fa-clipboard-check"></i></div>
            <div class="summary-info">
              <div class="label">Rata-rata Kehadiran</div>
              <div class="value" id="stat-avg-attendance"><?= $dashAvgAttendance ?>%</div>
            </div>
          </div>
        </div>

        <div class="dashboard-grid">

          <div class="card">
            <div class="card-header">
              <div>
                <span class="card-title">Tren Kehadiran Sub Event (6 Bulan)</span>
                <div class="card-subtitle">Akumulasi kehadiran seluruh sesi sub-aktivitas</div>
              </div>
            </div>
            <div class="card-body">
              <div class="chart-wrap">
                <canvas id="chart-attendance"></canvas>
              </div>
            </div>
          </div>

          <div class="card">
            <div class="card-header">
              <div>
                <span class="card-title">Leaderboard Keaktifan Anggota</span>
                <div class="card-subtitle">Perhitungan point (+3 Hadir, -1 Absen per Sub Event)</div>
              </div>
              <a href="#" class="action-link" onclick="navTo('anggota');return false;">Lihat Semua</a>
            </div>
            <div class="card-body" id="dashboard-top-members">

            </div>
          </div>

          <div class="card grid-col-2">
            <div class="card-header">
              <div>
                <span class="card-title">Event Utama & Ringkasan Sub Event</span>
                <div class="card-subtitle">Struktur hierarki event yang dikelola Super Admin</div>
              </div>
              <button class="btn btn-blue btn-sm" id="btn-create-event-dash" onclick="openModal('modal-add-event')">
                <i class="fa-solid fa-plus"></i> Buat Event Utama
              </button>
            </div>
            <form method="GET" action="" class="toolbar">
              <input type="hidden" name="page_view" value="dashboard" />
              <div class="search-wrap search-wrap-sm">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="dash_search" id="search-dashboard" placeholder="Cari Event Utama…" value="<?= htmlspecialchars($dashSearch) ?>" />
              </div>
              <select name="dash_status" class="filter-select" id="filter-dashboard-status" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="Not started" <?= $dashStatus === 'Not started' ? 'selected' : '' ?>>Not started</option>
                <option value="in progress" <?= $dashStatus === 'in progress' ? 'selected' : '' ?>>in progress</option>
                <option value="done" <?= $dashStatus === 'done' ? 'selected' : '' ?>>done</option>
              </select>
              <button type="submit" class="btn btn-blue btn-sm">Cari</button>
              <?php if (!empty($dashSearch) || !empty($dashStatus)): ?>
                <a href="/?page_view=dashboard" class="btn btn-outline btn-sm">Reset</a>
              <?php endif; ?>
            </form>
            <div class="table-wrap">
              <table>
                <thead>
                  <tr>
                    <th>Event Utama</th>
                    <th>Periode</th>
                    <th>Sub Event</th>
                    <th>Total Peserta</th>
                    <th>Status</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody id="dashboard-events-tbody">

                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
<script>
  window.dbDashboard = <?= json_encode($dashboardData) ?>;
  
</script>
