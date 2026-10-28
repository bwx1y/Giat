<div id="page-event-detail" class="page">

        <div class="breadcrumb-wrap">
          <a class="breadcrumb-item" onclick="navTo('event')"><i class="fa-solid fa-calendar-days"></i> Event</a>
          <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-right"></i></span>
          <span class="breadcrumb-current" id="detail-breadcrumb-name">Webinar Nasional 2026</span>
        </div>

        <div class="detail-hero">
          <div class="detail-hero-tag"><i class="fa-solid fa-layer-group"></i> Event Utama</div>
          <h1 id="detail-hero-title">Webinar Nasional 2026</h1>
          <p id="detail-hero-desc">Webinar berskala nasional membahas tren kecerdasan buatan dan inovasi teknologi
            terkini.</p>
          <div class="detail-meta-grid">
            <span><i class="fa-regular fa-calendar"></i> <strong id="detail-hero-date">10 - 20 September
                2026</strong></span>
            <span><i class="fa-solid fa-circle-check"></i> Status: <span class="badge badge-blue"
                id="detail-hero-status">Active</span></span>
            <span><i class="fa-solid fa-users"></i> <span id="detail-hero-total-peserta">120</span> Total Peserta</span>
            <span><i class="fa-solid fa-sitemap"></i> <span id="detail-hero-sub-count">5</span> Sub Event</span>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <div>
              <span class="card-title">Daftar Sub Event</span>
              <div class="card-subtitle">Aktivitas di bawah Event Utama. Absensi dan QR code berada pada masing-masing
                Sub Event.</div>
            </div>

            <button class="btn btn-blue" id="btn-add-subevent" onclick="openAddSubEventModal()">
              <i class="fa-solid fa-plus"></i> Tambah Sub Event
            </button>
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>Nama Sub Event</th>
                  <th>Tanggal</th>
                  <th>Waktu</th>
                  <th>Lokasi</th>
                  <th>PIC / Admin</th>
                  <th>Peserta</th>
                  <th>Kehadiran</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody id="subevents-tbody">

              </tbody>
            </table>
          </div>
        </div>
      </div>
