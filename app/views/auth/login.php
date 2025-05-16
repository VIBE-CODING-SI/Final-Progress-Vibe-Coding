<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login | CPSHaulage</title>
  <!-- Tailwind CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css"
    rel="stylesheet"
  />
  <!-- Alpine.js -->
  <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <!-- Font Awesome -->
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
  />
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100">
  <div class="w-full max-w-md bg-white rounded-lg shadow p-8" x-data="{ show: false }">
    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm text-gray-600" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2">
            <li class="inline-flex items-center">
                <a href="/" class="hover:text-blue-600 transition flex items-center">
                    <i class="fa-solid fa-house mr-1"></i> Beranda
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fa-solid fa-chevron-right mx-2 text-xs text-gray-400"></i>
                    <span class="font-semibold text-gray-800">Login</span>
                </div>
            </li>
        </ol>
    </nav>
    <h1 class="text-3xl font-bold text-center text-blue-700 mb-2">CPS Haulage</h1>
    <h2 class="text-xl font-semibold text-center text-gray-800 mb-4">Portal Karyawan</h2>
    <p class="text-sm text-gray-600 text-center mb-6">
      Selamat datang di portal karyawan CPSHaulage. Silakan login dengan akun Anda untuk mengakses dashboard karyawan.
    </p>

    <?php if (!empty($_SESSION['error'])): ?>
      <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
        <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
      </div>
    <?php endif; ?>

    <form action="/login" method="POST" class="space-y-5">
      <div>
        <label for="email" class="block text-gray-700 font-medium">Email Karyawan</label>
        <input
          id="email"
          name="email"
          type="email"
          placeholder="mail@gmail.com"
          required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>

      <div class="relative" x-data>
        <label for="password" class="block text-gray-700 font-medium">Password</label>
        <input
          id="password"
          name="password"
          :type="show ? 'text' : 'password'"
          placeholder="Masukkan password"
          required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
        <button
          type="button"
          @click="show = !show"
          class="absolute right-3 top-2/3 transform -translate-y-1/2 text-gray-600"
        >
          <template x-if="!show"><i class="fas fa-eye-slash"></i></template>
          <template x-if="show"><i class="fas fa-eye"></i></template>
        </button>
      </div>

      <button
        type="submit"
        class="w-full bg-blue-600 text-white py-2 rounded-lg font-semibold hover:bg-blue-700 transition"
      >
        Masuk
      </button>
    </form>

  </div>
</body>
</html>
