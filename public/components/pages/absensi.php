<?php
$_GET['page'] = $_GET['page'] ?? 1;
$_GET['size'] = $_GET['size'] ?? 1000;
require __DIR__ . '/../../admin/absensi/index.php';
?>
<div id="page-absensi" class="page">
        <div class="card">
          <div class="card-header">
            <div>
              <span class="card-title" id="absensi-page-title">Rekap Seluruh Absensi Sub Event</span>
              <div class="card-subtitle">Semua riwayat absensi tercatat berdasarkan sesi Sub Event terkait</div>
            </div>
          </div>
          <div class="toolbar">
            <div class="search-wrap">
              <i class="fa-solid fa-magnifying-glass"></i>
              <input type="text" id="absensi-search" placeholder="Cari anggota, event, atau sub event…"
                oninput="filterAbsensiList()" />
            </div>
            <select class="filter-select" id="absensi-filter-event" onchange="filterAbsensiList()">
              <option value="">Semua Event Utama</option>
              <?php foreach ($eventList as $event): ?>
                <option value="<?= htmlspecialchars((string) $event['id'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($event['title'], ENT_QUOTES, 'UTF-8') ?></option>
              <?php endforeach; ?>
            </select>
            <select class="filter-select" id="absensi-filter-status" onchange="filterAbsensiList()">
              <option value="">Semua Status</option>
              <option value="Hadir">Hadir (+3)</option>
              <option value="Tidak Hadir">Tidak Hadir (-1)</option>
            </select>
          </div>
                    <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>Nama Anggota</th>
                  <th>Event Utama</th>
                  <th>Sub Event</th>
                  <th>Tanggal</th>
                  <th>Status</th>
                  <th>Metode</th>
                  <th>Point</th>
                  <th>Aksi Koreksi</th>
                </tr>
              </thead>
              <tbody id="absensi-all-tbody">

              </tbody>
            </table>
          </div>
        </div>
      </div>
<script>
  window.dbAttendance = <?= json_encode($result) ?>;
</script>
