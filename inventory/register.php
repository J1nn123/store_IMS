<?php
include 'includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($full_name && $username && $email && $password) {
        // Hash password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Insert into DB
        $stmt = $conn->prepare("INSERT INTO users (full_name, username, email, password_hash) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $full_name, $username, $email, $password_hash);

        if ($stmt->execute()) {
            echo "<script>alert('Registration successful! You can now log in.'); window.location='login.php';</script>";
        } else {
            echo "<script>alert('Username or email already exists!');</script>";
        }
    } else {
        echo "<script>alert('Please fill out all fields.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register - Inventory System</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-screen bg-cover bg-center bg-no-repeat relative overflow-hidden flex items-center justify-center"  style="background-image: url('assets/bg image.png');">

  <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/40 to-black/70"></div>

<div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-8 w-96 text-white">
  
  <h1 class="text-2xl font-bold mb-6 text-center">Create Account</h1>

  <form method="POST">
    <input type="text" name="full_name" placeholder="Full Name" required class="w-full mb-3 px-4 py-2 rounded bg-white/10 focus:bg-white/30 outline-none" />
    <input type="text" name="username" placeholder="Username" required class="w-full mb-3 px-4 py-2 rounded bg-white/10 focus:bg-white/30 outline-none" />
    <input type="email" name="email" placeholder="Email" required class="w-full mb-3 px-4 py-2 rounded bg-white/10 focus:bg-white/30 outline-none" />
    <input type="password" name="password" placeholder="Password" required class="w-full mb-5 px-4 py-2 rounded bg-white/10 focus:bg-white/30 outline-none" />

    <button type="submit" class="w-full bg-gradient-to-r from-green-400 to-emerald-500 hover:scale-105 transition-transform text-white font-semibold py-2 rounded-lg">
      Register
    </button>
  </form>

  <p class="text-sm text-center mt-4">
    Already have an account? <a href="login.php" class="text-green-300 hover:underline">Login here</a>
  </p>
</div>

</body>
 <footer class="absolute bottom-6 text-center text-white text-sm tracking-wide">
    © <?php echo date("Y"); ?> RVM Sari-Sari Store Inventory System. All rights reserved.
  </footer>
</html>
