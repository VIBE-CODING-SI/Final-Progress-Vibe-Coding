<div class="bg-white rounded-lg shadow-sm p-6 mt-6">
    <h3 class="text-lg font-semibold">Monthly Sales</h3>
    <div class="flex gap-2">
        <select id="monthSelector" class="border rounded px-3 py-1 text-gray-700">
            <?php
            $months = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            $currentMonth = date('n');
            foreach ($months as $num => $name) {
                $selected = ($num === $currentMonth) ? 'selected' : '';
                echo "<option value=\"$num\" $selected>$name</option>";
            }
            ?>
        </select>

        <select id="yearSelector" class="border rounded px-3 py-1 text-gray-700">
            <?php
            $currentYear = date('Y');
            for ($y = $currentYear; $y >= $currentYear - 3; $y--) {
                $selected = ($y == $currentYear) ? 'selected' : '';
                echo "<option value=\"$y\" $selected>$y</option>";
            }
            ?>
        </select>
    </div>

    <canvas id="monthlySalesChart" height="160" class="rounded-md shadow-md"></canvas>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const ctxMonth = document.getElementById('monthlySalesChart').getContext('2d');
  let monthlySalesChart;
  const monthSelector = document.getElementById('monthSelector');
  const yearSelector = document.getElementById('yearSelector'); // tambahkan ini

  function renderMonthlyChart(data, title) {
    if (monthlySalesChart) monthlySalesChart.destroy();

    monthlySalesChart = new Chart(ctxMonth, {
      type: 'bar',
      data: {
        labels: Array.from({ length: data.length }, (_, i) => i + 1),
        datasets: [{
          label: `Penjualan Harian (${title})`,
          data: data,
          backgroundColor: 'rgba(59, 130, 246, 0.7)',
          borderColor: 'rgba(59, 130, 246, 1)',
          borderWidth: 1,
          borderRadius: 4,
          hoverBackgroundColor: 'rgba(37, 99, 235, 0.9)'
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            ticks: { stepSize: 5, color: '#4b5563', font: { size: 12 } },
            grid: { color: '#e5e7eb', borderDash: [4, 4] }
          },
          x: {
            ticks: { color: '#4b5563', font: { size: 12 } },
            grid: { display: false }
          }
        },
        plugins: {
          legend: {
            display: true,
            position: 'top',
            labels: { font: { size: 14, weight: 'bold' }, color: '#374151' }
          },
          tooltip: {
            enabled: true,
            backgroundColor: '#1f2937',
            titleFont: { weight: 'bold' },
            padding: 8,
            cornerRadius: 6
          }
        },
        animation: { duration: 1000, easing: 'easeOutQuart' }
      }
    });
  }

  function fetchMonthlyData(month, year) {
    const monthNames = {
      1: 'Januari', 2: 'Februari', 3: 'Maret', 4: 'April',
      5: 'Mei', 6: 'Juni', 7: 'Juli', 8: 'Agustus',
      9: 'September', 10: 'Oktober', 11: 'November', 12: 'Desember'
    };

    fetch(`/dashboard/penjualan/monthly-chart-data?month=${month}&year=${year}`)
      .then(res => res.json())
      .then(data => renderMonthlyChart(data, `${monthNames[month]} ${year}`))
      .catch(err => {
        console.error('Error fetching monthly chart data:', err);
        renderMonthlyChart(new Array(30).fill(0), `${monthNames[month]} ${year}`);
      });
  }

  // Trigger saat selector berubah
  monthSelector.addEventListener('change', () => {
    fetchMonthlyData(monthSelector.value, yearSelector.value);
  });

  yearSelector.addEventListener('change', () => {
    fetchMonthlyData(monthSelector.value, yearSelector.value);
  });

  // Load awal (default)
  fetchMonthlyData(monthSelector.value, yearSelector.value);
});
</script>