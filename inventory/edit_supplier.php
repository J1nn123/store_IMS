<?php
include 'includes/db.php';
$id = $_GET['id'];
$result = $conn->query("SELECT * FROM suppliers WHERE supplier_id = $id");
$supplier = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $contact = $_POST['contact_info'];
    $email = $_POST['email'];
    $stmt = $conn->prepare("UPDATE suppliers SET name=?, contact_info=?, email=? WHERE supplier_id=?");
    $stmt->bind_param("sssi", $name, $contact, $email, $id);
    $stmt->execute();
    header("Location: suppliers.php");
}
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="p-10">
    <h2 class="text-xl font-bold mb-4">Edit Supplier</h2>
    <form method="POST" class="space-y-4">
        <input name="name" value="<?php echo htmlspecialchars($supplier['name']); ?>" class="border px-4 py-2 w-full">
        <input name="contact_info" value="<?php echo htmlspecialchars($supplier['contact_info']); ?>" class="border px-4 py-2 w-full">
        <input name="email" type="email" value="<?php echo htmlspecialchars($supplier['email']); ?>" class="border px-4 py-2 w-full">
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Update</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
