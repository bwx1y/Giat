<?php
// $route_params['event_id'] disuntikkan oleh router.php saat URL /event/{id}
$_detailEventId = isset($route_params['event_id']) ? (int) $route_params['event_id'] : 0;
$memberAddedToastName = null;
$memberAddedFlash = $_SESSION['event_member_added'] ?? null;

if (
    is_array($memberAddedFlash)
    && (int) ($memberAddedFlash['event_id'] ?? 0) === $_detailEventId
    && is_string($memberAddedFlash['name'] ?? null)
) {
    $memberAddedToastName = $memberAddedFlash['name'];
    unset($_SESSION['event_member_added']);
}

$_dbEventDetail           = null;
$_dbEventMeetings         = [];
$_dbEventMemberCandidates = [];

if ($_detailEventId > 0) {
    $_GET['id'] = $_detailEventId;

    // Data event utama
    require __DIR__ . '/../../admin/event/[id]/update/index.php';
    $_dbEventDetail = $event ?? null;

    // Kandidat peserta (user belum terdaftar di event ini)
    require __DIR__ . '/../../admin/event/[id]/member/create/index.php';
    $_dbEventMemberCandidates = $userList ?? [];

    // Sub event (meetings) — belum ada backend file tersendiri, query langsung
    global $applicationDb;
    $_mtStmt = $applicationDb->prepare(
        'SELECT m.id, m.title, m.location, m.start_date, m.end_date, m.status,
                COALESCE(s.total_peserta, 0)        AS total_peserta,
                COALESCE(s.hadir, 0)                AS hadir,
                COALESCE(s.tidak_hadir, 0)          AS tidak_hadir,
                COALESCE(s.persentase_kehadiran, 0) AS persentase_kehadiran
         FROM meetings m
         LEFT JOIN vw_meeting_stats s ON s.meeting_id = m.id
         WHERE m.event_id = :event_id AND m.deleted_at IS NULL
         ORDER BY m.start_date ASC, m.id ASC'
    );
    $_mtStmt->bindValue(':event_id', $_detailEventId, PDO::PARAM_INT);
    $_mtStmt->execute();
    $_dbEventMeetings = $_mtStmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<script>
  window.dbEventDetail   = <?= json_encode($_dbEventDetail) ?>;
  window.dbEventMeetings = <?= json_encode($_dbEventMeetings) ?>;
  window.dbEventMemberCandidates = <?= json_encode($_dbEventMemberCandidates) ?>;
  window.eventMemberAddedName = <?= json_encode($memberAddedToastName, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
</script>

<style>
  /* Sembunyikan scrollbar pada modal dan dropdown opsi anggota */
  #modal-add-event-member .modal,
  #modal-add-event-member .modal-body,
  .modal,
  .modal-overlay .modal,
  .modal-body,
  .custom-select-options {
    scrollbar-width: none !important;
    -ms-overflow-style: none !important;
  }
  #modal-add-event-member .modal::-webkit-scrollbar,
  #modal-add-event-member .modal-body::-webkit-scrollbar,
  .modal::-webkit-scrollbar,
  .modal-overlay .modal::-webkit-scrollbar,
  .modal-body::-webkit-scrollbar,
  .custom-select-options::-webkit-scrollbar {
    display: none !important;
    width: 0 !important;
    height: 0 !important;
  }
</style>

<div id="page-event-detail" class="page">

        <div class="breadcrumb-wrap">
          <a class="breadcrumb-item" onclick="navTo('event')"><i class="fa-solid fa-calendar-days"></i> Event</a>
          <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-right"></i></span>
          <span class="breadcrumb-current" id="detail-breadcrumb-name">Webinar Nasional 2026</span>
        </div>

        <div class="detail-hero">
          <div class="detail-hero-tag"><i class="fa-solid fa-layer-group"></i> Event Utama</div>
          <h1 id="detail-hero-title">Webinar Nasional 2026</h1>
          <p id="detail-hero-desc">Webinar berskala nasional membahas tren kecerdasan buatan dan inovasi teknologi
            terkini.</p>
          <div class="detail-meta-grid">
            <span><i class="fa-regular fa-calendar"></i> <strong id="detail-hero-date">10 - 20 September
                2026</strong></span>
            <span><i class="fa-solid fa-circle-check"></i> Status: <span class="badge badge-blue"
                id="detail-hero-status">Active</span></span>
            <span><i class="fa-solid fa-users"></i> <span id="detail-hero-total-peserta">120</span> Total Peserta</span>
            <span><i class="fa-solid fa-sitemap"></i> <span id="detail-hero-sub-count">5</span> Sub Event</span>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <div>
              <span class="card-title">Daftar Sub Event</span>
              <div class="card-subtitle">Aktivitas di bawah Event Utama. Absensi dan QR code berada pada masing-masing
                Sub Event.</div>
            </div>

            <button class="btn btn-blue" id="btn-add-subevent" onclick="openAddSubEventModal(<?= (int)$_detailEventId ?>)" style="display:none;">
              <i class="fa-solid fa-plus"></i> Tambah Sub Event
            </button>
            <button class="btn btn-blue" id="btn-add-member-event" onclick="openAddEventMemberModal(<?= (int)$_detailEventId ?>)" style="display:none;">
              <i class="fa-solid fa-user-plus"></i> Tambah Peserta
            </button>
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>Nama Sub Event</th>
                  <th>Tanggal</th>
                  <th>Waktu</th>
                  <th>Lokasi</th>
                  <th>PIC / Admin</th>
                  <th>Peserta</th>
                  <th>Kehadiran</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody id="subevents-tbody">

              </tbody>
            </table>
          </div>
        </div>
      </div>

<div class="modal-overlay" id="modal-add-event-member">
  <div class="modal" onclick="event.stopPropagation()">
    <div class="modal-header">
      <h3>Tambah Peserta ke Event</h3>
      <button class="modal-close" onclick="closeModal('modal-add-event-member')">&times;</button>
    </div>
    <form method="POST" id="form-add-event-member">
      <?= csrf_field() ?>
      <div class="modal-body">
        <div class="field-group">
          <label>Pilih Anggota</label>
          <div class="custom-select-wrapper" id="wrap-add-member-user-id">
            <button type="button" class="custom-select-trigger" id="trigger-add-member-user-id" onclick="toggleCustomMemberSelect(event)">
              <span id="label-add-member-user-id">— Pilih anggota —</span>
              <i class="fa-solid fa-chevron-down"></i>
            </button>
            <div class="custom-select-options" id="options-add-member-user-id">
              <!-- diisi dinamis via JS -->
            </div>
            <input type="hidden" name="user_id" id="add-member-user-id" required />
          </div>
        </div>
        <div class="field-group">
          <label>Role di Event</label>
          <select name="role" id="add-member-role" onclick="event.stopPropagation()" onmousedown="event.stopPropagation()">
            <option value="Member">Member</option>
            <option value="Admin">Admin / PIC</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('modal-add-event-member')">Batal</button>
        <button type="submit" class="btn btn-blue"><i class="fa-solid fa-user-plus"></i> Tambah Peserta</button>
      </div>
    </form>
  </div>
</div>
