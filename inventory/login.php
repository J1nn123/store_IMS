<?php
include 'includes/db.php';
session_start();

$success = false;
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password_hash'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['avatar'] = $user['avatar'];

            $full_name = $user['full_name'];
            $success = true;

            // PHP redirect based on role
            if ($_SESSION['role'] === 'admin') {
                header("Location: index.php");
                exit;
            } if ($_SESSION['role'] === 'employee') {
                header("Location: products_dashboard.php");
                exit;
            }

        } else {
            $message = "❌ Invalid password. Please try again.";
        }
    } else {
        $message = "⚠️ User not found. Please register first.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Inventory System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-screen bg-cover bg-center flex items-center justify-center" style="background-image: url('assets/bg image.png');">

 <div class="absolute inset-0 bg-gradient-to-b from-black/70 via-black/50 to-black/90"></div>
<div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl p-10 w-[400px] text-white shadow-2xl z-10">
    <h1 class="text-3xl font-bold mb-6 text-center bg-gradient-to-r from-green-300 to-emerald-500 bg-clip-text text-transparent">
        Welcome Back
    </h1>

    <!-- Display error message -->
    <?php if (!empty($message)): ?>
        <div class="mt-4 bg-red-500/80 text-white text-center py-2 px-4 rounded-lg mb-6">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- Login Form -->
    <form method="POST" class="space-y-4">
        <input type="text" name="username" placeholder="Username" required
               class="w-full px-4 py-3 rounded-lg bg-white/20 text-white placeholder-gray-200 focus:bg-white/30 outline-none">
        <input type="password" name="password" placeholder="Password" required
               class="w-full px-4 py-3 rounded-lg bg-white/20 text-white placeholder-gray-200 focus:bg-white/30 outline-none">
        <button type="submit"
                class="w-full bg-gradient-to-r from-green-400 to-emerald-500 hover:from-emerald-500 hover:to-green-600 text-white font-semibold py-3 rounded-lg shadow-lg">
            Login
        </button>
    </form>

    <p class="text-sm text-center mt-6 text-gray-300">
        Don’t have an account?
        <a href="register.php" class="text-emerald-300 hover:underline font-medium">Register here</a>
    </p>
</div>

</body>
</html>
