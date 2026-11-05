<div class="modal-overlay" id="modal-add-subevent">
  <div class="modal">
    <div class="modal-header">
      <h3 id="modal-subevent-title">Tambah Sub Event</h3>
      <button class="modal-close" onclick="closeModal('modal-add-subevent')">&times;</button>
    </div>
    <div class="modal-body">
      <div class="field-group">
        <label>Event Utama (Parent Event)</label>
        <select id="new-sub-parent-select" class="select-disabled" disabled>

        </select>
      </div>
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
            <option value="Upcoming">Upcoming</option>
            <option value="Active">Active</option>
            <option value="Finished">Finished</option>
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
        <label>PIC / Admin Penanggung Jawab</label>
        <select id="new-sub-pic">
          <option value="Budi Santoso">Budi Santoso (Admin / PIC)</option>
          <option value="Andi Pratama">Andi Pratama</option>
          <option value="Rizky Ramadhan">Rizky Ramadhan</option>
          <option value="Rina Wijaya">Rina Wijaya</option>
          <option value="Hendra Kurnia">Hendra Kurnia</option>
        </select>
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
