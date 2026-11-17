// ============================================================
// pages/event.js — Daftar event utama, CRUD event
// ============================================================

function renderEvents() {
  const grid = document.getElementById('event-grid');
  if (!grid) return;
  grid.innerHTML = '';

  let list = eventsData;
  if (Array.isArray(window.dbEvents) && window.dbEvents.length > 0) {
    list = window.dbEvents.map(ev => ({
      id: ev.id,
      name: ev.title || ev.name,
      desc: ev.description || ev.desc || 'Tidak ada deskripsi kegiatan.',
      dateRange: (ev.start_date && ev.end_date)
        ? (formatDateId(ev.start_date.split(' ')[0]) + ' s/d ' + formatDateId(ev.end_date.split(' ')[0]))
        : (ev.dateRange || 'Belum diatur'),
      totalPeserta: ev.member_count !== undefined ? parseInt(ev.member_count) : (ev.totalPeserta || 0),
      subEvents: ev.sub_event_count !== undefined ? { length: parseInt(ev.sub_event_count) } : (ev.subEvents || []),
      status: ev.status || 'Not started',
    }));
  } else if (Array.isArray(window.dbEvents) && window.dbEvents.length === 0
    && (window.location.search.includes('search=') || window.location.search.includes('status='))) {
    list = [];
  }

  if (list.length === 0) {
    grid.innerHTML = '<div class="text-navy" style="grid-column: 1 / -1; padding: 40px; text-align: center;">Tidak ada event yang ditemukan.</div>';
    return;
  }

  list.forEach(ev => {
    const statusCls = ev.status === 'Active' || ev.status === 'In progress' ? 'badge-blue'
      : ev.status === 'Upcoming' || ev.status === 'Not started' ? 'badge-amber'
      : ev.status === 'Cancelled' ? 'badge-red'
      : 'badge-green';

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
          <span class="subevent-pill-count"><i class="fa-solid fa-sitemap"></i> ${ev.subEvents.length || 0} Sub Event</span>
          <span class="badge ${statusCls} ms-1">${ev.status}</span>
        </div>
      </div>
      <div class="event-primary-footer">
        <div class="actions-cell">
          <button class="btn btn-blue btn-sm" onclick="openEventDetail(${ev.id})">
            <i class="fa-solid fa-eye"></i> Lihat Detail
          </button>
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
  const q = document.getElementById('search-event').value.toLowerCase();
  const st = document.getElementById('filter-event-status').value;
  const cards = document.querySelectorAll('.event-primary-card');

  eventsData.forEach((ev, idx) => {
    const matchQ = ev.name.toLowerCase().includes(q) || ev.desc.toLowerCase().includes(q);
    const matchSt = !st || ev.status === st;
    if (cards[idx]) {
      cards[idx].style.display = (matchQ && matchSt) ? 'flex' : 'none';
    }
  });
}

function saveNewEvent() {
  if (currentRole !== 'superadmin') {
    showToast('Admin tidak dapat membuat event apapun. Hanya Super Admin yang berhak.', 'error');
    return;
  }

  const name = document.getElementById('new-event-name').value.trim();
  const start = document.getElementById('new-event-start').value;
  const end = document.getElementById('new-event-end').value;
  const desc = document.getElementById('new-event-desc').value.trim() || 'Kegiatan utama organisasi.';
  const status = 'Not started';

  if (!name) { showToast('Nama event harus diisi', 'error'); return; }

  const newId = eventsData.length > 0 ? Math.max(...eventsData.map(e => e.id)) + 1 : 1;
  const dateRange = (start && end) ? `${start} s/d ${end}` : 'September 2026';

  eventsData.push({
    id: newId, name, desc,
    startDate: start || '10 Sep 2026',
    endDate: end || '20 Sep 2026',
    dateRange, totalPeserta: 50, status, subEvents: [],
  });

  closeModal('modal-add-event');
  renderEvents();
  renderDashboardStats();
  showToast('Event Utama "' + name + '" berhasil dibuat!', 'success');
}

function editEventPrompt(id) {
  const ev = Array.isArray(window.dbEvents)
    ? window.dbEvents.find(event => String(event.id) === String(id))
    : null;
  if (!ev) { showToast('Data event tidak ditemukan.', 'error'); return; }

  if (ev.status !== 'Not started') {
    showToast('Edit event hanya dapat dilakukan untuk event yang belum dimulai.', 'error');
    return;
  }

  const form = document.getElementById('form-edit-event');
  form.action = `/admin/event/${encodeURIComponent(id)}/update/handler`;
  form.dataset.eventId = id;
  document.getElementById('edit-event-title').value = ev.title || '';
  document.getElementById('edit-event-start').value = ev.start_date ? ev.start_date.slice(0, 10) : '';
  document.getElementById('edit-event-end').value = ev.end_date ? ev.end_date.slice(0, 10) : '';
  document.getElementById('edit-event-location').value = ev.location || '';
  document.getElementById('edit-event-description').value = ev.description || '';
  document.getElementById('edit-event-status').value = ev.status;

  openModal('modal-edit-event');
}

function deleteEvent(id) {
  const event = Array.isArray(window.dbEvents)
    ? window.dbEvents.find(item => String(item.id) === String(id))
    : null;
  if (!event) { showToast('Data event tidak ditemukan.', 'error'); return; }

  pendingDeleteEventId = id;
  document.getElementById('delete-event-title').textContent = event.title || event.name || `#${id}`;
  openModal('modal-delete-event');
}

async function confirmDeleteEvent() {
  if (pendingDeleteEventId === null) return;

  const confirmButton = document.getElementById('confirm-delete-event');
  confirmButton.disabled = true;

  try {
    const response = await fetch(`/admin/event/${encodeURIComponent(pendingDeleteEventId)}/delete`, {
      method: 'DELETE',
    });
    if (!response.ok) throw new Error(`HTTP ${response.status}`);
    closeModal('modal-delete-event');
    showToast('Event berhasil dihapus.', 'success');
    pendingDeleteEventId = null;
    setTimeout(() => window.location.reload(), 2000);
  } catch (error) {
    showToast(`Gagal menghapus event: ${error.message}`, 'error');
  } finally {
    confirmButton.disabled = false;
  }
}

function openEventDetail(eventId) {
  window.location.assign(`/event/${encodeURIComponent(eventId)}`);
}

function setEventCrudFlash(flash) {
  sessionStorage.setItem(eventCrudFlashStorageKey, JSON.stringify(flash));
}

function showEventCrudFlash() {
  const savedFlash = sessionStorage.getItem(eventCrudFlashStorageKey);
  if (!savedFlash) return false;

  sessionStorage.removeItem(eventCrudFlashStorageKey);
  const flash = JSON.parse(savedFlash);
  const event = Array.isArray(window.dbEvents)
    ? window.dbEvents.find(item => String(item.id) === String(flash.id))
    : null;

  if (flash.action === 'create') {
    const previousIds = Array.isArray(flash.previousIds) ? flash.previousIds : [];
    const createdEvent = Array.isArray(window.dbEvents)
      ? window.dbEvents.find(item => item.title === flash.title && !previousIds.includes(String(item.id)))
      : null;
    showToast(
      createdEvent ? 'Event berhasil dibuat.' : 'Penyimpanan event gagal. Periksa kembali data dan koneksi.',
      createdEvent ? 'success' : 'error',
    );
    return true;
  }

  if (flash.action === 'update') {
    const updated = event
      && event.title === flash.title
      && String(event.start_date || '').slice(0, 10) === flash.start_date
      && String(event.end_date || '').slice(0, 10) === flash.end_date
      && (event.location || '') === flash.location
      && (event.description || '') === flash.description
      && event.status === flash.status;
    showToast(
      updated ? 'Event berhasil diperbarui.' : 'Perubahan event tidak tersimpan.',
      updated ? 'success' : 'error',
    );
    return true;
  }

  if (flash.action === 'delete') {
    const deleted = !event;
    showToast(
      deleted ? 'Event berhasil dihapus.' : 'Event gagal dihapus.',
      deleted ? 'success' : 'error',
    );
    return true;
  }

  return false;
}
