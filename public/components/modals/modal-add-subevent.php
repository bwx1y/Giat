<div class="modal-overlay" id="modal-add-subevent">
  <div class="modal">
    <div class="modal-header">
      <h3 id="modal-subevent-title">Tambah Sub Event</h3>
      <button class="modal-close" onclick="closeModal('modal-add-subevent')">&times;</button>
    </div>
    <form method="POST" id="form-add-subevent" onsubmit="return saveNewSubEvent(event)">
      <?= csrf_field() ?>
      <input type="hidden" name="meeting_action" value="create" />
      <div class="modal-body">
        <input type="hidden" id="new-sub-parent-select" />
        <div class="field-group">
          <label>Nama Sub Event</label>
          <input type="text" name="title" id="new-sub-name" maxlength="255" placeholder="Contoh: Rapat Persiapan 1, Technical Meeting, dll" required />
        </div>
        <div class="field-row">
          <div class="field-group">
            <label>Mulai</label>
            <input type="datetime-local" name="start_date" id="new-sub-start" required />
          </div>
          <div class="field-group">
            <label>Selesai</label>
            <input type="datetime-local" name="end_date" id="new-sub-end" required />
          </div>
        </div>
        <div class="field-group">
          <label>Lokasi / Ruangan</label>
          <input type="text" name="location" id="new-sub-loc" maxlength="255" placeholder="Ruang Meeting A / Zoom Link" />
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('modal-add-subevent')">Batal</button>
        <button type="submit" class="btn btn-blue"><i class="fa-solid fa-plus"></i> Simpan Sub Event</button>
      </div>
    </form>
  </div>
</div>
