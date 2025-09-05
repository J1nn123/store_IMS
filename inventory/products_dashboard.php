<?php include 'includes/db.php'; ?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="flex-1 p-10">
  <div class="flex justify-between items-center mb-4">
    <h2 class="text-2xl font-bold text-gray-700">Product List</h2>
    <a href="add_product.php" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
      + Add Product
    </a>
  </div>

  <div class="overflow-x-auto bg-white shadow rounded-lg">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
      <thead class="bg-gray-100 text-left text-gray-700 font-medium">
        <tr>
          <th class="px-4 py-3">Name</th>
          <th class="px-4 py-3">Quantity</th>
          <th class="px-4 py-3">Price (SRP)</th>
          <th class="px-4 py-3">Date Added</th>
          <th class="px-4 py-3">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        <?php
        $result = $conn->query("SELECT * FROM products");
        while ($row = $result->fetch_assoc()) {
          $id = $row['product_id'];
          echo "<tr class='hover:bg-gray-50'>
                  <td class='px-4 py-2'>".htmlspecialchars($row['product_name'])."</td>
                  <td class='px-4 py-2'>".htmlspecialchars($row['quantity'])."</td>
                  <td class='px-4 py-2'>".htmlspecialchars($row['price'])."</td>
                  <td class='px-4 py-2'>".htmlspecialchars($row['created_at'])."</td>
                  <td class='px-4 py-2 space-x-2'>
                    <a href='edit_product.php?id={$id}' class='text-blue-600 hover:underline'>Edit</a>
                    <a href='delete_product.php?product_id={$id}' class='text-red-600 hover:underline' onclick=\"return confirm('Are you sure you want to delete this product?')\">Delete</a>
                  </td>
                </tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
