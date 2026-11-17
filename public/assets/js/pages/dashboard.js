// ============================================================
// pages/dashboard.js — Dashboard Super Admin
// ============================================================

function renderDashboardStats() {
  const db = window.dbDashboard;

  if (db && db.stats) {
    document.getElementById('stat-total-events').textContent = db.stats.totalEvents;
    document.getElementById('stat-total-subevents').textContent = db.stats.totalSubEvents;
    const tm = document.getElementById('stat-total-members');
    if (tm) tm.textContent = db.stats.totalMembers;
    const ta = document.getElementById('stat-avg-attendance');
    if (ta) ta.textContent = db.stats.avgAttendance + '%';
  } else {
    let totalSubs = 0;
    eventsData.forEach(ev => totalSubs += ev.subEvents.length);
    document.getElementById('stat-total-events').textContent = eventsData.length;
    document.getElementById('stat-total-subevents').textContent = totalSubs;
  }

  _renderLeaderboard(db);
  _renderDashboardEventsTable(db);
}

function _renderLeaderboard(db) {
  const topContainer = document.getElementById('dashboard-top-members');
  topContainer.innerHTML = '';

  if (db && db.leaderboard && db.leaderboard.length > 0) {
    db.leaderboard.forEach((m, idx) => {
      topContainer.appendChild(_leaderboardRow(idx, m.member_name, `Hadir: ${m.hadir} sesi · Absen: ${m.absen} sesi`, m.point));
    });
  } else {
    const sorted = [...members].sort((a, b) => b.point - a.point).slice(0, 5);
    sorted.forEach((m, idx) => {
      topContainer.appendChild(_leaderboardRow(idx, m.name, `${m.div} · Hadir: ${m.hadir} sesi`, m.point));
    });
  }
}

function _leaderboardRow(idx, name, meta, point) {
  const bg = idx === 0 ? '#fef3c7' : idx === 1 ? '#e2e8f0' : '#f1f5f9';
  const color = idx === 0 ? '#d97706' : 'var(--navy)';
  const row = document.createElement('div');
  row.style.cssText = 'display:flex;align-items:center;gap:12px;padding:9px 0;border-bottom:1px solid var(--slate-100);';
  row.innerHTML = `
    <div style="width:24px;height:24px;border-radius:50%;background:${bg};color:${color};font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;">${idx + 1}</div>
    <div style="flex:1;">
      <div style="font-weight:600;font-size:13.5px;color:var(--navy);">${name}</div>
      <div style="font-size:11px;color:var(--slate-400);">${meta}</div>
    </div>
    <div style="font-size:14px;font-weight:800;color:var(--blue-600);">${point} pt</div>
  `;
  return row;
}

function _renderDashboardEventsTable(db) {
  const tbody = document.getElementById('dashboard-events-tbody');
  tbody.innerHTML = '';

  let eventList = eventsData;
  if (db && db.events && db.events.length > 0) {
    eventList = db.events.map(ev => ({
      id: ev.id,
      name: ev.title,
      dateRange: (ev.start_date && ev.end_date)
        ? (formatDateId(ev.start_date.split(' ')[0]) + ' s/d ' + formatDateId(ev.end_date.split(' ')[0]))
        : 'Belum diatur',
      subEvents: { length: parseInt(ev.sub_event_count) || 0 },
      totalPeserta: parseInt(ev.member_count) || 0,
      status: ev.status || 'Not started',
    }));
  } else if (db && Array.isArray(db.events) && db.events.length === 0) {
    eventList = [];
  }

  if (eventList.length === 0) {
    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:30px;color:var(--slate-400);">Tidak ada event yang ditemukan.</td></tr>';
    return;
  }

  eventList.forEach(ev => {
    const badgeCls = ev.status === 'in progress' ? 'badge-blue' : ev.status === 'Not started' ? 'badge-amber' : 'badge-gray';
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td><strong>${ev.name}</strong></td>
      <td>${ev.dateRange}</td>
      <td><span class="subevent-tree-badge"><i class="fa-solid fa-sitemap"></i> ${ev.subEvents.length || 0} Sub Event</span></td>
      <td>${ev.totalPeserta} Peserta</td>
      <td><span class="badge ${badgeCls}">${ev.status}</span></td>
      <td><button class="action-link" onclick="openEventDetail(${ev.id})">Lihat Detail</button></td>
    `;
    tbody.appendChild(tr);
  });
}
