<div class="modal-overlay" id="modal-add-event">
    <div class="modal">
      <div class="modal-header">
        <h3>Buat Event Utama Baru</h3>
        <button class="modal-close" onclick="closeModal('modal-add-event')">&times;</button>
      </div>
      <div class="modal-body">
        <div class="field-group">
          <label>Nama Event Utama</label>
          <input type="text" id="new-event-name" placeholder="Contoh: Webinar Nasional 2026" />
        </div>
        <div class="field-row">
          <div class="field-group">
            <label>Tanggal Mulai</label>
            <input type="date" id="new-event-start" />
          </div>
          <div class="field-group">
            <label>Tanggal Selesai</label>
            <input type="date" id="new-event-end" />
          </div>
        </div>
        <div class="field-group">
          <label>Deskripsi Event</label>
          <textarea id="new-event-desc" rows="3" placeholder="Keterangan singkat tentang event utama…"></textarea>
        </div>
        <div class="field-group">
          <label>Status Event</label>
          <select id="new-event-status">
            <option value="Active">Active</option>
            <option value="Upcoming">Upcoming</option>
            <option value="Finished">Finished</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline" onclick="closeModal('modal-add-event')">Batal</button>
        <button class="btn btn-blue" onclick="saveNewEvent()"><i class="fa-solid fa-plus"></i> Simpan Event
          Utama</button>
      </div>
    </div>
  </div>

  <div class="modal-overlay" id="modal-add-subevent">
    <div class="modal">
      <div class="modal-header">
        <h3 id="modal-subevent-title">Tambah Sub Event</h3>
        <button class="modal-close" onclick="closeModal('modal-add-subevent')">&times;</button>
      </div>
      <div class="modal-body">
        <div class="field-group">
          <label>Event Utama (Parent Event)</label>
          <select id="new-sub-parent-select" disabled style="background:var(--slate-100);font-weight:600;">

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
        <button class="btn btn-blue" onclick="saveNewSubEvent()"><i class="fa-solid fa-plus"></i> Simpan Sub
          Event</button>
      </div>
    </div>
  </div>

  <div class="modal-overlay" id="modal-qr">
    <div class="modal">
      <div class="modal-header">
        <h3>QR Code Absensi Sub Event</h3>
        <button class="modal-close" onclick="closeModal('modal-qr')">&times;</button>
      </div>
      <div class="modal-body">
        <div class="qr-container">
          <div id="qr-canvas"></div>
          <div>
            <h2 id="qr-modal-sub-name" style="font-size:18px;font-weight:800;color:var(--navy);">Rapat Persiapan 1</h2>
            <div style="font-size:13px;color:var(--slate-400);margin-top:2px;">
              Parent Event: <strong id="qr-modal-parent-name" style="color:var(--blue-600)">Webinar Nasional
                2026</strong>
            </div>
            <div style="display:flex;align-items:center;justify-content:center;gap:8px;margin-top:8px;">
              <span class="live-dot"></span>
              <span style="font-size:13px;font-weight:700;color:var(--green-500)">Absensi Aktif</span>
            </div>
            <div class="qr-timer-box" style="margin-top:10px;">
              Expired: <span id="qr-expire-time">21:00</span> &nbsp;|&nbsp; Sisa Waktu: <span
                id="qr-countdown">29:50</span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-danger" onclick="closeSessionFromModal()">
          <i class="fa-solid fa-lock"></i> Tutup Sesi Absensi
        </button>
        <button class="btn btn-outline" onclick="closeModal('modal-qr')">Selesai</button>
      </div>
    </div>
  </div>

  <div class="modal-overlay" id="modal-scanner">
    <div class="modal">
      <div class="modal-header">
        <h3>Scan QR Absensi Sub Event</h3>
        <button class="modal-close" onclick="closeModal('modal-scanner')">&times;</button>
      </div>
      <div class="modal-body" id="scanner-body">
        <div class="scanner-frame">
          <div class="scanner-line"></div>
          <i class="fa-solid fa-camera"></i>
          <p>Arahkan kamera ke QR Code Sub Event</p>
        </div>
        <p style="text-align:center;font-size:12px;color:var(--slate-400);margin-top:12px">
          Absensi akan otomatis tercatat untuk Sub Event yang sedang berlangsung
        </p>
      </div>
      <div class="modal-footer" id="scanner-footer">
        <button class="btn btn-outline" onclick="closeModal('modal-scanner')">Batal</button>
        <button class="btn btn-blue" onclick="simulateScanSuccess()">
          <i class="fa-solid fa-bolt"></i> SimulaGIATn QR Berhasil (+3 Point)
        </button>
      </div>
    </div>
  </div>

  <div class="modal-overlay" id="modal-add-member">
    <div class="modal">
      <div class="modal-header">
        <h3>Tambah Anggota Baru</h3>
        <button class="modal-close" onclick="closeModal('modal-add-member')">&times;</button>
      </div>
      <div class="modal-body">
        <div class="field-group"><label>Nama Lengkap</label><input id="new-name" type="text"
            placeholder="Nama anggota" /></div>
        <div class="field-row">
          <div class="field-group"><label>Email</label><input id="new-email" type="email" placeholder="email@org.id" />
          </div>
          <div class="field-group"><label>NIM / ID Anggota</label><input id="new-id" type="text" placeholder="A009" />
          </div>
        </div>
        <div class="field-group"><label>Divisi</label>
          <select id="new-div">
            <option>Divisi Teknologi</option>
            <option>Divisi Kreatif</option>
            <option>Divisi Humas</option>
            <option>Divisi Keuangan</option>
            <option>Divisi Akademik</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline" onclick="closeModal('modal-add-member')">Batal</button>
        <button class="btn btn-blue" onclick="addMember()"><i class="fa-solid fa-plus"></i> Tambah Anggota</button>
      </div>
    </div>
  </div>
