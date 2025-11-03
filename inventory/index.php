<?php
include 'includes/db.php';
include 'includes/header.php';
include 'includes/sidebar.php';

// Fetch values
$totalProducts = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
$totalSales = $conn->query("SELECT COUNT(*) as count FROM sales")->fetch_assoc()['count'];
$totalStocks = $conn->query("SELECT COALESCE(SUM(quantity), 0) as total FROM products")->fetch_assoc()['total'];
$totalSalesMoney = $conn->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM sales")->fetch_assoc()['total'];

// Monthly sales
$monthlySales = array_fill(1, 12, 0);
$query = $conn->query("SELECT MONTH(created_at) as month, SUM(total_amount) as total FROM sales GROUP BY MONTH(created_at)");
while ($row = $query->fetch_assoc()) {
  $monthlySales[(int)$row['month']] = (float)$row['total'];
}
$monthlyLabels = json_encode(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']);

// Pie chart data
$categoryLabels = [];
$categoryCounts = [];
$categoryQuery = $conn->query("
    SELECT c.name, COUNT(p.product_id) as total
    FROM categories c
    LEFT JOIN products p ON c.category_id = p.category_id
    GROUP BY c.category_id
");
while ($row = $categoryQuery->fetch_assoc()) {
  $categoryLabels[] = $row['name'];
  $categoryCounts[] = $row['total'];
}
?>
<div class="flex-1 p-6 bg-gray-100 min-h-screen">
  <h1 class="text-3xl font-bold text-gray-800 mb-6">📊 Dashboard Overview</h1>

  <!-- Compact Summary Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-gradient-to-br from-blue-500 to-blue-700 p-4 rounded-xl shadow text-white hover:scale-[1.02] transition">
      <p class="text-xs opacity-80">Total Products</p>
      <h2 class="text-2xl font-bold mt-1"><?php echo $totalProducts; ?></h2>
    </div>

    <div class="bg-gradient-to-br from-green-500 to-emerald-600 p-4 rounded-xl shadow text-white hover:scale-[1.02] transition">
      <p class="text-xs opacity-80">Total Sales</p>
      <h2 class="text-2xl font-bold mt-1"><?php echo $totalSales; ?></h2>
    </div>

    <div class="bg-gradient-to-br from-yellow-400 to-amber-500 p-4 rounded-xl shadow text-white hover:scale-[1.02] transition">
      <p class="text-xs opacity-80">Total Stocks</p>
      <h2 class="text-2xl font-bold mt-1"><?php echo $totalStocks; ?></h2>
    </div>

    <div class="bg-gradient-to-br from-red-500 to-rose-600 p-4 rounded-xl shadow text-white hover:scale-[1.02] transition">
      <p class="text-xs opacity-80">Total Sales Money</p>
      <h2 class="text-2xl font-bold mt-1">₱<?php echo number_format($totalSalesMoney, 2); ?></h2>
    </div>
  </div>

  <!-- Compact Chart Section -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Pie Chart -->
    <div class="bg-white p-4 rounded-xl shadow hover:shadow-md transition">
      <h3 class="text-md font-semibold text-gray-700 mb-3">Category Distribution</h3>
      <canvas id="categoryChart" height="120"></canvas>
    </div>

    <!-- Line Chart -->
    <div class="lg:col-span-2 bg-white p-4 rounded-xl shadow hover:shadow-md transition">
      <h3 class="text-md font-semibold text-gray-700 mb-3">Monthly Sales Overview</h3>
      <canvas id="salesChart" height="150"></canvas>
    </div>
  </div>
</div>


<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  // Line Chart
  new Chart(document.getElementById('salesChart'), {
    type: 'line',
    data: {
      labels: <?php echo $monthlyLabels; ?>,
      datasets: [{
        label: 'Sales (₱)',
        data: <?php echo json_encode(array_values($monthlySales)); ?>,
        borderColor: '#2563EB',
        backgroundColor: 'rgba(37,99,235,0.15)',
        fill: true,
        tension: 0.4,
        borderWidth: 2,
        pointRadius: 3,
        pointHoverRadius: 6,
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          display: false
        }
      },
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });

  // Pie Chart
  new Chart(document.getElementById('categoryChart'), {
    type: 'pie',
    data: {
      labels: <?php echo json_encode($categoryLabels); ?>,
      datasets: [{
        data: <?php echo json_encode($categoryCounts); ?>,
        backgroundColor: ['#3B82F6', '#22C55E', '#F59E0B', '#EF4444', '#8B5CF6', '#14B8A6'],
        borderColor: '#fff',
        borderWidth: 1.5
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            boxWidth: 12,
            font: {
              size: 10
            }
          }
        },
        tooltip: {
          callbacks: {
            label: (ctx) => `${ctx.label}: ${ctx.parsed}`
          }
        }
      }
    }
  });
</script>

<?php include 'includes/footer.php'; ?>