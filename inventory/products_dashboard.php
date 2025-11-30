<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include 'includes/db.php';
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="flex-1 p-10">
  <div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold text-gray-800">Products Dashboard</h2>
    <div class="text-right mb-6">
      <a href="add_product.php" class="bg-green-600 text-white px-5 py-2 rounded-lg shadow hover:bg-green-700 transition duration-200">
         + Add Product
      </a>
    </div>
  </div>

  <!-- Search + Sort + Category -->
  <div class="mb-4 flex justify-between items-center space-x-4">
    <input type="text" id="searchInput" placeholder="Search product name..." class="border border-gray-300 rounded-lg px-4 py-2 w-64 focus:outline-none focus:ring-2 focus:ring-green-500">

    <div class="flex space-x-2">
      <select id="sortSelect" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
        <option value="name_asc">Product Name (A-Z)</option>
        <option value="name_desc">Product Name (Z-A)</option>
        <option value="price_asc">Price (Low-High)</option>
        <option value="price_desc">Price (High-Low)</option>
        <option value="stock_asc">Stock (Low-High)</option>
        <option value="stock_desc">Stock (High-Low)</option>
        <option value="date_asc">Date Added (Old-New)</option>
        <option value="date_desc" selected>Date Added (New-Old)</option>
      
      </select>

      <select id="categorySelect" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
        <option value="">All Categories</option>
        <?php
        $catResult = $conn->query("SELECT category_id, name FROM categories ORDER BY name ASC");
        while ($cat = $catResult->fetch_assoc()) {
            echo "<option value='{$cat['category_id']}'>".htmlspecialchars($cat['name'])."</option>";
        }
        ?>
      </select>
    </div>
  </div>

  <!-- Table Container -->
  <div id="productTableContainer" class="bg-white shadow-lg rounded-xl relative overflow-x-auto">
    <div class="max-h-[750px] overflow-y-auto">
      <div id="productTableBody">
        <?php include 'product_table.php'; ?>
      </div>
    </div>
  </div>
</div>

<!-- DELETE CONFIRMATION MODAL -->
<div id="deleteModal" class="absolute inset-0 bg-black/30 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-80 text-center shadow-xl border">
        <h2 class="text-lg font-semibold mb-4">Are you sure you want to delete this product?</h2>
        <div class="flex justify-center space-x-4">
            <button id="confirmDelete" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Yes, Delete</button>
            <button id="cancelDelete" class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300">Cancel</button>
        </div>
    </div>
</div>

<script>
let deleteUrl = ""; 

function attachDeleteEvents() {
    const modal = document.getElementById("deleteModal");
    const confirmBtn = document.getElementById("confirmDelete");
    const cancelBtn = document.getElementById("cancelDelete");

    document.querySelectorAll(".deleteBtn").forEach(btn => {
        btn.addEventListener("click", e => {
            e.preventDefault();
            deleteUrl = btn.getAttribute("href");
            modal.classList.remove("hidden", "opacity-0");
        });
    });

    if(cancelBtn) cancelBtn.addEventListener("click", () => modal.classList.add("hidden"));
    if(confirmBtn) confirmBtn.addEventListener("click", () => {
        if(deleteUrl) window.location.href = deleteUrl;
    });

    // Price click events
    document.querySelectorAll('.product-price').forEach(price => {
        price.addEventListener('click', () => {
            const productId = price.getAttribute('data-product-id');
            const productPrice = price.getAttribute('data-price');
            alert(`Product ID: ${productId}\nPrice: ₱${parseFloat(productPrice).toFixed(2)}`);
        });
    });
}

function loadTable() {
    const search = document.getElementById('searchInput').value.trim();
    const sort = document.getElementById('sortSelect').value;
    const category = document.getElementById('categorySelect').value;

    fetch(`product_table.php?search=${encodeURIComponent(search)}&sort=${encodeURIComponent(sort)}&category=${encodeURIComponent(category)}`)
        .then(res => res.text())
        .then(html => {
            document.getElementById('productTableBody').innerHTML = html;
            attachDeleteEvents(); 
        })
        .catch(err => console.error(err));
}

document.getElementById('searchInput').addEventListener('keyup', loadTable);
document.getElementById('sortSelect').addEventListener('change', loadTable);
document.getElementById('categorySelect').addEventListener('change', loadTable);

attachDeleteEvents(); 
</script>

<?php include 'includes/footer.php'; ?>
