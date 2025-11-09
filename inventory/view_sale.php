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


error_reporting(E_ALL);
ini_set('display_errors', 1);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
include 'includes/db.php';
include 'includes/sidebar.php';

// ✅ Handle deletion (also restore stock)
if (isset($_GET['delete'])) {
    $sale_id = intval($_GET['delete']);

    // Restore stock for deleted sale items
    $items = $conn->query("SELECT product_id, quantity FROM sales_items WHERE sale_id = $sale_id");
    while ($item = $items->fetch_assoc()) {
        $conn->query("UPDATE products SET quantity = quantity + {$item['quantity']} WHERE product_id = {$item['product_id']}");
    }

    // Delete sale and its items
    $conn->query("DELETE FROM sales_items WHERE sale_id = $sale_id");
    $conn->query("DELETE FROM sales WHERE sale_id = $sale_id");

    echo "<script>
            alert('Sale deleted and stock restored!');
            window.location.href = 'view_sale.php';
          </script>";
    exit;
}

// ✅ Fetch all sales
$sales_query = "SELECT sale_id, created_at, total_amount FROM sales ORDER BY created_at DESC";
$sales_result = $conn->query($sales_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Sales</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100 text-gray-800">

<div class="max-w-5xl mx-auto mt-10 bg-white p-6 shadow-lg rounded-lg">
    <h1 class="text-2xl font-bold mb-4 text-center">Sales Records</h1>

    <table class="min-w-full border border-gray-300 rounded">
        <thead class="bg-gray-200">
            <tr>
                <th class="border px-4 py-2">Sale ID</th>
                <th class="border px-4 py-2">Date</th>
                <th class="border px-4 py-2">Products (Combined)</th>
                <th class="border px-4 py-2">Total Quantity</th>
                <th class="border px-4 py-2">Total Amount</th>
                <th class="border px-4 py-2">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($sales_result->num_rows > 0) {
                while ($sale = $sales_result->fetch_assoc()) {
                    $sale_id = $sale['sale_id'];

                    // ✅ Fetch all products in this sale
                    $items_query = "
                        SELECT p.product_name, si.quantity
                        FROM sales_items si
                        JOIN products p ON si.product_id = p.product_id
                        WHERE si.sale_id = $sale_id";
                    
                    $items_result = $conn->query($items_query);

                    // ✅ Combine identical products (e.g., Coke x3)
                    $products_combined = [];
                    $total_qty = 0;
                    while ($item = $items_result->fetch_assoc()) {
                        $name = $item['product_name'];
                        $qty = (int)$item['quantity'];
                        $total_qty += $qty;
                        if (isset($products_combined[$name])) {
                            $products_combined[$name] += $qty;
                        } else {
                            $products_combined[$name] = $qty;
                        }
                    }

                    // ✅ Format like: Coke (x3), Sprite (x2)
                    $product_display = [];
                    foreach ($products_combined as $name => $qty) {
                        $product_display[] = "$name (x$qty)";
                    }
                    $product_list = implode(', ', $product_display);

                    echo "<tr class='hover:bg-gray-50'>
                        <td class='border px-4 py-2 text-center'>{$sale['sale_id']}</td>
                        <td class='border px-4 py-2 text-center'>{$sale['created_at']}</td>
                        <td class='border px-4 py-2 text-left'>{$product_list}</td>
                        <td class='border px-4 py-2 text-center'>{$total_qty}</td>
                        <td class='border px-4 py-2 text-center font-semibold text-green-700'>₱{$sale['total_amount']}</td>
                        <td class='border px-4 py-2 text-center'>
                            <a href='?delete={$sale['sale_id']}'
                               onclick=\"return confirm('Are you sure you want to delete this sale?');\"
                               class='bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded'>
                               Delete
                            </a>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='6' class='text-center py-4 text-gray-500'>No sales recorded yet.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="mt-6 text-center">
        <a href="add_sales.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Add New Sale</a>
    </div>
</div>

</body>
</html>
