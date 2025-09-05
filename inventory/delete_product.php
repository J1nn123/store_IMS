<?php
include 'includes/db.php';

if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id']; // Make sure this matches the URL parameter

    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->close();
}

// Redirect back to the product list
header("Location: products_dashboard.php");
exit();
?>
