// ============================================================
// pages/absensi.js — Rekap absensi seluruh event
// ============================================================

function renderAbsensiList() {
  const tbody = document.getElementById('absensi-all-tbody');
  tbody.innerHTML = '';

  const attendanceRows = Array.isArray(window.dbAttendance)
    ? window.dbAttendance.map(item => {
        const isHadir = Number(item.status) === 1;
        const date = String(item.date || '').split(' ')[0];
        return {
          id: item.meeting_id,
          member: item.member_name || '',
          event: item.event || '',
          eventId: String(item.event_id || ''),
          subEvent: item.sub_event || '',
          date: date.includes('-') ? formatDateId(date) : date,
          status: isHadir ? 'Hadir' : 'Tidak Hadir',
          method: item.method || '—',
          point: isHadir ? 3 : -1,
        };
      })
    : attendanceHistory;

  if (attendanceRows.length === 0) {
    tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:30px;color:var(--slate-400);">Tidak ada data absensi.</td></tr>';
    return;
  }

  attendanceRows.forEach((item, idx) => {
    const isHadir = item.status === 'Hadir';
    const tr = document.createElement('tr');
    tr.dataset.member = item.member.toLowerCase();
    tr.dataset.event = item.event.toLowerCase();
    tr.dataset.eventId = item.eventId || '';
    tr.dataset.subevent = item.subEvent.toLowerCase();
    tr.dataset.status = item.status;
    tr.innerHTML = `
      <td><strong>${item.member}</strong></td>
      <td>${item.event}</td>
      <td><span class="subevent-tree-badge"><i class="fa-solid fa-sitemap"></i> ${item.subEvent}</span></td>
      <td>${item.date}</td>
      <td><span class="badge ${isHadir ? 'badge-green' : 'badge-red'}">${item.status}</span></td>
      <td>${item.method !== '—' ? `<span class="badge badge-blue">${item.method}</span>` : '—'}</td>
      <td><strong class="${isHadir ? 'point-positive' : 'point-negative'}">${isHadir ? '+3' : '-1'}</strong></td>
      <td>
        ${Array.isArray(window.dbAttendance)
          ? '—'
          : `<button class="action-link" onclick="koreksiGlobalAbsensi(${idx})">Koreksi</button>`
        }
      </td>
    `;
    tbody.appendChild(tr);
  });
}

function filterAbsensiList() {
  const q = document.getElementById('absensi-search').value.toLowerCase();
  const evFilter = document.getElementById('absensi-filter-event').value;
  const stFilter = document.getElementById('absensi-filter-status').value;

  document.querySelectorAll('#absensi-all-tbody tr').forEach(row => {
    const matchQ = row.dataset.member.includes(q)
      || row.dataset.event.includes(q)
      || row.dataset.subevent.includes(q);
    const matchEv = !evFilter || (Array.isArray(window.dbAttendance)
      ? row.dataset.eventId === evFilter
      : row.dataset.event === evFilter.toLowerCase());
    const matchSt = !stFilter || row.dataset.status === stFilter;
    row.style.display = matchQ && matchEv && matchSt ? '' : 'none';
  });
}

function koreksiGlobalAbsensi(idx) {
  const item = attendanceHistory[idx];
  if (!item) return;
  if (item.status === 'Hadir') {
    item.status = 'Tidak Hadir';
    item.point = -1;
    item.method = '—';
    showToast('Koreksi: Status ' + item.member + ' diubah menjadi Tidak Hadir (-1)', 'info');
  } else {
    item.status = 'Hadir';
    item.point = 3;
    item.method = 'Manual';
    showToast('Koreksi: Status ' + item.member + ' diubah menjadi Hadir (+3)', 'success');
  }
  renderAbsensiList();
}
