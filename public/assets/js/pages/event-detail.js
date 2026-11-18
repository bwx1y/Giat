  // ============================================================
// pages/event-detail.js — Detail event, sub event CRUD, QR
// ============================================================

  let qrEventSource = null;

function toggleCustomMemberSelect(event) {
  if (event) {
    event.preventDefault();
    event.stopPropagation();
  }
  const wrap = document.getElementById('wrap-add-member-user-id');
  if (wrap) {
    wrap.classList.toggle('open');
  }
}

function selectCustomMember(id, name) {
  const hiddenInput = document.getElementById('add-member-user-id');
  const label = document.getElementById('label-add-member-user-id');
  const wrap = document.getElementById('wrap-add-member-user-id');
  const options = document.querySelectorAll('#options-add-member-user-id .custom-select-option');

  if (hiddenInput) hiddenInput.value = id;
  if (label) {
    label.textContent = name;
    label.style.color = 'var(--navy)';
  }
  options.forEach(opt => {
    if (opt.textContent === name) {
      opt.classList.add('selected');
    } else {
      opt.classList.remove('selected');
    }
  });
  if (wrap) wrap.classList.remove('open');
}

function escapeToastText(value) {
  return String(value).replace(/[&<>"']/g, character => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#39;',
  })[character]);
}

document.addEventListener('DOMContentLoaded', function() {
  if (typeof window.eventMemberAddedName === 'string' && window.eventMemberAddedName) {
    showToast(
      `Peserta "${escapeToastText(window.eventMemberAddedName)}" berhasil ditambahkan`,
      'success'
    );
  }
});

function openAddEventMemberModal(eventId) {
  const form = document.getElementById('form-add-event-member');
  if (form) {
    form.action = `/admin/event/${encodeURIComponent(eventId)}/member/create/handler`;
    form.onsubmit = function(e) {
      const hiddenInput = document.getElementById('add-member-user-id');
      if (!hiddenInput || !hiddenInput.value) {
        e.preventDefault();
        if (typeof showToast === 'function') {
          showToast('Silakan pilih anggota terlebih dahulu.', 'warning');
        } else {
          alert('Silakan pilih anggota terlebih dahulu.');
        }
        return false;
      }
    };
  }

  const hiddenInput = document.getElementById('add-member-user-id');
  if (hiddenInput) hiddenInput.value = '';

  const label = document.getElementById('label-add-member-user-id');
  if (label) {
    label.textContent = '— Pilih anggota —';
    label.style.color = '';
  }

  const optionsContainer = document.getElementById('options-add-member-user-id');
  if (optionsContainer) {
    optionsContainer.innerHTML = '';

    let candidates = Array.isArray(window.dbEventMemberCandidates) ? window.dbEventMemberCandidates : [];
    if (candidates.length === 0 && typeof members !== 'undefined' && Array.isArray(members)) {
      candidates = members.map(m => ({ id: m.id, full_name: m.name }));
    }

    if (candidates.length === 0) {
      optionsContainer.innerHTML = '<div class="custom-select-option disabled">Tidak ada anggota yang tersedia</div>';
    } else {
      candidates.forEach(u => {
        const div = document.createElement('div');
        div.className = 'custom-select-option';
        const name = u.full_name || u.name || '';
        div.textContent = name;
        div.onclick = function(e) {
          e.stopPropagation();
          selectCustomMember(u.id, name);
        };
        optionsContainer.appendChild(div);
      });
    }
  }

  const wrap = document.getElementById('wrap-add-member-user-id');
  if (wrap) wrap.classList.remove('open');

  openModal('modal-add-event-member');
}

document.addEventListener('click', function(e) {
  const wrap = document.getElementById('wrap-add-member-user-id');
  if (wrap && !wrap.contains(e.target)) {
    wrap.classList.remove('open');
  }
});

function deleteEventMember(eventId, userId) {
  if (!window.confirm('Hapus anggota ini dari event?')) return;

  fetch(`/event/${encodeURIComponent(eventId)}`, {
    method: 'DELETE',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
    body: new URLSearchParams({ member_id: String(userId) }),
  })
    .then(response => {
      if (!response.ok) throw new Error(`HTTP ${response.status}`);
      window.location.reload();
    })
    .catch(error => showToast(`Gagal menghapus anggota: ${error.message}`, 'error'));
}

function renderEventDetail(eventId) {
  // ── Prioritaskan data dari DB, fallback ke mock jika belum ada ──
  let ev = null;
  let subEventRows = null;

  if (window.dbEventDetail && Number(window.dbEventDetail.id) === Number(eventId)) {
    const d = window.dbEventDetail;
    ev = {
      id:           Number(d.id),
      name:         d.title || '',
      desc:         d.description || 'Tidak ada deskripsi kegiatan.',
      dateRange:    (d.start_date && d.end_date)
                      ? (formatDateId(d.start_date.split(' ')[0]) + ' s/d ' + formatDateId(d.end_date.split(' ')[0]))
                      : 'Belum diatur',
      totalPeserta: parseInt(d.member_count) || 0,
      status:       d.status || 'Not started',
      location:     d.location || '-',
      subCount:     parseInt(d.sub_event_count) || 0,
    };
    subEventRows = Array.isArray(window.dbEventMeetings) ? window.dbEventMeetings : [];
  } else {
    // Fallback ke mock-data
    const mock = eventsData.find(e => e.id === eventId);
    if (!mock) return;
    ev = {
      id:           mock.id,
      name:         mock.name,
      desc:         mock.desc,
      dateRange:    mock.dateRange,
      totalPeserta: mock.totalPeserta,
      status:       mock.status,
      location:     mock.loc || '-',
      subCount:     mock.subEvents ? mock.subEvents.length : 0,
    };
    subEventRows = null; // akan dirender dari mock di bawah
  }

  document.getElementById('detail-breadcrumb-name').textContent = ev.name;
  document.getElementById('detail-hero-title').textContent = ev.name;
  document.getElementById('detail-hero-desc').textContent = ev.desc;
  document.getElementById('detail-hero-date').textContent = ev.dateRange;
  document.getElementById('detail-hero-status').textContent = ev.status;
  document.getElementById('detail-hero-total-peserta').textContent = ev.totalPeserta;
  document.getElementById('detail-hero-sub-count').textContent = subEventRows !== null ? subEventRows.length : ev.subCount;

  const btnAddSub = document.getElementById('btn-add-subevent');
  if (btnAddSub) btnAddSub.style.display = (currentRole === 'admin') ? 'inline-flex' : 'none';

  const btnAddMember = document.getElementById('btn-add-member-event');
  if (btnAddMember) btnAddMember.style.display = (currentRole === 'superadmin') ? 'inline-flex' : 'none';

  const tbody = document.getElementById('subevents-tbody');
  tbody.innerHTML = '';

  // ── Render dari data DB ──
  if (subEventRows !== null) {
    if (subEventRows.length === 0) {
      tbody.innerHTML = `<tr><td colspan="9" style="text-align:center;padding:30px;color:var(--slate-400);">Belum ada Sub Event. Silakan klik tombol "+ Tambah Sub Event".</td></tr>`;
      return;
    }

    subEventRows.forEach(sub => {
      const total = parseInt(sub.total_peserta) || 0;
      const hadir = parseInt(sub.hadir) || 0;
      const pct   = parseInt(sub.persentase_kehadiran) || (total > 0 ? Math.round((hadir / total) * 100) : 0);

      const rawStatus = (sub.status || '').toLowerCase();
      const badgeCls  = rawStatus === 'done'        ? 'badge-gray'
                      : rawStatus === 'in progress' ? 'badge-blue'
                      : 'badge-amber';
      const statusLabel = sub.status || 'Not started';

      const dateStr  = sub.start_date ? formatDateId(sub.start_date.split(' ')[0]) : '-';
      const timeStr  = (sub.start_date && sub.end_date)
                         ? (sub.start_date.split(' ')[1]?.slice(0, 5) || '-') + ' – ' + (sub.end_date.split(' ')[1]?.slice(0, 5) || '-')
                         : '-';
      const locStr   = sub.location || '-';
      const canManage = currentRole === 'admin' || currentRole === 'superadmin';

      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>
          <strong style="color:var(--navy);">${sub.title}</strong>
        </td>
        <td>${dateStr}</td>
        <td>${timeStr}</td>
        <td>${locStr}</td>
        <td>—</td>
        <td>${total}</td>
        <td>
          <div style="display:flex;align-items:center;gap:6px;">
            <div class="attendance-progress-bar"><div class="attendance-progress-fill" style="width:${pct}%"></div></div>
            <span>${hadir} / ${total} (${pct}%)</span>
          </div>
        </td>
        <td><span class="badge ${badgeCls}">${statusLabel}</span></td>
        <td>
          <div class="actions-cell">
            <button class="btn btn-outline btn-sm" onclick="openSubEventDetail(${ev.id}, ${sub.id})">
              <i class="fa-solid fa-clipboard-check"></i> Kelola Absensi
            </button>
            ${canManage ? `
              <button class="action-link" onclick="editSubEventPrompt(${ev.id}, ${sub.id})">Edit</button>
              <button class="action-link danger" onclick="deleteSubEvent(${ev.id}, ${sub.id})">Hapus</button>
            ` : ''}
          </div>
        </td>
      `;
      tbody.appendChild(tr);
    });
    return;
  }

  // ── Fallback: render dari mock-data ──
  const mock = eventsData.find(e => e.id === eventId);
  if (!mock || mock.subEvents.length === 0) {
    tbody.innerHTML = `<tr><td colspan="9" style="text-align:center;padding:30px;color:var(--slate-400);">Belum ada Sub Event. Silakan klik tombol "+ Tambah Sub Event".</td></tr>`;
    return;
  }

  mock.subEvents.forEach(sub => {
    const pct = sub.total > 0 ? Math.round((sub.hadir / sub.total) * 100) : 0;
    const badgeCls = (sub.status === 'done' || sub.status === 'Finished') ? 'badge-gray' : (sub.status === 'in progress' || sub.status === 'Active') ? 'badge-blue' : 'badge-amber';
    const canManage = currentRole === 'admin' || currentRole === 'superadmin';

    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>
        <strong style="color:var(--navy);">${sub.name}</strong>
        <div style="font-size:11px;color:var(--slate-400);">${sub.desc || ''}</div>
      </td>
      <td>${sub.date}</td>
      <td>${sub.time}</td>
      <td>${sub.loc}</td>
      <td><span class="badge badge-blue"><i class="fa-solid fa-user-tie"></i> ${sub.pic}</span></td>
      <td>${sub.total}</td>
      <td>
        <div style="display:flex;align-items:center;gap:6px;">
          <div class="attendance-progress-bar"><div class="attendance-progress-fill" style="width:${pct}%"></div></div>
          <span>${sub.hadir} / ${sub.total} (${pct}%)</span>
        </div>
      </td>
      <td><span class="badge ${badgeCls}">${sub.status}</span></td>
      <td>
        <div class="actions-cell">
          <button class="btn btn-outline btn-sm" onclick="openSubEventDetail(${mock.id}, ${sub.id})">
            <i class="fa-solid fa-clipboard-check"></i> Kelola Absensi
          </button>
          ${canManage ? `
            <button class="action-link" onclick="editSubEventPrompt(${mock.id}, ${sub.id})">Edit</button>
            <button class="action-link danger" onclick="deleteSubEvent(${mock.id}, ${sub.id})">Hapus</button>
          ` : ''}
        </div>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

function openAddSubEventModal(targetEventId) {
  if (currentRole === 'superadmin') {
    showToast('Super Admin tidak diizinkan membuat Sub Event. Sub Event hanya dapat dibuat oleh Admin.', 'error');
    return;
  }
  if (currentRole === 'member') {
    showToast('Anggota tidak memiliki izin untuk membuat Sub Event.', 'error');
    return;
  }

  if (targetEventId) {
    currentActiveEventId = parseInt(targetEventId, 10);
  } else if (window.dbEventDetail && window.dbEventDetail.id) {
    currentActiveEventId = parseInt(window.dbEventDetail.id, 10);
  } else if (!currentActiveEventId && eventsData.length > 0) {
    currentActiveEventId = eventsData[0].id;
  }

  const parentInput = document.getElementById('new-sub-parent-select');
  if (parentInput) {
    parentInput.value = currentActiveEventId;
  }

  const form = document.getElementById('form-add-subevent');
  if (form) {
    form.action = `/event/${encodeURIComponent(currentActiveEventId)}`;
  }

  document.getElementById('new-sub-name').value = '';
  const defaultStart = new Date();
  defaultStart.setMinutes(0, 0, 0);
  const defaultEnd = new Date(defaultStart.getTime() + 60 * 60 * 1000);
  document.getElementById('new-sub-start').value = formatDateTimeLocal(defaultStart);
  document.getElementById('new-sub-end').value = formatDateTimeLocal(defaultEnd);
  document.getElementById('new-sub-loc').value = 'Ruang Meeting A';

  openModal('modal-add-subevent');
}

function formatDateTimeLocal(date) {
  const localDate = new Date(date.getTime() - date.getTimezoneOffset() * 60000);
  return localDate.toISOString().slice(0, 16);
}

function saveNewSubEvent(event) {
  if (currentRole === 'superadmin') {
    showToast('Super Admin tidak dapat membuat Sub Event. Hanya Admin yang berhak.', 'error');
    event.preventDefault();
    return false;
  }
  if (currentRole !== 'admin') {
    showToast('Hanya PIC/Admin event yang dapat menambahkan Sub Event.', 'error');
    event.preventDefault();
    return false;
  }

  const form = document.getElementById('form-add-subevent');
  const parentId = Number(document.getElementById('new-sub-parent-select')?.value);
  if (!form || !Number.isInteger(parentId) || parentId <= 0) {
    showToast('Pilih Event Utama terlebih dahulu.', 'error');
    event.preventDefault();
    return false;
  }

  form.action = `/event/${encodeURIComponent(parentId)}`;
  return true;
}

function editSubEventPrompt(evId, subId) {
  const ev = eventsData.find(e => e.id === evId);
  const sub = ev ? ev.subEvents.find(s => s.id === subId) : null;
  if (!sub) return;

  const newName = prompt('Edit Nama Sub Event:', sub.name);
  if (newName && newName.trim()) {
    sub.name = newName.trim();
    if (currentPage === 'event-detail') renderEventDetail(evId);
    if (currentPage === 'admin-dashboard') renderAdminDashboard();
    showToast('Sub Event berhasil diperbarui', 'success');
  }
}

function deleteSubEvent(evId, subId) {
  if (!confirm('Hapus Sub Event ini?')) return;
  const ev = eventsData.find(e => e.id === evId);
  if (!ev) return;
  ev.subEvents = ev.subEvents.filter(s => s.id !== subId);
  if (currentPage === 'event-detail') renderEventDetail(evId);
  if (currentPage === 'admin-dashboard') renderAdminDashboard();
  renderDashboardStats();
  showToast('Sub Event berhasil dihapus', 'info');
}

function openSubEventDetail(evId, subId) {
  currentActiveEventId = evId;
  currentActiveSubEventId = subId;
  navTo('subevent-detail');
}

function backToParentEvent() {
  if (currentRole === 'admin') {
    navTo('admin-dashboard');
  } else {
    openEventDetail(currentActiveEventId);
  }
}

// ---- Sub Event Detail & Absensi ----

function renderSubEventDetail(evId, subId) {
  let parentName = '';
  let subName = '';
  let dateStr = '';
  let timeStr = '';
  let locStr = '';
  let picStr = 'Budi Santoso';
  let statusStr = 'Not started';
  let total = 0;
  let hadir = 0;
  let absen = 0;
  let pct = 0;
  let participants = null;

  if (window.dbSubMeetingDetail && Number(window.dbSubMeetingDetail.id) === Number(subId)) {
    const m = window.dbSubMeetingDetail;
    const ev = window.dbSubEventDetail;
    const stats = window.dbSubMeetingStats;

    parentName = (ev && ev.title) ? ev.title : 'Event Utama';
    subName = m.title || 'Sub Event';
    dateStr = m.start_date ? formatDateId(m.start_date.split(' ')[0]) : '-';
    timeStr = (m.start_date && m.end_date)
      ? (m.start_date.split(' ')[1]?.slice(0, 5) || '-') + ' – ' + (m.end_date.split(' ')[1]?.slice(0, 5) || '-')
      : '-';
    locStr = m.location || 'Ruang Meeting';
    statusStr = m.status || 'Not started';

    if (stats) {
      total = parseInt(stats.total_peserta) || 0;
      hadir = parseInt(stats.hadir) || 0;
      absen = parseInt(stats.tidak_hadir) || Math.max(0, total - hadir);
      pct = parseInt(stats.persentase_kehadiran) || (total > 0 ? Math.round((hadir / total) * 100) : 0);
    }

    if (Array.isArray(window.dbSubParticipants)) {
      participants = window.dbSubParticipants.map(p => ({
        id: p.user_id,
        name: p.name || 'Peserta',
        div: p.div || 'Anggota',
        status: Number(p.status) === 1 ? 'Hadir' : 'Tidak Hadir',
        time: p.attended_at ? p.attended_at.split(' ')[1]?.slice(0, 5) || '—' : '—',
        method: p.method || '—',
        point: Number(p.status) === 1 ? 3 : -1,
      }));
    }
  } else {
    const ev = eventsData.find(e => e.id === evId);
    if (!ev) return;
    const sub = ev.subEvents.find(s => s.id === subId);
    if (!sub) return;

    parentName = ev.name;
    subName = sub.name;
    dateStr = sub.date;
    timeStr = sub.time;
    locStr = sub.loc;
    picStr = sub.pic;
    statusStr = sub.status;
    total = sub.total || 40;
    hadir = sub.hadir || 0;
    absen = Math.max(0, total - hadir);
    pct = total > 0 ? Math.round((hadir / total) * 100) : 0;
    participants = sub.participants;
  }

  const subBcParent = document.getElementById('sub-bc-parent');
  if (subBcParent) subBcParent.textContent = parentName;
  const subBcCurrent = document.getElementById('sub-bc-current');
  if (subBcCurrent) subBcCurrent.textContent = subName;
  const subInfoParent = document.getElementById('sub-info-parent');
  if (subInfoParent) subInfoParent.textContent = parentName;
  const subInfoTitle = document.getElementById('sub-info-title');
  if (subInfoTitle) subInfoTitle.textContent = subName;
  const subInfoDate = document.getElementById('sub-info-date');
  if (subInfoDate) subInfoDate.textContent = dateStr;
  const subInfoTime = document.getElementById('sub-info-time');
  if (subInfoTime) subInfoTime.textContent = timeStr;
  const subInfoLoc = document.getElementById('sub-info-loc');
  if (subInfoLoc) subInfoLoc.textContent = locStr;
  const subInfoPic = document.getElementById('sub-info-pic');
  if (subInfoPic) subInfoPic.textContent = picStr;

  const statusEl = document.getElementById('sub-info-status');
  if (statusEl) {
    statusEl.textContent = statusStr;
    statusEl.className = 'badge ' + ((statusStr === 'done' || statusStr === 'Finished') ? 'badge-gray' : (statusStr === 'in progress' || statusStr === 'Active') ? 'badge-green' : 'badge-amber');
  }

  const elTotal = document.getElementById('sub-stat-total');
  if (elTotal) elTotal.textContent = total;
  const elHadir = document.getElementById('sub-stat-hadir');
  if (elHadir) elHadir.textContent = hadir;
  const elAbsen = document.getElementById('sub-stat-absen');
  if (elAbsen) elAbsen.textContent = absen;
  const elPct = document.getElementById('sub-stat-pct');
  if (elPct) elPct.textContent = pct + '%';

  renderSubParticipants(participants);
}

function renderSubParticipants(pList) {
  const tbody = document.getElementById('sub-participants-tbody');
  if (!tbody) return;
  tbody.innerHTML = '';

  if (!pList || pList.length === 0) {
    if (typeof eventsData !== 'undefined') {
      pList = [
        { name: 'Andi Pratama', div: 'Divisi Teknologi', status: 'Hadir', time: '19:04', method: 'QR', point: 3 },
        { name: 'Rizky Ramadhan', div: 'Divisi Kreatif', status: 'Hadir', time: '19:08', method: 'QR', point: 3 },
        { name: 'Siti Aulia', div: 'Divisi Humas', status: 'Belum Hadir', time: '—', method: '—', point: 0 },
        { name: 'Fajar Nugraha', div: 'Divisi Teknologi', status: 'Hadir', time: '19:15', method: 'Manual', point: 3 },
        { name: 'Dina Maharani', div: 'Divisi Keuangan', status: 'Belum Hadir', time: '—', method: '—', point: 0 },
      ];
    } else {
      tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;padding:30px;color:var(--slate-400);">Tidak ada peserta untuk Sub Event ini.</td></tr>`;
      return;
    }
  }

  pList.forEach((p, idx) => {
    const isHadir = p.status === 'Hadir';
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td><strong>${p.name}</strong></td>
      <td>${p.div || 'Anggota'}</td>
      <td><span class="badge ${isHadir ? 'badge-green' : 'badge-red'}">${p.status}</span></td>
      <td>${p.time || '—'}</td>
      <td>${p.method && p.method !== '—' ? `<span class="badge badge-blue">${p.method}</span>` : '—'}</td>
      <td><strong class="${isHadir ? 'point-positive' : 'point-negative'}">${isHadir ? '+3' : (p.status === 'Tidak Hadir' ? '-1' : '0')}</strong></td>
      <td>
        ${!isHadir
          ? `<button class="btn btn-sm btn-green" onclick="manualAbsenSubParticipant(${idx}, ${p.id || 0})"><i class="fa-solid fa-check"></i> Absen Manual</button>`
          : `<button class="action-link danger" onclick="koreksiAbsenSubParticipant(${idx}, ${p.id || 0})">Koreksi (Batal)</button>`
        }
      </td>
    `;
    tbody.appendChild(tr);
  });
}

function manualAbsenSubParticipant(idx, userId) {
  if (userId) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = window.location.pathname;
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'user_id';
    input.value = userId;
    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
    return;
  }

  const ev = eventsData.find(e => e.id === currentActiveEventId);
  const sub = ev ? ev.subEvents.find(s => s.id === currentActiveSubEventId) : null;
  if (!sub || !sub.participants[idx]) return;

  const now = new Date();
  const time = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');

  sub.participants[idx] = { ...sub.participants[idx], status: 'Hadir', time, method: 'Manual', point: 3 };
  sub.hadir = (sub.hadir || 0) + 1;

  attendanceHistory.unshift({
    id: Date.now(),
    member: sub.participants[idx].name,
    event: ev.name,
    subEvent: sub.name,
    date: sub.date,
    status: 'Hadir',
    method: 'Manual',
    point: 3,
  });

  renderSubEventDetail(ev.id, sub.id);
  showToast(sub.participants[idx].name + ' berhasil diabsen secara manual (+3 Point)', 'success');
}

function koreksiAbsenSubParticipant(idx, userId) {
  if (userId) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = window.location.pathname;
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'user_id';
    input.value = userId;
    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
    return;
  }

  const ev = eventsData.find(e => e.id === currentActiveEventId);
  const sub = ev ? ev.subEvents.find(s => s.id === currentActiveSubEventId) : null;
  if (!sub || !sub.participants[idx]) return;

  sub.participants[idx] = { ...sub.participants[idx], status: 'Tidak Hadir', time: '—', method: '—', point: -1 };
  sub.hadir = Math.max(0, (sub.hadir || 1) - 1);

  renderSubEventDetail(ev.id, sub.id);
  showToast('Koreksi berhasil: status diubah menjadi Tidak Hadir (-1 Point)', 'info');
}

function filterSubParticipants(q) {
  q = q.toLowerCase();
  document.querySelectorAll('#sub-participants-tbody tr').forEach(row => {
    row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
  });
}

// ---- QR Absensi ----

function openQrForCurrentSubEvent() {
  const eventId = Number(window.routeParams?.event_id || currentActiveEventId);
  const meetingId = Number(window.routeParams?.subevent_id || currentActiveSubEventId);
  if (!Number.isInteger(eventId) || eventId <= 0 || !Number.isInteger(meetingId) || meetingId <= 0) {
    showToast('Event atau sub-event tidak valid.', 'error');
    return;
  }

  const event = eventsData.find(item => Number(item.id) === eventId);
  const subEvent = event?.subEvents.find(item => Number(item.id) === meetingId);
  document.getElementById('qr-modal-sub-name').textContent =
    subEvent?.name || document.getElementById('sub-info-title')?.textContent || 'Sub Event';
  document.getElementById('qr-modal-parent-name').textContent =
    event?.name || document.getElementById('sub-info-parent')?.textContent || 'Event';

  const container = document.getElementById('qr-canvas');
  const status = document.getElementById('qr-stream-status');
  container.replaceChildren();
  status.textContent = 'Menghubungkan ke server QR...';
  openModal('modal-qr');
  stopQrEventStream();

  const source = new EventSource(
    `/member/event/${eventId}/meeting/${meetingId}/qr`
  );
  qrEventSource = source;

  source.addEventListener('qr_code', eventMessage => {
    if (qrEventSource !== source) return;

    try {
      const payload = JSON.parse(eventMessage.data);
      if (
        typeof payload.code !== 'string'
        || Number(payload.event_id) !== eventId
        || Number(payload.meeting_id) !== meetingId
      ) {
        throw new Error('Data QR dari server tidak valid.');
      }
      if (typeof QRCode !== 'function') {
        throw new Error('Library pembuat QR tidak tersedia.');
      }

      container.replaceChildren();
      new QRCode(container, {
        text: JSON.stringify({
          type: 'giat-attendance',
          event_id: eventId,
          meeting_id: meetingId,
          code: payload.code,
        }),
        width: 180,
        height: 180,
        colorDark: '#0f172a',
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.H,
      });
      status.textContent = 'QR aktif. Kode akan berubah setelah presensi berhasil.';
    } catch (error) {
      status.textContent = error.message || 'Gagal menampilkan QR dari server.';
    }
  });

  source.addEventListener('qr_error', eventMessage => {
    if (qrEventSource !== source) return;
    try {
      const payload = JSON.parse(eventMessage.data);
      status.textContent = payload.message || 'Server gagal membuat QR.';
    } catch {
      status.textContent = 'Server gagal membuat QR.';
    }
  });

  source.onopen = () => {
    if (qrEventSource === source) status.textContent = 'Terhubung. Menunggu kode QR...';
  };
  source.onerror = () => {
    if (qrEventSource === source) status.textContent = 'Koneksi terputus; mencoba menyambungkan kembali...';
  };
}

function stopQrEventStream() {
  if (qrEventSource) {
    qrEventSource.close();
    qrEventSource = null;
  }
}
