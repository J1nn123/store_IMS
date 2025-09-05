<?php
include 'includes/db.php';
$id = $_GET['id'];
$conn->query("DELETE FROM suppliers WHERE supplier_id = $id");
header("Location: suppliers.php");
exit;
?>
