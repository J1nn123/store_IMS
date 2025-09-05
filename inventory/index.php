<?php
include 'includes/db.php';
include 'includes/header.php';
include 'includes/sidebar.php';

// Fetch values from database
$totalProducts = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
$totalSales = $conn->query("SELECT COUNT(*) as count FROM sales")->fetch_assoc()['count'];

// Always sum the live quantity from products table
$totalStocks = $conn->query("
    SELECT COALESCE(SUM(quantity), 0) as total 
    FROM products
")->fetch_assoc()['total'];

// Get total money from sales
$totalSalesMoney = $conn->query("
    SELECT COALESCE(SUM(total_amount), 0) as total 
    FROM sales
")->fetch_assoc()['total'];

// Fetch real monthly sales data
$monthlySales = array_fill(1, 12, 0);

$query = $conn->query("
    SELECT MONTH(created_at) as month, SUM(total_amount) as total 
    FROM sales 
    GROUP BY MONTH(created_at)
");

while ($row = $query->fetch_assoc()) {
    $month = (int)$row['month'];
    $monthlySales[$month] = (float)$row['total'];
}

$monthlyLabels = json_encode(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']);
?>

<div class="flex-1 p-10 bg-gray-100 min-h-screen">
  <h1 class="text-3xl font-semibold text-gray-800 mb-6">Dashboard</h1>

  <!-- Summary Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-4 gap-6 mb-10">
    <div class="bg-white p-6 rounded-xl shadow text-center">
      <p class="text-gray-500 text-sm">Total Products</p>
      <h2 class="text-3xl font-bold text-blue-600"><?php echo $totalProducts; ?></h2>
    </div>
    <div class="bg-white p-6 rounded-xl shadow text-center">
      <p class="text-gray-500 text-sm">Total Sales</p>
      <h2 class="text-3xl font-bold text-green-600"><?php echo $totalSales; ?></h2>
    </div>
    <div class="bg-white p-6 rounded-xl shadow text-center">
      <p class="text-gray-500 text-sm">Total Stocks</p>
      <h2 class="text-3xl font-bold text-yellow-600"><?php echo $totalStocks; ?></h2>
    </div>
    <div class="bg-white p-6 rounded-xl shadow text-center">
      <p class="text-gray-500 text-sm">Total Sales Money</p>
      <h2 class="text-3xl font-bold text-red-600">₱<?php echo number_format($totalSalesMoney, 2); ?></h2>
    </div>
  </div>

  <!-- Chart Section -->
  <div class="bg-white p-6 rounded-xl shadow">
    <h3 class="text-xl font-semibold text-gray-700 mb-4">Monthly Sales Overview</h3>
    <canvas id="salesChart" height="100"></canvas>
  </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const ctx = document.getElementById('salesChart').getContext('2d');
  const salesChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: <?php echo $monthlyLabels; ?>,
      datasets: [{
        label: 'Sales (₱)',
        data: <?php echo json_encode(array_values($monthlySales)); ?>,
        borderColor: '#1D4ED8',
        backgroundColor: 'rgba(29, 78, 216, 0.1)',
        fill: true,
        tension: 0.3
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
</script>

<?php include 'includes/footer.php'; ?>
