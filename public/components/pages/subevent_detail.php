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

        <div class="card" style="border-left: 4px solid var(--blue-600);">
          <div class="card-body" style="padding: 24px;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:14px;">
              <div>
                <div class="subevent-tree-badge">
                  <i class="fa-solid fa-sitemap"></i> Sub Event dari: <strong id="sub-info-parent"
                    style="color:var(--navy);margin-left:4px;">Webinar Nasional 2026</strong>
                </div>
                <h1 id="sub-info-title" style="font-size:22px;font-weight:800;color:var(--navy);margin:10px 0 6px;">
                  Rapat Persiapan 1</h1>
                <div style="display:flex;flex-wrap:wrap;gap:18px;font-size:13px;color:var(--navy-600);margin-top:8px;">
                  <span><i class="fa-regular fa-calendar" style="color:var(--blue-500)"></i> <strong
                      id="sub-info-date">12 September 2026</strong></span>
                  <span><i class="fa-regular fa-clock" style="color:var(--blue-500)"></i> <span id="sub-info-time">19:00
                      - 21:00</span></span>
                  <span><i class="fa-solid fa-location-dot" style="color:var(--blue-500)"></i> <span
                      id="sub-info-loc">Ruang Meeting A</span></span>
                  <span><i class="fa-solid fa-user-tie" style="color:var(--blue-500)"></i> PIC: <strong
                      id="sub-info-pic">Budi Santoso</strong></span>
                  <span>Status: <span class="badge badge-green" id="sub-info-status">Finished</span></span>
                </div>
              </div>
              <div>

                <button class="btn btn-blue" style="padding:10px 18px;" onclick="openQrForCurrentSubEvent()">
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
              <div class="value" id="sub-stat-hadir" style="color:var(--green-500)">38</div>
            </div>
          </div>
          <div class="summary-card">
            <div class="summary-icon icon-amber" style="background:var(--red-100);color:var(--red-500)"><i
                class="fa-solid fa-user-xmark"></i></div>
            <div class="summary-info">
              <div class="label">Tidak Hadir</div>
              <div class="value" id="sub-stat-absen" style="color:var(--red-500)">2</div>
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
            <div class="search-wrap" style="max-width:240px">
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
