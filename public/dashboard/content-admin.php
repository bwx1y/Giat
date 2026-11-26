<?php
// perbaiki routing

$dashSearch = $_GET['dash_search'] ?? '';
$dashStatus = $_GET['dash_status'] ?? '';

$hadSearch = array_key_exists('search', $_GET);
$previousSearch = $_GET['search'] ?? null;
$hadStatus = array_key_exists('status', $_GET);
$previousStatus = $_GET['status'] ?? null;

$_GET['search'] = $dashSearch;
$_GET['status'] = $dashStatus;
require __DIR__ . '/../admin/index.php';

if ($hadSearch) {
    $_GET['search'] = $previousSearch;
} else {
    unset($_GET['search']);
}
if ($hadStatus) {
    $_GET['status'] = $previousStatus;
} else {
    unset($_GET['status']);
}

$dashEvents = $eventsResult;
$dashTotalEvents = count($dashEvents);
$dashTotalSubEvents = array_reduce(
    $dashEvents,
    static fn (int $total, array $event): int => $total + (int) ($event['sub_event_count'] ?? 0),
    0
);
$dashTotalMembers = (int) $userActive;
$dashAvgAttendance = (float) $avgAttendanceResult;
$dashTrend = $attendanceTrendResult;
$dashLeaderboard = array_map(
    static fn (array $member): array => [
        'member_name' => $member['member_name'],
        'hadir' => (int) $member['total_attended'],
        'absen' => (int) $member['total_absent'],
        'point' => (int) $member['total_points'],
    ],
    $leaderboardResult
);

$dashboardData = [
    'stats' => [
        'totalEvents'    => $dashTotalEvents,
        'totalSubEvents' => $dashTotalSubEvents,
        'totalMembers'   => $dashTotalMembers,
        'avgAttendance'  => $dashAvgAttendance,
    ],
    'events'      => $dashEvents,
    'trend'       => $dashTrend,
    'leaderboard' => $dashLeaderboard,
];
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
            <form method="GET" action="/dashboard" class="toolbar">
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
                <a href="/dashboard" class="btn btn-outline btn-sm">Reset</a>
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
