<?php include 'includes/db.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales List</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

<!-- Wrapper: Sidebar + Main -->
<div class="flex min-h-screen">

    <!-- Sidebar -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="flex-1 p-10">
        <div class="max-w-6xl mx-auto bg-white p-8 rounded shadow">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-blue-600">Sales Records</h1>
                <a href="add_sales.php" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                    + Add Sale
                </a>
            </div>

            <table class="w-full border text-left">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="px-4 py-2 border">#</th>
                        <th class="px-4 py-2 border">Date</th>
                        <th class="px-4 py-2 border">Products Sold</th>
                        <th class="px-4 py-2 border">Total Amount</th>
                        <th class="px-4 py-2 border">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM sales ORDER BY created_at DESC");
                    while ($row = $result->fetch_assoc()):
                        $sale_id = $row['sale_id'];

                        // Fetch related products
                        $items_result = $conn->query("
                            SELECT p.product_name, si.quantity
                            FROM sales_items si
                            JOIN products p ON si.product_id = p.product_id
                            WHERE si.sale_id = $sale_id
                        ");
                        
                        $product_list = "";
                        while ($item = $items_result->fetch_assoc()) {
                            $product_list .= "{$item['product_name']} (x{$item['quantity']}), ";
                        }
                        $product_list = rtrim($product_list, ', ');
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 border"><?= $sale_id; ?></td>
                        <td class="px-4 py-2 border"><?= $row['created_at']; ?></td>
                        <td class="px-4 py-2 border"><?= $product_list ?: '<em>No products</em>'; ?></td>
                        <td class="px-4 py-2 border">₱<?= number_format($row['total_amount'], 2); ?></td>
                        <td class="px-4 py-2 border">
                            <a href="delete_sales.php?id=<?= $sale_id; ?>" 
                               class="text-red-600 hover:underline"
                               onclick="return confirm('Delete this sale?')">
                               Delete
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>
