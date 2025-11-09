<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Access Denied</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px);}
      to { opacity: 1; transform: translateY(0);}
    }
    .animate-fadeIn { animation: fadeIn 0.6s ease-out forwards; }
  </style>
</head>
<body  class="h-screen bg-cover bg-center bg-no-repeat relative overflow-hidden flex items-center justify-center"  style="background-image: url('assets/bg image.png');">
 
   <!-- Gradient overlay for better contrast -->
  <div class="absolute inset-0 bg-gradient-to-b from-black/90 via-black/50 to-black/90"></div>

  <!-- Card -->
  <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl p-10 text-center shadow-2xl w-full max-w-sm animate-fadeIn">
    
   <!-- Warning Icon -->
<div class="flex items-center justify-center mb-6">
  <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
    <path stroke-linecap="round" stroke-linejoin="round" d="M10.49 3.86L1.82 18a2 2 0 001.71 3h18.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0zM12 9v4m0 4h.01"/>
  </svg>
</div>

    
    <!-- Heading -->
     
    <h1 class="text-3xl font-extrabold text-red-500 mb-4">
      OWNER ONLY!
    </h1>
    
    <!-- Subtext -->
    <p class="text-gray-300 mb-8">
      You don’t have permission to view this page.
    </p>
    
    <!-- Go Back Button -->
    <div class="flex justify-center">
      <button onclick="window.location.href='products_dashboard.php'" 
              class="flex items-center justify-center gap-3 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold py-3 px-8 rounded-2xl shadow-lg transition-all transform hover:scale-105 hover:shadow-2xl">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Go Back
      </button>
    </div>
    
  </div>

</body>
</html>
