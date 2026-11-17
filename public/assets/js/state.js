// ============================================================
// state.js — Global state dan konfigurasi navigasi
// ============================================================

let currentRole = 'superadmin';
let currentPage = 'dashboard';
let currentActiveEventId = 1;
let currentActiveSubEventId = 101;
let pendingDeleteEventId = null;
let pendingDeleteMemberId = null;
const eventCrudFlashStorageKey = 'eventCrudFlash';
let chartInstances = {};

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
  member: 'user-dashboard',
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
