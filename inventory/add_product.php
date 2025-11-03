<?php include 'includes/db.php'; ?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="flex-1 p-10">
    <h2 class="text-2xl font-bold mb-4 text-gray-700">Add Product</h2>

    <form action="add_product.php" method="POST" enctype="multipart/form-data"
        class="bg-white shadow-lg rounded-lg p-6 space-y-4 max-w-lg">

        <!-- Product Name -->
        <div>
            <label class="block text-gray-700 font-medium">Product Name</label>
            <input type="text" name="product_name" required
                class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-green-200">
        </div>

        <!-- Category -->
        <div>
            <label class="block text-gray-700 font-medium">Category</label>
            <select name="category_id" required class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-green-200">
                <option value="">Select category</option>
                <?php
                $categories = $conn->query("SELECT * FROM categories");
                while ($cat = $categories->fetch_assoc()) {
                    echo "<option value='{$cat['category_id']}'>" . htmlspecialchars($cat['category_name']) . "</option>";
                }
                ?>
            </select>
        </div>

        <!-- Supplier -->
        <div>
            <label class="block text-gray-700 font-medium">Supplier</label>
            <select name="supplier_id" required class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-green-200">
                <option value="">Select supplier</option>
                <?php
                $suppliers = $conn->query("SELECT * FROM suppliers");
                while ($sup = $suppliers->fetch_assoc()) {
                    echo "<option value='{$sup['supplier_id']}'>" . htmlspecialchars($sup['supplier_name']) . "</option>";
                }
                ?>
            </select>
        </div>

        <!-- Quantity -->
        <div>
            <label class="block text-gray-700 font-medium">Quantity</label>
            <input type="number" name="quantity" required
                class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-green-200">
        </div>

        <!-- Price -->
        <div>
            <label class="block text-gray-700 font-medium">Price (₱)</label>
            <input type="number" step="0.01" name="price" required
                class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-green-200">
        </div>

        <!-- Image -->
        <div>
            <label class="block text-gray-700 font-medium">Product Image</label>
            <input type="file" name="image" accept="image/*"
                class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-green-200">
        </div>

        <!-- Submit -->
        <button type="submit" name="submit"
            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
            Save Product
        </button>
    </form>
</div>

<?php
if (isset($_POST['submit'])) {
    $product_name = $conn->real_escape_string($_POST['product_name']);
    $category_id = intval($_POST['category_id']);
    $supplier_id = intval($_POST['supplier_id']);
    $quantity = intval($_POST['quantity']);
    $price = floatval($_POST['price']);

    // Handle image upload
    $imageName = "";
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $imageName = time() . "_" . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $imageName;
        move_uploaded_file($_FILES['image']['tmp_name'], $targetFile);
    }

    // ✅ Insert record
    $sql = "INSERT INTO products (product_name, category_id, supplier_id, quantity, price, image, created_at) 
            VALUES ('$product_name', $category_id, $supplier_id, $quantity, $price, '$imageName', NOW())";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Product added successfully!'); window.location='products_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error adding product: " . addslashes($conn->error) . "');</script>";
    }
}
?>

<?php include 'includes/footer.php'; ?>