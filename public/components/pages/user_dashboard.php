<div id="page-user-dashboard" class="page">
        <div class="detail-hero"
          style="background: linear-gradient(135deg, var(--blue-800), var(--blue-600)); margin-bottom: 22px;">
          <h1 style="font-size:22px;font-weight:800;">Selamat datang, Andi Pratama! 👋</h1>
          <p style="opacity:.9;margin-bottom:0;">Pantau kehadiran pada setiap Sub Event dan kumpulkan point keaktifan
            Anda.</p>
        </div>

        <div class="summary-grid">
          <div class="summary-card">
            <div class="summary-icon icon-blue"><i class="fa-solid fa-star"></i></div>
            <div class="summary-info">
              <div class="label">Total Point Saya</div>
              <div class="value" id="user-total-point" style="color:var(--blue-600)">46 pt</div>
            </div>
          </div>
          <div class="summary-card">
            <div class="summary-icon icon-green"><i class="fa-solid fa-circle-check"></i></div>
            <div class="summary-info">
              <div class="label">Sub Event Hadir</div>
              <div class="value" id="user-total-hadir">16</div>
            </div>
          </div>
          <div class="summary-card">
            <div class="summary-icon icon-amber" style="background:var(--red-100);color:var(--red-500)"><i
                class="fa-solid fa-circle-xmark"></i></div>
            <div class="summary-info">
              <div class="label">Tidak Hadir</div>
              <div class="value" id="user-total-absen">2</div>
            </div>
          </div>
          <div class="summary-card">
            <div class="summary-icon icon-purple"><i class="fa-solid fa-percent"></i></div>
            <div class="summary-info">
              <div class="label">Tingkat Kehadiran</div>
              <div class="value">88.9%</div>
            </div>
          </div>
        </div>

        <div class="card"
          style="border: 2px solid var(--blue-300); background: linear-gradient(to right, #eff6ff, #ffffff); margin-bottom: 24px;">
          <div class="card-body"
            style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
            <div>
              <div
                style="font-size:11px;font-weight:700;color:var(--blue-600);text-transform:uppercase;letter-spacing:.06em;display:flex;align-items:center;gap:6px;">
                <span class="live-dot"></span> Sesi Absensi Aktif Saat Ini
              </div>
              <h2 style="font-size:18px;font-weight:800;color:var(--navy);margin:6px 0 3px;" id="active-session-name">
                Rapat Persiapan 1</h2>
              <div
                style="font-size:13px;color:var(--slate-400);display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
                <span><i class="fa-solid fa-layer-group" style="color:var(--blue-500)"></i> Webinar Nasional 2026</span>
                <span><i class="fa-regular fa-clock" style="color:var(--blue-500)"></i> 19:00 - 21:00</span>
                <span><i class="fa-solid fa-location-dot" style="color:var(--blue-500)"></i> Ruang Meeting A</span>
              </div>
            </div>
            <button class="btn btn-blue" style="padding:12px 20px;" onclick="openModal('modal-scanner')">
              <i class="fa-solid fa-qrcode"></i> Scan QR Sub Event (+3 Point)
            </button>
          </div>
        </div>

        <div style="margin-bottom: 16px; display:flex; justify-content:space-between; align-items:center;">
          <h2 style="font-size:16px;font-weight:700;color:var(--navy);">Daftar Event & Progres Sub Event</h2>
          <a href="#" class="action-link" onclick="navTo('user-history');return false;">Lihat Riwayat Lengkap</a>
        </div>

        <div id="user-events-progress-container">

        </div>
      </div>
