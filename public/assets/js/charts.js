// ============================================================
// charts.js — Inisialisasi Chart.js (dashboard & statistik)
// ============================================================

function destroyCharts() {
  Object.values(chartInstances).forEach(c => { try { c.destroy(); } catch (e) {} });
  chartInstances = {};
}

function initDashboardChart() {
  if (chartInstances['attendance']) return;
  const ctx = document.getElementById('chart-attendance');
  if (!ctx) return;

  let labels = ['Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep'];
  let data = [80, 84, 79, 88, 86, 92];

  const db = window.dbDashboard;
  if (db && db.trend && db.trend.length > 0) {
    labels = db.trend.map(t => t.month);
    data = db.trend.map(t => parseFloat(t.attendance_percentage) || 0);
  }

  chartInstances['attendance'] = new Chart(ctx, {
    type: 'line',
    data: {
      labels,
      datasets: [{
        label: 'Kehadiran Sub Event (%)',
        data,
        borderColor: '#2563eb',
        backgroundColor: 'rgba(37,99,235,.08)',
        borderWidth: 2.5,
        tension: .35,
        fill: true,
        pointBackgroundColor: '#2563eb',
        pointRadius: 4,
      }],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        y: { min: 0, max: 100, grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 } } },
        x: { grid: { display: false }, ticks: { font: { size: 11 } } },
      },
    },
  });
}

function initStatCharts() {
  const db = window.dbStatistik || {};

  // Chart 1 — Tingkat Kehadiran Per Bulan
  const monthlyLabels = db.monthlyTrend && db.monthlyTrend.length
    ? db.monthlyTrend.map(r => r.month)
    : ['Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep'];
  const monthlyData = db.monthlyTrend && db.monthlyTrend.length
    ? db.monthlyTrend.map(r => parseFloat(r.attendance_percentage) || 0)
    : [78, 82, 75, 85, 80, 89];

  // Chart 2 — Top 5 Anggota
  const topLabels = db.topMembers && db.topMembers.length
    ? db.topMembers.map(r => r.member_name)
    : ['Andi', 'Rizky', 'Siti', 'Fajar', 'Dina'];
  const topData = db.topMembers && db.topMembers.length
    ? db.topMembers.map(r => parseInt(r.total_points) || 0)
    : [46, 42, 38, 34, 30];

  // Chart 3 — Rata-rata Kehadiran per Event
  const eventLabels = db.eventRate && db.eventRate.length
    ? db.eventRate.map(r => r.event_title)
    : ['Webinar 2026', 'Workshop UI/UX', 'Musyawarah'];
  const eventData = db.eventRate && db.eventRate.length
    ? db.eventRate.map(r => parseFloat(r.attendance_percentage) || 0)
    : [91, 89, 82];

  // Chart 4 — Distribusi per Divisi
  const divColors = { Teknologi: '#2563eb', Kreatif: '#8b5cf6', Humas: '#06b6d4', Keuangan: '#22c55e', Akademik: '#f59e0b' };
  const divLabels = db.divisionDist && db.divisionDist.length
    ? db.divisionDist.map(r => r.division)
    : ['Teknologi', 'Kreatif', 'Humas', 'Keuangan', 'Akademik'];
  const divData = db.divisionDist && db.divisionDist.length
    ? db.divisionDist.map(r => parseFloat(r.attendance_percentage) || 0)
    : [88, 80, 76, 84, 72];
  const divBg = divLabels.map(l => divColors[l] || '#94a3b8');

  const charts = [
    {
      id: 'chart-monthly', type: 'bar',
      data: {
        labels: monthlyLabels,
        datasets: [{ label: 'Kehadiran (%)', data: monthlyData, backgroundColor: 'rgba(37,99,235,.8)', borderRadius: 6 }],
      },
      opts: { scales: { y: { min: 0, max: 100 } } },
    },
    {
      id: 'chart-top-member', type: 'bar',
      data: {
        labels: topLabels,
        datasets: [{
          label: 'Point', data: topData,
          backgroundColor: ['#2563eb', '#3b82f6', '#60a5fa', '#93c5fd', '#bfdbfe'],
          borderRadius: 6,
        }],
      },
      opts: { indexAxis: 'y', scales: { x: { min: 0 } } },
    },
    {
      id: 'chart-event-rate', type: 'bar',
      data: {
        labels: eventLabels,
        datasets: [{ label: 'Kehadiran (%)', data: eventData, backgroundColor: 'rgba(34,197,94,.8)', borderRadius: 6 }],
      },
      opts: { scales: { y: { min: 0, max: 100 } } },
    },
    {
      id: 'chart-division', type: 'doughnut',
      data: {
        labels: divLabels,
        datasets: [{ data: divData, backgroundColor: divBg, borderWidth: 2, borderColor: '#fff' }],
      },
      opts: {},
    },
  ];

  charts.forEach(cfg => {
    const ctx = document.getElementById(cfg.id);
    if (!ctx || chartInstances[cfg.id]) return;
    chartInstances[cfg.id] = new Chart(ctx, {
      type: cfg.type,
      data: cfg.data,
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: cfg.type === 'doughnut',
            position: 'bottom',
            labels: { font: { size: 11 }, boxWidth: 12 },
          },
        },
        scales: cfg.type === 'doughnut' ? {} : {
          y: Object.assign({ grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 } } }, (cfg.opts.scales || {}).y || {}),
          x: Object.assign({ grid: { display: false }, ticks: { font: { size: 11 } } }, {}),
        },
        indexAxis: cfg.opts.indexAxis,
      },
    });
  });
}
