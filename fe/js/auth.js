function doLogin() {
  const roleSelect = document.getElementById('login-role');
  const role = roleSelect ? roleSelect.value : 'superadmin';
  setCurrentRole(role);
  window.location.href = 'dashboard.html';
}

function doLogout() {
  localStorage.removeItem('attendify_current_role');
  window.location.href = 'index.html';
}

function requireAuth() {
  const role = localStorage.getItem('attendify_current_role');
  if (!role) {
    window.location.href = 'index.html';
    return null;
  }
  return role;
}

function applyRoleUI(role) {
  const roleBadge = document.getElementById('topbar-role-badge');
  if (roleBadge && userInfo[role]) {
    roleBadge.textContent = userInfo[role].role;
  }

  const btnCreateMain = document.getElementById('btn-create-event-main');
  const btnCreateDash = document.getElementById('btn-create-event-dash');
  if (btnCreateMain) btnCreateMain.style.display = (role === 'superadmin') ? 'inline-flex' : 'none';
  if (btnCreateDash) btnCreateDash.style.display = (role === 'superadmin') ? 'inline-flex' : 'none';

  const btnAddSub = document.getElementById('btn-add-subevent');
  if (btnAddSub) btnAddSub.style.display = (role === 'admin') ? 'inline-flex' : 'none';

  const subBcRoot = document.getElementById('sub-bc-root');
  if (subBcRoot) {
    subBcRoot.textContent = (role === 'admin') ? 'Event Saya' : 'Event';
  }
}
