<div id="page-subevent-detail" class="page">
  <div class="breadcrumb-wrap">
    <a class="breadcrumb-item" onclick="navTo(currentRole === 'admin' ? 'admin-dashboard' : 'event')">
      <i class="fa-solid fa-calendar-days"></i> <span id="sub-bc-root">Event</span>
    </a>
    <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-right"></i></span>
    <a class="breadcrumb-item" id="sub-bc-parent" onclick="backToParentEvent()">Webinar Nasional 2026</a>
    <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="breadcrumb-current" id="sub-bc-current">Rapat Persiapan 1</span>
  </div>

  <div class="card card-accent-blue">
    <div class="card-body card-body-padded">
      <div class="subevent-hero-flex">
        <div>
          <div class="subevent-tree-badge">
            <i class="fa-solid fa-sitemap"></i> Sub Event dari: <strong id="sub-info-parent" class="text-navy ms-1">Webinar Nasional 2026</strong>
          </div>
          <h1 id="sub-info-title" class="subevent-title">Rapat Persiapan 1</h1>
          <div class="subevent-meta">
            <span><i class="fa-regular fa-calendar text-blue-500"></i> <strong id="sub-info-date">12 September 2026</strong></span>
            <span><i class="fa-regular fa-clock text-blue-500"></i> <span id="sub-info-time">19:00 - 21:00</span></span>
            <span><i class="fa-solid fa-location-dot text-blue-500"></i> <span id="sub-info-loc">Ruang Meeting A</span></span>
            <span><i class="fa-solid fa-user-tie text-blue-500"></i> PIC: <strong id="sub-info-pic">Budi Santoso</strong></span>
            <span>Status: <span class="badge badge-green" id="sub-info-status">Finished</span></span>
          </div>
        </div>
        <div>
          <button class="btn btn-blue btn-qr-gen" onclick="openQrForCurrentSubEvent()">
            <i class="fa-solid fa-qrcode"></i> Generate QR Absensi
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="summary-grid">
    <div class="summary-card">
      <div class="summary-icon icon-blue"><i class="fa-solid fa-users"></i></div>
      <div class="summary-info">
        <div class="label">Total Peserta</div>
        <div class="value" id="sub-stat-total">40</div>
      </div>
    </div>
    <div class="summary-card">
      <div class="summary-icon icon-green"><i class="fa-solid fa-user-check"></i></div>
      <div class="summary-info">
        <div class="label">Hadir</div>
        <div class="value text-green" id="sub-stat-hadir">38</div>
      </div>
    </div>
    <div class="summary-card">
      <div class="summary-icon icon-red"><i class="fa-solid fa-user-xmark"></i></div>
      <div class="summary-info">
        <div class="label">Tidak Hadir</div>
        <div class="value text-red" id="sub-stat-absen">2</div>
      </div>
    </div>
    <div class="summary-card">
      <div class="summary-icon icon-purple"><i class="fa-solid fa-chart-pie"></i></div>
      <div class="summary-info">
        <div class="label">Persentase Kehadiran</div>
        <div class="value" id="sub-stat-pct">95%</div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <div>
        <span class="card-title">Daftar Kehadiran Peserta Sub Event</span>
        <div class="card-subtitle">Absensi real-time melalui scan QR code atau manual oleh PIC</div>
      </div>
      <div class="search-wrap search-wrap-sm">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" placeholder="Cari peserta…" oninput="filterSubParticipants(this.value)" />
      </div>
    </div>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Nama Peserta</th>
            <th>Divisi</th>
            <th>Status Kehadiran</th>
            <th>Waktu Absen</th>
            <th>Metode</th>
            <th>Point Didapat</th>
            <th>Aksi / Koreksi</th>
          </tr>
        </thead>
        <tbody id="sub-participants-tbody">

        </tbody>
      </table>
    </div>
  </div>
</div>
