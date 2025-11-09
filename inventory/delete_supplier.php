<?php
//admin only restriction
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


$id = $_GET['id'];
$conn->query("DELETE FROM suppliers WHERE supplier_id = $id");
header("Location: suppliers.php");
exit;
?>
