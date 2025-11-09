

<?php
 session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: no_access.php");
    exit;
}

include 'includes/db.php';
include 'includes/sidebar.php';


if (isset($_GET['id'])) {
    $sale_id = $_GET['id'];

    // First delete related rows in sales_items
    $conn->query("DELETE FROM sales_items WHERE sale_id = $sale_id");

    // Then delete from sales table
    $conn->query("DELETE FROM sales WHERE sale_id = $sale_id");

    // Redirect back to the previous page
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}
?>
