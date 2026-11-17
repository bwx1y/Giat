// ============================================================
// router.js — Navigasi SPA: navTo, pageRoute, pageFromLocation
// ============================================================

function pageRoute(pageId) {
  if (pageId === 'dashboard' || pageId === 'admin-dashboard' || pageId === 'user-dashboard') {
    return '/dashboard';
  }
  if (pageId === 'event-detail') {
    return `/event/${encodeURIComponent(currentActiveEventId)}`;
  }
  if (pageId === 'subevent-detail') {
    return `/event/${encodeURIComponent(currentActiveEventId)}/subevent/${encodeURIComponent(currentActiveSubEventId)}`;
  }

  const routes = {
    anggota: '/users',
    event: '/event',
    absensi: '/absensi',
    statistik: '/statistik',
    'user-history': '/riwayat',
  };
  return routes[pageId] || '/dashboard';
}

function pageFromLocation() {
  const path = window.location.pathname.replace(/\/+$/, '') || '/';

  const subEventMatch = path.match(/^\/event\/(\d+)\/subevent\/(\d+)$/);
  if (subEventMatch) {
    currentActiveEventId = Number(subEventMatch[1]);
    currentActiveSubEventId = Number(subEventMatch[2]);
    return 'subevent-detail';
  }

  const eventMatch = path.match(/^\/event\/(\d+)$/);
  if (eventMatch) {
    currentActiveEventId = Number(eventMatch[1]);
    return 'event-detail';
  }

  if (path === '/dashboard') return defaultPage[currentRole];

  const routePages = {
    '/users': 'anggota',
    '/anggota': 'anggota',
    '/event': 'event',
    '/absensi': 'absensi',
    '/statistik': 'statistik',
    '/riwayat': 'user-history',
  };
  return routePages[path]
    || new URLSearchParams(window.location.search).get('page_view')
    || defaultPage[currentRole];
}

function navTo(pageId, historyMode = 'push') {
  if (pageId !== currentPage) {
    if (typeof stopQrEventStream === 'function') stopQrEventStream();
    if (typeof stopAttendanceQrScanner === 'function') stopAttendanceQrScanner();
  }

  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));

  const target = document.getElementById('page-' + pageId);
  if (!target) {
    // Halaman tidak ada di DOM (server hanya render page yang relevan dengan URL saat ini).
    // Lakukan hard navigation ke URL tujuan agar server me-render page yang benar.
    const route = pageRoute(pageId);
    if (historyMode !== 'replace' && window.location.pathname !== route) {
      window.location.href = route;
    }
    return;
  }
  target.classList.add('active');
  currentPage = pageId;

  const route = pageRoute(pageId);
  if (historyMode === 'replace') {
    window.history.replaceState({ pageId }, '', route);
  } else if (historyMode === 'push' && window.location.pathname !== route) {
    window.history.pushState({ pageId }, '', route);
  }

  document.getElementById('topbar-title').textContent = pageTitles[pageId] || 'GIAT';

  document.querySelectorAll('.nav-item').forEach(el => {
    el.classList.toggle('active', el.dataset.page === pageId);
  });

  if (pageId === 'dashboard')       { renderDashboardStats(); initDashboardChart(); }
  if (pageId === 'anggota')         { renderMembers(); }
  if (pageId === 'event')           { renderEvents(); }
  if (pageId === 'event-detail')    { renderEventDetail(currentActiveEventId); }
  if (pageId === 'subevent-detail') { renderSubEventDetail(currentActiveEventId, currentActiveSubEventId); }
  if (pageId === 'admin-dashboard') { renderAdminDashboard(); }
  if (pageId === 'absensi')         { renderAbsensiList(); }
  if (pageId === 'statistik')       { initStatCharts(); }
  if (pageId === 'user-dashboard')  { renderUserDashboard(); }
  if (pageId === 'user-history')    { renderUserHistory(); }
}

window.addEventListener('popstate', function () {
  navTo(pageFromLocation(), 'none');
});
