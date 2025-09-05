<?php include 'includes/db.php'; ?>

<?php
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $_POST['product_name'];
    $category_id = $_POST['category_id'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];

    // If custom supplier is filled, add it to suppliers table first
    if (!empty($_POST['new_supplier_name'])) {
        $new_supplier_name = $_POST['new_supplier_name'];
        $new_supplier_contact = $_POST['new_supplier_contact'];
        $new_supplier_email = $_POST['new_supplier_email'];

        $stmt = $conn->prepare("INSERT INTO suppliers (name, contact_info, email) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $new_supplier_name, $new_supplier_contact, $new_supplier_email);
        $stmt->execute();
        $supplier_id = $stmt->insert_id; // Get the newly added supplier ID
        $stmt->close();
    } else {
        $supplier_id = $_POST['supplier_id']; // Existing supplier from dropdown
    }

    // Insert product
   $stmt = $conn->prepare("INSERT INTO products (product_name, category_id, supplier_id, quantity, price) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("siiid", $product_name, $category_id, $supplier_id, $quantity, $price);
    $stmt->execute();
    $stmt->close();

    header("Location: products_dashboard.php");
    exit();
}

// Fetch categories and suppliers
$categories = $conn->query("SELECT category_id, name FROM categories");
$suppliers = $conn->query("SELECT supplier_id, name FROM suppliers");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function toggleCustomSupplier() {
            document.getElementById('customSupplierFields').classList.toggle('hidden');
        }
    </script>
</head>
<body class="bg-gray-100 text-gray-800">
<div class="flex min-h-screen">

    <?php include 'includes/sidebar.php'; ?>

    <div class="flex-1 p-10">
        <div class="mb-6">
            <a href="products_dashboard.php" class="bg-blue-600 text-white px-4 py-2 rounded">← Back</a>
        </div>

        <div class="bg-white shadow-lg rounded-xl p-10 w-full max-w-md mx-auto">
            <h2 class="text-2xl font-semibold text-center mb-6">Add New Product</h2>
            <form method="POST" class="space-y-4">

                <div>
                    <label class="block mb-1">Product Name</label>
                   
                    <input type="text" name="product_name" required class="w-full border rounded p-2">

                </div>

                <div>
                    <label class="block mb-1">Category</label>
                    <select name="category_id" required class="w-full border rounded p-2">
                        <option value="" disabled selected>Select Category</option>
                        <?php while ($row = $categories->fetch_assoc()): ?>
                            <option value="<?= $row['category_id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div>
                    <label class="block mb-1">Supplier</label>
                    <select name="supplier_id" class="w-full border rounded p-2">
                        <option value="">Select Existing Supplier</option>
                        <?php while ($row = $suppliers->fetch_assoc()): ?>
                            <option value="<?= $row['supplier_id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                    <button type="button" onclick="toggleCustomSupplier()" class="mt-2 text-blue-600 underline">
                        + Add Custom Supplier
                    </button>
                </div>

                <div id="customSupplierFields" class="hidden space-y-3 border p-4 rounded mt-4 bg-gray-50">
                    <div>
                        <label class="block mb-1">Supplier Name</label>
                        <input type="text" name="new_supplier_name" class="w-full border rounded p-2">
                    </div>
                    <div>
                        <label class="block mb-1">Contact Info</label>
                        <input type="text" name="new_supplier_contact" class="w-full border rounded p-2">
                    </div>
                    <div>
                        <label class="block mb-1">Email</label>
                        <input type="email" name="new_supplier_email" class="w-full border rounded p-2">
                    </div>
                </div>

                <div>
                    <label class="block mb-1">Quantity</label>
                    <input type="number" name="quantity" required min="0" class="w-full border rounded p-2">
                </div>

                <div>
                    <label class="block mb-1">Price (₱)</label>
                    <input type="number" name="price" step="0" required class="w-full border rounded p-2">
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded">
                    Add Product
                </button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
