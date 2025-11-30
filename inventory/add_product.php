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

include 'includes/db.php'; 
include 'includes/header.php'; 
include 'includes/sidebar.php'; 
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- ✅ Modern Add Product Layout -->
<div class="flex-1 flex justify-center items-center min-h-screen bg-gray-100">
  <div class="bg-white/30 backdrop-blur-md border border-white/40 shadow-2xl rounded-2xl p-10 w-full max-w-xl">

    <h2 class="text-3xl font-bold text-center text-gray-800 mb-8 drop-shadow-sm">🛒 Add New Product</h2>

    <form action="add_product.php" method="POST" enctype="multipart/form-data" class="space-y-6">

      <!-- Product Name -->
      <div>
        <label class="block text-gray-700 font-semibold mb-1">Product Name</label>
        <input type="text" id="product_name" name="product_name" required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none transition">
      </div>

      <!-- Category -->
      <div>
        <label class="block text-gray-700 font-semibold mb-1">Category</label>
        <select name="category_id" required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400">
          <option value="">Select category</option>
          <?php
          $categories = $conn->query("SELECT * FROM categories");
          while ($cat = $categories->fetch_assoc()) {
              echo "<option value='{$cat['category_id']}'>" . htmlspecialchars($cat['name']) . "</option>";
          }
          ?>
        </select>
      </div>

      <!-- Supplier -->
      <div>
        <label class="block text-gray-700 font-semibold mb-1">Supplier</label>
        <select name="supplier_id" id="supplier_select" required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400">
          <option value="">Select supplier</option>
          <?php
          $suppliers = $conn->query("SELECT * FROM suppliers");
          while ($sup = $suppliers->fetch_assoc()) {
              echo "<option value='{$sup['supplier_id']}'>" . htmlspecialchars($sup['name']) . "</option>";
          }
          ?>
          <option value="add_new">➕ Add New Supplier</option>
        </select>

        <!-- Hidden new supplier input -->
        <div id="new_supplier_field" class="mt-3 hidden">
          <label class="block text-gray-700 font-semibold mb-1">New Supplier Name</label>
          <input type="text" name="new_supplier_name" id="new_supplier_name"
            placeholder="Enter new supplier name"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400">
        </div>
      </div>

      <!-- Quantity -->
      <div>
        <label class="block text-gray-700 font-semibold mb-1">Quantity</label>
        <input type="number" name="quantity" required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400">
      </div>

      <!-- Expiration Date -->
      <div>
        <label class="block text-gray-700 font-semibold mb-1">Expiration Date</label>
        <input type="date" name="expiration_date" id="expiration_date"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400">
      </div>

      <!-- Price -->
      <div>
        <label class="block text-gray-700 font-semibold mb-1">Price (₱)</label>
        <input type="number" step="0.01" id="price" name="price" required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400">
      </div>

      <!-- Product Image -->
      <div>
        <label class="block text-gray-700 font-semibold mb-1">Product Image</label>
        <input type="file" name="image" id="imageUpload" accept="image/*"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400">
        <div class="flex justify-center mt-3">
          <img id="imagePreview" class="hidden w-32 h-32 object-cover rounded-lg shadow-md border border-gray-300" />
        </div>
      </div>

      <!-- Submit -->
      <button type="submit" name="submit"
        class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-semibold shadow-md transition duration-300">
        💾 Save Product
      </button>
    </form>
  </div>
</div>

<!-- JS Scripts -->
<script>
// 🧠 AUTO-FETCH PRODUCT PRICE
document.getElementById('product_name').addEventListener('blur', function() {
  let productName = this.value.trim();
  if (productName === '') return;

  fetch('fetch_price.php?name=' + encodeURIComponent(productName))
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        document.getElementById('price').value = data.price;
      }
    })
    .catch(err => console.error('Error:', err));
});

// 🧩 Show/Hide New Supplier Field
document.getElementById('supplier_select').addEventListener('change', function() {
  const newSupplierField = document.getElementById('new_supplier_field');
  if (this.value === 'add_new') {
    newSupplierField.classList.remove('hidden');
    document.getElementById('new_supplier_name').required = true;
  } else {
    newSupplierField.classList.add('hidden');
    document.getElementById('new_supplier_name').required = false;
  }
});

// 🖼️ Image Preview
document.getElementById('imageUpload').addEventListener('change', function(e) {
  const [file] = e.target.files;
  const preview = document.getElementById('imagePreview');
  if (file) {
    preview.src = URL.createObjectURL(file);
    preview.classList.remove('hidden');
  } else {
    preview.classList.add('hidden');
  }
});
</script>

<?php
if (isset($_POST['submit'])) {
    $product_name = $conn->real_escape_string($_POST['product_name']);
    $category_id = intval($_POST['category_id']);
    $supplier_id = $_POST['supplier_id'];
    $quantity = intval($_POST['quantity']);
    $price = floatval($_POST['price']);
    $expiration_date = !empty($_POST['expiration_date']) ? $conn->real_escape_string($_POST['expiration_date']) : NULL;

    // ✅ Add new supplier if chosen
    if ($supplier_id === 'add_new' && !empty($_POST['new_supplier_name'])) {
        $new_supplier_name = $conn->real_escape_string($_POST['new_supplier_name']);
        $conn->query("INSERT INTO suppliers (name) VALUES ('$new_supplier_name')");
        $supplier_id = $conn->insert_id;
    } else {
        $supplier_id = intval($supplier_id);
    }

    // ✅ Image upload
    $imagePath = "";
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $imageName = time() . "_" . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $imageName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imagePath = $targetFile;
        }
    }

    // ✅ Insert product
    $sql = "INSERT INTO products (product_name, category_id, supplier_id, quantity, price, image, expiration_date, created_at)
            VALUES ('$product_name', $category_id, $supplier_id, $quantity, $price, '$imagePath', " . ($expiration_date ? "'$expiration_date'" : "NULL") . ", NOW())";

    if ($conn->query($sql) === TRUE) {
        echo "
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Product Added!',
                text: 'The new product has been successfully saved.',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = 'products_dashboard.php';
            });
        </script>";
    } else {
        echo "
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Something went wrong while adding the product.',
            });
        </script>";
    }
}
?>

<?php include 'includes/footer.php'; ?>
