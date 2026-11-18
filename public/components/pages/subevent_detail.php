<?php
$_subDetailEventId = isset($route_params['event_id']) ? (int) $route_params['event_id'] : (int) ($_GET['id'] ?? 0);
$_subDetailMeetingId = isset($route_params['subevent_id']) ? (int) $route_params['subevent_id'] : (int) ($_GET['meeting_id'] ?? 0);

$_subSearch = $_GET['q'] ?? $_GET['search'] ?? '';

$_dbSubEventDetail = null;
$_dbSubMeetingStats = null;
$_dbSubMeetingDetail = null;
$_dbSubParticipants = [];

if ($_subDetailEventId > 0 && $_subDetailMeetingId > 0) {
    $_GET['id'] = (string) $_subDetailEventId;
    $_GET['meeting_id'] = (string) $_subDetailMeetingId;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require __DIR__ . '/../../member/event/[id]/meeting/[meeting_id]/absent.php';
    }

    // 1. Require backend index untuk data event utama dan statistik meeting (vw_meeting_stats)
    require __DIR__ . '/../../member/event/[id]/meeting/[meeting_id]/index.php';
    $_dbSubEventDetail = $event ?? null;
    $_dbSubMeetingStats = $stats ?? null;

    // 2. Require backend meeting update/index untuk detail data meeting (meetings table)
    require __DIR__ . '/../../admin/event/[id]/meeting/[meeting_id]/update/index.php';
    $_dbSubMeetingDetail = $meeting ?? null;

    // 3. Ambil daftar peserta dari view vw_absent
    global $applicationDb;
    $pQuery = 'SELECT v.user_id, v.member_name AS name, v.method, v.status,
                      um.created_at AS attended_at,
                      CASE WHEN v.status = 1 THEN 3 ELSE 0 END AS point
               FROM vw_absent v
               LEFT JOIN user_meetings um ON um.user_id = v.user_id AND um.meeting_id = v.meeting_id AND um.deleted_at IS NULL
               WHERE v.meeting_id = :meeting_id';
    $pParams = [':meeting_id' => $_subDetailMeetingId];

    if (!empty($_subSearch)) {
        $pQuery .= ' AND v.member_name ILIKE :search';
        $pParams[':search'] = '%' . $_subSearch . '%';
    }

    $pQuery .= ' ORDER BY v.status DESC, v.member_name ASC';

    $pStmt = $applicationDb->prepare($pQuery);
    $pStmt->execute($pParams);
    $_dbSubParticipants = $pStmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<div id="page-subevent-detail" class="page">
  <div class="breadcrumb-wrap">
    <a class="breadcrumb-item" onclick="navTo(currentRole === 'admin' ? 'admin-dashboard' : 'event')">
      <i class="fa-solid fa-calendar-days"></i> <span id="sub-bc-root">Event</span>
    </a>
    <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-right"></i></span>
    <a class="breadcrumb-item" id="sub-bc-parent" onclick="backToParentEvent()">Webinar Nasional 2026</a>
    <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="breadcrumb-current" id="sub-bc-current">Rapat Persiapan 1</span>
  </div>

  <div class="card card-accent-blue">
    <div class="card-body card-body-padded">
      <div class="subevent-hero-flex">
        <div>
          <div class="subevent-tree-badge">
            <i class="fa-solid fa-sitemap"></i> Sub Event dari: <strong id="sub-info-parent" class="text-navy ms-1">Webinar Nasional 2026</strong>
          </div>
          <h1 id="sub-info-title" class="subevent-title">Rapat Persiapan 1</h1>
          <div class="subevent-meta">
            <span><i class="fa-regular fa-calendar text-blue-500"></i> <strong id="sub-info-date">12 September 2026</strong></span>
            <span><i class="fa-regular fa-clock text-blue-500"></i> <span id="sub-info-time">19:00 - 21:00</span></span>
            <span><i class="fa-solid fa-location-dot text-blue-500"></i> <span id="sub-info-loc">Ruang Meeting A</span></span>
            <span><i class="fa-solid fa-user-tie text-blue-500"></i> PIC: <strong id="sub-info-pic">Budi Santoso</strong></span>
            <span>Status: <span class="badge badge-green" id="sub-info-status">Finished</span></span>
          </div>
        </div>
        <div>
          <button class="btn btn-blue btn-qr-gen" onclick="openQrForCurrentSubEvent()">
            <i class="fa-solid fa-qrcode"></i> Generate QR Absensi
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="summary-grid">
    <div class="summary-card">
      <div class="summary-icon icon-blue"><i class="fa-solid fa-users"></i></div>
      <div class="summary-info">
        <div class="label">Total Peserta</div>
        <div class="value" id="sub-stat-total">40</div>
      </div>
    </div>
    <div class="summary-card">
      <div class="summary-icon icon-green"><i class="fa-solid fa-user-check"></i></div>
      <div class="summary-info">
        <div class="label">Hadir</div>
        <div class="value text-green" id="sub-stat-hadir">38</div>
      </div>
    </div>
    <div class="summary-card">
      <div class="summary-icon icon-red"><i class="fa-solid fa-user-xmark"></i></div>
      <div class="summary-info">
        <div class="label">Tidak Hadir</div>
        <div class="value text-red" id="sub-stat-absen">2</div>
      </div>
    </div>
    <div class="summary-card">
      <div class="summary-icon icon-purple"><i class="fa-solid fa-chart-pie"></i></div>
      <div class="summary-info">
        <div class="label">Persentase Kehadiran</div>
        <div class="value" id="sub-stat-pct">95%</div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <div>
        <span class="card-title">Daftar Kehadiran Peserta Sub Event</span>
        <div class="card-subtitle">Absensi real-time melalui scan QR code atau manual oleh PIC</div>
      </div>
      <form method="GET" action="/event/<?= (int)$_subDetailEventId ?>/subevent/<?= (int)$_subDetailMeetingId ?>" class="d-flex align-items-center gap-2">
        <div class="search-wrap search-wrap-sm">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" name="q" placeholder="Cari peserta…" value="<?= htmlspecialchars($_subSearch) ?>" oninput="filterSubParticipants(this.value)" />
        </div>
        <button type="submit" class="btn btn-blue btn-sm">Cari</button>
        <?php if (!empty($_subSearch)): ?>
          <a href="/event/<?= (int)$_subDetailEventId ?>/subevent/<?= (int)$_subDetailMeetingId ?>" class="btn btn-outline btn-sm">Reset</a>
        <?php endif; ?>
      </form>
    </div>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Nama Peserta</th>
            <th>Divisi</th>
            <th>Status Kehadiran</th>
            <th>Waktu Absen</th>
            <th>Metode</th>
            <th>Point Didapat</th>
            <th>Aksi / Koreksi</th>
          </tr>
        </thead>
        <tbody id="sub-participants-tbody">

        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
  window.dbSubEventDetail   = <?= json_encode($_dbSubEventDetail) ?>;
  window.dbSubMeetingStats  = <?= json_encode($_dbSubMeetingStats) ?>;
  window.dbSubMeetingDetail = <?= json_encode($_dbSubMeetingDetail) ?>;
  window.dbSubParticipants  = <?= json_encode($_dbSubParticipants) ?>;
</script>
