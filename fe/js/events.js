function renderEvents() {
  const grid = document.getElementById('event-grid');
  if (!grid) return;
  grid.innerHTML = '';

  const events = getEvents();
  const currentRole = getCurrentRole();

  events.forEach(ev => {
    const card = document.createElement('div');
    card.className = 'event-primary-card';
    card.innerHTML = `
      <div class="event-primary-header">
        <div class="event-type-badge"><i class="fa-solid fa-layer-group"></i> Event Utama</div>
        <div class="event-primary-title">${ev.name}</div>
      </div>
      <div class="event-primary-body">
        <div class="event-primary-desc">${ev.desc}</div>
        <div class="event-meta-list">
          <div class="event-meta-item"><i class="fa-regular fa-calendar"></i> ${ev.dateRange}</div>
          <div class="event-meta-item"><i class="fa-solid fa-users"></i> ${ev.totalPeserta} Total Peserta</div>
        </div>
        <div>
          <span class="subevent-pill-count"><i class="fa-solid fa-sitemap"></i> ${(ev.subEvents || []).length} Sub Event</span>
          <span class="badge ${ev.status === 'Active' ? 'badge-blue' : ev.status === 'Upcoming' ? 'badge-amber' : 'badge-gray'}" style="margin-left:6px;">${ev.status}</span>
        </div>
      </div>
      <div class="event-primary-footer">
        <div class="actions-cell">
          <a class="btn btn-blue btn-sm" href="event-detail.html?id=${ev.id}">
            <i class="fa-solid fa-eye"></i> Lihat Detail
          </a>
          ${currentRole === 'superadmin' ? `
            <button class="action-link" onclick="editEventPrompt(${ev.id})">Edit</button>
            <button class="action-link danger" onclick="deleteEvent(${ev.id})">Hapus</button>
          ` : ''}
        </div>
      </div>
    `;
    grid.appendChild(card);
  });
}

function filterEvents() {
  const searchInput = document.getElementById('search-event');
  const statusSelect = document.getElementById('filter-event-status');
  const q = searchInput ? searchInput.value.toLowerCase() : '';
  const st = statusSelect ? statusSelect.value : '';
  const cards = document.querySelectorAll('.event-primary-card');
  const events = getEvents();

  events.forEach((ev, idx) => {
    const matchQ = ev.name.toLowerCase().includes(q) || ev.desc.toLowerCase().includes(q);
    const matchSt = !st || ev.status === st;
    if (cards[idx]) {
      cards[idx].style.display = (matchQ && matchSt) ? 'flex' : 'none';
    }
  });
}

function saveNewEvent() {
  const currentRole = getCurrentRole();
  if (currentRole !== 'superadmin') {
    showToast('Admin tidak dapat membuat event apapun. Hanya Super Admin yang berhak.', 'error');
    return;
  }

  const nameInput = document.getElementById('new-event-name');
  const startInput = document.getElementById('new-event-start');
  const endInput = document.getElementById('new-event-end');
  const descInput = document.getElementById('new-event-desc');
  const statusInput = document.getElementById('new-event-status');

  const name = nameInput ? nameInput.value.trim() : '';
  const start = startInput ? startInput.value : '';
  const end = endInput ? endInput.value : '';
  const desc = (descInput && descInput.value.trim()) ? descInput.value.trim() : 'Kegiatan utama organisasi.';
  const status = statusInput ? statusInput.value : 'Active';

  if (!name) {
    showToast('Nama event harus diisi', 'error');
    return;
  }

  const events = getEvents();
  const newId = events.length > 0 ? Math.max(...events.map(e => e.id)) + 1 : 1;
  const dateRange = (start && end) ? `${start} s/d ${end}` : 'September 2026';

  events.push({
    id: newId,
    name,
    desc,
    startDate: start || '10 Sep 2026',
    endDate: end || '20 Sep 2026',
    dateRange,
    totalPeserta: 50,
    status,
    subEvents: []
  });

  saveEvents(events);
  closeModal('modal-add-event');

  if (document.getElementById('event-grid')) {
    renderEvents();
  }
  if (document.getElementById('stat-total-events')) {
    renderDashboardStats();
  }
  showToast('Event Utama "' + name + '" berhasil dibuat!', 'success');
}

function editEventPrompt(id) {
  const events = getEvents();
  const ev = events.find(e => e.id === id);
  if (!ev) return;
  const newName = prompt('Edit Nama Event Utama:', ev.name);
  if (newName && newName.trim()) {
    ev.name = newName.trim();
    saveEvents(events);
    if (document.getElementById('event-grid')) renderEvents();
    if (document.getElementById('stat-total-events')) renderDashboardStats();
    if (document.getElementById('detail-hero-title')) renderEventDetail(id);
    showToast('Event Utama berhasil diperbarui', 'success');
  }
}

function deleteEvent(id) {
  if (!confirm('Hapus Event Utama ini beserta seluruh Sub Event di dalamnya?')) return;
  let events = getEvents();
  events = events.filter(e => e.id !== id);
  saveEvents(events);
  if (document.getElementById('event-grid')) renderEvents();
  if (document.getElementById('stat-total-events')) renderDashboardStats();
  showToast('Event Utama telah dihapus', 'info');
}

function renderEventDetail(eventId) {
  const events = getEvents();
  const ev = events.find(e => e.id === eventId);
  if (!ev) return;

  const currentRole = getCurrentRole();

  const bcName = document.getElementById('detail-breadcrumb-name');
  const heroTitle = document.getElementById('detail-hero-title');
  const heroDesc = document.getElementById('detail-hero-desc');
  const heroDate = document.getElementById('detail-hero-date');
  const heroStatus = document.getElementById('detail-hero-status');
  const heroTotal = document.getElementById('detail-hero-total-peserta');
  const heroSubCount = document.getElementById('detail-hero-sub-count');

  if (bcName) bcName.textContent = ev.name;
  if (heroTitle) heroTitle.textContent = ev.name;
  if (heroDesc) heroDesc.textContent = ev.desc;
  if (heroDate) heroDate.textContent = ev.dateRange;
  if (heroStatus) heroStatus.textContent = ev.status;
  if (heroTotal) heroTotal.textContent = ev.totalPeserta;
  if (heroSubCount) heroSubCount.textContent = (ev.subEvents || []).length;

  const btnAddSub = document.getElementById('btn-add-subevent');
  if (btnAddSub) btnAddSub.style.display = (currentRole === 'admin') ? 'inline-flex' : 'none';

  const tbody = document.getElementById('subevents-tbody');
  if (!tbody) return;
  tbody.innerHTML = '';

  const subEvents = ev.subEvents || [];
  if (subEvents.length === 0) {
    tbody.innerHTML = `<tr><td colspan="9" style="text-align:center;padding:30px;color:var(--slate-400);">Belum ada Sub Event. Silakan klik tombol "+ Tambah Sub Event".</td></tr>`;
    return;
  }

  subEvents.forEach(sub => {
    const pct = sub.total > 0 ? Math.round((sub.hadir / sub.total) * 100) : 0;
    const badgeCls = sub.status === 'Finished' ? 'badge-gray' : sub.status === 'Active' ? 'badge-blue' : 'badge-amber';

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
        <div style="display:flex;align-items:center;">
          <div class="attendance-progress-bar"><div class="attendance-progress-fill" style="width:${pct}%"></div></div>
          <span>${sub.hadir} / ${sub.total} (${pct}%)</span>
        </div>
      </td>
      <td><span class="badge ${badgeCls}">${sub.status}</span></td>
      <td>
        <div class="actions-cell">
          <a class="btn btn-outline btn-sm" href="subevent-detail.html?eventId=${ev.id}&subId=${sub.id}">
            <i class="fa-solid fa-clipboard-check"></i> Kelola Absensi
          </a>
          ${(currentRole === 'admin' || currentRole === 'superadmin') ? `
            <button class="action-link" onclick="editSubEventPrompt(${ev.id}, ${sub.id})">Edit</button>
            <button class="action-link danger" onclick="deleteSubEvent(${ev.id}, ${sub.id})">Hapus</button>
          ` : ''}
        </div>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

function openAddSubEventModal(targetEventId) {
  const currentRole = getCurrentRole();
  if (currentRole === 'superadmin') {
    showToast('Super Admin tidak diizinkan membuat Sub Event. Sub Event hanya dapat dibuat oleh Admin.', 'error');
    return;
  }
  if (currentRole === 'member') {
    showToast('Anggota tidak memiliki izin untuk membuat Sub Event.', 'error');
    return;
  }

  const events = getEvents();
  if (events.length === 0) {
    showToast('Belum ada Event Utama. Buat Event Utama terlebih dahulu.', 'error');
    return;
  }

  let activeEvId = targetEventId;
  if (!activeEvId) {
    const urlParams = new URLSearchParams(window.location.search);
    activeEvId = parseInt(urlParams.get('id') || urlParams.get('eventId'), 10) || events[0].id;
  }

  const select = document.getElementById('new-sub-parent-select');
  if (select) {
    select.innerHTML = '';
    events.forEach(ev => {
      const opt = document.createElement('option');
      opt.value = ev.id;
      opt.textContent = ev.name;
      if (ev.id === activeEvId) opt.selected = true;
      select.appendChild(opt);
    });
    select.disabled = false;
    select.style.background = 'var(--white)';
  }

  const subName = document.getElementById('new-sub-name');
  const subDate = document.getElementById('new-sub-date');
  const subLoc = document.getElementById('new-sub-loc');
  const subDesc = document.getElementById('new-sub-desc');

  if (subName) subName.value = '';
  if (subDate) subDate.value = '2026-09-18';
  if (subLoc) subLoc.value = 'Ruang Meeting A';
  if (subDesc) subDesc.value = '';

  openModal('modal-add-subevent');
}

function saveNewSubEvent() {
  const currentRole = getCurrentRole();
  if (currentRole === 'superadmin') {
    showToast('Super Admin tidak dapat membuat Sub Event. Hanya Admin yang berhak.', 'error');
    return;
  }

  const parentSelect = document.getElementById('new-sub-parent-select');
  const parentId = parentSelect ? parseInt(parentSelect.value, 10) : null;
  const events = getEvents();
  const ev = events.find(e => e.id === parentId);
  if (!ev) {
    showToast('Pilih Event Utama terlebih dahulu', 'error');
    return;
  }

  const nameInput = document.getElementById('new-sub-name');
  const dateInput = document.getElementById('new-sub-date');
  const timeStartInput = document.getElementById('new-sub-time-start');
  const timeEndInput = document.getElementById('new-sub-time-end');
  const locInput = document.getElementById('new-sub-loc');
  const picInput = document.getElementById('new-sub-pic');
  const statusInput = document.getElementById('new-sub-status');
  const descInput = document.getElementById('new-sub-desc');

  const name = nameInput ? nameInput.value.trim() : '';
  const dateVal = dateInput ? dateInput.value : '';
  const timeStart = timeStartInput ? timeStartInput.value || '19:00' : '19:00';
  const timeEnd = timeEndInput ? timeEndInput.value || '21:00' : '21:00';
  const loc = locInput ? locInput.value.trim() || 'Ruang Meeting' : 'Ruang Meeting';
  const pic = picInput ? picInput.value : 'Budi Santoso';
  const status = statusInput ? statusInput.value : 'Upcoming';
  const desc = descInput ? descInput.value.trim() : '';

  if (!name) {
    showToast('Nama Sub Event harus diisi', 'error');
    return;
  }

  if (!ev.subEvents) ev.subEvents = [];
  const newSubId = Date.now();
  ev.subEvents.push({
    id: newSubId,
    name,
    date: dateVal ? formatDateId(dateVal) : '18 Sep 2026',
    time: `${timeStart} - ${timeEnd}`,
    loc,
    pic,
    total: ev.totalPeserta || 40,
    hadir: 0,
    status,
    desc,
    participants: []
  });

  saveEvents(events);
  closeModal('modal-add-subevent');

  if (document.getElementById('subevents-tbody')) {
    renderEventDetail(ev.id);
  }
  if (document.getElementById('admin-managed-container')) {
    renderAdminDashboard();
  }
  if (document.getElementById('stat-total-subevents')) {
    renderDashboardStats();
  }

  showToast('Sub Event "' + name + '" berhasil ditambahkan ke ' + ev.name, 'success');
}

function formatDateId(dateStr) {
  const parts = dateStr.split('-');
  if (parts.length === 3) {
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    const mIdx = parseInt(parts[1], 10) - 1;
    return `${parts[2]} ${months[mIdx] || 'Sep'} ${parts[0]}`;
  }
  return dateStr;
}

function editSubEventPrompt(evId, subId) {
  const events = getEvents();
  const ev = events.find(e => e.id === evId);
  const sub = ev ? (ev.subEvents || []).find(s => s.id === subId) : null;
  if (!sub) return;

  const newName = prompt('Edit Nama Sub Event:', sub.name);
  if (newName && newName.trim()) {
    sub.name = newName.trim();
    saveEvents(events);
    if (document.getElementById('subevents-tbody')) renderEventDetail(evId);
    if (document.getElementById('admin-managed-container')) renderAdminDashboard();
    showToast('Sub Event berhasil diperbarui', 'success');
  }
}

function deleteSubEvent(evId, subId) {
  if (!confirm('Hapus Sub Event ini?')) return;
  const events = getEvents();
  const ev = events.find(e => e.id === evId);
  if (!ev) return;
  ev.subEvents = (ev.subEvents || []).filter(s => s.id !== subId);
  saveEvents(events);
  if (document.getElementById('subevents-tbody')) renderEventDetail(evId);
  if (document.getElementById('admin-managed-container')) renderAdminDashboard();
  if (document.getElementById('stat-total-subevents')) renderDashboardStats();
  showToast('Sub Event berhasil dihapus', 'info');
}
