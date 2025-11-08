<!-- Add Alpine.js for toggling mobile sidebar -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<script src="https://kit.fontawesome.com/96e37b53f1.js" crossorigin="anonymous"></script>
<!-- Sidebar Layout -->
<div x-data="{ open: false }" class="flex min-h-screen bg-gray-100 overflow-hidden">

  <!-- ✅ Sticky Sidebar -->
  <div
    :class="open ? 'translate-x-0' : '-translate-x-full'"
    class="fixed top-0 left-0 z-30 w-64 h-screen bg-teal-700 text-white transform transition-transform duration-300 ease-in-out lg:translate-x-0 flex flex-col">
    
    <div class="p-4 border-b border-teal-600 flex items-center justify-between lg:justify-center">
      <h2 class="text-2xl font-bold">Inventory Menu</h2>
      <!-- Mobile Close Button -->
      <button @click="open = false" class="lg:hidden">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <nav class="flex-1 overflow-y-auto p-5 space-y-2 text-xl font-bold">
  <a href="index.php" class="flex items-center px-3 py-2 rounded hover:bg-teal-600 transition gap-2">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6" />
    </svg>
    Dashboard
  </a>
  
  <a href="products_dashboard.php" class="flex items-center px-3 py-2 rounded hover:bg-teal-600 transition gap-2">
   <i class="fa-brands fa-product-hunt"></i>
    Products
  </a>

  <a href="categories.php" class="flex items-center px-3 py-2 rounded hover:bg-teal-600 transition gap-2">
   <i class="fa-solid fa-list"></i>
    Categories
  </a>

  <a href="suppliers.php" class="flex items-center px-3 py-2 rounded hover:bg-teal-600 transition gap-2">
  <i class="fa-solid fa-truck-field"></i>
    Suppliers
  </a>

  <a href="add_sales.php" class="flex items-center px-3 py-2 rounded hover:bg-teal-600 transition gap-2">
    <i class="fa-solid fa-chart-simple"></i>
    Sales 
  </a>
</nav>


  </div>

  <!-- ✅ Main Content -->
  <div class="flex-1 flex flex-col lg:ml-64 min-h-screen overflow-hidden">
    
    <!-- Mobile Top Bar -->
    <header class="bg-white shadow-md p-4 flex justify-between items-center lg:hidden fixed top-0 left-0 right-0 z-20">
      <h1 class="text-xl font-semibold text-gray-700">Sari-Sari Inventory</h1>
      <button @click="open = true" class="text-teal-700">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>
    </header>

      </div>
    </main>
  </div>
</div>
