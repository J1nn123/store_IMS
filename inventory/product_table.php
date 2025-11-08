<?php
include 'includes/db.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'date_desc'; // default to newest first

// Determine ORDER BY
switch ($sort) {
    case 'name_asc': $orderBy = "product_name ASC"; break;
    case 'name_desc': $orderBy = "product_name DESC"; break;
    case 'price_asc': $orderBy = "AVG(price) ASC"; break;
    case 'price_desc': $orderBy = "AVG(price) DESC"; break;
    case 'stock_asc': $orderBy = "SUM(quantity) ASC"; break;
    case 'stock_desc': $orderBy = "SUM(quantity) DESC"; break;
    case 'date_asc': $orderBy = "MIN(created_at) ASC"; break;
    case 'date_desc': $orderBy = "MIN(created_at) DESC"; break; // newest first
    default: $orderBy = "MIN(created_at) DESC"; break;
}

// SQL Query
if ($search !== '') {
  $stmt = $conn->prepare("
    SELECT 
      MAX(product_id) AS product_id,
      product_name,
      COALESCE(SUM(quantity),0) AS total_quantity,
      ROUND(AVG(price),2) AS avg_price,
      MIN(created_at) AS first_added,
      MAX(image) AS image
    FROM products
    WHERE product_name LIKE ?
    GROUP BY product_name
    ORDER BY $orderBy
  ");
  $likeSearch = "%$search%";
  $stmt->bind_param("s", $likeSearch);
  $stmt->execute();
  $result = $stmt->get_result();
} else {
  $result = $conn->query("
    SELECT 
      MAX(product_id) AS product_id,
      product_name,
      COALESCE(SUM(quantity),0) AS total_quantity,
      ROUND(AVG(price),2) AS avg_price,
      MIN(created_at) AS first_added,
      MAX(image) AS image
    FROM products
    GROUP BY product_name
    ORDER BY $orderBy
  ");
}


?>

<table class="min-w-full text-sm">
  
 <thead class="bg-gray-100 text-gray-700 uppercase text-xs font-semibold tracking-wider">
  <tr>
    <th class="px-4 py-3 text-left">Product</th>
    <th class="px-4 py-3 text-center">Total Quantity</th>
    <th class="px-4 py-3 text-center">SRP (₱)</th>
    <th class="px-4 py-3 text-center">First Added</th>
    <th class="px-4 py-3 text-center">Actions</th>
  </tr>
</thead>

  </thead>
 <tbody class="divide-y divide-gray-200">
<?php
if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $id = $row['product_id'];
    $imagePath = !empty($row['image']) ? $row['image'] : "assets/no-image.png";

    // ✅ Check if quantity is 0
    $quantityDisplay = ($row['total_quantity'] <= 0) 
      ? "<span class='text-red-600 font-semibold'>Out of Stock</span>" 
      : htmlspecialchars($row['total_quantity']);

    echo "
   <tr class='hover:bg-gray-50 transition duration-150'>
  <td class='px-4 py-3'>
    <div class='flex items-center space-x-3'>
      <img src='".htmlspecialchars($imagePath)."' 
           alt='Product Image' 
           class='w-12 h-12 object-cover rounded-md border border-gray-300 shadow-sm'>
      <span class='font-medium text-gray-900'>".htmlspecialchars($row['product_name'])."</span>
    </div>
  </td>
  <td class='px-4 py-3 text-gray-600 text-center align-middle'>
    {$quantityDisplay}
  </td>
  <td class='px-4 py-3 text-center font-semibold text-green-700 align-middle'>
    ₱".number_format($row['avg_price'],2)." 
    <span class='text-gray-500 text-xs'>(each)</span>
  </td>
  <td class='px-4 py-3 text-gray-500 text-center align-middle'>
    ".htmlspecialchars($row['first_added'])."
  </td>
  <td class='px-4 py-3 text-center space-x-3 align-middle'>
    <a href='edit_product.php?id={$id}' class='text-blue-600 hover:text-blue-800 font-medium'>Edit</a>
    <a href='delete_product.php?product_id={$id}' class='text-red-600 hover:text-red-800 font-medium' onclick=\"return confirm('Are you sure you want to delete this product?')\">Delete</a>
  </td>
</tr>
";
  }
} else {
  echo "<tr><td colspan='6' class='text-center text-gray-500 py-6'>No products found</td></tr>";
}
?>
</tbody>
</table>
