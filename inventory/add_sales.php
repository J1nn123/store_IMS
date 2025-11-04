<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
include 'includes/db.php';

$success = false;

// Fetch products
$product_result = $conn->query("SELECT product_id, product_name, price, quantity FROM products");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $total_amount = $_POST['total_amount'];
    $product_ids = $_POST['product_id'];
    $custom_names = $_POST['custom_name'];
    $custom_prices = $_POST['custom_price'];
    $quantities = $_POST['quantity'];

    // Combine duplicates before inserting
    $combined_items = [];
    for ($i = 0; $i < count($quantities); $i++) {
        $product_id = $product_ids[$i];
        $quantity = intval($quantities[$i]);
        if ($quantity <= 0) continue;

        if ($product_id === 'custom') {
            $name = trim($custom_names[$i]);
            $price = floatval($custom_prices[$i]);
            if ($name === '' || $price <= 0) continue;
            $key = "custom_" . strtolower($name);
            if (isset($combined_items[$key])) {
                $combined_items[$key]['quantity'] += $quantity;
            } else {
                $combined_items[$key] = [
                    'product_id' => 'custom',
                    'name' => $name,
                    'price' => $price,
                    'quantity' => $quantity
                ];
            }
        } else {
            $key = "product_" . $product_id;
            if (isset($combined_items[$key])) {
                $combined_items[$key]['quantity'] += $quantity;
            } else {
                $product_result->data_seek(0);
                while ($prod = $product_result->fetch_assoc()) {
                    if ($prod['product_id'] == $product_id) {
                        $price = $prod['price'];
                        break;
                    }
                }
                $combined_items[$key] = [
                    'product_id' => $product_id,
                    'quantity' => $quantity,
                    'price' => $price
                ];
            }
        }
    }

    // Insert sale
    $stmt = $conn->prepare("INSERT INTO sales (total_amount) VALUES (?)");
    $stmt->bind_param("d", $total_amount);
    if ($stmt->execute()) {
        $sale_id = $stmt->insert_id;

        $item_stmt = $conn->prepare("INSERT INTO sales_items (sale_id, product_id, quantity) VALUES (?, ?, ?)");
        $custom_stmt = $conn->prepare("INSERT INTO products (product_name, price) VALUES (?, ?)");

        foreach ($combined_items as $item) {
            if ($item['product_id'] === 'custom') {
                $custom_stmt->bind_param("sd", $item['name'], $item['price']);
                $custom_stmt->execute();
                $product_id = $custom_stmt->insert_id;
            } else {
                $product_id = $item['product_id'];
            }

            $quantity = $item['quantity'];

            // Insert into sales_items
            $item_stmt->bind_param("iii", $sale_id, $product_id, $quantity);
            $item_stmt->execute();

            // ✅ Update product inventory
            $conn->query("UPDATE products SET quantity = quantity - $quantity WHERE product_id = $product_id");
        }

        $item_stmt->close();
        $custom_stmt->close();
        $success = true;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Sale</title>
<script src="https://cdn.tailwindcss.com"></script>
<script>
function addProductRow() {
    const container = document.getElementById('product-container');
    const row = document.querySelector('.product-row').cloneNode(true);
    row.querySelectorAll('input, select').forEach(el => el.value = '');
    row.querySelector('.custom-fields').classList.add('hidden');
    container.appendChild(row);
}

function toggleCustom(select) {
    const customFields = select.closest('.product-row').querySelector('.custom-fields');
    if (select.value === 'custom') {
        customFields.classList.remove('hidden');
    } else {
        customFields.classList.add('hidden');
    }
    updateTotal();
}

function updateTotal() {
    let total = 0;
    document.querySelectorAll('.product-row').forEach(row => {
        const select = row.querySelector('[name="product_id[]"]');
        const quantity = parseFloat(row.querySelector('[name="quantity[]"]').value) || 0;
        let price = 0;

        if (select.value === 'custom') {
            price = parseFloat(row.querySelector('[name="custom_price[]"]').value) || 0;
        } else if (select.value !== '') {
            price = parseFloat(select.selectedOptions[0].dataset.price) || 0;
        }

        total += quantity * price;
    });

    document.querySelector('[name="total_amount"]').value = total.toFixed(2);
}

document.addEventListener('input', e => {
    if (e.target.matches('[name="quantity[]"], [name="custom_price[]"], select')) {
        updateTotal();
    }
});
</script>
</head>
<body class="bg-gray-100 text-gray-800 flex">
<?php include 'includes/sidebar.php'; ?>

<div class="flex-1 p-8">
    <div class="max-w-3xl mx-auto bg-white p-8 rounded shadow-md">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-blue-600">Add New Sale</h2>
            <a href="view_sale.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                View Sales Records
            </a>
        </div>

        <?php if ($success): ?>
        <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded">
            Sale recorded successfully!
        </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div id="product-container" class="space-y-6">
                <div class="product-row">
                    <div class="flex gap-4 mb-2">
                        <div class="w-2/5">
                            <label class="block mb-1 font-semibold">Product</label>
                            <select name="product_id[]" onchange="toggleCustom(this);" required class="w-full px-4 py-2 border rounded">
                                <option value="">Select product</option>
                                <?php
                                $product_result->data_seek(0);
                                while ($product = $product_result->fetch_assoc()): ?>
                                    <option value="<?= $product['product_id'] ?>" data-price="<?= $product['price'] ?>">
                                        <?= htmlspecialchars($product['product_name']) ?> (₱<?= $product['price'] ?>)
                                    </option>
                                <?php endwhile; ?>
                                <option value="custom">-- Custom Product --</option>
                            </select>
                        </div>
                        <div class="w-1/5">
                            <label class="block mb-1 font-semibold">Quantity</label>
                            <input type="number" name="quantity[]" min="1" value="1" class="w-full px-4 py-2 border rounded" required>
                        </div>
                    </div>

                    <div class="custom-fields hidden mb-4">
                        <div class="flex gap-4">
                            <div class="w-2/5">
                                <label class="block mb-1 font-semibold">Custom Name</label>
                                <input type="text" name="custom_name[]" class="w-full px-4 py-2 border rounded">
                            </div>
                            <div class="w-1/5">
                                <label class="block mb-1 font-semibold">Price (₱)</label>
                                <input type="number" step="0.01" name="custom_price[]" class="w-full px-4 py-2 border rounded">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" onclick="addProductRow()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition">
                Add Another Product
            </button>

            <div class="mt-6">
                <label class="block mb-1 font-semibold">Total Amount (₱)</label>
                <input type="number" name="total_amount" step="0.01" readonly class="w-full px-4 py-2 border rounded bg-gray-100 cursor-not-allowed">
            </div>

            <button type="submit" class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700 transition font-semibold">
                Save Sale
            </button>
        </form>
    </div>
</div>
</body>
</html>
