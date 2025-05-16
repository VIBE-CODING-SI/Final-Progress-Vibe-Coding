<main class="container mx-auto px-4 py-12">
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
                    <span class="font-semibold text-gray-800">Tronton</span>
                </div>
            </li>
        </ol>
    </nav>
    
    <section class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-2xl p-8 md:p-12 mb-16 text-center text-white">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Tronton Kami</h1>
        <p class="text-xl md:text-2xl font-light max-w-3xl mx-auto">Pilih Tronton yang tepat untuk kebutuhan transportasi Anda</p>
        <div class="mt-8 flex justify-center">
            <div class="inline-flex rounded-md shadow">
                <a href="#vehicles" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-blue-700 bg-white hover:bg-gray-50 transition duration-300">
                    Lihat Armada Kami
                </a>
            </div>
        </div>
    </section>

    <!-- Vehicle Filter-->
    <div class="mb-12 bg-white rounded-xl shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4 text-gray-800">Filter Tronton</h2>
        <form id="filterForm" method="GET">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label for="capacity" class="block text-sm font-medium text-gray-700 mb-1">Kapasitas</label>
                    <select name="capacity" id="capacity" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option <?= ($_GET['capacity'] ?? '') === 'Semua' ? 'selected' : '' ?>>Semua</option>
                        <option <?= ($_GET['capacity'] ?? '') === '10-15 ton' ? 'selected' : '' ?>>10-15 ton</option>
                        <option <?= ($_GET['capacity'] ?? '') === '15-20 ton' ? 'selected' : '' ?>>15-20 ton</option>
                        <option <?= ($_GET['capacity'] ?? '') === '20+ ton' ? 'selected' : '' ?>>20+ ton</option>
                    </select>
                </div>
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Harga</label>
                    <select name="price" id="price" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option <?= ($_GET['price'] ?? '') === 'Semua' ? 'selected' : '' ?>>Semua</option>
                        <option <?= ($_GET['price'] ?? '') === 'Terendah' ? 'selected' : '' ?>>Terendah</option>
                        <option <?= ($_GET['price'] ?? '') === 'Tertinggi' ? 'selected' : '' ?>>Tertinggi</option>
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option <?= (strtolower($_GET['status'] ?? '') === 'semua' || !isset($_GET['status'])) ? 'selected' : '' ?>>Semua</option>
                        <option <?= (strtolower($_GET['status'] ?? '') === 'tersedia') ? 'selected' : '' ?>>Tersedia</option>
                        <option <?= (strtolower($_GET['status'] ?? '') === 'digunakan') ? 'selected' : '' ?>>Digunakan</option>
                        <option <?= (strtolower($_GET['status'] ?? '') === 'maintenance') ? 'selected' : '' ?>>Maintenance</option>
                        <option <?= (strtolower($_GET['status'] ?? '') === 'nonaktif') ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md transition duration-300">
                        Terapkan Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Vehicle Listing -->
    <?php if (!empty($trontonList)): ?>
    <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        <?php foreach ($trontonList as $armada): ?>
        <div class="bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
            <div class="h-48 bg-gray-200 flex items-center justify-center overflow-hidden">
                    <img src="/uploads/armada/<?= htmlspecialchars($armada['gambar_kendaraan']) ?>" alt="<?= htmlspecialchars($armada['nama_kendaraan']) ?>" class="w-full h-full object-cover">
            </div>
            <div class="p-6">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($armada['nama_kendaraan']) ?></h3>
                    <span class="<?= $armada['bgClass'] ?> <?= $armada['textClass'] ?> text-xs font-semibold px-2.5 py-0.5 rounded">
                        <?= htmlspecialchars(ucfirst($armada['status_kendaraan'])) ?>
                    </span>
                </div>
                <div class="mb-4">
                    <span class="text-blue-600 font-semibold">Rp <?= number_format($armada['harga_sewa'], 0, ',', '.') ?>/hari</span>
                </div>
                <ul class="space-y-2 mb-6">
                    <li class="flex items-start">
                        <i class="fas fa-check text-blue-500 mr-2 mt-0.5 flex-shrink-0"></i>
                        <span class="text-gray-700">Kapasitas: <span class="font-medium"><?= htmlspecialchars($armada['kapasitas_kendaraan']) ?> ton</span></span>
                    </li>
                </ul>
                <div class="flex space-x-3">
                    <a href="/detail/<?= urlencode($armada['id']) ?>" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded-lg text-center transition flex items-center justify-center">
                        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Detail
                    </a>

                    <?php if (strtolower($armada['status_kendaraan']) === 'tersedia'): ?>
                        <a href="/order/<?= urlencode($armada['id']) ?>" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg text-center transition flex items-center justify-center">
                            <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Pesan
                        </a>
                    <?php else: ?>
                        <button class="flex-1 bg-gray-300 text-gray-500 font-medium py-2 px-4 rounded-lg text-center cursor-not-allowed flex items-center justify-center" disabled>
                            <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Tidak Tersedia
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </section>
    <?php else: ?>
    <p class="text-center text-gray-500 mt-8">Tidak ada armada tronton tersedia saat ini.</p>
    <?php endif; ?>

    <!-- Call to Action -->
    <section class="bg-gray-50 rounded-2xl p-8 md:p-12 text-center">
        <h2 class="text-3xl font-bold text-gray-800 mb-4">Butuh Bantuan Memilih Tronton?</h2>
        <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">Tim ahli kami siap membantu Anda menemukan solusi transportasi yang tepat untuk kebutuhan bisnis Anda.</p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="#" class="px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition duration-300 shadow-sm flex items-center gap-2">
                <i class="fab fa-whatsapp"></i>
                Hubungi Kami
            </a>
        </div>
    </section>
</main>