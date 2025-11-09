<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

//admin only restriction

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: no_access.php");
    exit;
} 



include 'includes/db.php';
include 'includes/header.php';
include 'includes/sidebar.php';


?>

<!--style of dashboard-->
<link rel="stylesheet" href="assets/styleofdash.css">
<script src="https://kit.fontawesome.com/96e37b53f1.js" crossorigin="anonymous"></script>

<!-- MAIN DASHBOARD AREA -->
<div class="flex-1 min-h-screen overflow-hidden bg-gray-100">
<!-- ✅ NAVBAR HEADER -->

  
  <!-- Left: Title -->
  <h1 class=" ml-10 text-xl md:text-2xl font-bold flex items-center gap-2 mt-2 text-gray-700  ">
    <i class="fa-solid fa-chart-pie text-lg md:text-xl"></i>
    Dashboard Overview
  </h1>




  <!-- MAIN DASHBOARD CONTENT -->
  <div class="p-6">
    <!-- Summary + Best Sellers -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-0.1 items-start">
      <div id="summary-cards" class="lg:col-span-4"></div>
      <div id="best-sellers" class="best-sellers-box"></div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
      <div class="chart-container bg-white p-4 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold mb-2">Products Sold Overview</h3>
        <canvas id="salesProductChart" height="100"></canvas>
      </div>
      <div class="lg:col-span-2 chart-container bg-white p-4 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold mb-2">Monthly Sales Overview</h3>
        <canvas id="salesChart" height="120"></canvas>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let salesChart, productChart;

// Fetch dashboard data
async function fetchDashboardData() {
  const res = await fetch('fetch_dashboard_data.php?t=' + new Date().getTime());
  const data = await res.json();

  // Update Summary Cards
  document.getElementById('summary-cards').innerHTML = `
    <div class="summary-card accent-blue">
      <div class="label"><i class="fa-solid fa-truck-field"></i> Total Products</div> 
      <div class="value">${data.totalProducts}</div>
    </div>
    <div class="summary-card accent-green">
      <div class="label"> <i class="fa-solid fa-chart-simple"></i> Total Sales</div>
      <div class="value">${data.totalSales}</div>
    </div>
    <div class="summary-card accent-yellow">
      <div class="label"> <i class="fa-solid fa-layer-group"></i> Total Stocks</div>
      <div class="value">${data.totalStocks}</div>
    </div>
    <div class="summary-card accent-red">
      <div class="label"> <i class="fa-solid fa-money-bill"></i> Total Sales Money</div>
      <div class="value">₱${parseFloat(data.totalSalesMoney).toLocaleString()}</div>
    </div>
  `;

  // Update Best Sellers
  const bestSellersDiv = document.getElementById('best-sellers');
  bestSellersDiv.innerHTML = `
    <div class="best-sellers-header"><h1>Best Selling Products</h1></div>
    <div class="best-sellers-list">
      ${data.bestProducts.map(p => `
        <div class="best-seller-item">
          <img src="${p.image}" class="w-16 h-16 object-cover rounded-md shadow-sm">
          <div class="best-seller-info">
            <h4>${p.product_name}</h4>
            <p>${p.total_sold} sold</p>
          </div>
          <span class="revenue-tag">₱${parseFloat(p.total_revenue).toLocaleString()}</span>
        </div>
      `).join('')}
    </div>
  `;

  // Update Charts
  if (salesChart) salesChart.destroy();
  if (productChart) productChart.destroy();

  salesChart = new Chart(document.getElementById('salesChart'), {
    type: 'line',
    data: {
      labels: data.monthlyLabels,
      datasets: [{
        label: 'Sales (₱)',
        data: data.monthlySales,
        borderColor: '#2563EB',
        backgroundColor: 'rgba(37,99,235,0.1)',
        fill: true,
        tension: 0.4,
        borderWidth: 2,
        pointRadius: 3
      }]
    },
    options: { responsive: true, plugins: { legend: { display: false } } }
  });

  productChart = new Chart(document.getElementById('salesProductChart'), {
    type: 'pie',
    data: {
      labels: data.productLabels,
      datasets: [{
        data: data.productCounts,
        backgroundColor: [
          '#3B82F6','#22C55E','#F59E0B','#EF4444','#8B5CF6',
          '#14B8A6','#E879F9','#F97316','#6366F1','#10B981'
        ],
        borderColor: '#fff', borderWidth: 1.5
      }]
    },
    options: { responsive: true, plugins: { legend: { display: false } } }
  });
}

fetchDashboardData();
setInterval(fetchDashboardData, 1000);
</script>
