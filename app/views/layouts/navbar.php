<nav id="navbar" class="fixed top-0 w-full z-50 transition-all duration-300 transform">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center h-20">
      
      <!-- Logo Kiri -->
      <div class="flex-shrink-0">
        <a href="/" class="text-2xl font-bold text-white tracking-wide">
          <img src="/assets/logo.png" alt="Logo" class="h-10 w-auto">
        </a>
      </div>

      <!-- Menu Tengah -->
      <nav class="hidden md:flex space-x-8 items-center relative">

        <a href="/" class="text-blue-600 hover:text-blue-300 transition">Beranda</a>
        <a href="#tentang" class="text-blue-600 hover:text-blue-300 transition">Tentang</a>
        <a href="#layanan" class="text-blue-600 hover:text-blue-300 transition">Layanan</a>
        <div class="relative">
          <button id="dropdownBtn" class="inline-flex items-center text-blue-600 hover:text-blue-300 transition focus:outline-none">
            Armada <i class="fas fa-caret-down ml-1"></i>
          </button>
          <div id="dropdownMenu" class="absolute left-0 mt-2 w-40 bg-black bg-opacity-90 rounded-md shadow-lg opacity-0 invisible transition-opacity duration-300 z-50">
            <a href="/tronton" class="block px-4 py-2 text-white hover:bg-blue-600 hover:text-white">Tronton</a>
            <a href="/trailer" class="block px-4 py-2 text-white hover:bg-blue-600 hover:text-white">Trailer</a>
          </div>
        </div>  
      </nav>

      <!-- Button Kanan -->
      <div class="hidden md:block">
        <a href="https://wa.me/6289696499238?text=<?= urlencode('Halo saya ingin bertanya tentang CPS Haulage min') ?>" target="_blank" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-full shadow transition">
          Contact Us
        </a>
      </div>

      <!-- Hamburger Mobile -->
      <div class="md:hidden">
        <button id="menuToggle" class="text-white focus:outline-none">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>
    </div>
  </div>

  <!-- Mobile Menu -->
  <div id="mobileMenu" class="hidden md:hidden bg-black bg-opacity-80 px-4 pb-4 space-y-2">
    <a href="/" class="block text-white hover:text-blue-300">Beranda</a>
    <a href="#tentang" class="block text-white hover:text-blue-300">Tentang</a>
    <a href="#layanan" class="block text-white hover:text-blue-300">Layanan</a>
    <a href="#fleet" class="block text-white hover:text-blue-300">Armada</a>
    <a href="https://wa.me/6289696499238?text=<?= urlencode('Halo saya ingin bertanya tentang CPS Haulage min') ?>" target="_blank" class="block text-center bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-md font-medium">Contact Us</a>
  </div>
</nav>


<!-- Script -->
<script>
  document.querySelectorAll('nav a[href^="#"], #mobileMenu a[href^="#"]').forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault(); // cegah lompat langsung
      const targetID = this.getAttribute('href').substring(1); // ambil tanpa "#"
      const targetEl = document.getElementById(targetID);
      if (targetEl) {
        targetEl.scrollIntoView({ behavior: 'smooth' });
        // Jika kamu ingin menutup menu mobile setelah klik (opsional)
        if (!menu.classList.contains('hidden')) {
          menu.classList.add('hidden');
        }
      }
    });
  });

  const dropdownBtn = document.getElementById('dropdownBtn');
  const dropdownMenu = document.getElementById('dropdownMenu');

  dropdownBtn.addEventListener('click', () => {
    const isVisible = !dropdownMenu.classList.contains('opacity-0');
    if (isVisible) {
      dropdownMenu.classList.add('opacity-0', 'invisible');
      dropdownMenu.classList.remove('opacity-100', 'visible');
    } else {
      dropdownMenu.classList.remove('opacity-0', 'invisible');
      dropdownMenu.classList.add('opacity-100', 'visible');
    }
  });

  const toggle = document.getElementById('menuToggle');
  const menu = document.getElementById('mobileMenu');
  toggle.addEventListener('click', () => {
    menu.classList.toggle('hidden');
  });

  let lastScrollTop = 0;
  const navbar = document.getElementById('navbar');

  window.addEventListener("scroll", function () {
    let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

  if (scrollTop <= 10) {
    // Navbar di posisi paling atas
    navbar.classList.remove('bg-black', 'shadow', 'navbar-scrolled');
    navbar.classList.remove('-translate-y-full');
    navLinks.forEach(link => {
      link.classList.remove('text-white');
      link.classList.add('text-blue-600');
    });
  } else if (scrollTop > lastScrollTop) {
    // Scroll ke bawah, sembunyikan navbar
    navbar.classList.add('-translate-y-full');
  } else {
    // Scroll ke atas, navbar muncul dan teks jadi putih
    navbar.classList.remove('-translate-y-full');
    navbar.classList.add('bg-black', 'shadow', 'navbar-scrolled');
    navLinks.forEach(link => {
      link.classList.remove('text-blue-600');
      link.classList.add('text-white');
    });
  }

    lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
  });
</script>