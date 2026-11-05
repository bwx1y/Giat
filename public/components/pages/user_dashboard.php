<div id="page-user-dashboard" class="page">
  <div class="detail-hero hero-gradient">
    <h1 class="hero-title">Selamat datang, Andi Pratama! 👋</h1>
    <p class="hero-desc">Pantau kehadiran pada setiap Sub Event dan kumpulkan point keaktifan Anda.</p>
  </div>

  <div class="summary-grid">
    <div class="summary-card">
      <div class="summary-icon icon-blue"><i class="fa-solid fa-star"></i></div>
      <div class="summary-info">
        <div class="label">Total Point Saya</div>
        <div class="value text-blue" id="user-total-point">46 pt</div>
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
      <div class="summary-icon icon-red"><i class="fa-solid fa-circle-xmark"></i></div>
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

  <div class="card card-active-session">
    <div class="card-body card-body-between">
      <div>
        <div class="session-tag">
          <span class="live-dot"></span> Sesi Absensi Aktif Saat Ini
        </div>
        <h2 class="session-title" id="active-session-name">Rapat Persiapan 1</h2>
        <div class="session-meta">
          <span><i class="fa-solid fa-layer-group text-blue-500"></i> Webinar Nasional 2026</span>
          <span><i class="fa-regular fa-clock text-blue-500"></i> 19:00 - 21:00</span>
          <span><i class="fa-solid fa-location-dot text-blue-500"></i> Ruang Meeting A</span>
        </div>
      </div>
      <button class="btn btn-blue btn-scan-qr" onclick="openModal('modal-scanner')">
        <i class="fa-solid fa-qrcode"></i> Scan QR Sub Event (+3 Point)
      </button>
    </div>
  </div>

  <div class="section-header">
    <h2 class="section-title">Daftar Event & Progres Sub Event</h2>
    <a href="#" class="action-link" onclick="navTo('user-history');return false;">Lihat Riwayat Lengkap</a>
  </div>

  <div id="user-events-progress-container">

  </div>
</div>
