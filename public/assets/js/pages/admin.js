// ============================================================
// pages/admin.js — Dashboard Admin / PIC (event yang dikelola)
// ============================================================

function renderAdminDashboard() {
  const container = document.getElementById('admin-managed-container');
  if (!container) return;
  container.innerHTML = '';

  let hasAny = false;

  let list = eventsData;
  if (Array.isArray(window.dbAdminEvents)) {
    list = window.dbAdminEvents.map(ev => {
      const match = eventsData.find(e => e.id === ev.id || e.name === ev.title);
      return {
        id: ev.id,
        name: ev.title || ev.name,
        desc: ev.description || ev.desc || '',
        status: ev.status || 'Active',
        totalPeserta: ev.member_count !== undefined ? parseInt(ev.member_count) : (match ? match.totalPeserta : 40),
        subEvents: match ? match.subEvents : (ev.subEvents || [])
      };
    });
  }

  list.forEach(ev => {
    const assignedSubs = Array.isArray(ev.subEvents)
      ? ev.subEvents.filter(s => currentRole === 'admin' || currentRole === 'superadmin' || (s.pic && s.pic.toLowerCase().includes('budi')))
      : [];

    hasAny = true;

    let subItemsHtml = '';
    if (assignedSubs.length === 0) {
      subItemsHtml = `
        <div style="padding: 16px; text-align: center; color: var(--slate-400); font-size: 13px;">
          Belum ada Sub Event. Silakan klik tombol <strong>"+ Tambah Sub Event"</strong> untuk menambah aktivitas.
        </div>
      `;
    } else {
      assignedSubs.forEach(s => {
        const statusCls = (s.status === 'done' || s.status === 'Finished') ? 'badge-gray' : (s.status === 'in progress' || s.status === 'Active') ? 'badge-green' : 'badge-amber';
        subItemsHtml += `
          <div class="managed-tree-item">
            <div>
              <div class="managed-sub-name">${s.name}</div>
              <div class="managed-sub-meta">
                <span><i class="fa-regular fa-calendar"></i> ${s.date}</span>
                <span><i class="fa-regular fa-clock"></i> ${s.time}</span>
                <span><i class="fa-solid fa-location-dot"></i> ${s.loc}</span>
                <span><i class="fa-solid fa-user-check"></i> ${s.hadir} / ${s.total} Hadir</span>
                <span class="badge ${statusCls}">${s.status}</span>
              </div>
            </div>
            <div style="display:flex;gap:8px;">
              <button class="btn btn-blue btn-sm" onclick="openSubEventDetail(${ev.id}, ${s.id})">
                <i class="fa-solid fa-qrcode"></i> Buka Sub Event & QR
              </button>
            </div>
          </div>
        `;
      });
    }

    const card = document.createElement('div');
    card.className = 'managed-tree-card';
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
          <button class="btn btn-outline btn-sm" onclick="openEventDetail(${ev.id})">
            <i class="fa-solid fa-eye"></i> Lihat Detail
          </button>
        </div>
      </div>
      <div class="managed-tree-sublist">${subItemsHtml}</div>
    `;
    container.appendChild(card);
  });

  if (!hasAny) {
    container.innerHTML = `<div class="empty-state"><i class="fa-solid fa-calendar-check"></i><p>Belum ada Event atau Sub Event yang ditugaskan kepada Anda. Klik tombol <strong>"+ Tambah Sub Event"</strong> di atas untuk membuat Sub Event baru.</p></div>`;
  }
}
