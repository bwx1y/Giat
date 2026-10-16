let chartInstances = {};

function renderDashboardStats() {
  const events = getEvents();
  const members = getMembers();

  let totalSubs = 0;
  events.forEach(ev => totalSubs += (ev.subEvents ? ev.subEvents.length : 0));

  const statEvents = document.getElementById('stat-total-events');
  const statSubs = document.getElementById('stat-total-subevents');
  if (statEvents) statEvents.textContent = events.length;
  if (statSubs) statSubs.textContent = totalSubs;

  const topContainer = document.getElementById('dashboard-top-members');
  if (topContainer) {
    topContainer.innerHTML = '';
    const sorted = [...members].sort((a, b) => b.point - a.point).slice(0, 5);
    sorted.forEach((m, idx) => {
      const row = document.createElement('div');
      row.style.cssText = 'display:flex;align-items:center;gap:12px;padding:9px 0;border-bottom:1px solid var(--slate-100);';
      row.innerHTML = `
        <div style="width:24px;height:24px;border-radius:50%;background:${idx === 0 ? '#fef3c7' : idx === 1 ? '#e2e8f0' : '#f1f5f9'};color:${idx === 0 ? '#d97706' : 'var(--navy)'};font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;">${idx + 1}</div>
        <div style="flex:1;">
          <div style="font-weight:600;font-size:13.5px;color:var(--navy);">${m.name}</div>
          <div style="font-size:11px;color:var(--slate-400);">${m.div} · Hadir: ${m.hadir} sesi</div>
        </div>
        <div style="font-size:14px;font-weight:800;color:var(--blue-600);">${m.point} pt</div>
      `;
      topContainer.appendChild(row);
    });
  }

  const tbody = document.getElementById('dashboard-events-tbody');
  if (tbody) {
    tbody.innerHTML = '';
    events.forEach(ev => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td><strong>${ev.name}</strong></td>
        <td>${ev.dateRange}</td>
        <td><span class="subevent-tree-badge"><i class="fa-solid fa-sitemap"></i> ${ev.subEvents ? ev.subEvents.length : 0} Sub Event</span></td>
        <td>${ev.totalPeserta} Peserta</td>
        <td><span class="badge ${ev.status === 'Active' ? 'badge-blue' : ev.status === 'Upcoming' ? 'badge-amber' : 'badge-gray'}">${ev.status}</span></td>
        <td>
          <a class="action-link" href="event-detail.html?id=${ev.id}">Lihat Detail</a>
        </td>
      `;
      tbody.appendChild(tr);
    });
  }
}

function initDashboardChart() {
  if (chartInstances['attendance']) return;
  const ctx = document.getElementById('chart-attendance');
  if (!ctx || typeof Chart === 'undefined') return;
  chartInstances['attendance'] = new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep'],
      datasets: [{
        label: 'Kehadiran Sub Event (%)',
        data: [80, 84, 79, 88, 86, 92],
        borderColor: '#2563eb',
        backgroundColor: 'rgba(37,99,235,.08)',
        borderWidth: 2.5,
        tension: .35,
        fill: true,
        pointBackgroundColor: '#2563eb',
        pointRadius: 4,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        y: { min: 60, max: 100, grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 } } },
        x: { grid: { display: false }, ticks: { font: { size: 11 } } }
      }
    }
  });
}

function renderAdminDashboard() {
  const container = document.getElementById('admin-managed-container');
  if (!container) return;
  container.innerHTML = '';

  const events = getEvents();
  const currentRole = getCurrentRole();
  let hasAny = false;

  events.forEach(ev => {
    const assignedSubs = (ev.subEvents || []).filter(s => s.pic.toLowerCase().includes('budi') || currentRole === 'superadmin');

    if (assignedSubs.length > 0) {
      hasAny = true;
      const card = document.createElement('div');
      card.className = 'managed-tree-card';

      let subItemsHtml = '';
      assignedSubs.forEach(s => {
        subItemsHtml += `
          <div class="managed-tree-item">
            <div>
              <div class="managed-sub-name">${s.name}</div>
              <div class="managed-sub-meta">
                <span><i class="fa-regular fa-calendar"></i> ${s.date}</span>
                <span><i class="fa-regular fa-clock"></i> ${s.time}</span>
                <span><i class="fa-solid fa-location-dot"></i> ${s.loc}</span>
                <span><i class="fa-solid fa-user-check"></i> ${s.hadir} / ${s.total} Hadir</span>
                <span class="badge ${s.status === 'Finished' ? 'badge-gray' : s.status === 'Active' ? 'badge-green' : 'badge-amber'}">${s.status}</span>
              </div>
            </div>
            <div style="display:flex;gap:8px;">
              <a class="btn btn-blue btn-sm" href="subevent-detail.html?eventId=${ev.id}&subId=${s.id}">
                <i class="fa-solid fa-qrcode"></i> Buka Sub Event & QR
              </a>
            </div>
          </div>
        `;
      });

      card.innerHTML = `
        <div class="managed-tree-parent" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
          <div>
            <div class="managed-parent-title">
              <i class="fa-solid fa-layer-group" style="color:var(--blue-600)"></i>
              <span>${ev.name}</span>
              <span class="badge badge-blue" style="font-size:11px;">${ev.status}</span>
            </div>
            <div style="font-size:12.5px;color:var(--slate-400);margin-top:2px;">
              ${assignedSubs.length} Sub Event Ditugaskan kepada Anda
            </div>
          </div>
          <div style="display:flex;gap:8px;">
            <button class="btn btn-outline btn-sm" onclick="openAddSubEventModal(${ev.id})">
              <i class="fa-solid fa-plus"></i> Tambah Sub Event
            </button>
            <a class="btn btn-outline btn-sm" href="event-detail.html?id=${ev.id}">
              <i class="fa-solid fa-eye"></i> Lihat Detail
            </a>
          </div>
        </div>
        <div class="managed-tree-sublist">
          ${subItemsHtml}
        </div>
      `;
      container.appendChild(card);
    }
  });

  if (!hasAny) {
    container.innerHTML = `<div class="empty-state"><i class="fa-solid fa-calendar-check"></i><p>Belum ada Event atau Sub Event yang ditugaskan kepada Anda. Klik tombol <strong>"+ Tambah Sub Event"</strong> di atas untuk membuat Sub Event baru.</p></div>`;
  }
}

function renderUserDashboard() {
  const container = document.getElementById('user-events-progress-container');
  if (!container) return;
  container.innerHTML = '';

  const events = getEvents();
  const history = getAttendanceHistory();

  events.forEach(ev => {
    let completedCount = 0;
    let eventPoints = 0;

    let rowsHtml = '';
    (ev.subEvents || []).forEach(sub => {
      let statusIcon = '<i class="fa-regular fa-circle"></i>';
      let statusCls = 'status-upcoming';
      let pointText = '<span style="color:var(--slate-400)">Upcoming</span>';

      if (sub.status === 'Finished') {
        completedCount++;
        const found = history.find(a => a.event === ev.name && a.subEvent === sub.name && a.member === 'Andi Pratama');
        if (found && found.status === 'Hadir') {
          statusIcon = '<i class="fa-solid fa-check"></i>';
          statusCls = 'status-hadir';
          pointText = '<strong class="point-positive">+3 Point</strong>';
          eventPoints += 3;
        } else {
          statusIcon = '<i class="fa-solid fa-xmark"></i>';
          statusCls = 'status-absen';
          pointText = '<strong class="point-negative">-1 Point</strong>';
          eventPoints -= 1;
        }
      }

      rowsHtml += `
        <div class="user-subevent-row ${statusCls}">
          <div style="display:flex;align-items:center;">
            <span class="subevent-status-icon">${statusIcon}</span>
            <div>
              <strong>${sub.name}</strong>
              <span style="font-size:12px;color:var(--slate-400);margin-left:8px;">· ${sub.date}, ${sub.time}</span>
            </div>
          </div>
          <div>${pointText}</div>
        </div>
      `;
    });

    const block = document.createElement('div');
    block.className = 'user-event-block';
    block.innerHTML = `
      <div class="user-event-head">
        <div>
          <div class="user-event-title">${ev.name}</div>
          <div class="user-event-dates"><i class="fa-regular fa-calendar"></i> ${ev.dateRange}</div>
        </div>
        <div style="text-align:right;">
          <div style="font-size:12px;color:var(--slate-400);">Progress Selesai</div>
          <div style="font-weight:700;font-size:14px;color:var(--navy);">${completedCount} / ${(ev.subEvents || []).length} Sub Event Selesai</div>
          <div style="font-size:12px;margin-top:2px;">
            Total Point Event: <strong style="color:var(--blue-600);font-size:13px;">${eventPoints >= 0 ? '+' + eventPoints : eventPoints} Point</strong>
          </div>
        </div>
      </div>
      <div style="font-size:12.5px;font-weight:600;color:var(--navy-600);text-transform:uppercase;letter-spacing:.04em;">Daftar Sub Event:</div>
      <div class="user-subevent-list">
        ${rowsHtml}
      </div>
    `;
    container.appendChild(block);
  });
}

function simulateScanSuccess() {
  const body = document.getElementById('scanner-body');
  const footer = document.getElementById('scanner-footer');
  if (body) {
    body.innerHTML = `
      <div style="text-align:center;padding:16px 0;">
        <div style="width:68px;height:68px;border-radius:50%;background:var(--green-100);color:var(--green-500);font-size:32px;display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px;">
          <i class="fa-solid fa-check"></i>
        </div>
        <h3 style="font-size:19px;font-weight:800;color:var(--navy);">Absensi Berhasil!</h3>
        <p style="color:var(--slate-400);font-size:13px;margin:4px 0 10px;">Sub Event: Rapat Persiapan 1 (Webinar Nasional 2026)</p>
        <div style="font-size:26px;font-weight:800;color:var(--green-500);margin-bottom:6px;">+3 Point Didapat</div>
        <p style="color:var(--navy-600);font-size:12px;">Kehadiran Anda telah terverifikasi secara valid.</p>
      </div>
    `;
  }
  if (footer) {
    footer.innerHTML = `
      <button class="btn btn-blue" onclick="closeModal('modal-scanner')">Selesai</button>
    `;
  }

  const userPt = document.getElementById('user-total-point');
  if (userPt) userPt.textContent = '49 pt';

  const history = getAttendanceHistory();
  history.unshift({
    id: Date.now(),
    member: 'Andi Pratama',
    event: 'Webinar Nasional 2026',
    subEvent: 'Rapat Persiapan 1',
    date: '12 Sep 2026',
    status: 'Hadir',
    method: 'QR',
    point: 3
  });
  saveAttendanceHistory(history);

  showToast('Absensi Sub Event Berhasil! +3 Point ditambahkan 🎉', 'success');
}
