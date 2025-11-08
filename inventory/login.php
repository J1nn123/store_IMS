<?php
include 'includes/db.php';
session_start();

$success = false;
$full_name = "";

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
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $full_name = $user['full_name'];
            $success = true;
        } else {
            $full_name = "❌ Invalid password. Please try again.";
        }
    } else {
        $full_name = "⚠️ User not found. Please register first.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - Inventory System</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Simple fade-in animation for popup */
    @keyframes fadeInDown {
      0% { opacity: 0; transform: translateY(-20px);}
      100% { opacity: 1; transform: translateY(0);}
    }
    .popup {
      animation: fadeInDown 0.5s ease-out forwards;
    }
  </style>
</head>
<body class="h-screen flex items-center justify-center bg-gradient-to-br from-green-700 via-emerald-700 to-green-900" style="background-image: url('assets/bg image.png');">

  <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/40 to-black/70"></div>
  <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl p-10 w-[400px] text-white shadow-2xl">
    
    <h1 class="text-3xl font-bold mb-6 text-center bg-gradient-to-r from-green-300 to-emerald-500 bg-clip-text text-transparent">
      Welcome Back
    </h1>

    <form method="POST" class="space-y-4">
      <input type="text" name="username" placeholder="Username" required
             class="w-full px-4 py-3 rounded-lg bg-white/20 text-white placeholder-gray-200 focus:bg-white/30 focus:ring-2 focus:ring-emerald-400 outline-none transition duration-300">
      <input type="password" name="password" placeholder="Password" required
             class="w-full px-4 py-3 rounded-lg bg-white/20 text-white placeholder-gray-200 focus:bg-white/30 focus:ring-2 focus:ring-emerald-400 outline-none transition duration-300">
      <button type="submit"
              class="w-full bg-gradient-to-r from-green-400 to-emerald-500 hover:from-emerald-500 hover:to-green-600 transition-transform transform hover:scale-105 text-white font-semibold py-3 rounded-lg shadow-lg">
        Login
      </button>
    </form>

    <p class="text-sm text-center mt-6 text-gray-300">
      Don’t have an account?
      <a href="register.php" class="text-emerald-300 hover:underline font-medium">Register here</a>
    </p>
  </div>

  <!-- Modern popup for greeting -->
  <?php if ($success): ?>
    <div id="greetingPopup" class="fixed inset-0 flex items-center justify-center z-50 pointer-events-none">
      <div class="popup bg-white/90 backdrop-blur-lg rounded-xl p-8 text-center shadow-2xl max-w-sm w-full pointer-events-auto">
        <h2 class="text-2xl font-bold text-emerald-600 mb-2">Welcome,</h2>
        <p class="text-lg text-gray-800 mb-4"><?= htmlspecialchars($full_name) ?></p>
        <p class="text-sm text-gray-500">Redirecting to your dashboard...</p>
      </div>
    </div>

    <script>
      setTimeout(() => {
        document.getElementById('greetingPopup').style.display = 'none';
        window.location.href = 'index.php';
      }, 2000); // popup visible for 2 seconds
    </script>
  <?php endif; ?>

</body>
 <footer class="absolute bottom-6 text-center text-gray-300 text-sm tracking-wide">
    © <?php echo date("Y"); ?> RVM Sari-Sari Store Inventory System. All rights reserved.
  </footer>
</html>
