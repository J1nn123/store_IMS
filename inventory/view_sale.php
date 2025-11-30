<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}




error_reporting(E_ALL);
ini_set('display_errors', 1);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

include 'includes/db.php';
include 'includes/sidebar.php';

// --- HANDLE DELETE ---
if (isset($_GET['delete'])) {
    $sale_id = intval($_GET['delete']);

    // Restore stock
    $items = $conn->query("SELECT product_id, quantity FROM sales_items WHERE sale_id = $sale_id");
    while ($item = $items->fetch_assoc()) {
        $conn->query("UPDATE products SET quantity = quantity + {$item['quantity']} WHERE product_id = {$item['product_id']}");
    }

    // Delete sale records
    $conn->query("DELETE FROM sales_items WHERE sale_id = $sale_id");
    $conn->query("DELETE FROM sales WHERE sale_id = $sale_id");

    echo "<script>
            alert('Sale deleted and stock restored!');
            window.location.href = 'view_sale.php';
          </script>";
    exit;
}

// --- FETCH SALES ---
$sales_result = $conn->query("SELECT sale_id, created_at, total_amount FROM sales ORDER BY created_at DESC");
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

    <div class="mt-5 mb-5">
        <a href="add_sales.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
            Add New Sale
        </a>
    </div>

    <table class="min-w-full border border-gray-300 rounded">
        <thead class="bg-gray-200">
            <tr>
                <th class="border px-4 py-2">Date Sold</th>
                <th class="border px-4 py-2">Product</th>
                <th class="border px-4 py-2">Quantity</th>
                <th class="border px-4 py-2">Amount</th>
                <th class="border px-4 py-2">Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if ($sales_result->num_rows > 0) {
            while ($sale = $sales_result->fetch_assoc()) {
                $sale_id = $sale['sale_id'];

                $items_query = "
                    SELECT 
                        p.product_name, 
                        si.quantity, 
                        p.price,
                        (si.quantity * p.price) AS total_price
                    FROM sales_items si
                    JOIN products p ON si.product_id = p.product_id
                    WHERE si.sale_id = $sale_id
                ";

                $items_result = $conn->query($items_query);

                while ($item = $items_result->fetch_assoc()) {
                    echo "
                    <tr class='hover:bg-gray-50'>
                        <td class='border px-4 py-2 text-center'>{$sale['created_at']}</td>
                        <td class='border px-4 py-2'>{$item['product_name']}</td>
                        <td class='border px-4 py-2 text-center'>{$item['quantity']}</td>
                        <td class='border px-4 py-2 text-center font-semibold text-green-700'>₱{$item['total_price']}</td>
                        <td class='border px-4 py-2 text-center'>
                            <button 
                                class='deleteBtn bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded'
                                data-sale-id='{$sale_id}'>
                                Delete
                            </button>
                        </td>
                    </tr>";
                }
            }
        } else {
            echo "<tr><td colspan='5' class='text-center py-4 text-gray-500'>No sales recorded yet.</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>

<!-- DELETE CONFIRMATION MODAL -->
<div id="deleteModal" class="fixed inset-0 bg-black/30 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-80 text-center shadow-xl border">
        <h2 class="text-lg font-semibold mb-4">
            Are you sure you want to delete this sale?
        </h2>
        <div class="flex justify-center space-x-4">
            <button id="confirmDelete" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                Yes, Delete
            </button>
            <button id="cancelDelete" class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300">
                Cancel
            </button>
        </div>
    </div>
</div>

<script>
let deleteUrl = "";

function attachDeleteEvents() {
    const modal = document.getElementById('deleteModal');
    const confirmBtn = document.getElementById('confirmDelete');
    const cancelBtn = document.getElementById('cancelDelete');

    document.querySelectorAll('.deleteBtn').forEach(btn => {
        btn.addEventListener('click', () => {
            const saleId = btn.getAttribute('data-sale-id');
            deleteUrl = `?delete=${saleId}`;
            modal.classList.remove('hidden');
        });
    });

    if(cancelBtn) cancelBtn.addEventListener('click', () => modal.classList.add('hidden'));
    if(confirmBtn) confirmBtn.addEventListener('click', () => {
        if(deleteUrl) window.location.href = deleteUrl;
    });
}

// Attach events on page load
attachDeleteEvents();
</script>

</body>
</html>
