let statChartInstances = {};

function destroyCharts() {
  Object.values(statChartInstances).forEach(c => {
    try { c.destroy(); } catch (e) { }
  });
  statChartInstances = {};
}

function initStatCharts() {
  if (typeof Chart === 'undefined') return;

  const charts = [
    {
      id: 'chart-monthly',
      type: 'bar',
      data: {
        labels: ['Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep'],
        datasets: [{
          label: 'Kehadiran (%)',
          data: [78, 82, 75, 85, 80, 89],
          backgroundColor: 'rgba(37,99,235,.8)',
          borderRadius: 6
        }]
      },
      opts: { scales: { y: { min: 60, max: 100 } } }
    },
    {
      id: 'chart-top-member',
      type: 'bar',
      data: {
        labels: ['Andi', 'Rizky', 'Siti', 'Fajar', 'Dina'],
        datasets: [{
          label: 'Point',
          data: [46, 42, 38, 34, 30],
          backgroundColor: ['#2563eb', '#3b82f6', '#60a5fa', '#93c5fd', '#bfdbfe'],
          borderRadius: 6
        }]
      },
      opts: { indexAxis: 'y', scales: { x: { min: 0, max: 50 } } }
    },
    {
      id: 'chart-event-rate',
      type: 'bar',
      data: {
        labels: ['Webinar 2026', 'Workshop UI/UX', 'Musyawarah'],
        datasets: [{
          label: 'Kehadiran Sub Event (%)',
          data: [91, 89, 82],
          backgroundColor: 'rgba(34,197,94,.8)',
          borderRadius: 6
        }]
      },
      opts: { scales: { y: { min: 60, max: 100 } } }
    },
    {
      id: 'chart-division',
      type: 'doughnut',
      data: {
        labels: ['Teknologi', 'Kreatif', 'Humas', 'Keuangan', 'Akademik'],
        datasets: [{
          data: [88, 80, 76, 84, 72],
          backgroundColor: ['#2563eb', '#8b5cf6', '#06b6d4', '#22c55e', '#f59e0b'],
          borderWidth: 2,
          borderColor: '#fff'
        }]
      },
      opts: {}
    }
  ];

  charts.forEach(cfg => {
    const ctx = document.getElementById(cfg.id);
    if (!ctx || statChartInstances[cfg.id]) return;
    statChartInstances[cfg.id] = new Chart(ctx, {
      type: cfg.type,
      data: cfg.data,
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: cfg.type === 'doughnut',
            position: 'bottom',
            labels: { font: { size: 11 }, boxWidth: 12 }
          }
        },
        scales: cfg.type === 'doughnut' ? {} : {
          y: Object.assign({ grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 } } }, (cfg.opts.scales || {}).y || {}),
          x: Object.assign({ grid: { display: false }, ticks: { font: { size: 11 } } }, (cfg.opts.indexAxis === 'y' ? {} : {}))
        },
        indexAxis: cfg.opts.indexAxis
      }
    });
  });
}
