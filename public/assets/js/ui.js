// ============================================================
// ui.js — Toast, modal, sidebar, topbar
// ============================================================

function showToast(msg, type = 'info') {
  const c = document.getElementById('toast-container');
  if (!c) return;
  const icon = { success: 'fa-circle-check', error: 'fa-circle-xmark', info: 'fa-circle-info' }[type] || 'fa-circle-info';
  const div = document.createElement('div');
  div.className = `toast toast-${type}`;
  div.innerHTML = `<i class="fa-solid ${icon}"></i> <span>${msg}</span>`;
  c.appendChild(div);
  setTimeout(() => {
    div.style.transition = 'opacity .3s, transform .3s';
    div.style.opacity = '0';
    div.style.transform = 'translateX(20px)';
    setTimeout(() => div.remove(), 300);
  }, 2300);
}

function openModal(id) {
  if (id === 'modal-add-event' && currentRole !== 'superadmin') {
    showToast('Admin tidak dapat membuat Event Utama. Hanya Super Admin yang berhak.', 'error');
    return;
  }
  if (id === 'modal-add-subevent' && currentRole === 'superadmin') {
    showToast('Super Admin tidak dapat membuat Sub Event. Sub Event hanya dapat dibuat oleh Admin.', 'error');
    return;
  }
  const el = document.getElementById(id);
  if (el) el.classList.add('open');
}

function closeModal(id) {
  const el = document.getElementById(id);
  if (el) el.classList.remove('open');
  if (id === 'modal-qr') {
    stopQrEventStream();
  }
  if (id === 'modal-scanner') {
    stopAttendanceQrScanner();
  }
}

function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('open');
  const overlay = document.getElementById('sidebar-overlay');
  overlay.style.display = overlay.style.display === 'block' ? 'none' : 'block';
}

function closeSidebar() {
  document.getElementById('sidebar').classList.remove('open');
  document.getElementById('sidebar-overlay').style.display = 'none';
}

function applyRoleUI(role) {
  const roleBadge = document.getElementById('topbar-role-badge');
  roleBadge.textContent = userInfo[role].role;

  const btnCreateMain = document.getElementById('btn-create-event-main');
  const btnCreateDash = document.getElementById('btn-create-event-dash');
  if (btnCreateMain) btnCreateMain.style.display = (role === 'superadmin') ? 'inline-flex' : 'none';
  if (btnCreateDash) btnCreateDash.style.display = (role === 'superadmin') ? 'inline-flex' : 'none';

  // Tombol di event detail: superadmin → Tambah Peserta, admin → Tambah Sub Event
  const btnAddSub = document.getElementById('btn-add-subevent');
  const btnAddMember = document.getElementById('btn-add-member-event');
  if (btnAddSub) btnAddSub.style.display = (role === 'admin') ? 'inline-flex' : 'none';
  if (btnAddMember) btnAddMember.style.display = (role === 'superadmin') ? 'inline-flex' : 'none';

  const subBcRoot = document.getElementById('sub-bc-root');
  if (subBcRoot) {
    subBcRoot.textContent = (role === 'admin') ? 'Event Saya' : 'Event';
  }
}

function buildSidebar(role) {
  const nav = document.getElementById('sidebar-nav');
  nav.innerHTML = '';
  const items = navConfig[role] || [];

  const secTitle = document.createElement('div');
  secTitle.className = 'nav-section-title';
  secTitle.textContent = (role === 'superadmin')
    ? 'Super Admin Portal'
    : (role === 'admin' ? 'Admin / PIC Portal' : 'Member Portal');
  nav.appendChild(secTitle);

  items.forEach(item => {
    const el = document.createElement('div');
    el.className = 'nav-item';
    el.dataset.page = item.id;
    el.innerHTML = `<i class="${item.icon}"></i><span>${item.label}</span>`;
    el.onclick = () => { navTo(item.id); closeSidebar(); };
    nav.appendChild(el);
  });
}

function setUserInfo(role) {
  const u = userInfo[role];
  document.getElementById('sb-name').textContent = u.name;
  document.getElementById('sb-role').textContent = u.role;
  document.getElementById('sb-avatar').textContent = u.initials;
  document.getElementById('top-avatar').textContent = u.initials;
}

function doLogout() {
  window.location.href = '/logout';
}

// Prototype function — login ditangani PHP, tidak digunakan di production
function doLogin() {
  const role = document.getElementById('login-role')?.value;
  if (!role) return;
  currentRole = role;
  document.getElementById('login-page').style.display = 'none';
  document.getElementById('app-shell').style.display = 'flex';
  applyRoleUI(role);
  buildSidebar(role);
  setUserInfo(role);
  navTo(defaultPage[role]);
  showToast('Masuk sebagai ' + userInfo[role].name + ' (' + userInfo[role].role + ')', 'success');
}
