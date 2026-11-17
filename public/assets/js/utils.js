// ============================================================
// utils.js — Helper umum yang dipakai lintas modul
// ============================================================

function formatDateId(dateStr) {
  const parts = dateStr.split('-');
  if (parts.length === 3) {
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    const mIdx = parseInt(parts[1], 10) - 1;
    return `${parts[2]} ${months[mIdx] || 'Sep'} ${parts[0]}`;
  }
  return dateStr;
}
