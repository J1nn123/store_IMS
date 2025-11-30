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
// suppliers.php
include 'includes/db.php';
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="flex-1 p-10">
  
    <h2 class="text-2xl font-bold mb-4 text-gray-700">Supplier List</h2>


  <div class="overflow-x-auto bg-white shadow rounded-lg">
    <table class="min-w-full divide-y divide-gray-200 text-sm">


  


    <div class="overflow-x-auto bg-white shadow rounded-lg">
      <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead>
          <tr class="bg-gray-200 w-full text-left text-gray-700 font-medium">
            <th class="px-4 py-3">Supplier ID</th>
            <th class="px-4 py-3">Name</th>
            <th class="px-4 py-3">Contact Numbers</th>
            <th class="px-4 py-3">Email</th>
             <th class="px-4 py-3">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
        <?php
        $result = $conn->query("SELECT * FROM suppliers ORDER BY supplier_id ASC");

        if ($result && $result->num_rows > 0):
          while ($row = $result->fetch_assoc()):
        ?>
        
        <tr class="hover:bg-gray-50">
         
          <td class="px-4 py-2"><?php echo htmlspecialchars($row['supplier_id']); ?></td>
          
          <td class="px-4 py-2"><?php echo htmlspecialchars($row['name']); ?></td>
          <td class="px-4 py-2"><?php echo htmlspecialchars($row['contact_info']); ?></td>
          <td class ="px-4 py-2"><?php echo htmlspecialchars($row['email']); ?></td>
         <td class="px-4 py-2">
  <a href="edit_supplier.php?id=<?php echo $row['supplier_id']; ?>" class="text-blue-600 hover:underline">Edit</a>
  

</td>

        </tr>
        <?php
          endwhile;
        else:
        ?>
        <tr>
          <td class="px-4 py-6 text-center text-gray-500" colspan="5">
            No suppliers found.
          </td>
        </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
