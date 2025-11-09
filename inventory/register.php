<?php
include 'includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if ($full_name && $username && $email && $password && $role) {

        // Hash password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Handle avatar upload
        $avatar_path = 'assets/user_avatar.png'; // default avatar
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
            $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $new_name = uniqid('avatar_', true) . "." . $ext;
            $upload_dir = 'uploads/';
            
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
            
            $target_file = $upload_dir . $new_name;
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target_file)) {
                $avatar_path = $target_file;
            }
        }

        // Insert into DB including avatar
        $stmt = $conn->prepare("INSERT INTO users (full_name, username, email, password_hash, role, avatar) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $full_name, $username, $email, $password_hash, $role, $avatar_path);

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

    <form method="POST" enctype="multipart/form-data">
      <input type="text" name="full_name" placeholder="Full Name/nickname" required class="w-full mb-3 px-4 py-2 rounded bg-white/10 focus:bg-white/30 outline-none" />
      <input type="text" name="username" placeholder="Username" required class="w-full mb-3 px-4 py-2 rounded bg-white/10 focus:bg-white/30 outline-none" />
      <input type="email" name="email" placeholder="Email" required class="w-full mb-3 px-4 py-2 rounded bg-white/10 focus:bg-white/30 outline-none" />
      <input type="password" name="password" placeholder="Password" required class="w-full mb-3 px-4 py-2 rounded bg-white/10 focus:bg-white/30 outline-none" />
      
      <!-- Role dropdown -->
      <div class="relative w-full mb-3">
        <select name="role" required class="w-full px-4 py-2 rounded bg-white/10 focus:bg-white/30 outline-none text-white appearance-none">
          <option value="" disabled selected hidden>Select Role</option>
          <option value="admin" class="text-black">Admin</option>
          <option value="employee" class="text-black">Employee</option>
        </select>
        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-white pointer-events-none">▼</span>
      </div>

      <!-- Profile Image Upload -->
      <label class="text-white/80 mb-1 block">Profile Image (optional)</label>
      <input type="file" name="avatar" accept="image/*" class="w-full mb-5 px-4 py-2 rounded bg-white/10 focus:bg-white/30 outline-none text-white" />

       
      <button type="submit" class="w-full bg-gradient-to-r from-green-400 to-emerald-500 hover:scale-105 transition-transform text-white font-semibold py-2 rounded-lg">
        Register
      </button>
    </form>

    <p class="text-sm text-center mt-4">
      Already have an account? <a href="login.php" class="text-green-300 hover:underline">Login here</a>
    </p>
  </div>

  <footer class="absolute bottom-6 text-center text-white text-sm tracking-wide">
    © <?php echo date("Y"); ?> RVM Sari-Sari Store Inventory System. All rights reserved.
  </footer>

</body>
</html>
