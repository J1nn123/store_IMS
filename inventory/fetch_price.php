<?php
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
