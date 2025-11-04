<?php include 'includes/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sari-Sari Store Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
  <!-- 🧭 Header -->
  <header class="flex justify-between items-center px-8 py-4 bg-white shadow-sm">
    <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
      <i data-lucide="bar-chart-3" class="w-6 h-6 text-blue-600"></i>
      Dashboard Overview
    </h1>
    <a href="cover.php" 
       class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow transition duration-200">
      <i data-lucide="log-out" class="w-5 h-5"></i> Logout
    </a>
  </header>

  <!-- 📊 Dashboard Content -->
  <main class="flex-1 p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

      <!-- 🏆 Top Seller Card -->
      <div class="backdrop-blur-md bg-white/70 border border-gray-200 rounded-2xl p-6 shadow-md hover:shadow-lg transition duration-200">
        <h2 class="text-xl font-semibold text-gray-700 flex items-center gap-2 mb-3">
          <i data-lucide="trophy" class="w-5 h-5 text-yellow-500"></i>
          Top Seller
        </h2>
        <p class="text-gray-600 text-sm">Displays your best-selling product this week.</p>
        <div class="mt-4 p-4 bg-gradient-to-r from-yellow-100 to-yellow-50 rounded-lg text-gray-800 text-center font-medium">
          <?php
            $result = $conn->query("
              SELECT p.product_name, SUM(s.quantity) AS total_sold
              FROM sales_items s
              JOIN products p ON s.product_id = p.product_id
              GROUP BY s.product_id
              ORDER BY total_sold DESC
              LIMIT 1
            ");
            if ($row = $result->fetch_assoc()) {
              echo "<h3 class='text-lg font-bold'>{$row['product_name']}</h3>";
              echo "<p class='text-sm text-gray-600'>Sold: {$row['total_sold']} units</p>";
            } else {
              echo "<p class='text-gray-500 italic'>No sales yet</p>";
            }
          ?>
        </div>
      </div>

      <!-- 🧾 Total Sold Items -->
      <div class="backdrop-blur-md bg-white/70 border border-gray-200 rounded-2xl p-6 shadow-md hover:shadow-lg transition duration-200">
        <h2 class="text-xl font-semibold text-gray-700 flex items-center gap-2 mb-3">
          <i data-lucide="shopping-bag" class="w-5 h-5 text-green-500"></i>
          Sold Items
        </h2>
        <p class="text-gray-600 text-sm">Total number of items sold.</p>
        <div class="mt-4 text-center text-3xl font-bold text-green-700">
          <?php
            $sold = $conn->query("SELECT SUM(quantity) AS total FROM sales_items")->fetch_assoc();
            echo $sold['total'] ? $sold['total'] : 0;
          ?>
        </div>
      </div>

      <!-- 💰 Total Sales -->
      <div class="backdrop-blur-md bg-white/70 border border-gray-200 rounded-2xl p-6 shadow-md hover:shadow-lg transition duration-200">
        <h2 class="text-xl font-semibold text-gray-700 flex items-center gap-2 mb-3">
          <i data-lucide="wallet" class="w-5 h-5 text-blue-500"></i>
          Total Sales
        </h2>
        <p class="text-gray-600 text-sm">Total sales revenue generated.</p>
        <div class="mt-4 text-center text-3xl font-bold text-blue-700">
          ₱
          <?php
            $sales = $conn->query("SELECT SUM(total_amount) AS revenue FROM sales")->fetch_assoc();
            echo $sales['revenue'] ? number_format($sales['revenue'], 2) : "0.00";
          ?>
        </div>
      </div>

    </div>
  </main>

  <!-- ⚓ Footer -->
  <footer class="text-center text-gray-500 text-sm py-6">
    © <?php echo date("Y"); ?> Sari-Sari Store Inventory System. All rights reserved.
  </footer>

  <script>lucide.createIcons();</script>
</body>
</html>
