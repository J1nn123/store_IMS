<?php 
include 'includes/db.php';

$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'date_desc';
$category = $_GET['category'] ?? '';

switch ($sort) {
    case 'name_asc': $orderBy = "p.product_name ASC"; break;
    case 'name_desc': $orderBy = "p.product_name DESC"; break;
    case 'price_asc': $orderBy = "avg_price ASC"; break;
    case 'price_desc': $orderBy = "avg_price DESC"; break;
    case 'stock_asc': $orderBy = "total_qty ASC"; break;
    case 'stock_desc': $orderBy = "total_qty DESC"; break;
    case 'date_asc': $orderBy = "first_added ASC"; break;
    default: $orderBy = "first_added DESC"; break;
}

// Fetch products
$sql = "
SELECT 
    MAX(p.product_id) AS product_id,
    p.product_name,
    COALESCE(SUM(p.quantity),0) AS total_qty,
    ROUND(AVG(p.price),2) AS avg_price,
    MIN(p.created_at) AS first_added,
    MAX(p.image) AS image,
    c.name AS category_name,
    MAX(p.expiration_date) AS expiration_date
FROM products p
LEFT JOIN categories c ON p.category_id = c.category_id
WHERE p.product_name LIKE ?
";

$params = ["%$search%"];
$types = "s";

if(!empty($category)) {
    $sql .= " AND p.category_id = ?";
    $types .= "i";
    $params[] = $category;
}

$sql .= " GROUP BY p.product_name, c.name ORDER BY $orderBy";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<table class="min-w-full text-sm border-collapse">
<thead class="bg-gray-100 text-gray-700 uppercase text-xs font-semibold sticky top-0 z-10">
<tr>
    <th class="px-4 py-3 text-left">Product</th>
    <th class="px-4 py-3 text-center">Category</th>
    <th class="px-4 py-3 text-center">Total Qty</th>
    <th class="px-4 py-3 text-center">SRP (₱)</th>
    <th class="px-4 py-3 text-center">Date Added</th>
    <th class="px-4 py-3 text-center">Actions</th>
</tr>
</thead>
<tbody class="divide-y divide-gray-200">
<?php
if($result->num_rows>0){
    while($row=$result->fetch_assoc()){
        $id = $row['product_id'];
        $img = $row['image'] ?: "assets/no-image.png";

        // Check if expired
        $isExpired = (!empty($row['expiration_date']) && strtotime($row['expiration_date']) < time());
        $expiredText = $isExpired ? " <span class='text-red-600 font-bold'>(Expired)</span>" : "";

        // Tooltip content
        $tooltip = "Category: ".htmlspecialchars($row['category_name'] ?: "Uncategorized") .
                   "\nStock: ".$row['total_qty'].
                   "\nPrice: ₱".number_format($row['avg_price'],2).
                   "\nExpiration: ".($row['expiration_date'] ?: 'N/A');

        echo "
        <tr class='hover:bg-gray-50'>
            <td class='px-4 py-3'>
                <div class='flex items-center space-x-3' title='".htmlspecialchars($tooltip)."'>
                    <img src='{$img}' class='w-12 h-12 object-cover rounded border'>
                    <span class='font-medium text-gray-900'>{$row['product_name']}{$expiredText}</span>
                </div>
            </td>
            <td class='px-4 py-3 text-center'>".($row['category_name'] ?: "Uncategorized")."</td>
            <td class='px-4 py-3 text-center'>".($row['total_qty']>0?$row['total_qty']:"<span class='text-red-600 font-semibold'>Out of Stock</span>")."</td>
            <td class='px-4 py-3 text-center'>
                <span class='product-price cursor-pointer text-gray-800 font-semibold' data-product-id='{$id}' data-price='{$row['avg_price']}'>
                    ₱".number_format($row['avg_price'],2)." (each)
                </span>
            </td>
            <td class='px-4 py-3 text-center'>{$row['first_added']}</td>
            <td class='px-4 py-3 text-center'>
                <a href='edit_product.php?id={$id}' class='bg-blue-500/30 text-blue-900 px-3 py-1 rounded border'>Edit</a>
                <a href='delete_product.php?product_id={$id}' class='deleteBtn bg-red-500/30 text-red-900 px-3 py-1 rounded border'>Delete</a>
            </td>
        </tr>";
    }
}else{
    echo "<tr><td colspan='6' class='text-center text-gray-500 py-6'>No products found</td></tr>";
}
?>
</tbody>
</table>
