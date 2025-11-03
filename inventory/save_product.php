<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_inventory_system";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Sanitize input
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$name = $conn->real_escape_string($_POST['name']);
$category_id = intval($_POST['category_id']);
$supplier_id = intval($_POST['supplier_id']);
$quantity = intval($_POST['quantity']);
$price = floatval($_POST['price']);

if ($product_id > 0) {
    // Update existing product
    $sql = "UPDATE products SET 
            name = '$product_name,
            category_id = $category_id,
            supplier_id = $supplier_id,
            quantity = $quantity,
            price = $price
            WHERE product_id = $product_id";
} else {
    // Insert new product
    $sql = "INSERT INTO products (product_name, category_id, supplier_id, quantity, price) 
            VALUES ('$product_name', $category_id, $supplier_id, $quantity, $price)";
}

if ($conn->query($sql) === TRUE) {
    header("Location: inventory.php");
    exit();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
