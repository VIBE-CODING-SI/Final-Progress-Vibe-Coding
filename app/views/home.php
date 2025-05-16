<!-- Landing Page Section -->
<?php
require_once 'company/hero.php';
require_once 'company/services.php';
require_once 'company/about.php';
require_once 'company/testimoni.php';
?>

<!-- Tombol Scroll to Top -->
<button id="scrollToTopBtn" onclick="scrollToHero()"
  class="fixed bottom-6 right-6 bg-gradient-to-br from-blue-600 to-indigo-700 text-white text-xl w-16 h-16 rounded-full shadow-[0_10px_20px_rgba(0,0,0,0.3)] opacity-0 pointer-events-none transition-all duration-500 z-50
    hover:scale-110 hover:shadow-[0_15px_25px_rgba(0,0,0,0.4)] hover:from-blue-500 hover:to-indigo-600 focus:outline-none focus:ring-4 focus:ring-blue-300 animate-bounce-slow">
  <i class="fa fa-arrow-up text-2xl animate-pulse"></i>
</button>

<!-- Script -->
<script>
  function showMoreInfo() {
    const button = document.getElementById('cta-button');
    const desc = document.getElementById('cta-description');

    button.style.display = 'none';
    desc.classList.remove('opacity-0', 'max-h-0', 'overflow-hidden');
    desc.classList.add('opacity-100', 'max-h-[500px]');
  }
  
  const scrollToTopBtn = document.getElementById("scrollToTopBtn");

  window.addEventListener("scroll", () => {
    const layananSection = document.getElementById("layanan");
    const layananTop = layananSection.getBoundingClientRect().top;

    // Tampilkan tombol jika sudah scroll melewati section layanan
    if (layananTop < -200) {
      scrollToTopBtn.classList.remove("opacity-0", "pointer-events-none");
    } else {
      scrollToTopBtn.classList.add("opacity-0", "pointer-events-none");
    }
  });

  function scrollToHero() {
    const heroSection = document.querySelector("section");
    heroSection.scrollIntoView({ behavior: "smooth" });
  }
</script>