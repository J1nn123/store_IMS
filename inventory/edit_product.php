<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: no_access.php"); exit; }
include 'includes/db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) die("Invalid product ID.");
$product_id = (int) $_GET['id'];

// Fetch product
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$product) die("Product not found.");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Handle new supplier inline
    if (!empty($_POST['new_supplier_name_inline'])) {
        $new_supplier = trim($_POST['new_supplier_name_inline']);
        $stmt = $conn->prepare("INSERT INTO suppliers (name) VALUES (?)");
        $stmt->bind_param("s", $new_supplier);
        $stmt->execute();
        $supplier_id = $stmt->insert_id; // use new supplier
        $stmt->close();
    } else {
        $supplier_id = (int) $_POST['supplier_id'];
    }

    // Update product
    $product_name = trim($_POST['product_name']);
    $category_id = (int) $_POST['category_id'];
    $quantity = (float) $_POST['quantity'];
    $price = (float) $_POST['price'];
    $expiration_date = !empty($_POST['expiration_date']) ? $_POST['expiration_date'] : NULL;
    $imagePath = $product['image'] ?? null;

    if (!empty($_FILES['product_image']['name'])) {
        $targetDir = "uploads/products/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $fileName = time() . "_" . basename($_FILES["product_image"]["name"]);
        $targetFile = $targetDir . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg','jpeg','png','gif'];
        if (in_array($imageFileType, $allowedTypes)) {
            move_uploaded_file($_FILES["product_image"]["tmp_name"], $targetFile);
            $imagePath = $targetFile;
        }
    }

    // Prepare and execute update
    $stmt = $conn->prepare("UPDATE products SET product_name=?, category_id=?, supplier_id=?, quantity=?, price=?, image=?, expiration_date=? WHERE product_id=?");
    $stmt->bind_param("siidsssi", $product_name, $category_id, $supplier_id, $quantity, $price, $imagePath, $expiration_date, $product_id);
    $stmt->execute();
    $stmt->close();

    header("Location: products_dashboard.php");
    exit();
}

// Fetch dropdowns
$categories = $conn->query("SELECT category_id, name FROM categories ORDER BY name ASC");
$suppliers = $conn->query("SELECT supplier_id, name FROM suppliers ORDER BY name ASC");

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="flex-1 p-10">
  <div class="max-w-xl mx-auto bg-white p-6 rounded-lg shadow">
    <h2 class="text-2xl font-bold text-gray-700 mb-6">Edit Product</h2>
    <form method="POST" enctype="multipart/form-data" class="space-y-4">

      <!-- Product Name -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Product Name</label>
        <input type="text" name="product_name" value="<?= htmlspecialchars($product['product_name']) ?>" required class="w-full border px-3 py-2 rounded mt-1">
      </div>

      <!-- Category -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Category</label>
        <select name="category_id" required class="w-full border px-3 py-2 rounded mt-1">
          <option value="">-- Select Category --</option>
          <?php while($cat = $categories->fetch_assoc()): ?>
            <option value="<?= $cat['category_id'] ?>" <?= $cat['category_id'] == $product['category_id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat['name']) ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <!-- Supplier with inline add -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Supplier</label>
        <select name="supplier_id" id="supplierDropdown" class="w-full border px-3 py-2 rounded mt-1" onchange="toggleSupplierInput()">
          <option value="">-- Select Supplier --</option>
          <?php while($supplier = $suppliers->fetch_assoc()): ?>
            <option value="<?= $supplier['supplier_id'] ?>" <?= $supplier['supplier_id'] == $product['supplier_id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($supplier['name']) ?>
            </option>
          <?php endwhile; ?>
          <option value="add_new">+ Add New Supplier</option>
        </select>

        <div id="newSupplierBox" class="hidden mt-3">
          <input type="text" name="new_supplier_name_inline" class="w-full border px-3 py-2 rounded mt-1" placeholder="Enter new supplier name">
        </div>
      </div>

      <!-- Quantity -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Quantity</label>
        <input type="number" step="0.01" name="quantity" value="<?= $product['quantity'] ?>" required class="w-full border px-3 py-2 rounded mt-1">
      </div>

      <!-- Expiration Date -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Expiration Date</label>
        <input type="date" name="expiration_date" value="<?= $product['expiration_date'] ?? '' ?>" class="w-full border px-3 py-2 rounded mt-1">
      </div>

      <!-- Price -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Price</label>
        <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" required class="w-full border px-3 py-2 rounded mt-1">
      </div>

      <!-- Image -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Product Image</label>
        <?php if(!empty($product['image'])): ?>
          <img src="<?= htmlspecialchars($product['image']) ?>" class="w-32 h-32 object-cover mb-2 rounded">
        <?php endif; ?>
        <input type="file" name="product_image" accept="image/*" class="w-full border px-3 py-2 rounded mt-1">
      </div>

      <!-- Submit -->
      <div class="pt-4">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded w-full">Update Product</button>
      </div>
    </form>
  </div>
</div>

<script>
function toggleSupplierInput(){
  let dropdown = document.getElementById('supplierDropdown');
  let box = document.getElementById('newSupplierBox');
  box.classList.toggle('hidden', dropdown.value !== 'add_new');
}
</script>

<?php include 'includes/footer.php'; ?>
