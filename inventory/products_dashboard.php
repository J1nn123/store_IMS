<?php include 'includes/db.php'; ?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="flex-1 p-10">
  <div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold text-gray-800">Product Dashboard</h2>
    <a href="add_product.php"
      class="bg-green-600 text-white px-5 py-2 rounded-lg shadow hover:bg-green-700 transition duration-200">
      + Add Product
    </a>
  </div>

  <div class="overflow-x-auto bg-white shadow-lg rounded-xl">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-100 text-gray-700 uppercase text-xs font-semibold tracking-wider">
        <tr>
          <th class="px-4 py-3">Image</th>
          <th class="px-4 py-3">Product Name</th>
          <th class="px-4 py-3">Total Quantity</th>
          <th class="px-4 py-3">Average Price (₱)</th>
          <th class="px-4 py-3">First Added</th>
          <th class="px-4 py-3 text-center">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        <?php
        // Get grouped products with latest ID for edit/delete reference
        $result = $conn->query("
          SELECT 
            MAX(product_id) AS product_id,
            product_name,
            COALESCE(SUM(quantity), 0) AS total_quantity,
            ROUND(AVG(price), 2) AS avg_price,
            MIN(created_at) AS first_added,
            MAX(image) AS image
          FROM products
          GROUP BY product_name
          ORDER BY product_name ASC
        ");

        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            $id = $row['product_id'];
            $imagePath = !empty($row['image']) ? $row['image'] : "assets/no-image.png";

            echo "
            <tr class='hover:bg-gray-50 transition duration-150'>
              <td class='px-4 py-3'>
                <img src='" . htmlspecialchars($imagePath) . "' 
                     alt='Product Image' 
                     class='w-14 h-14 object-cover rounded-lg border border-gray-300 shadow-sm'>
              </td>
              <td class='px-4 py-3 font-medium text-gray-900'>" . htmlspecialchars($row['product_name']) . "</td>
              <td class='px-4 py-3 text-gray-600'>" . htmlspecialchars($row['total_quantity']) . "</td>
              <td class='px-4 py-3 font-semibold text-green-700'>₱" . number_format($row['avg_price'], 2) . "</td>
              <td class='px-4 py-3 text-gray-500'>" . htmlspecialchars($row['first_added']) . "</td>
              <td class='px-4 py-3 text-center space-x-3'>
                <a href='edit_product.php?id={$id}' 
                   class='text-blue-600 hover:text-blue-800 font-medium'>Edit</a>
                <a href='delete_product.php?product_id={$id}' 
                   class='text-red-600 hover:text-red-800 font-medium'
                   onclick=\"return confirm('Are you sure you want to delete this product?')\">
                   Delete
                </a>
              </td>
            </tr>";
          }
        } else {
          echo "
          <tr>
            <td colspan='6' class='text-center text-gray-500 py-6'>No products found</td>
          </tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</div>

<?php include 'includes/footer.php'; ?>