function buildSidebar(role, activePageId) {
  const nav = document.getElementById('sidebar-nav');
  if (!nav) return;
  nav.innerHTML = '';
  const items = navConfig[role] || [];

  const secTitle = document.createElement('div');
  secTitle.className = 'nav-section-title';
  secTitle.textContent = (role === 'superadmin') ? 'Super Admin Portal' : (role === 'admin' ? 'Admin / PIC Portal' : 'Member Portal');
  nav.appendChild(secTitle);

  items.forEach(item => {
    const el = document.createElement('a');
    el.className = 'nav-item' + (item.id === activePageId ? ' active' : '');
    el.href = item.url;
    el.style.textDecoration = 'none';
    el.innerHTML = `<i class="${item.icon}"></i><span>${item.label}</span>`;
    nav.appendChild(el);
  });
}

function setUserInfo(role) {
  const u = userInfo[role];
  if (!u) return;
  const sbName = document.getElementById('sb-name');
  const sbRole = document.getElementById('sb-role');
  const sbAvatar = document.getElementById('sb-avatar');
  const topAvatar = document.getElementById('top-avatar');

  if (sbName) sbName.textContent = u.name;
  if (sbRole) sbRole.textContent = u.role;
  if (sbAvatar) sbAvatar.textContent = u.initials;
  if (topAvatar) topAvatar.textContent = u.initials;
}

function toggleSidebar() {
  const sb = document.getElementById('sidebar');
  if (sb) sb.classList.toggle('open');
  const overlay = document.getElementById('sidebar-overlay');
  if (overlay) {
    overlay.style.display = overlay.style.display === 'block' ? 'none' : 'block';
  }
}

function closeSidebar() {
  const sb = document.getElementById('sidebar');
  if (sb) sb.classList.remove('open');
  const overlay = document.getElementById('sidebar-overlay');
  if (overlay) overlay.style.display = 'none';
}

function openModal(id) {
  const role = getCurrentRole();
  if (id === 'modal-add-event' && role !== 'superadmin') {
    showToast('Admin tidak dapat membuat Event Utama. Hanya Super Admin yang berhak.', 'error');
    return;
  }
  if (id === 'modal-add-subevent' && role === 'superadmin') {
    showToast('Super Admin tidak dapat membuat Sub Event. Sub Event hanya dapat dibuat oleh Admin.', 'error');
    return;
  }
  const el = document.getElementById(id);
  if (el) el.classList.add('open');
}

function closeModal(id) {
  const el = document.getElementById(id);
  if (el) el.classList.remove('open');
  if (id === 'modal-qr' && typeof qrTimerInterval !== 'undefined' && qrTimerInterval) {
    clearInterval(qrTimerInterval);
    qrTimerInterval = null;
  }
}

function showToast(msg, type = 'info') {
  let c = document.getElementById('toast-container');
  if (!c) {
    c = document.createElement('div');
    c.id = 'toast-container';
    document.body.appendChild(c);
  }
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
  }, 3200);
}

function initLayout(activePageId) {
  const role = requireAuth();
  if (!role) return;

  buildSidebar(role, activePageId);
  setUserInfo(role);
  applyRoleUI(role);

  document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function (e) {
      if (e.target === this) closeModal(this.id);
    });
  });
}
