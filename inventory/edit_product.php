<?php 
include 'includes/db.php'; 
include 'includes/header.php'; 
include 'includes/sidebar.php'; 

// Validate product_id from GET
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid product ID.");
}
$product_id = (int) $_GET['id'];

// Fetch product details
$query = "SELECT * FROM products WHERE product_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) {
    die("Product not found.");
}

// Fetch suppliers for dropdown
$suppliers = $conn->query("SELECT supplier_id, name FROM suppliers ORDER BY name ASC");

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = trim($_POST['product_name']);
    $category_id = (int) $_POST['category_id'];
    $supplier_id = (int) $_POST['supplier_id'];
    $quantity = (float) $_POST['quantity']; // allow decimals
    $price = (float) $_POST['price'];

    $stmt = $conn->prepare("
        UPDATE products 
        SET product_name = ?, category_id = ?, supplier_id = ?, quantity = ?, price = ? 
        WHERE product_id = ?
    ");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    // "siiddi" => string, int, int, double, double, int
    $stmt->bind_param("siiddi", $product_name, $category_id, $supplier_id, $quantity, $price, $product_id);

    if ($stmt->execute()) {
        header("Location: products_dashboard.php");
        exit();
    } else {
        die("Update failed: " . $stmt->error);
    }
}
?>
<div class="flex-1 p-10">
  <div class="max-w-xl mx-auto bg-white p-6 rounded-lg shadow">
    <h2 class="text-2xl font-bold text-gray-700 mb-6">Edit Product</h2>
    
    <form method="POST" action="edit_product.php?id=<?= $product_id ?>" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700">Product Name</label>
        <input type="text" name="product_name" value="<?= htmlspecialchars($product['product_name']) ?>" required
               class="w-full border border-gray-300 rounded px-3 py-2 mt-1 focus:outline-none focus:ring focus:border-blue-300">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Category ID</label>
        <input type="number" name="category_id" value="<?= htmlspecialchars($product['category_id']) ?>" required
               class="w-full border border-gray-300 rounded px-3 py-2 mt-1 focus:outline-none focus:ring focus:border-blue-300">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Supplier</label>
        <select name="supplier_id" required
                class="w-full border border-gray-300 rounded px-3 py-2 mt-1 focus:outline-none focus:ring focus:border-blue-300">
          <option value="">-- Select Supplier --</option>
          <?php while ($supplier = $suppliers->fetch_assoc()): ?>
            <option value="<?= $supplier['supplier_id'] ?>" 
              <?= $supplier['supplier_id'] == $product['supplier_id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($supplier['name']) ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Quantity</label>
        <input type="number" step="0.01" name="quantity" value="<?= htmlspecialchars($product['quantity']) ?>" required
               class="w-full border border-gray-300 rounded px-3 py-2 mt-1 focus:outline-none focus:ring focus:border-blue-300">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Price</label>
        <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($product['price']) ?>" required
               class="w-full border border-gray-300 rounded px-3 py-2 mt-1 focus:outline-none focus:ring focus:border-blue-300">
      </div>

      <div class="pt-4">
        <button type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition w-full">
          Update Product
        </button>
      </div>
    </form>
  </div>
</div>


<?php include 'includes/footer.php'; ?>
