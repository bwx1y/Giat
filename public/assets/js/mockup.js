let currentRole = 'superadmin';
    let currentPage = 'dashboard';
    let currentActiveEventId = 1;
    let currentActiveSubEventId = 101;
    let qrTimerInterval = null;
    let chartInstances = {};

    let members = [
      { id: 'A001', name: 'Andi Pratama', div: 'Divisi Teknologi', hadir: 16, absen: 2, point: 46, status: 'Aktif' },
      { id: 'A002', name: 'Rizky Ramadhan', div: 'Divisi Kreatif', hadir: 15, absen: 3, point: 42, status: 'Aktif' },
      { id: 'A003', name: 'Siti Aulia', div: 'Divisi Humas', hadir: 14, absen: 4, point: 38, status: 'Aktif' },
      { id: 'A004', name: 'Fajar Nugraha', div: 'Divisi Teknologi', hadir: 13, absen: 5, point: 34, status: 'Aktif' },
      { id: 'A005', name: 'Dina Maharani', div: 'Divisi Keuangan', hadir: 12, absen: 6, point: 30, status: 'Aktif' },
      { id: 'A006', name: 'Bimo Santoso', div: 'Divisi Akademik', hadir: 11, absen: 7, point: 26, status: 'Aktif' },
      { id: 'A007', name: 'Layla Sari', div: 'Divisi Humas', hadir: 10, absen: 8, point: 22, status: 'Non-Aktif' },
      { id: 'A008', name: 'Hendra Kurnia', div: 'Divisi Kreatif', hadir: 9, absen: 9, point: 18, status: 'Non-Aktif' },
    ];

    let eventsData = [
      {
        id: 1,
        name: 'Webinar Nasional 2026',
        desc: 'Webinar berskala nasional membahas tren kecerdasan buatan dan inovasi teknologi terkini.',
        startDate: '10 Sep 2026',
        endDate: '20 Sep 2026',
        dateRange: '10 - 20 September 2026',
        loc: 'Hybrid (Aula Utama & Zoom)',
        totalPeserta: 120,
        status: 'Active',
        subEvents: [
          {
            id: 101,
            name: 'Rapat Persiapan 1',
            date: '12 Sep 2026',
            time: '19:00 - 21:00',
            loc: 'Ruang Meeting A',
            pic: 'Budi Santoso',
            total: 40,
            hadir: 38,
            status: 'Finished',
            desc: 'Koordinasi awal pembagian divisi dan teknis acara.',
            participants: [
              { name: 'Andi Pratama', div: 'Divisi Teknologi', status: 'Hadir', time: '19:04', method: 'QR', point: 3 },
              { name: 'Rizky Ramadhan', div: 'Divisi Kreatif', status: 'Hadir', time: '19:07', method: 'QR', point: 3 },
              { name: 'Siti Aulia', div: 'Divisi Humas', status: 'Hadir', time: '19:12', method: 'QR', point: 3 },
              { name: 'Fajar Nugraha', div: 'Divisi Teknologi', status: 'Hadir', time: '19:15', method: 'Manual', point: 3 },
              { name: 'Dina Maharani', div: 'Divisi Keuangan', status: 'Tidak Hadir', time: '—', method: '—', point: -1 },
              { name: 'Bimo Santoso', div: 'Divisi Akademik', status: 'Tidak Hadir', time: '—', method: '—', point: -1 },
            ]
          },
          {
            id: 102,
            name: 'Rapat Persiapan 2',
            date: '14 Sep 2026',
            time: '19:00 - 21:00',
            loc: 'Ruang Meeting A',
            pic: 'Budi Santoso',
            total: 40,
            hadir: 35,
            status: 'Finished',
            desc: 'Finalisasi rundown, konsumsi, dan kesiapan pembicara.',
            participants: [
              { name: 'Andi Pratama', div: 'Divisi Teknologi', status: 'Hadir', time: '19:02', method: 'QR', point: 3 },
              { name: 'Rizky Ramadhan', div: 'Divisi Kreatif', status: 'Hadir', time: '19:05', method: 'QR', point: 3 },
              { name: 'Siti Aulia', div: 'Divisi Humas', status: 'Tidak Hadir', time: '—', method: '—', point: -1 },
              { name: 'Fajar Nugraha', div: 'Divisi Teknologi', status: 'Hadir', time: '19:10', method: 'QR', point: 3 },
            ]
          },
          {
            id: 103,
            name: 'Technical Meeting',
            date: '17 Sep 2026',
            time: '18:00 - 20:00',
            loc: 'Zoom Meeting',
            pic: 'Andi Pratama',
            total: 100,
            hadir: 0,
            status: 'Upcoming',
            desc: 'Uji coba platform streaming dan gladi kotor panitia.',
            participants: [
              { name: 'Andi Pratama', div: 'Divisi Teknologi', status: 'Belum Hadir', time: '—', method: '—', point: 0 },
              { name: 'Rizky Ramadhan', div: 'Divisi Kreatif', status: 'Belum Hadir', time: '—', method: '—', point: 0 },
              { name: 'Siti Aulia', div: 'Divisi Humas', status: 'Belum Hadir', time: '—', method: '—', point: 0 },
            ]
          },
          {
            id: 104,
            name: 'Pelaksanaan Webinar',
            date: '20 Sep 2026',
            time: '08:00 - 13:00',
            loc: 'Aula Utama & Live Stream',
            pic: 'Rizky Ramadhan',
            total: 120,
            hadir: 0,
            status: 'Upcoming',
            desc: 'Hari H pelaksanaan sesi utama bersama narasumber nasional.',
            participants: []
          },
          {
            id: 105,
            name: 'Rapat Evaluasi',
            date: '22 Sep 2026',
            time: '19:00 - 21:00',
            loc: 'Ruang Meeting B',
            pic: 'Budi Santoso',
            total: 35,
            hadir: 0,
            status: 'Upcoming',
            desc: 'Laporan pertanggungjawaban divisi dan review kegiatan.',
            participants: []
          }
        ]
      },
      {
        id: 2,
        name: 'Workshop UI/UX',
        desc: 'Pelatihan intensif perancangan antarmuka dan pengalaman pengguna modern berbasis Figma.',
        startDate: '23 Agu 2026',
        endDate: '30 Agu 2026',
        dateRange: '23 - 30 Agustus 2026',
        loc: 'Lab Komputer 3 & Online',
        totalPeserta: 40,
        status: 'Finished',
        subEvents: [
          {
            id: 201,
            name: 'Briefing',
            date: '23 Agu 2026',
            time: '16:00 - 18:00',
            loc: 'Google Meet',
            pic: 'Andi Pratama',
            total: 40,
            hadir: 38,
            status: 'Finished',
            desc: 'Pengenalan kurikulum dan instalasi tools Figma.',
            participants: [
              { name: 'Andi Pratama', div: 'Divisi Teknologi', status: 'Hadir', time: '16:05', method: 'QR', point: 3 },
              { name: 'Siti Aulia', div: 'Divisi Humas', status: 'Hadir', time: '16:10', method: 'QR', point: 3 },
            ]
          },
          {
            id: 202,
            name: 'Technical Meeting',
            date: '24 Agu 2026',
            time: '19:00 - 20:30',
            loc: 'Zoom Meeting',
            pic: 'Budi Santoso',
            total: 40,
            hadir: 37,
            status: 'Finished',
            desc: 'Pengecekan akun dan pembagian kelompok studi kasus.',
            participants: [
              { name: 'Andi Pratama', div: 'Divisi Teknologi', status: 'Hadir', time: '19:03', method: 'QR', point: 3 },
              { name: 'Rizky Ramadhan', div: 'Divisi Kreatif', status: 'Hadir', time: '19:04', method: 'QR', point: 3 },
            ]
          },
          {
            id: 203,
            name: 'Workshop Day 1',
            date: '27 Agu 2026',
            time: '09:00 - 15:00',
            loc: 'Lab Komputer 3',
            pic: 'Andi Pratama',
            total: 40,
            hadir: 36,
            status: 'Finished',
            desc: 'Sesi Design Thinking, User Persona, dan Wireframing.',
            participants: [
              { name: 'Andi Pratama', div: 'Divisi Teknologi', status: 'Hadir', time: '09:02', method: 'QR', point: 3 },
            ]
          },
          {
            id: 204,
            name: 'Workshop Day 2',
            date: '28 Agu 2026',
            time: '09:00 - 15:00',
            loc: 'Lab Komputer 3',
            pic: 'Andi Pratama',
            total: 40,
            hadir: 35,
            status: 'Finished',
            desc: 'Interactive Prototyping & Usability Testing.',
            participants: [
              { name: 'Andi Pratama', div: 'Divisi Teknologi', status: 'Hadir', time: '09:05', method: 'QR', point: 3 },
            ]
          },
          {
            id: 205,
            name: 'Evaluasi',
            date: '30 Agu 2026',
            time: '14:00 - 16:00',
            loc: 'Google Meet',
            pic: 'Rina Wijaya',
            total: 40,
            hadir: 34,
            status: 'Finished',
            desc: 'Presentasi hasil karya dan pemberian feedback juri.',
            participants: []
          }
        ]
      },
      {
        id: 3,
        name: 'Musyawarah Organisasi',
        desc: 'Forum tahunan musyawarah pembahasan AD/ART dan suksesi kepemimpinan.',
        startDate: '28 Sep 2026',
        endDate: '05 Okt 2026',
        dateRange: '28 Sep - 05 Okt 2026',
        loc: 'Gedung Serbaguna Kampus',
        totalPeserta: 85,
        status: 'Upcoming',
        subEvents: [
          {
            id: 301,
            name: 'Rapat Persiapan',
            date: '28 Sep 2026',
            time: '19:00 - 21:00',
            loc: 'Ruang Sidang 1',
            pic: 'Hendra Kurnia',
            total: 25,
            hadir: 0,
            status: 'Upcoming',
            desc: 'Penyusunan tata tertib dan materi musyawarah.',
            participants: []
          },
          {
            id: 302,
            name: 'Musyawarah Pleno',
            date: '03 Okt 2026',
            time: '08:00 - 17:00',
            loc: 'Gedung Serbaguna',
            pic: 'Budi Santoso',
            total: 85,
            hadir: 0,
            status: 'Upcoming',
            desc: 'Sidang pleno pembahasan AD/ART dan pemilihan ketua.',
            participants: []
          },
          {
            id: 303,
            name: 'Evaluasi & Serah Terima',
            date: '05 Okt 2026',
            time: '13:00 - 16:00',
            loc: 'Ruang Sidang 1',
            pic: 'Rina Wijaya',
            total: 40,
            hadir: 0,
            status: 'Upcoming',
            desc: 'Penandatanganan berita acara dan serah terima jabatan.',
            participants: []
          }
        ]
      }
    ];

    let attendanceHistory = [
      { id: 1, member: 'Andi Pratama', event: 'Webinar Nasional 2026', subEvent: 'Rapat Persiapan 1', date: '12 Sep 2026', status: 'Hadir', method: 'QR', point: 3 },
      { id: 2, member: 'Andi Pratama', event: 'Webinar Nasional 2026', subEvent: 'Rapat Persiapan 2', date: '14 Sep 2026', status: 'Hadir', method: 'QR', point: 3 },
      { id: 3, member: 'Andi Pratama', event: 'Webinar Nasional 2026', subEvent: 'Technical Meeting', date: '17 Sep 2026', status: 'Tidak Hadir', method: '—', point: -1 },
      { id: 4, member: 'Rizky Ramadhan', event: 'Webinar Nasional 2026', subEvent: 'Rapat Persiapan 1', date: '12 Sep 2026', status: 'Hadir', method: 'QR', point: 3 },
      { id: 5, member: 'Siti Aulia', event: 'Webinar Nasional 2026', subEvent: 'Rapat Persiapan 1', date: '12 Sep 2026', status: 'Hadir', method: 'QR', point: 3 },
      { id: 6, member: 'Fajar Nugraha', event: 'Webinar Nasional 2026', subEvent: 'Rapat Persiapan 1', date: '12 Sep 2026', status: 'Hadir', method: 'Manual', point: 3 },
      { id: 7, member: 'Dina Maharani', event: 'Webinar Nasional 2026', subEvent: 'Rapat Persiapan 1', date: '12 Sep 2026', status: 'Tidak Hadir', method: '—', point: -1 },
      { id: 8, member: 'Andi Pratama', event: 'Workshop UI/UX', subEvent: 'Briefing', date: '23 Agu 2026', status: 'Hadir', method: 'QR', point: 3 },
      { id: 9, member: 'Andi Pratama', event: 'Workshop UI/UX', subEvent: 'Technical Meeting', date: '24 Agu 2026', status: 'Hadir', method: 'QR', point: 3 },
      { id: 10, member: 'Andi Pratama', event: 'Workshop UI/UX', subEvent: 'Workshop Day 1', date: '27 Agu 2026', status: 'Hadir', method: 'QR', point: 3 },
      { id: 11, member: 'Andi Pratama', event: 'Workshop UI/UX', subEvent: 'Workshop Day 2', date: '28 Agu 2026', status: 'Hadir', method: 'QR', point: 3 },
    ];

    const navConfig = {
      superadmin: [
        { id: 'dashboard', label: 'Dashboard', icon: 'fa-solid fa-gauge-high' },
        { id: 'anggota', label: 'Anggota', icon: 'fa-solid fa-users' },
        { id: 'event', label: 'Event', icon: 'fa-solid fa-calendar-days' },
        { id: 'absensi', label: 'Absensi', icon: 'fa-solid fa-clipboard-list' },
        { id: 'statistik', label: 'Statistik', icon: 'fa-solid fa-chart-bar' },
      ],
      admin: [
        { id: 'admin-dashboard', label: 'Event Saya', icon: 'fa-solid fa-list-check' },
        { id: 'event', label: 'Daftar Event', icon: 'fa-solid fa-calendar-days' },
        { id: 'absensi', label: 'Rekap Absensi', icon: 'fa-solid fa-clipboard-list' },
      ],
      member: [
        { id: 'user-dashboard', label: 'Dashboard', icon: 'fa-solid fa-gauge-high' },
        { id: 'user-history', label: 'Riwayat', icon: 'fa-solid fa-clock-rotate-left' },
      ],
    };

    const userInfo = {
      superadmin: { name: 'Super Admin', role: 'Super Admin', initials: 'SA' },
      admin: { name: 'Budi Santoso', role: 'Admin / PIC', initials: 'BS' },
      member: { name: 'Andi Pratama', role: 'Anggota', initials: 'AP' },
    };

    const defaultPage = {
      superadmin: 'dashboard',
      admin: 'admin-dashboard',
      member: 'user-dashboard'
    };

    const pageTitles = {
      dashboard: 'Dashboard Super Admin',
      anggota: 'Manajemen Data Anggota',
      event: 'Manajemen Event Utama',
      'event-detail': 'Detail Event & Sub Event',
      'subevent-detail': 'Detail Sub Event & Absensi',
      'admin-dashboard': 'Event yang Saya Kelola',
      absensi: 'Rekap Seluruh Absensi',
      statistik: 'Statistik & Laporan',
      'user-dashboard': 'Dashboard Anggota',
      'user-history': 'Riwayat Kehadiran & Point',
    };

    function doLogin() {
      const role = document.getElementById('login-role').value;
      currentRole = role;
      document.getElementById('login-page').style.display = 'none';
      document.getElementById('app-shell').style.display = 'flex';

      applyRoleUI(role);
      buildSidebar(role);
      setUserInfo(role);
      navTo(defaultPage[role]);

      showToast('Masuk sebagai ' + userInfo[role].name + ' (' + userInfo[role].role + ')', 'success');
    }

    function doLogout() {
      window.location.href = '/logout';
    }

    function applyRoleUI(role) {

      const roleBadge = document.getElementById('topbar-role-badge');
      roleBadge.textContent = userInfo[role].role;

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

    function buildSidebar(role) {
      const nav = document.getElementById('sidebar-nav');
      nav.innerHTML = '';
      const items = navConfig[role] || [];

      const secTitle = document.createElement('div');
      secTitle.className = 'nav-section-title';
      secTitle.textContent = (role === 'superadmin') ? 'Super Admin Portal' : (role === 'admin' ? 'Admin / PIC Portal' : 'Member Portal');
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

    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('open');
      const overlay = document.getElementById('sidebar-overlay');
      overlay.style.display = overlay.style.display === 'block' ? 'none' : 'block';
    }

    function closeSidebar() {
      document.getElementById('sidebar').classList.remove('open');
      document.getElementById('sidebar-overlay').style.display = 'none';
    }

    function navTo(pageId) {
      document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));

      const target = document.getElementById('page-' + pageId);
      if (!target) return;
      target.classList.add('active');
      currentPage = pageId;

      document.getElementById('topbar-title').textContent = pageTitles[pageId] || 'GIAT';

      document.querySelectorAll('.nav-item').forEach(el => {
        el.classList.toggle('active', el.dataset.page === pageId);
      });

      if (pageId === 'dashboard') {
        renderDashboardStats();
        initDashboardChart();
      }
      if (pageId === 'anggota') {
        renderMembers(members);
      }
      if (pageId === 'event') {
        renderEvents();
      }
      if (pageId === 'event-detail') {
        renderEventDetail(currentActiveEventId);
      }
      if (pageId === 'subevent-detail') {
        renderSubEventDetail(currentActiveEventId, currentActiveSubEventId);
      }
      if (pageId === 'admin-dashboard') {
        renderAdminDashboard();
      }
      if (pageId === 'absensi') {
        renderAbsensiList();
      }
      if (pageId === 'statistik') {
        initStatCharts();
      }
      if (pageId === 'user-dashboard') {
        renderUserDashboard();
      }
      if (pageId === 'user-history') {
        renderUserHistory();
      }
    }

    function renderDashboardStats() {
      let totalSubs = 0;
      eventsData.forEach(ev => totalSubs += ev.subEvents.length);
      document.getElementById('stat-total-events').textContent = eventsData.length;
      document.getElementById('stat-total-subevents').textContent = totalSubs;

      const topContainer = document.getElementById('dashboard-top-members');
      topContainer.innerHTML = '';
      const sorted = [...members].sort((a, b) => b.point - a.point).slice(0, 5);
      sorted.forEach((m, idx) => {
        const row = document.createElement('div');
        row.style.cssText = 'display:flex;align-items:center;gap:12px;padding:9px 0;border-bottom:1px solid var(--slate-100);';
        row.innerHTML = `
      <div style="width:24px;height:24px;border-radius:50%;background:${idx === 0 ? '#fef3c7' : idx === 1 ? '#e2e8f0' : '#f1f5f9'};color:${idx === 0 ? '#d97706' : 'var(--navy)'};font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;">${idx + 1}</div>
      <div style="flex:1;">
        <div style="font-weight:600;font-size:13.5px;color:var(--navy);">${m.name}</div>
        <div style="font-size:11px;color:var(--slate-400);">${m.div} · Hadir: ${m.hadir} sesi</div>
      </div>
      <div style="font-size:14px;font-weight:800;color:var(--blue-600);">${m.point} pt</div>
    `;
        topContainer.appendChild(row);
      });

      const tbody = document.getElementById('dashboard-events-tbody');
      tbody.innerHTML = '';
      eventsData.forEach(ev => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
      <td><strong>${ev.name}</strong></td>
      <td>${ev.dateRange}</td>
      <td><span class="subevent-tree-badge"><i class="fa-solid fa-sitemap"></i> ${ev.subEvents.length} Sub Event</span></td>
      <td>${ev.totalPeserta} Peserta</td>
      <td><span class="badge ${ev.status === 'Active' ? 'badge-blue' : ev.status === 'Upcoming' ? 'badge-amber' : 'badge-gray'}">${ev.status}</span></td>
      <td>
        <button class="action-link" onclick="openEventDetail(${ev.id})">Lihat Detail</button>
      </td>
    `;
        tbody.appendChild(tr);
      });
    }

    function renderEvents() {
      const grid = document.getElementById('event-grid');
      grid.innerHTML = '';

      eventsData.forEach(ev => {
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
          <span class="subevent-pill-count"><i class="fa-solid fa-sitemap"></i> ${ev.subEvents.length} Sub Event</span>
          <span class="badge ${ev.status === 'Active' ? 'badge-blue' : ev.status === 'Upcoming' ? 'badge-amber' : 'badge-gray'}" style="margin-left:6px;">${ev.status}</span>
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
      const status = document.getElementById('new-event-status').value;

      if (!name) { showToast('Nama event harus diisi', 'error'); return; }

      const newId = eventsData.length > 0 ? Math.max(...eventsData.map(e => e.id)) + 1 : 1;
      const dateRange = (start && end) ? `${start} s/d ${end}` : 'September 2026';

      eventsData.push({
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

      closeModal('modal-add-event');
      renderEvents();
      renderDashboardStats();
      showToast('Event Utama "' + name + '" berhasil dibuat!', 'success');
    }

    function editEventPrompt(id) {
      const ev = eventsData.find(e => e.id === id);
      if (!ev) return;
      const newName = prompt('Edit Nama Event Utama:', ev.name);
      if (newName && newName.trim()) {
        ev.name = newName.trim();
        renderEvents();
        renderDashboardStats();
        showToast('Event Utama berhasil diperbarui', 'success');
      }
    }

    function deleteEvent(id) {
      if (!confirm('Hapus Event Utama ini beserta seluruh Sub Event di dalamnya?')) return;
      eventsData = eventsData.filter(e => e.id !== id);
      renderEvents();
      renderDashboardStats();
      showToast('Event Utama telah dihapus', 'info');
    }

    function openEventDetail(eventId) {
      currentActiveEventId = eventId;
      navTo('event-detail');
    }

    function renderEventDetail(eventId) {
      const ev = eventsData.find(e => e.id === eventId);
      if (!ev) return;

      document.getElementById('detail-breadcrumb-name').textContent = ev.name;

      document.getElementById('detail-hero-title').textContent = ev.name;
      document.getElementById('detail-hero-desc').textContent = ev.desc;
      document.getElementById('detail-hero-date').textContent = ev.dateRange;
      document.getElementById('detail-hero-status').textContent = ev.status;
      document.getElementById('detail-hero-total-peserta').textContent = ev.totalPeserta;
      document.getElementById('detail-hero-sub-count').textContent = ev.subEvents.length;

      const btnAddSub = document.getElementById('btn-add-subevent');
      if (btnAddSub) btnAddSub.style.display = (currentRole === 'admin') ? 'inline-flex' : 'none';

      const tbody = document.getElementById('subevents-tbody');
      tbody.innerHTML = '';

      if (ev.subEvents.length === 0) {
        tbody.innerHTML = `<tr><td colspan="9" style="text-align:center;padding:30px;color:var(--slate-400);">Belum ada Sub Event. Silakan klik tombol "+ Tambah Sub Event".</td></tr>`;
        return;
      }

      ev.subEvents.forEach((sub, sIdx) => {
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
          <button class="btn btn-outline btn-sm" onclick="openSubEventDetail(${ev.id}, ${sub.id})">
            <i class="fa-solid fa-clipboard-check"></i> Kelola Absensi
          </button>
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
      if (currentRole === 'superadmin') {
        showToast('Super Admin tidak diizinkan membuat Sub Event. Sub Event hanya dapat dibuat oleh Admin.', 'error');
        return;
      }
      if (currentRole === 'member') {
        showToast('Anggota tidak memiliki izin untuk membuat Sub Event.', 'error');
        return;
      }

      if (targetEventId) {
        currentActiveEventId = targetEventId;
      } else if (!currentActiveEventId && eventsData.length > 0) {
        currentActiveEventId = eventsData[0].id;
      }

      const select = document.getElementById('new-sub-parent-select');
      select.innerHTML = '';
      eventsData.forEach(ev => {
        const opt = document.createElement('option');
        opt.value = ev.id;
        opt.textContent = ev.name;
        if (ev.id === currentActiveEventId) opt.selected = true;
        select.appendChild(opt);
      });
      select.disabled = false;
      select.style.background = 'var(--white)';
      select.onchange = (e) => {
        currentActiveEventId = parseInt(e.target.value, 10);
      };

      document.getElementById('new-sub-name').value = '';
      document.getElementById('new-sub-date').value = '2026-09-18';
      document.getElementById('new-sub-loc').value = 'Ruang Meeting A';
      document.getElementById('new-sub-desc').value = '';

      openModal('modal-add-subevent');
    }

    function saveNewSubEvent() {
      if (currentRole === 'superadmin') {
        showToast('Super Admin tidak dapat membuat Sub Event. Hanya Admin yang berhak.', 'error');
        return;
      }

      const parentSelect = document.getElementById('new-sub-parent-select');
      const parentId = parseInt(parentSelect.value, 10) || currentActiveEventId;
      const ev = eventsData.find(e => e.id === parentId);
      if (!ev) { showToast('Pilih Event Utama terlebih dahulu', 'error'); return; }

      const name = document.getElementById('new-sub-name').value.trim();
      const dateVal = document.getElementById('new-sub-date').value;
      const timeStart = document.getElementById('new-sub-time-start').value || '19:00';
      const timeEnd = document.getElementById('new-sub-time-end').value || '21:00';
      const loc = document.getElementById('new-sub-loc').value.trim() || 'Ruang Meeting';
      const pic = document.getElementById('new-sub-pic').value;
      const status = document.getElementById('new-sub-status').value;
      const desc = document.getElementById('new-sub-desc').value.trim();

      if (!name) { showToast('Nama Sub Event harus diisi', 'error'); return; }

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

      closeModal('modal-add-subevent');
      if (currentPage === 'event-detail') {
        renderEventDetail(ev.id);
      }
      if (currentPage === 'admin-dashboard') {
        renderAdminDashboard();
      }
      renderDashboardStats();
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

    function renderSubEventDetail(evId, subId) {
      const ev = eventsData.find(e => e.id === evId);
      if (!ev) return;
      const sub = ev.subEvents.find(s => s.id === subId);
      if (!sub) return;

      document.getElementById('sub-bc-parent').textContent = ev.name;
      document.getElementById('sub-bc-current').textContent = sub.name;

      document.getElementById('sub-info-parent').textContent = ev.name;
      document.getElementById('sub-info-title').textContent = sub.name;
      document.getElementById('sub-info-date').textContent = sub.date;
      document.getElementById('sub-info-time').textContent = sub.time;
      document.getElementById('sub-info-loc').textContent = sub.loc;
      document.getElementById('sub-info-pic').textContent = sub.pic;
      document.getElementById('sub-info-status').textContent = sub.status;
      document.getElementById('sub-info-status').className = 'badge ' + (sub.status === 'Finished' ? 'badge-gray' : sub.status === 'Active' ? 'badge-green' : 'badge-amber');

      const total = sub.total || 40;
      const hadir = sub.hadir || 0;
      const absen = Math.max(0, total - hadir);
      const pct = total > 0 ? Math.round((hadir / total) * 100) : 0;

      document.getElementById('sub-stat-total').textContent = total;
      document.getElementById('sub-stat-hadir').textContent = hadir;
      document.getElementById('sub-stat-absen').textContent = absen;
      document.getElementById('sub-stat-pct').textContent = pct + '%';

      renderSubParticipants(sub);
    }

    function renderSubParticipants(sub) {
      const tbody = document.getElementById('sub-participants-tbody');
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
      const ev = eventsData.find(e => e.id === currentActiveEventId);
      const sub = ev ? ev.subEvents.find(s => s.id === currentActiveSubEventId) : null;
      if (!sub || !sub.participants[idx]) return;

      const now = new Date();
      const time = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');

      sub.participants[idx].status = 'Hadir';
      sub.participants[idx].time = time;
      sub.participants[idx].method = 'Manual';
      sub.participants[idx].point = 3;
      sub.hadir = (sub.hadir || 0) + 1;

      attendanceHistory.unshift({
        id: Date.now(),
        member: sub.participants[idx].name,
        event: ev.name,
        subEvent: sub.name,
        date: sub.date,
        status: 'Hadir',
        method: 'Manual',
        point: 3
      });

      renderSubEventDetail(ev.id, sub.id);
      showToast(sub.participants[idx].name + ' berhasil diabsen secara manual (+3 Point)', 'success');
    }

    function koreksiAbsenSubParticipant(idx) {
      const ev = eventsData.find(e => e.id === currentActiveEventId);
      const sub = ev ? ev.subEvents.find(s => s.id === currentActiveSubEventId) : null;
      if (!sub || !sub.participants[idx]) return;

      sub.participants[idx].status = 'Tidak Hadir';
      sub.participants[idx].time = '—';
      sub.participants[idx].method = '—';
      sub.participants[idx].point = -1;
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

    function openQrForCurrentSubEvent() {
      const ev = eventsData.find(e => e.id === currentActiveEventId);
      const sub = ev ? ev.subEvents.find(s => s.id === currentActiveSubEventId) : null;
      if (!ev || !sub) return;

      document.getElementById('qr-modal-sub-name').textContent = sub.name;
      document.getElementById('qr-modal-parent-name').textContent = ev.name;

      const container = document.getElementById('qr-canvas');
      container.innerHTML = '';
      new QRCode(container, {
        text: `GIAT://EVENT/${ev.id}/SUBEVENT/${sub.id}/KEY/${Date.now()}`,
        width: 180,
        height: 180,
        colorDark: '#0f172a',
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.H,
      });

      const now = new Date();
      now.setMinutes(now.getMinutes() + 45);
      const expStr = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
      document.getElementById('qr-expire-time').textContent = expStr;

      startQrCountdown(now);
      openModal('modal-qr');
    }

    function startQrCountdown(expireDate) {
      clearInterval(qrTimerInterval);
      qrTimerInterval = setInterval(() => {
        const remaining = expireDate - new Date();
        if (remaining <= 0) {
          clearInterval(qrTimerInterval);
          document.getElementById('qr-countdown').textContent = 'Sesi Berakhir';
          return;
        }
        const mins = Math.floor(remaining / 60000);
        const secs = Math.floor((remaining % 60000) / 1000);
        document.getElementById('qr-countdown').textContent =
          mins.toString().padStart(2, '0') + ':' + secs.toString().padStart(2, '0');
      }, 1000);
    }

    function closeSessionFromModal() {
      clearInterval(qrTimerInterval);
      closeModal('modal-qr');
      showToast('Sesi absensi untuk Sub Event telah ditutup.', 'info');
    }

    function renderAdminDashboard() {
      const container = document.getElementById('admin-managed-container');
      container.innerHTML = '';

      const adminName = userInfo.admin.name;

      let hasAny = false;

      eventsData.forEach(ev => {
        const assignedSubs = ev.subEvents.filter(s => s.pic.toLowerCase().includes('budi') || currentRole === 'superadmin');

        if (assignedSubs.length > 0) {
          hasAny = true;
          const card = document.createElement('div');
          card.className = 'managed-tree-card';

          let subItemsHtml = '';
          assignedSubs.forEach(s => {
            subItemsHtml += `
          <div class="managed-tree-item">
            <div>
              <div class="managed-sub-name">${s.name}</div>
              <div class="managed-sub-meta">
                <span><i class="fa-regular fa-calendar"></i> ${s.date}</span>
                <span><i class="fa-regular fa-clock"></i> ${s.time}</span>
                <span><i class="fa-solid fa-location-dot"></i> ${s.loc}</span>
                <span><i class="fa-solid fa-user-check"></i> ${s.hadir} / ${s.total} Hadir</span>
                <span class="badge ${s.status === 'Finished' ? 'badge-gray' : s.status === 'Active' ? 'badge-green' : 'badge-amber'}">${s.status}</span>
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
        <div class="managed-tree-sublist">
          ${subItemsHtml}
        </div>
      `;
          container.appendChild(card);
        }
      });

      if (!hasAny) {
        container.innerHTML = `<div class="empty-state"><i class="fa-solid fa-calendar-check"></i><p>Belum ada Event atau Sub Event yang ditugaskan kepada Anda. Klik tombol <strong>"+ Tambah Sub Event"</strong> di atas untuk membuat Sub Event baru.</p></div>`;
      }
    }

    function renderUserDashboard() {
      const container = document.getElementById('user-events-progress-container');
      container.innerHTML = '';

      eventsData.forEach(ev => {
        let completedCount = 0;
        let eventPoints = 0;

        let rowsHtml = '';
        ev.subEvents.forEach(sub => {

          let userStatus = 'Upcoming';
          let statusIcon = '<i class="fa-regular fa-circle"></i>';
          let statusCls = 'status-upcoming';
          let pointText = '<span style="color:var(--slate-400)">Upcoming</span>';

          if (sub.status === 'Finished') {
            completedCount++;

            const found = attendanceHistory.find(a => a.event === ev.name && a.subEvent === sub.name && a.member === 'Andi Pratama');
            if (found && found.status === 'Hadir') {
              userStatus = 'Hadir';
              statusIcon = '<i class="fa-solid fa-check"></i>';
              statusCls = 'status-hadir';
              pointText = '<strong class="point-positive">+3 Point</strong>';
              eventPoints += 3;
            } else {
              userStatus = 'Tidak Hadir';
              statusIcon = '<i class="fa-solid fa-xmark"></i>';
              statusCls = 'status-absen';
              pointText = '<strong class="point-negative">-1 Point</strong>';
              eventPoints -= 1;
            }
          }

          rowsHtml += `
        <div class="user-subevent-row ${statusCls}">
          <div style="display:flex;align-items:center;">
            <span class="subevent-status-icon">${statusIcon}</span>
            <div>
              <strong>${sub.name}</strong>
              <span style="font-size:12px;color:var(--slate-400);margin-left:8px;">· ${sub.date}, ${sub.time}</span>
            </div>
          </div>
          <div>${pointText}</div>
        </div>
      `;
        });

        const block = document.createElement('div');
        block.className = 'user-event-block';
        block.innerHTML = `
      <div class="user-event-head">
        <div>
          <div class="user-event-title">${ev.name}</div>
          <div class="user-event-dates"><i class="fa-regular fa-calendar"></i> ${ev.dateRange}</div>
        </div>
        <div style="text-align:right;">
          <div style="font-size:12px;color:var(--slate-400);">Progress Selesai</div>
          <div style="font-weight:700;font-size:14px;color:var(--navy);">${completedCount} / ${ev.subEvents.length} Sub Event Selesai</div>
          <div style="font-size:12px;margin-top:2px;">
            Total Point Event: <strong style="color:var(--blue-600);font-size:13px;">${eventPoints >= 0 ? '+' + eventPoints : eventPoints} Point</strong>
          </div>
        </div>
      </div>
      <div style="font-size:12.5px;font-weight:600;color:var(--navy-600);text-transform:uppercase;letter-spacing:.04em;">Daftar Sub Event:</div>
      <div class="user-subevent-list">
        ${rowsHtml}
      </div>
    `;
        container.appendChild(block);
      });
    }

    function simulateScanSuccess() {

      document.getElementById('scanner-body').innerHTML = `
    <div style="text-align:center;padding:16px 0;">
      <div style="width:68px;height:68px;border-radius:50%;background:var(--green-100);color:var(--green-500);font-size:32px;display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px;">
        <i class="fa-solid fa-check"></i>
      </div>
      <h3 style="font-size:19px;font-weight:800;color:var(--navy);">Absensi Berhasil!</h3>
      <p style="color:var(--slate-400);font-size:13px;margin:4px 0 10px;">Sub Event: Rapat Persiapan 1 (Webinar Nasional 2026)</p>
      <div style="font-size:26px;font-weight:800;color:var(--green-500);margin-bottom:6px;">+3 Point Didapat</div>
      <p style="color:var(--navy-600);font-size:12px;">Kehadiran Anda telah terverifikasi secara valid.</p>
    </div>
  `;
      document.getElementById('scanner-footer').innerHTML = `
    <button class="btn btn-blue" onclick="closeModal('modal-scanner')">Selesai</button>
  `;

      const userPt = document.getElementById('user-total-point');
      if (userPt) userPt.textContent = '49 pt';

      showToast('Absensi Sub Event Berhasil! +3 Point ditambahkan 🎉', 'success');
    }

    function renderUserHistory() {
      const tbody = document.getElementById('user-history-tbody');
      tbody.innerHTML = '';

      const userItems = attendanceHistory.filter(a => a.member === 'Andi Pratama');
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

    function renderAbsensiList() {
      const tbody = document.getElementById('absensi-all-tbody');
      tbody.innerHTML = '';

      attendanceHistory.forEach((item, idx) => {
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
      const q = document.getElementById('absensi-search').value.toLowerCase();
      const evFilter = document.getElementById('absensi-filter-event').value;
      const stFilter = document.getElementById('absensi-filter-status').value;

      const rows = document.querySelectorAll('#absensi-all-tbody tr');
      attendanceHistory.forEach((item, idx) => {
        const matchQ = item.member.toLowerCase().includes(q) || item.event.toLowerCase().includes(q) || item.subEvent.toLowerCase().includes(q);
        const matchEv = !evFilter || item.event === evFilter;
        const matchSt = !stFilter || item.status === stFilter;
        if (rows[idx]) {
          rows[idx].style.display = (matchQ && matchEv && matchSt) ? '' : 'none';
        }
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

    function renderMembers(list) {
      const tbody = document.getElementById('member-tbody');
      tbody.innerHTML = '';
      list.forEach((m, i) => {
        const row = document.createElement('tr');
        row.innerHTML = `
      <td>${m.id}</td>
      <td><strong>${m.name}</strong></td>
      <td>${m.div}</td>
      <td>${m.hadir} sesi</td>
      <td>${m.absen} sesi</td>
      <td><strong style="color:var(--blue-600);font-size:14px;">${m.point} pt</strong></td>
      <td><span class="badge ${m.status === 'Aktif' ? 'badge-green' : 'badge-gray'}">${m.status}</span></td>
      <td>
        <div class="actions-cell">
          <button class="action-link" onclick="showToast('Edit anggota: ${m.name}','info')">Edit</button>
          <button class="action-link danger" onclick="deleteMemberRow(${i})">Hapus</button>
        </div>
      </td>
    `;
        tbody.appendChild(row);
      });
    }

    function filterMembers() {
      const q = document.getElementById('member-search').value.toLowerCase();
      const div = document.getElementById('member-filter-div').value;
      const filtered = members.filter(m =>
        (m.name.toLowerCase().includes(q) || m.id.toLowerCase().includes(q)) &&
        (div === '' || m.div === div)
      );
      renderMembers(filtered);
    }

    function addMember() {
      const name = document.getElementById('new-name').value.trim();
      if (!name) { showToast('Nama anggota tidak boleh kosong', 'error'); return; }
      const div = document.getElementById('new-div').value;
      const id = 'A' + String(members.length + 1).padStart(3, '0');

      members.push({ id, name, div, hadir: 0, absen: 0, point: 0, status: 'Aktif' });
      renderMembers(members);
      closeModal('modal-add-member');
      document.getElementById('new-name').value = '';
      document.getElementById('new-email').value = '';
      document.getElementById('new-id').value = '';
      showToast('Anggota ' + name + ' berhasil ditambahkan!', 'success');
    }

    function deleteMemberRow(idx) {
      if (!confirm('Hapus anggota ini dari daftar?')) return;
      members.splice(idx, 1);
      renderMembers(members);
      showToast('Anggota berhasil dihapus', 'info');
    }

    function destroyCharts() {
      Object.values(chartInstances).forEach(c => { try { c.destroy(); } catch (e) { } });
      chartInstances = {};
    }

    function initDashboardChart() {
      if (chartInstances['attendance']) return;
      const ctx = document.getElementById('chart-attendance');
      if (!ctx) return;
      chartInstances['attendance'] = new Chart(ctx, {
        type: 'line',
        data: {
          labels: ['Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep'],
          datasets: [{
            label: 'Kehadiran Sub Event (%)',
            data: [80, 84, 79, 88, 86, 92],
            borderColor: '#2563eb',
            backgroundColor: 'rgba(37,99,235,.08)',
            borderWidth: 2.5,
            tension: .35,
            fill: true,
            pointBackgroundColor: '#2563eb',
            pointRadius: 4,
          }]
        },
        options: {
          responsive: true, maintainAspectRatio: false,
          plugins: { legend: { display: false } },
          scales: {
            y: { min: 60, max: 100, grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 } } },
            x: { grid: { display: false }, ticks: { font: { size: 11 } } }
          }
        }
      });
    }

    function initStatCharts() {
      const charts = [
        {
          id: 'chart-monthly', type: 'bar',
          data: {
            labels: ['Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep'],
            datasets: [{
              label: 'Kehadiran (%)', data: [78, 82, 75, 85, 80, 89],
              backgroundColor: 'rgba(37,99,235,.8)', borderRadius: 6
            }]
          },
          opts: { scales: { y: { min: 60, max: 100 } } }
        },
        {
          id: 'chart-top-member', type: 'bar',
          data: {
            labels: ['Andi', 'Rizky', 'Siti', 'Fajar', 'Dina'],
            datasets: [{
              label: 'Point', data: [46, 42, 38, 34, 30],
              backgroundColor: ['#2563eb', '#3b82f6', '#60a5fa', '#93c5fd', '#bfdbfe'],
              borderRadius: 6
            }]
          },
          opts: { indexAxis: 'y', scales: { x: { min: 0, max: 50 } } }
        },
        {
          id: 'chart-event-rate', type: 'bar',
          data: {
            labels: ['Webinar 2026', 'Workshop UI/UX', 'Musyawarah'],
            datasets: [{
              label: 'Kehadiran Sub Event (%)', data: [91, 89, 82],
              backgroundColor: 'rgba(34,197,94,.8)', borderRadius: 6
            }]
          },
          opts: { scales: { y: { min: 60, max: 100 } } }
        },
        {
          id: 'chart-division', type: 'doughnut',
          data: {
            labels: ['Teknologi', 'Kreatif', 'Humas', 'Keuangan', 'Akademik'],
            datasets: [{
              data: [88, 80, 76, 84, 72],
              backgroundColor: ['#2563eb', '#8b5cf6', '#06b6d4', '#22c55e', '#f59e0b'],
              borderWidth: 2, borderColor: '#fff'
            }]
          },
          opts: {}
        },
      ];

      charts.forEach(cfg => {
        const ctx = document.getElementById(cfg.id);
        if (!ctx || chartInstances[cfg.id]) return;
        chartInstances[cfg.id] = new Chart(ctx, {
          type: cfg.type,
          data: cfg.data,
          options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
              legend: {
                display: cfg.type === 'doughnut', position: 'bottom',
                labels: { font: { size: 11 }, boxWidth: 12 }
              }
            },
            scales: cfg.type === 'doughnut' ? {} : {
              y: Object.assign({ grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 } } }, (cfg.opts.scales || {}).y || {}),
              x: Object.assign({ grid: { display: false }, ticks: { font: { size: 11 } } }, (cfg.opts.indexAxis === 'y' ? {} : {}))
            },
            indexAxis: cfg.opts.indexAxis,
          }
        });
      });
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
        clearInterval(qrTimerInterval);
        qrTimerInterval = null;
      }
    }

    document.querySelectorAll('.modal-overlay').forEach(overlay => {
      overlay.addEventListener('click', function (e) {
        if (e.target === this) closeModal(this.id);
      });
    });

    function showToast(msg, type = 'info') {
      const c = document.getElementById('toast-container');
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

if (typeof window !== 'undefined' && window.currentUser) {
      document.addEventListener('DOMContentLoaded', function () {
        const r = (window.currentUser.role || 'Member').toLowerCase();
        const roleKey = (r === 'admin') ? 'superadmin' : 'member';
        currentRole = roleKey;
        if (userInfo[roleKey]) {
          userInfo[roleKey].name = window.currentUser.full_name || window.currentUser.username;
          userInfo[roleKey].role = window.currentUser.role;
          const initials = (window.currentUser.full_name || window.currentUser.username || 'US')
            .split(' ').filter(Boolean).map(w => w[0]).join('').substring(0, 2).toUpperCase();
          userInfo[roleKey].initials = initials;
        }
        applyRoleUI(roleKey);
        buildSidebar(roleKey);
        setUserInfo(roleKey);
        navTo(defaultPage[roleKey]);
      });
    }
