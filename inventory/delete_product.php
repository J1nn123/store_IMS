

<?php
//employee only restriction
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: no_access.php");
    exit;
}

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
