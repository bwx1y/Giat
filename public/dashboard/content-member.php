<?php
// perbaiki routing

$memberDashboardStats    = [];
$memberDashboardProgress = [];

if (has_role('Member')) {
    require __DIR__ . '/../member/index.php';
    $memberDashboardStats    = $statch[0] ?? [];
    $memberDashboardProgress = $event;
}

$memberDashboardName = htmlspecialchars(
    $currentUser['full_name'] ?? $currentUser['username'] ?? 'Anggota',
    ENT_QUOTES,
    'UTF-8'
);
$memberTotalPoints    = (int)   ($memberDashboardStats['total_point']        ?? 0);
$memberAttendedCount  = (int)   ($memberDashboardStats['sub_event_hadir']    ?? 0);
$memberAbsentCount    = (int)   ($memberDashboardStats['tidak_hadir']        ?? 0);
$memberAttendanceRate = (float) ($memberDashboardStats['tingkat_kehadiran']  ?? 0);
?>
<div id="page-user-dashboard" class="page">
  <div class="detail-hero hero-gradient">
    <h1 class="hero-title">Selamat datang, <?= $memberDashboardName ?>! 👋</h1>
    <p class="hero-desc">Pantau kehadiran pada setiap Sub Event dan kumpulkan point keaktifan Anda.</p>
  </div>

  <div class="summary-grid">
    <div class="summary-card">
      <div class="summary-icon icon-blue"><i class="fa-solid fa-star"></i></div>
      <div class="summary-info">
        <div class="label">Total Point Saya</div>
        <div class="value text-blue" id="user-total-point"><?= $memberTotalPoints ?> pt</div>
      </div>
    </div>
    <div class="summary-card">
      <div class="summary-icon icon-green"><i class="fa-solid fa-circle-check"></i></div>
      <div class="summary-info">
        <div class="label">Sub Event Hadir</div>
        <div class="value" id="user-total-hadir"><?= $memberAttendedCount ?></div>
      </div>
    </div>
    <div class="summary-card">
      <div class="summary-icon icon-red"><i class="fa-solid fa-circle-xmark"></i></div>
      <div class="summary-info">
        <div class="label">Tidak Hadir</div>
        <div class="value" id="user-total-absen"><?= $memberAbsentCount ?></div>
      </div>
    </div>
    <div class="summary-card">
      <div class="summary-icon icon-purple"><i class="fa-solid fa-percent"></i></div>
      <div class="summary-info">
        <div class="label">Tingkat Kehadiran</div>
        <div class="value" id="user-attendance-rate"><?= number_format($memberAttendanceRate, 1) ?>%</div>
      </div>
    </div>
  </div>

  <div class="card card-active-session">
    <div class="card-body card-body-between">
      <div>
        <div class="session-tag">
          <span class="live-dot"></span> Sesi Absensi Aktif Saat Ini
        </div>
        <h2 class="session-title" id="active-session-name">Rapat Persiapan 1</h2>
        <div class="session-meta">
          <span><i class="fa-solid fa-layer-group text-blue-500"></i> Webinar Nasional 2026</span>
          <span><i class="fa-regular fa-clock text-blue-500"></i> 19:00 - 21:00</span>
          <span><i class="fa-solid fa-location-dot text-blue-500"></i> Ruang Meeting A</span>
        </div>
      </div>
      <button class="btn btn-blue btn-scan-qr" onclick="openAttendanceQrScanner()">
        <i class="fa-solid fa-qrcode"></i> Scan QR Sub Event (+3 Point)
      </button>
    </div>
  </div>

  <div class="section-header">
    <h2 class="section-title">Daftar Event & Progres Sub Event</h2>
    <div class="d-flex align-items-center gap-2">
      <form method="GET" action="/dashboard">
        <button type="submit" class="btn btn-outline btn-sm">Muat Ulang Data</button>
      </form>
      <a href="#" class="action-link" onclick="navTo('user-history');return false;">Lihat Riwayat Lengkap</a>
    </div>
  </div>

  <div id="user-events-progress-container">

  </div>
</div>
<script>
  window.dbUserDashboardProgress = <?= json_encode(
      $memberDashboardProgress,
      JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
  ) ?>;
</script>
