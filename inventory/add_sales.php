<?php include 'includes/db.php'; ?>

<?php
$success = false;

// Fetch products
$product_result = $conn->query("SELECT product_id, product_name, price FROM products");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $total_amount = $_POST['total_amount'];
    $product_ids = $_POST['product_id'];
    $custom_names = $_POST['custom_name'];
    $custom_prices = $_POST['custom_price'];
    $quantities = $_POST['quantity'];

    // Insert into sales table
    $stmt = $conn->prepare("INSERT INTO sales (total_amount) VALUES (?)");
    $stmt->bind_param("d", $total_amount);

    if ($stmt->execute()) {
        $sale_id = $stmt->insert_id;

        $item_stmt = $conn->prepare("INSERT INTO sales_items (sale_id, product_id, quantity) VALUES (?, ?, ?)");
        $custom_stmt = $conn->prepare("INSERT INTO products (product_name, price) VALUES (?, ?)");

        for ($i = 0; $i < count($quantities); $i++) {
            $product_id = $product_ids[$i];

            // Handle custom product
            if ($product_id === "custom" && !empty($custom_names[$i]) && !empty($custom_prices[$i])) {
                $custom_stmt->bind_param("sd", $custom_names[$i], $custom_prices[$i]);
                $custom_stmt->execute();
                $product_id = $custom_stmt->insert_id;
            }

            // Insert sales item
            $item_stmt->bind_param("iii", $sale_id, $product_id, $quantities[$i]);
            $item_stmt->execute();
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
        }

        function updateTotal() {
            let total = 0;
            document.querySelectorAll('.product-row').forEach(row => {
                const quantity = parseFloat(row.querySelector('[name="quantity[]"]').value) || 0;
                const select = row.querySelector('[name="product_id[]"]');
                let price = 0;

                if (select.value === 'custom') {
                    price = parseFloat(row.querySelector('[name="custom_price[]"]').value) || 0;
                } else {
                    const selected = select.selectedOptions[0];
                    price = parseFloat(selected.getAttribute('data-price')) || 0;
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
                                <select name="product_id[]" onchange="toggleCustom(this); updateTotal();" required
                                    class="w-full px-4 py-2 border rounded">
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
                                <input type="number" name="quantity[]" min="1" value="1"
                                    class="w-full px-4 py-2 border rounded" required>
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
                                    <input type="number" step="0.01" name="custom_price[]"
                                        class="w-full px-4 py-2 border rounded">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

             

                <div class="mt-6">
                    <label class="block mb-1 font-semibold">Total Amount (₱)</label>
                    <input type="number" name="total_amount" step="0.01" readonly
                        class="w-full px-4 py-2 border rounded bg-gray-100 cursor-not-allowed">
                </div>

                <button type="submit"
                    class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700 transition font-semibold">
                    Save Sale
                </button>
            </form>
        </div>
    </div>
</body>
</html>
