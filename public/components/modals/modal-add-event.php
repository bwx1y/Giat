<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<div class="modal-overlay" id="modal-add-event">
  <div class="modal">
    <div class="modal-header">
      <h3>Buat Event Utama Baru</h3>
      <button class="modal-close" onclick="closeModal('modal-add-event')">&times;</button>
    </div>
    <form method="POST" action="/admin/event/create/handler" target="event-create-result-frame">
      <?= csrf_field() ?>
      <div class="modal-body">
        <div class="field-group">
          <label>Nama Event Utama</label>
          <input type="text" name="title" id="new-event-name" placeholder="Contoh: Webinar Nasional 2026" required />
        </div>
        <div class="field-row">
          <div class="field-group">
            <label>Tanggal Mulai</label>
            <input type="text" name="start_date" id="new-event-start" class="datepicker" placeholder="Pilih tanggal mulai" required />
          </div>
          <div class="field-group">
            <label>Tanggal Selesai</label>
            <!-- Ubah type ke "text" agar kontrol tanggal dihandle oleh Flatpickr -->
            <input type="text" name="end_date" id="new-event-end" class="datepicker" placeholder="Pilih tanggal selesai" required />
          </div>
        </div>
        <div class="field-group">
          <label>Lokasi Event</label>
          <input type="text" name="location" id="new-event-loc" placeholder="Lokasi kegiatan (Online/Hybrid/Ruang)" />
        </div>
        <div class="field-group">
          <label>Deskripsi Event</label>
          <textarea name="description" id="new-event-desc" rows="3" placeholder="Keterangan singkat tentang event utama…"></textarea>
        </div>
        <div class="field-group">
          <label>Status Event</label>
          <select name="status" id="new-event-status">
            <option value="Not started">Not started</option>
            <option value="in progress">in progress</option>
            <option value="done">done</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('modal-add-event')">Batal</button>
        <button type="submit" class="btn btn-blue"><i class="fa-solid fa-plus"></i> Simpan Event Utama</button>
      </div>
    </form>
  </div>
</div>
<iframe name="event-create-result-frame" id="event-create-result-frame" title="Hasil buat event" hidden></iframe>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    flatpickr(".datepicker", {
      dateFormat: "Y-m-d",
      allowInput: true
    });
  });
</script>