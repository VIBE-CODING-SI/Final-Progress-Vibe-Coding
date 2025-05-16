<main class="container mx-auto px-4 py-12">
    <section class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-2xl p-8 md:p-12 mb-16 text-center text-white">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Trailer Kami</h1>
        <p class="text-xl md:text-2xl font-light max-w-3xl mx-auto">Temukan Trailer terbaik untuk kebutuhan logistik Anda</p>
        <div class="mt-8 flex justify-center">
            <div class="inline-flex rounded-md shadow">
                <a href="#vehicles" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-blue-700 bg-white hover:bg-gray-50 transition duration-300">
                    Lihat Armada Trailer
                </a>
            </div>
        </div>
    </section>

    <!-- Filter Section -->
    <div class="mb-12 bg-white rounded-xl shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4 text-gray-800">Filter Trailer</h2>
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

    <!-- Vehicle List -->
    <?php if (!empty($trailerList)): ?>
    <section id="vehicles" class="mb-16">
        <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">Armada Trailer Tersedia</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($trailerList as $trailer): ?>
                <?php
                    $statusKey = strtolower($trailer['status_kendaraan']);
                    $bgClass = $statusColors[$statusKey]['bg'] ?? 'bg-gray-100';
                    $textClass = $statusColors[$statusKey]['text'] ?? 'text-gray-800';
                ?>
                <div class="bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                    <div class="h-48 bg-gray-200 flex items-center justify-center">
                        <?php if (!empty($trailer['gambar_kendaraan'])): ?>
                            <img src="/uploads/armada/<?= htmlspecialchars($trailer['gambar_kendaraan']) ?>" alt="<?= htmlspecialchars($trailer['nama_kendaraan']) ?>" class="object-cover h-full w-full">
                        <?php else: ?>
                            <svg class="h-24 w-24 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 16l2.879-2.879m0 0a3 3 0 104.243-4.242 3 3 0 00-4.243 4.242zM21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        <?php endif; ?>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($trailer['nama_kendaraan']) ?></h3>
                                <span class="<?= $bgClass ?> <?= $textClass ?> text-xs font-semibold px-2.5 py-0.5 rounded">
                                    <?= htmlspecialchars(ucfirst($trailer['status_kendaraan'])) ?>
                                </span>
                        </div>
                        <div class="mb-4">
                            <span class="text-blue-600 font-semibold">Rp <?= number_format($trailer['harga_sewa'], 0, ',', '.') ?>/hari</span>
                        </div>
                        <ul class="space-y-2 mb-6">
                            <li class="text-gray-700">Plat: <span class="font-medium"><?= htmlspecialchars($trailer['plat_kendaraan']) ?></span></li>
                            <li class="text-gray-700">Kapasitas: <span class="font-medium"><?= htmlspecialchars($trailer['kapasitas_kendaraan']) ?> orang/ton</span></li>
                            <li class="text-gray-700">Tipe: <span class="font-medium"><?= htmlspecialchars($trailer['tipe_kendaraan']) ?></span></li>
                        </ul>
                        <div class="flex space-x-3">
                            <a href="/detail/<?= urlencode($trailer['id']) ?>" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded-lg text-center transition flex items-center justify-center">
                                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Detail
                            </a>
                            <?php if (strtolower($trailer['status_kendaraan']) === 'tersedia'): ?>
                            <a href="/order/<?= urlencode($trailer['id']) ?>" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg text-center transition flex items-center justify-center">
                                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Pesan
                            </a>
                            <?php else: ?>
                                <button
                                    class="flex items-center justify-center px-6 py-3 bg-gray-300 text-gray-500 cursor-not-allowed rounded-full font-semibold text-base transition-all shadow"
                                    disabled>
                                    <i class="fa-solid fa-calendar-xmark mr-2"></i> Tidak Tersedia
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
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