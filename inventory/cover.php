<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sari-Sari Store Inventory System</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Fade-in + floating animation */
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    .animate-fadeInUp {
      animation: fadeInUp 1s ease-out forwards;
    }
  </style>
</head>

<body 
  class="h-screen bg-cover bg-center bg-no-repeat relative overflow-hidden flex items-center justify-center" 
  style="background-image: url('assets/bg image.png');">

  <!-- Gradient overlay for better contrast -->
  <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/40 to-black/70"></div>

  <!-- Floating circles for modern effect -->
  <div class="absolute top-10 left-10 w-64 h-64 bg-green-400/20 rounded-full blur-3xl animate-pulse"></div>
  <div class="absolute bottom-10 right-10 w-72 h-72 bg-emerald-500/20 rounded-full blur-3xl animate-pulse"></div>

  <!-- Glass container -->
  <div class="relative z-10 bg-white/15 backdrop-blur-2xl border border-white/20 rounded-3xl shadow-[0_0_25px_rgba(0,0,0,0.3)] p-12 max-w-xl w-full text-center text-white animate-fadeInUp">

    <h1 class="text-4xl md:text-5xl font-extrabold mb-4 bg-gradient-to-r from-green-300 to-emerald-500 bg-clip-text text-transparent drop-shadow-md">
      RVM Enterprices Inventory System
    </h1>

    <p class="text-lg mb-8 text-gray-200">
      A smart, simple, and modern solution for managing your products, sales, and suppliers.
    </p>

    <a href="index.php"
      class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-emerald-600 hover:to-green-700 text-white px-8 py-3 rounded-xl text-lg font-semibold shadow-lg transition-transform transform hover:scale-105">
      Enter Dashboard →
    </a>

  </div>

  <!-- Floating footer text -->
  <footer class="absolute bottom-6 text-center text-gray-300 text-sm tracking-wide">
    © <?php echo date("Y"); ?> Sari-Sari Store Inventory System. All rights reserved.
  </footer>
</body>
</html>
