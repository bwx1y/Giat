let currentActiveEventId = 1;
let currentActiveSubEventId = 101;
let qrTimerInterval = null;

function renderSubEventDetail(evId, subId) {
  currentActiveEventId = evId;
  currentActiveSubEventId = subId;

  const events = getEvents();
  const ev = events.find(e => e.id === evId);
  if (!ev) return;
  const sub = (ev.subEvents || []).find(s => s.id === subId);
  if (!sub) return;

  const bcParent = document.getElementById('sub-bc-parent');
  const bcCurrent = document.getElementById('sub-bc-current');
  const infoParent = document.getElementById('sub-info-parent');
  const infoTitle = document.getElementById('sub-info-title');
  const infoDate = document.getElementById('sub-info-date');
  const infoTime = document.getElementById('sub-info-time');
  const infoLoc = document.getElementById('sub-info-loc');
  const infoPic = document.getElementById('sub-info-pic');
  const infoStatus = document.getElementById('sub-info-status');

  if (bcParent) {
    bcParent.textContent = ev.name;
    bcParent.href = `event-detail.html?id=${ev.id}`;
  }
  if (bcCurrent) bcCurrent.textContent = sub.name;
  if (infoParent) infoParent.textContent = ev.name;
  if (infoTitle) infoTitle.textContent = sub.name;
  if (infoDate) infoDate.textContent = sub.date;
  if (infoTime) infoTime.textContent = sub.time;
  if (infoLoc) infoLoc.textContent = sub.loc;
  if (infoPic) infoPic.textContent = sub.pic;
  if (infoStatus) {
    infoStatus.textContent = sub.status;
    infoStatus.className = 'badge ' + (sub.status === 'Finished' ? 'badge-gray' : sub.status === 'Active' ? 'badge-green' : 'badge-amber');
  }

  const total = sub.total || 40;
  const hadir = sub.hadir || 0;
  const absen = Math.max(0, total - hadir);
  const pct = total > 0 ? Math.round((hadir / total) * 100) : 0;

  const statTotal = document.getElementById('sub-stat-total');
  const statHadir = document.getElementById('sub-stat-hadir');
  const statAbsen = document.getElementById('sub-stat-absen');
  const statPct = document.getElementById('sub-stat-pct');

  if (statTotal) statTotal.textContent = total;
  if (statHadir) statHadir.textContent = hadir;
  if (statAbsen) statAbsen.textContent = absen;
  if (statPct) statPct.textContent = pct + '%';

  renderSubParticipants(sub, ev);
}

function renderSubParticipants(sub, ev) {
  const tbody = document.getElementById('sub-participants-tbody');
  if (!tbody) return;
  tbody.innerHTML = '';

  let pList = sub.participants;
  if (!pList || pList.length === 0) {
    pList = [
      { name: 'Andi Pratama', div: 'Divisi Teknologi', status: 'Hadir', time: '19:04', method: 'QR', point: 3 },
      { name: 'Rizky Ramadhan', div: 'Divisi Kreatif', status: 'Hadir', time: '19:08', method: 'QR', point: 3 },
      { name: 'Siti Aulia', div: 'Divisi Humas', status: 'Belum Hadir', time: '—', method: '—', point: 0 },
      { name: 'Fajar Nugraha', div: 'Divisi Teknologi', status: 'Hadir', time: '19:15', method: 'Manual', point: 3 },
      { name: 'Dina Maharani', div: 'Divisi Keuangan', status: 'Belum Hadir', time: '—', method: '—', point: 0 },
    ];
    sub.participants = pList;
    const events = getEvents();
    const parent = events.find(e => e.id === ev.id);
    if (parent) {
      const s = (parent.subEvents || []).find(x => x.id === sub.id);
      if (s) s.participants = pList;
      saveEvents(events);
    }
  }

  pList.forEach((p, idx) => {
    const isHadir = p.status === 'Hadir';
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td><strong>${p.name}</strong></td>
      <td>${p.div}</td>
      <td>
        <span class="badge ${isHadir ? 'badge-green' : 'badge-red'}">${p.status}</span>
      </td>
      <td>${p.time}</td>
      <td>${p.method !== '—' ? `<span class="badge badge-blue">${p.method}</span>` : '—'}</td>
      <td>
        <strong class="${isHadir ? 'point-positive' : 'point-negative'}">${isHadir ? '+3' : (p.status === 'Tidak Hadir' ? '-1' : '0')}</strong>
      </td>
      <td>
        ${!isHadir ? `
          <button class="btn btn-sm btn-green" onclick="manualAbsenSubParticipant(${idx})">
            <i class="fa-solid fa-check"></i> Absen Manual
          </button>
        ` : `
          <button class="action-link danger" onclick="koreksiAbsenSubParticipant(${idx})">
            Koreksi (Batal)
          </button>
        `}
      </td>
    `;
    tbody.appendChild(tr);
  });
}

function manualAbsenSubParticipant(idx) {
  const events = getEvents();
  const ev = events.find(e => e.id === currentActiveEventId);
  const sub = ev ? (ev.subEvents || []).find(s => s.id === currentActiveSubEventId) : null;
  if (!sub || !sub.participants || !sub.participants[idx]) return;

  const now = new Date();
  const time = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');

  sub.participants[idx].status = 'Hadir';
  sub.participants[idx].time = time;
  sub.participants[idx].method = 'Manual';
  sub.participants[idx].point = 3;
  sub.hadir = (sub.hadir || 0) + 1;

  saveEvents(events);

  const history = getAttendanceHistory();
  history.unshift({
    id: Date.now(),
    member: sub.participants[idx].name,
    event: ev.name,
    subEvent: sub.name,
    date: sub.date,
    status: 'Hadir',
    method: 'Manual',
    point: 3
  });
  saveAttendanceHistory(history);

  renderSubEventDetail(ev.id, sub.id);
  showToast(sub.participants[idx].name + ' berhasil diabsen secara manual (+3 Point)', 'success');
}

function koreksiAbsenSubParticipant(idx) {
  const events = getEvents();
  const ev = events.find(e => e.id === currentActiveEventId);
  const sub = ev ? (ev.subEvents || []).find(s => s.id === currentActiveSubEventId) : null;
  if (!sub || !sub.participants || !sub.participants[idx]) return;

  sub.participants[idx].status = 'Tidak Hadir';
  sub.participants[idx].time = '—';
  sub.participants[idx].method = '—';
  sub.participants[idx].point = -1;
  sub.hadir = Math.max(0, (sub.hadir || 1) - 1);

  saveEvents(events);

  renderSubEventDetail(ev.id, sub.id);
  showToast('Koreksi berhasil: status diubah menjadi Tidak Hadir (-1 Point)', 'info');
}

function filterSubParticipants(q) {
  q = (q || '').toLowerCase();
  document.querySelectorAll('#sub-participants-tbody tr').forEach(row => {
    row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
  });
}

function openQrForCurrentSubEvent() {
  const events = getEvents();
  const ev = events.find(e => e.id === currentActiveEventId);
  const sub = ev ? (ev.subEvents || []).find(s => s.id === currentActiveSubEventId) : null;
  if (!ev || !sub) return;

  const modalSubName = document.getElementById('qr-modal-sub-name');
  const modalParentName = document.getElementById('qr-modal-parent-name');
  if (modalSubName) modalSubName.textContent = sub.name;
  if (modalParentName) modalParentName.textContent = ev.name;

  const container = document.getElementById('qr-canvas');
  if (container && typeof QRCode !== 'undefined') {
    container.innerHTML = '';
    new QRCode(container, {
      text: `ATTENDIFY://EVENT/${ev.id}/SUBEVENT/${sub.id}/KEY/${Date.now()}`,
      width: 180,
      height: 180,
      colorDark: '#0f172a',
      colorLight: '#ffffff',
      correctLevel: QRCode.CorrectLevel.H,
    });
  }

  const now = new Date();
  now.setMinutes(now.getMinutes() + 45);
  const expStr = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
  const expireEl = document.getElementById('qr-expire-time');
  if (expireEl) expireEl.textContent = expStr;

  startQrCountdown(now);
  openModal('modal-qr');
}

function startQrCountdown(expireDate) {
  clearInterval(qrTimerInterval);
  qrTimerInterval = setInterval(() => {
    const remaining = expireDate - new Date();
    const countdownEl = document.getElementById('qr-countdown');
    if (!countdownEl) {
      clearInterval(qrTimerInterval);
      return;
    }
    if (remaining <= 0) {
      clearInterval(qrTimerInterval);
      countdownEl.textContent = 'Sesi Berakhir';
      return;
    }
    const mins = Math.floor(remaining / 60000);
    const secs = Math.floor((remaining % 60000) / 1000);
    countdownEl.textContent = mins.toString().padStart(2, '0') + ':' + secs.toString().padStart(2, '0');
  }, 1000);
}

function closeSessionFromModal() {
  clearInterval(qrTimerInterval);
  closeModal('modal-qr');
  showToast('Sesi absensi untuk Sub Event telah ditutup.', 'info');
}

function renderAbsensiList() {
  const tbody = document.getElementById('absensi-all-tbody');
  if (!tbody) return;
  tbody.innerHTML = '';

  const history = getAttendanceHistory();
  history.forEach((item, idx) => {
    const isHadir = item.status === 'Hadir';
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td><strong>${item.member}</strong></td>
      <td>${item.event}</td>
      <td><span class="subevent-tree-badge"><i class="fa-solid fa-sitemap"></i> ${item.subEvent}</span></td>
      <td>${item.date}</td>
      <td><span class="badge ${isHadir ? 'badge-green' : 'badge-red'}">${item.status}</span></td>
      <td>${item.method !== '—' ? `<span class="badge badge-blue">${item.method}</span>` : '—'}</td>
      <td><strong class="${isHadir ? 'point-positive' : 'point-negative'}">${isHadir ? '+3' : '-1'}</strong></td>
      <td>
        <button class="action-link" onclick="koreksiGlobalAbsensi(${idx})">
          Koreksi
        </button>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

function filterAbsensiList() {
  const searchInput = document.getElementById('absensi-search');
  const evFilterSelect = document.getElementById('absensi-filter-event');
  const stFilterSelect = document.getElementById('absensi-filter-status');

  const q = searchInput ? searchInput.value.toLowerCase() : '';
  const evFilter = evFilterSelect ? evFilterSelect.value : '';
  const stFilter = stFilterSelect ? stFilterSelect.value : '';

  const rows = document.querySelectorAll('#absensi-all-tbody tr');
  const history = getAttendanceHistory();

  history.forEach((item, idx) => {
    const matchQ = item.member.toLowerCase().includes(q) || item.event.toLowerCase().includes(q) || item.subEvent.toLowerCase().includes(q);
    const matchEv = !evFilter || item.event === evFilter;
    const matchSt = !stFilter || item.status === stFilter;
    if (rows[idx]) {
      rows[idx].style.display = (matchQ && matchEv && matchSt) ? '' : 'none';
    }
  });
}

function koreksiGlobalAbsensi(idx) {
  const history = getAttendanceHistory();
  const item = history[idx];
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

  saveAttendanceHistory(history);
  renderAbsensiList();
}

function renderUserHistory() {
  const tbody = document.getElementById('user-history-tbody');
  if (!tbody) return;
  tbody.innerHTML = '';

  const history = getAttendanceHistory();
  const userItems = history.filter(a => a.member === 'Andi Pratama');
  userItems.forEach(item => {
    const isHadir = item.status === 'Hadir';
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td><strong>${item.event}</strong></td>
      <td><span class="subevent-tree-badge"><i class="fa-solid fa-sitemap"></i> ${item.subEvent}</span></td>
      <td>${item.date}</td>
      <td><span class="badge ${isHadir ? 'badge-green' : 'badge-red'}">${item.status}</span></td>
      <td>${item.method !== '—' ? `<span class="badge badge-blue">${item.method}</span>` : '—'}</td>
      <td><strong class="${isHadir ? 'point-positive' : 'point-negative'}">${isHadir ? '+3' : '-1'}</strong></td>
    `;
    tbody.appendChild(tr);
  });
}
