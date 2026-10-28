<div id="page-dashboard" class="page">
        <div class="summary-grid">
          <div class="summary-card">
            <div class="summary-icon icon-blue"><i class="fa-solid fa-calendar-days"></i></div>
            <div class="summary-info">
              <div class="label">Total Event Utama</div>
              <div class="value" id="stat-total-events">3</div>
            </div>
          </div>
          <div class="summary-card">
            <div class="summary-icon icon-purple"><i class="fa-solid fa-sitemap"></i></div>
            <div class="summary-info">
              <div class="label">Total Sub Event</div>
              <div class="value" id="stat-total-subevents">13</div>
            </div>
          </div>
          <div class="summary-card">
            <div class="summary-icon icon-green"><i class="fa-solid fa-users"></i></div>
            <div class="summary-info">
              <div class="label">Total Anggota</div>
              <div class="value">120</div>
            </div>
          </div>
          <div class="summary-card">
            <div class="summary-icon icon-amber"><i class="fa-solid fa-clipboard-check"></i></div>
            <div class="summary-info">
              <div class="label">Rata-rata Kehadiran</div>
              <div class="value">88.5%</div>
            </div>
          </div>
        </div>

        <div class="dashboard-grid">

          <div class="card">
            <div class="card-header">
              <div>
                <span class="card-title">Tren Kehadiran Sub Event (6 Bulan)</span>
                <div class="card-subtitle">Akumulasi kehadiran seluruh sesi sub-aktivitas</div>
              </div>
            </div>
            <div class="card-body">
              <div class="chart-wrap">
                <canvas id="chart-attendance"></canvas>
              </div>
            </div>
          </div>

          <div class="card">
            <div class="card-header">
              <div>
                <span class="card-title">Leaderboard Keaktifan Anggota</span>
                <div class="card-subtitle">Perhitungan point (+3 Hadir, -1 Absen per Sub Event)</div>
              </div>
              <a href="#" class="action-link" onclick="navTo('anggota');return false;">Lihat Semua</a>
            </div>
            <div class="card-body" id="dashboard-top-members">

            </div>
          </div>

          <div class="card grid-col-2">
            <div class="card-header">
              <div>
                <span class="card-title">Event Utama & Ringkasan Sub Event</span>
                <div class="card-subtitle">Struktur hierarki event yang dikelola Super Admin</div>
              </div>
              <button class="btn btn-blue btn-sm" id="btn-create-event-dash" onclick="openModal('modal-add-event')">
                <i class="fa-solid fa-plus"></i> Buat Event Utama
              </button>
            </div>
            <div class="table-wrap">
              <table>
                <thead>
                  <tr>
                    <th>Event Utama</th>
                    <th>Periode</th>
                    <th>Sub Event</th>
                    <th>Total Peserta</th>
                    <th>Status</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody id="dashboard-events-tbody">

                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
