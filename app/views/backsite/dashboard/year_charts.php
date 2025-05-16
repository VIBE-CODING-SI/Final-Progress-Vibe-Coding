<div class="bg-white rounded-xl shadow-lg p-4 max-w-3xl mx-auto">
  <div class="flex justify-between items-center mb-4">
    <h3 class="text-xl font-bold text-gray-900 tracking-tight">Year Sales Overview</h3>
    <div class="flex space-x-2">
      <button id="btn-bar" class="px-3 py-1.5 rounded-md bg-blue-600 text-white font-semibold shadow hover:bg-blue-700 transition duration-300 ease-in-out text-sm">Bar</button>
      <button id="btn-line" class="px-3 py-1.5 rounded-md bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 transition duration-300 ease-in-out text-sm">Line</button>
      <button id="btn-pie" class="px-3 py-1.5 rounded-md bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 transition duration-300 ease-in-out text-sm">Pie</button>
    </div>
  </div>

  <canvas id="salesChart" height="160" class="rounded-md shadow-md"></canvas>

  <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
    <div class="bg-green-50 rounded-md p-4 shadow-inner border border-green-200">
      <p class="text-2xl font-bold text-green-700" id="total-sales">0</p>
      <p class="text-xs font-medium text-green-600 mt-1">Total Sales This Year</p>
    </div>
    <div class="bg-yellow-50 rounded-md p-4 shadow-inner border border-yellow-200">
      <p class="text-2xl font-bold text-yellow-700" id="average-sales">0</p>
      <p class="text-xs font-medium text-yellow-600 mt-1">Average Year Sales</p>
    </div>
    <div class="bg-red-50 rounded-md p-4 shadow-inner border border-red-200">
      <p class="text-2xl font-bold text-red-700" id="best-month">-</p>
      <p class="text-xs font-medium text-red-600 mt-1">Best Performing Month</p>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const ctx = document.getElementById('salesChart').getContext('2d');
  let currentChartType = 'bar';
  let salesChart;

  function renderChart(type, data) {
    if (salesChart) salesChart.destroy();

    salesChart = new Chart(ctx, {
      type: type,
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
        datasets: [{
          label: 'Jumlah Penjualan',
          data: data,
          backgroundColor: type === 'pie'
            ? [
                '#22c55e', '#facc15', '#ef4444', '#3b82f6', '#8b5cf6', '#14b8a6',
                '#f97316', '#a855f7', '#eab308', '#10b981', '#ef4444', '#6366f1'
              ]
            : 'rgba(37, 99, 235, 0.7)',
          borderColor: type === 'pie' ? '#fff' : 'rgba(37, 99, 235, 1)',
          borderWidth: 2,
          fill: type === 'line',
          tension: 0.3,
          pointRadius: type === 'line' ? 4 : 0,
          hoverRadius: 6,
        }]
      },
      options: {
        responsive: true,
        animation: {
          duration: 1000,
          easing: 'easeOutQuart'
        },
        plugins: {
          legend: {
            display: type !== 'bar',
            position: 'top',
            labels: {
              font: { size: 12, weight: 'bold' },
              color: '#374151'
            }
          },
          tooltip: {
            enabled: true,
            mode: 'nearest',
            intersect: false,
            backgroundColor: '#1f2937',
            titleFont: { weight: 'bold' },
            padding: 8,
            cornerRadius: 6
          }
        },
        scales: (type === 'bar' || type === 'line') ? {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 10,
              color: '#4b5563',
              font: { size: 12 }
            },
            grid: {
              color: '#e5e7eb',
              borderDash: [4, 4]
            }
          },
          x: {
            ticks: {
              color: '#4b5563',
              font: { size: 12 }
            },
            grid: {
              display: false
            }
          }
        } : {}
      }
    });
  }

  // Fetch data & initialize chart
  fetch('/dashboard/penjualan/chart-data')
    .then(res => res.json())
    .then(data => {
      renderChart(currentChartType, data);

      const totalSales = data.reduce((a,b) => a + b, 0);
      const avgSales = (totalSales / data.length).toFixed(2);
      const bestMonthIdx = data.indexOf(Math.max(...data));
      const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

      document.getElementById('total-sales').textContent = totalSales.toLocaleString();
      document.getElementById('average-sales').textContent = avgSales.toLocaleString();
      document.getElementById('best-month').textContent = monthNames[bestMonthIdx];
    })
    .catch(err => console.error('Error fetching chart data:', err));

  ['bar', 'line', 'pie'].forEach(type => {
    document.getElementById(`btn-${type}`).addEventListener('click', () => {
      currentChartType = type;
      fetchAndRender();
      toggleActiveButton(`btn-${type}`);
    });
  });

  function fetchAndRender() {
    fetch('/dashboard/penjualan/chart-data')
      .then(res => res.json())
      .then(data => renderChart(currentChartType, data))
      .catch(err => console.error('Error fetching chart data:', err));
  }

  function toggleActiveButton(activeId) {
    ['btn-bar', 'btn-line', 'btn-pie'].forEach(id => {
      const btn = document.getElementById(id);
      if (id === activeId) {
        btn.classList.add('bg-blue-600', 'text-white', 'shadow-lg');
        btn.classList.remove('bg-gray-200', 'text-gray-700');
      } else {
        btn.classList.remove('bg-blue-600', 'text-white', 'shadow-lg');
        btn.classList.add('bg-gray-200', 'text-gray-700');
      }
    });
  }
});
</script>