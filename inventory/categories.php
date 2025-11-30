
<?php
ob_start(); // Start output buffering
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Admin only restriction
if ($_SESSION['role'] !== 'admin') {
    header("Location: no_access.php");
    exit;
}

include 'includes/db.php';

// Handle add, update, delete BEFORE including header/sidebar
if (isset($_POST['add_category'])) {
    $name = trim($_POST['category_name']);
    if (!empty($name)) {
        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: categories.php");
    exit;
}

if (isset($_POST['update_category'])) {
    $category_id = $_POST['category_id'];
    $name = trim($_POST['category_name']);
    if (!empty($name)) {
        $stmt = $conn->prepare("UPDATE categories SET name = ? WHERE category_id = ?");
        $stmt->bind_param("si", $name, $category_id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: categories.php");
    exit;
}

if (isset($_GET['delete'])) {
    $category_id = $_GET['delete'];
    $check = $conn->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
    $check->bind_param("i", $category_id);
    $check->execute();
    $check->bind_result($count);
    $check->fetch();
    $check->close();

    if ($count == 0) {
        $stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: categories.php");
    exit;
}

// Now safe to include header and sidebar
include 'includes/header.php';
include 'includes/sidebar.php';

ob_end_flush(); // Send output buffer
?>
<?php

// Fetch all categories with product counts
$result = $conn->query("
    SELECT c.category_id, c.name, COUNT(p.product_id) AS product_count
    FROM categories c
    LEFT JOIN products p ON c.category_id = p.category_id
    GROUP BY c.category_id, c.name
    ORDER BY c.category_id DESC
");
?>

<div class="flex-1 p-10 bg-gray-100 min-h-screen">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Manage Categories</h1>

    <!-- Add Category Form -->
    <form method="POST" class="bg-white p-6 rounded-xl shadow mb-8 max-w-md">
        <h2 class="text-xl font-semibold mb-4 text-gray-700">Add New Category</h2>
        <input type="text" name="category_name" required placeholder="Category Name"
            class="w-full p-3 border border-gray-300 rounded-lg mb-4 focus:outline-none focus:ring-2 focus:ring-blue-400" />
        <button type="submit" name="add_category"
            class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 transition">+ Add Category</button>
    </form>

    <!-- Category Table -->
    <div class="bg-white p-6 rounded-xl shadow">
        <h2 class="text-xl font-semibold mb-4 text-gray-700">Category List</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm w-full border border-gray-200 rounded-lg">
                <thead>
                    <tr class="bg-gray-100 text-gray-700 uppercase text-xs font-semibold">
                        <th class="py-3 px-4">Category ID</th>
                        <th class="py-3 px-4">Category Name</th>
                        <th class="py-3 px-4">Product Count</th>
                        <th class="py-3 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 divide-y divide-gray-200">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-3 px-4 font-medium text-gray-900"><?= $row['category_id']; ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($row['name']); ?></td>
                            <td class="py-3 px-4 text-center"><?= $row['product_count']; ?></td>
                            <td class="py-3 px-4 text-center space-x-2">
                                <button onclick="openEditModal('<?= $row['category_id']; ?>', '<?= htmlspecialchars(addslashes($row['name'])); ?>')"
                                    class="bg-yellow-400 text-white px-3 py-1 rounded hover:bg-yellow-500">Edit</button>
                                <?php if ($row['product_count'] == 0): ?>
                                    <a href="categories.php?delete=<?= $row['category_id']; ?>"
                                        onclick="return confirm('Are you sure you want to delete this category?')"
                                        class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Delete</a>
                                <?php else: ?>
                                    <span class="text-gray-400 text-xs italic">In Use</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">Edit Category</h2>
        <form method="POST">
            <input type="hidden" name="category_id" id="edit_id">
            <input type="text" name="category_name" id="edit_name"
                class="w-full p-3 border border-gray-300 rounded-lg mb-4 focus:outline-none focus:ring-2 focus:ring-blue-400"
                required>
            <div class="flex justify-end">
                <button type="button" onclick="closeEditModal()"
                    class="mr-2 px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                <button type="submit" name="update_category"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditModal(id, name) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_name').value = name;
        document.getElementById('editModal').classList.remove('hidden');
        document.getElementById('editModal').classList.add('flex');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }
</script>

<?php include 'includes/footer.php'; ?>