<div class="modal-overlay" id="modal-add-subevent">
  <div class="modal">
    <div class="modal-header">
      <h3 id="modal-subevent-title">Tambah Sub Event</h3>
      <button class="modal-close" onclick="closeModal('modal-add-subevent')">&times;</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="new-sub-parent-select" />
      <div class="field-group">
        <label>Nama Sub Event</label>
        <input type="text" id="new-sub-name" placeholder="Contoh: Rapat Persiapan 1, Technical Meeting, dll" />
      </div>
      <div class="field-row">
        <div class="field-group">
          <label>Tanggal</label>
          <input type="date" id="new-sub-date" />
        </div>
        <div class="field-group">
          <label>Status</label>
          <select id="new-sub-status">
            <option value="Not started">Not started</option>
            <option value="in progress">in progress</option>
            <option value="done">done</option>
          </select>
        </div>
      </div>
      <div class="field-row">
        <div class="field-group">
          <label>Jam Mulai</label>
          <input type="time" id="new-sub-time-start" value="19:00" />
        </div>
        <div class="field-group">
          <label>Jam Selesai</label>
          <input type="time" id="new-sub-time-end" value="21:00" />
        </div>
      </div>
      <div class="field-group">
        <label>Lokasi / Ruangan</label>
        <input type="text" id="new-sub-loc" placeholder="Ruang Meeting A / Zoom Link" />
      </div>
      <div class="field-group">
        <label>Deskripsi Sub Event</label>
        <textarea id="new-sub-desc" rows="2" placeholder="Agenda atau tujuan sub event…"></textarea>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal('modal-add-subevent')">Batal</button>
      <button class="btn btn-blue" onclick="saveNewSubEvent()"><i class="fa-solid fa-plus"></i> Simpan Sub Event</button>
    </div>
  </div>
</div>
