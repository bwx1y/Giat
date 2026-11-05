<?php
$eventSearch = $_GET['search'] ?? '';
$eventStatus = $_GET['status'] ?? '';

$eventFilters = [];
if (!empty($eventSearch)) {
    $eventFilters[] = [
        'key' => 'search',
        'query' => 'title ILIKE :search',
        'value' => "%$eventSearch%",
    ];
}
if (!empty($eventStatus)) {
    $eventFilters[] = [
        'key' => 'status',
        'query' => 'status = :status',
        'value' => $eventStatus,
    ];
}

$eventsDataList = [];
try {
    $eventStmt = queryBuilder(
        table: 'vw_event',
      filters: $eventFilters,
        size: 50,
        page: 1,
    );
    if ($eventStmt) {
        $eventsDataList = $eventStmt->fetchAll(PDO::FETCH_ASSOC);

        // echo '<pre>';
        // var_dump($eventsDataList);
        // echo '</pre>';
        // exit;
    }
} catch (\Throwable $e) {
    $eventsDataList = [];
}
?>
<div id="page-event" class="page">
  <div class="card">
    <div class="card-header">
      <div>
        <span class="card-title">Daftar Event Utama</span>
        <div class="card-subtitle">Setiap Event Utama memayungi beberapa Sub Event aktivitas</div>
      </div>

      <button class="btn btn-blue" id="btn-create-event-main" onclick="openModal('modal-add-event')">
        <i class="fa-solid fa-plus"></i> Buat Event
      </button>
    </div>
    <form method="GET" action="" class="toolbar">
      <input type="hidden" name="page_view" value="event" />
      <div class="search-wrap search-wrap-sm">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" name="search" id="search-event" placeholder="Cari Event Utama…" value="<?= htmlspecialchars($eventSearch) ?>" />
      </div>
      <select name="status" class="filter-select" id="filter-event-status" onchange="this.form.submit()">
        <option value="">Semua Status</option>
        <option value="Not started" <?= $eventStatus === 'Not started' ? 'selected' : '' ?>>Not started</option>
        <option value="in progress" <?= $eventStatus === 'in progress' ? 'selected' : '' ?>>in progress</option>
        <option value="done" <?= $eventStatus === 'done' ? 'selected' : '' ?>>done</option>
      </select>
      <button type="submit" class="btn btn-blue btn-sm">Cari</button>
      <?php if (!empty($eventSearch) || !empty($eventStatus)): ?>
        <a href="/?page_view=event" class="btn btn-outline btn-sm">Reset</a>
      <?php endif; ?>
    </form>
    <div class="event-primary-grid" id="event-grid">

    </div>
    <div id="event-pagination" class="d-flex justify-content-between align-items-center flex-wrap gap-2 px-3 pb-3"></div>
  </div>

  <div class="modal-overlay" id="modal-edit-event">
    <div class="modal">
      <div class="modal-header">
        <h3>Edit Event Utama</h3>
        <button type="button" class="modal-close" onclick="closeModal('modal-edit-event')">&times;</button>
      </div>
      <form id="form-edit-event" method="POST" target="event-edit-result-frame">
        <?= csrf_field() ?>
        <div class="modal-body">
          <div class="field-group">
            <label for="edit-event-title">Nama Event Utama</label>
            <input type="text" name="title" id="edit-event-title" required />
          </div>
          <div class="field-row">
            <div class="field-group">
              <label for="edit-event-start">Tanggal Mulai</label>
              <input type="text" name="start_date" id="edit-event-start" class="datepicker" required />
            </div>
            <div class="field-group">
              <label for="edit-event-end">Tanggal Selesai</label>
              <input type="text" name="end_date" id="edit-event-end" class="datepicker" required />
            </div>
          </div>
          <div class="field-group">
            <label for="edit-event-location">Lokasi Event</label>
            <input type="text" name="location" id="edit-event-location" />
          </div>
          <div class="field-group">
            <label for="edit-event-description">Deskripsi Event</label>
            <textarea name="description" id="edit-event-description" rows="3"></textarea>
          </div>
          <div class="field-group">
            <label for="edit-event-status">Status Event</label>
            <select name="status" id="edit-event-status">
              <option value="Not started">Not started</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline" onclick="closeModal('modal-edit-event')">Batal</button>
          <button type="submit" class="btn btn-blue">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
  <iframe name="event-edit-result-frame" id="event-edit-result-frame" title="Hasil edit event" hidden></iframe>

  <div class="modal-overlay" id="modal-delete-event">
    <div class="modal">
      <div class="modal-header">
        <h3>Hapus Event Utama</h3>
        <button type="button" class="modal-close" onclick="closeModal('modal-delete-event')">&times;</button>
      </div>
      <div class="modal-body">
        <p>Yakin ingin menghapus event <strong id="delete-event-title"></strong>? Sub event terkait juga dapat terhapus.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('modal-delete-event')">Batal</button>
        <button type="button" class="btn btn-blue" id="confirm-delete-event" onclick="confirmDeleteEvent()">Ya, Hapus</button>
      </div>
    </div>
  </div>
</div>
<script>
  window.dbEvents = <?= json_encode($eventsDataList) ?>;
</script>
