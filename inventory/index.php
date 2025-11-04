<?php
include 'includes/db.php';
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<style>
  html, body {
    height: 100%;
    overflow: hidden;
  }

  .best-sellers-box {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 1rem;
    padding: 1.2rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    margin-bottom: 10px;
  }

  .best-sellers-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.8rem;
  }

  .best-sellers-header h2 {
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
  }

  .best-sellers-list {
    display: flex;
    flex-direction: column;
    gap: 0.7rem;
  }

  .best-seller-item {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #f9fafb;
    border-radius: 0.75rem;
    padding: 0.6rem 0.75rem;
    border: 1px solid #e5e7eb;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }

  .best-seller-item:hover {
    transform: scale(1.02);
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
  }

  .best-seller-item img {
    width: 40px;
    height: 40px;
    border-radius: 0.5rem;
    object-fit: cover;
  }

  .best-seller-info h4 {
    font-size: 0.9rem;
    font-weight: 600;
    color: #374151;
  }

  .best-seller-info p {
    font-size: 0.8rem;
    color: #6b7280;
  }

  .revenue-tag {
    margin-left: auto;
    font-size: 0.8rem;
    font-weight: 600;
    color: #10b981;
    background: #ecfdf5;
    padding: 0.25rem 0.5rem;
    border-radius: 0.4rem;
  }

  /* Summary cards grid: 1 column on small, 2 columns on medium+ */
  #summary-cards {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
    align-items:center;
  }

  @media (min-width: 640px) {
    #summary-cards {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  /* Modern card style used by summary cards */
  .summary-card {
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 10px;
    padding: 1.1rem 1.25rem;
    border-radius: 50px;
    color: #ffffff;
    box-shadow: 0 8px 20px rgba(16,24,40,0.06);
    border: 1px solid rgba(255,255,255,0.06);
    transition: transform 0.18s ease, box-shadow 0.18s ease;
    margin-bottom: 20px;
  }

  .summary-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(16,24,40,0.12);
  }

  .summary-card .label {
    font-size: 20px;
    opacity: 0.9;
    text-align: center;
  }

  .summary-card .value {
    font-size: 1.6rem;
    font-weight: 700;
    letter-spacing: -0.02em;
    text-align: center;
  }

  .accent-blue { background: linear-gradient(135deg,#2563eb,#1e40af); }
  .accent-green { background: linear-gradient(135deg,#10b981,#047857); }
  .accent-yellow { background: linear-gradient(135deg,#f59e0b,#d97706); }
  .accent-red { background: linear-gradient(135deg,#ef4444,#b91c1c); }

  /* Chart container tweaks */
  .chart-container {
    background: #ffffff;
    padding: 1rem;
    border-radius: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    transition: box-shadow 0.2s ease;
    margin-bottom: 1rem;
    
  }

  .chart-container:hover {
    box-shadow: 0 6px 16px rgba(0,0,0,0.1);
  }

  .chart-container h3 {
    font-size: 0.95rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.6rem;
  }
</style>

<div class="flex-1 p-6 bg-gray-100 min-h-screen overflow-hidden">
  <!-- Header -->
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
      📊 Dashboard Overview
    </h1>
    <a href="cover.php" 
      class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg shadow-md transition duration-200 ease-in-out hover:scale-105">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 002 2h3a2 2 0 002-2v-1m-7-10V5a2 2 0 012-2h3a2 2 0 012 2v1" />
      </svg>
      Logout
    </a>
  </div>

  <!-- Summary + Best Sellers -->
  <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-0.1 items-start">
    <div id="summary-cards" class="lg:col-span-4"></div>
    <div id="best-sellers" class="best-sellers-box"></div>
  </div>

  <!-- Charts -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="chart-container">
      <h3>Products Sold Overview</h3>
      <canvas id="salesProductChart" height="100"></canvas>
    </div>
    <div class="lg:col-span-2 chart-container">
      <h3>Monthly Sales Overview</h3>
      <canvas id="salesChart" height="120"></canvas>
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
      <div class="label">Total Products</div>
      <div class="value">${data.totalProducts}</div>
    </div>
    <div class="summary-card accent-green">
      <div class="label">Total Sales</div>
      <div class="value">${data.totalSales}</div>
    </div>
    <div class="summary-card accent-yellow">
      <div class="label">Total Stocks</div>
      <div class="value">${data.totalStocks}</div>
    </div>
    <div class="summary-card accent-red">
      <div class="label">Total Sales Money</div>
      <div class="value">₱${parseFloat(data.totalSalesMoney).toLocaleString()}</div>
    </div>
  `;

  // Update Best Sellers
  const bestSellersDiv = document.getElementById('best-sellers');
  bestSellersDiv.innerHTML = `
    <div class="best-sellers-header"><h1>Best Selling Products</h1</div>
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

// Load + refresh every second
fetchDashboardData();
setInterval(fetchDashboardData, 1000);
</script>
