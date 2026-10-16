const defaultMembers = [
  { id: 'A001', name: 'Andi Pratama', div: 'Divisi Teknologi', hadir: 16, absen: 2, point: 46, status: 'Aktif' },
  { id: 'A002', name: 'Rizky Ramadhan', div: 'Divisi Kreatif', hadir: 15, absen: 3, point: 42, status: 'Aktif' },
  { id: 'A003', name: 'Siti Aulia', div: 'Divisi Humas', hadir: 14, absen: 4, point: 38, status: 'Aktif' },
  { id: 'A004', name: 'Fajar Nugraha', div: 'Divisi Teknologi', hadir: 13, absen: 5, point: 34, status: 'Aktif' },
  { id: 'A005', name: 'Dina Maharani', div: 'Divisi Keuangan', hadir: 12, absen: 6, point: 30, status: 'Aktif' },
  { id: 'A006', name: 'Bimo Santoso', div: 'Divisi Akademik', hadir: 11, absen: 7, point: 26, status: 'Aktif' },
  { id: 'A007', name: 'Layla Sari', div: 'Divisi Humas', hadir: 10, absen: 8, point: 22, status: 'Non-Aktif' },
  { id: 'A008', name: 'Hendra Kurnia', div: 'Divisi Kreatif', hadir: 9, absen: 9, point: 18, status: 'Non-Aktif' }
];

const defaultEventsData = [
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
          { name: 'Bimo Santoso', div: 'Divisi Akademik', status: 'Tidak Hadir', time: '—', method: '—', point: -1 }
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
          { name: 'Fajar Nugraha', div: 'Divisi Teknologi', status: 'Hadir', time: '19:10', method: 'QR', point: 3 }
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
          { name: 'Siti Aulia', div: 'Divisi Humas', status: 'Belum Hadir', time: '—', method: '—', point: 0 }
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
          { name: 'Siti Aulia', div: 'Divisi Humas', status: 'Hadir', time: '16:10', method: 'QR', point: 3 }
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
          { name: 'Rizky Ramadhan', div: 'Divisi Kreatif', status: 'Hadir', time: '19:04', method: 'QR', point: 3 }
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
          { name: 'Andi Pratama', div: 'Divisi Teknologi', status: 'Hadir', time: '09:02', method: 'QR', point: 3 }
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
          { name: 'Andi Pratama', div: 'Divisi Teknologi', status: 'Hadir', time: '09:05', method: 'QR', point: 3 }
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

const defaultAttendanceHistory = [
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
  { id: 11, member: 'Andi Pratama', event: 'Workshop UI/UX', subEvent: 'Workshop Day 2', date: '28 Agu 2026', status: 'Hadir', method: 'QR', point: 3 }
];

const navConfig = {
  superadmin: [
    { id: 'dashboard', url: 'dashboard.html', label: 'Dashboard', icon: 'fa-solid fa-gauge-high' },
    { id: 'anggota', url: 'anggota.html', label: 'Anggota', icon: 'fa-solid fa-users' },
    { id: 'event', url: 'event.html', label: 'Event', icon: 'fa-solid fa-calendar-days' },
    { id: 'absensi', url: 'absensi.html', label: 'Absensi', icon: 'fa-solid fa-clipboard-list' },
    { id: 'statistik', url: 'statistik.html', label: 'Statistik', icon: 'fa-solid fa-chart-bar' }
  ],
  admin: [
    { id: 'dashboard', url: 'dashboard.html', label: 'Event Saya', icon: 'fa-solid fa-list-check' },
    { id: 'event', url: 'event.html', label: 'Daftar Event', icon: 'fa-solid fa-calendar-days' },
    { id: 'absensi', url: 'absensi.html', label: 'Rekap Absensi', icon: 'fa-solid fa-clipboard-list' }
  ],
  member: [
    { id: 'dashboard', url: 'dashboard.html', label: 'Dashboard', icon: 'fa-solid fa-gauge-high' },
    { id: 'user-history', url: 'user-history.html', label: 'Riwayat', icon: 'fa-solid fa-clock-rotate-left' }
  ]
};

const userInfo = {
  superadmin: { name: 'Super Admin', role: 'Super Admin', initials: 'SA' },
  admin: { name: 'Budi Santoso', role: 'Admin / PIC', initials: 'BS' },
  member: { name: 'Andi Pratama', role: 'Anggota', initials: 'AP' }
};

function getStorageItem(key, defaultValue) {
  const item = localStorage.getItem(key);
  if (!item) {
    localStorage.setItem(key, JSON.stringify(defaultValue));
    return defaultValue;
  }
  try {
    return JSON.parse(item);
  } catch (e) {
    return defaultValue;
  }
}

function setStorageItem(key, value) {
  localStorage.setItem(key, JSON.stringify(value));
}

function getMembers() {
  return getStorageItem('attendify_members', defaultMembers);
}

function saveMembers(data) {
  setStorageItem('attendify_members', data);
}

function getEvents() {
  return getStorageItem('attendify_events', defaultEventsData);
}

function saveEvents(data) {
  setStorageItem('attendify_events', data);
}

function getAttendanceHistory() {
  return getStorageItem('attendify_attendance', defaultAttendanceHistory);
}

function saveAttendanceHistory(data) {
  setStorageItem('attendify_attendance', data);
}

function getCurrentRole() {
  return localStorage.getItem('attendify_current_role') || 'superadmin';
}

function setCurrentRole(role) {
  localStorage.setItem('attendify_current_role', role);
}
