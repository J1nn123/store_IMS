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
 <a href="add_product.php"
     class="bg-green-600 text-white px-5 py-2 rounded-lg shadow hover:bg-green-700 transition duration-200">
     + Add Product
    
  </a>
  
</div>

  </div> 


  <!-- 🔍 Search + Sort Form -->
 <!-- Search + Sort Row -->
<div class="mb-4 flex justify-between items-center">
  <!-- Left: Search Input -->
  <input type="text" id="searchInput" name="search" 
         placeholder="Search product name..." 
         class="border border-gray-300 rounded-lg px-4 py-2 w-64 focus:outline-none focus:ring-2 focus:ring-green-500">

  <!-- Right: Sort Dropdown -->
<select id="sortSelect" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
  <option value="name_asc">Product Name (A-Z)</option>
  <option value="name_desc">Product Name (Z-A)</option>
  <option value="price_asc">Price (Low-High)</option>
  <option value="price_desc">Price (High-Low)</option>
  <option value="stock_asc">Stock (Low-High)</option>
  <option value="stock_desc">Stock (High-Low)</option>
  <option value="date_asc">Date Added (Old-New)</option>
  <option value="date_desc" selected>Date Added (New-Old)</option> <!-- default selected -->
</select>

</div>

  <!-- Table Container -->
  <div class="overflow-x-auto bg-white shadow-lg rounded-xl" id="productTableContainer">
    <?php include 'product_table.php'; ?>
  </div>
</div>

<!-- JS for search + sorting -->
<script>
const searchInput = document.getElementById('searchInput');
const sortSelect = document.getElementById('sortSelect');
const tableContainer = document.getElementById('productTableContainer');

function loadTable() {
  const searchQuery = searchInput.value.trim();
  const sortOption = sortSelect.value;

  fetch(`product_table.php?search=${encodeURIComponent(searchQuery)}&sort=${encodeURIComponent(sortOption)}`)
    .then(response => response.text())
    .then(data => tableContainer.innerHTML = data)
    .catch(err => console.error(err));
}

// Live search
searchInput.addEventListener('keyup', loadTable);
// Sorting
sortSelect.addEventListener('change', loadTable);
</script>

<?php include 'includes/footer.php'; ?>
