<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

//admin only restriction

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: no_access.php");
    exit;
} 

include 'includes/db.php';

header('Content-Type: application/json');

if (isset($_GET['name'])) {
    $name = $conn->real_escape_string($_GET['name']);
    $query = $conn->query("SELECT price FROM products WHERE product_name = '$name' LIMIT 1");

    if ($query->num_rows > 0) {
        $data = $query->fetch_assoc();
        echo json_encode(['success' => true, 'price' => $data['price']]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>
