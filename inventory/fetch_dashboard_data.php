<?php
//admin only restriction
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: no_access.php");
    exit;
} 
include 'includes/db.php';


header('Content-Type: application/json');


// Totals
$totalProducts = $conn->query("SELECT COUNT(*) as c FROM products")->fetch_assoc()['c'];
$totalSales = $conn->query("SELECT COUNT(*) as c FROM sales")->fetch_assoc()['c'];
$totalStocks = $conn->query("SELECT COALESCE(SUM(quantity),0) as t FROM products")->fetch_assoc()['t'];
$totalSalesMoney = $conn->query("SELECT COALESCE(SUM(total_amount),0) as t FROM sales")->fetch_assoc()['t'];

// Monthly Sales
$monthly = array_fill(1, 12, 0);
$q = $conn->query("SELECT MONTH(created_at) m, SUM(total_amount) s FROM sales GROUP BY m");
while ($r = $q->fetch_assoc()) $monthly[(int)$r['m']] = (float)$r['s'];

// Best Sellers (Top 2)
$best = [];
$q = $conn->query("
  SELECT p.product_name, p.image, COALESCE(SUM(si.quantity),0) total_sold,
         COALESCE(SUM(si.quantity * p.price),0) total_revenue
  FROM products p
  LEFT JOIN sales_items si ON p.product_id = si.product_id
  GROUP BY p.product_id
  ORDER BY total_sold DESC
  LIMIT 2
");
while ($r = $q->fetch_assoc()) $best[] = $r;

// Pie Chart Data
$labels = []; $counts = [];
$q = $conn->query("
  SELECT p.product_name, COALESCE(SUM(si.quantity),0) total_sold
  FROM products p
  LEFT JOIN sales_items si ON p.product_id = si.product_id
  GROUP BY p.product_name
  ORDER BY total_sold DESC
");
while ($r = $q->fetch_assoc()) {
  $labels[] = $r['product_name'];
  $counts[] = $r['total_sold'];
}

echo json_encode([
  'totalProducts' => $totalProducts,
  'totalSales' => $totalSales,
  'totalStocks' => $totalStocks,
  'totalSalesMoney' => $totalSalesMoney,
  'monthlyLabels' => ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
  'monthlySales' => array_values($monthly),
  'bestProducts' => $best,
  'productLabels' => $labels,
  'productCounts' => $counts
]);
?>
