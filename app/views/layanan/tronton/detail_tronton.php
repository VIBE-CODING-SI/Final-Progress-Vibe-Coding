<main class="container mx-auto px-4 py-20">
    <!-- Breadcrumb -->
    <nav class="container mx-auto px-4 pt-8 pb-2 text-sm" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2 text-gray-600">
            <li class="inline-flex items-center">
                <a href="/" class="inline-flex items-center hover:text-blue-600 transition">
                    <i class="fa-solid fa-house mr-1"></i> Beranda
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fa-solid fa-chevron-right mx-2 text-xs text-gray-400"></i>
                    <a href="/tronton" class="hover:text-blue-600 transition">Tronton</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center font-semibold text-gray-800">
                    <i class="fa-solid fa-chevron-right mx-2 text-xs text-gray-400"></i>
                    <?= htmlspecialchars($armada['nama_kendaraan'] ?? 'Detail Armada') ?>
                </div>
            </li>
        </ol>
    </nav>
    
    <!-- Wrapper Detail Kendaraan -->
    <section class="bg-white rounded-3xl shadow-2xl overflow-hidden transition-all duration-300">
        <div class="grid grid-cols-1 md:grid-cols-2">

        <!-- Gambar Armada -->
        <div class="relative h-64 md:h-full">
            <img src="/uploads/armada/<?= htmlspecialchars($armada['gambar_kendaraan']) ?>" alt="<?= htmlspecialchars($armada['nama_kendaraan']) ?>"
                class="w-full h-full object-cover object-center transition-transform duration-300 hover:scale-105">
            <!-- Badge Status -->
            <?php 
                // mapping warna status
                $statusColors = [
                    'tersedia' => ['bg' => 'bg-green-600', 'text' => 'text-white'],
                    'nonaktif' => ['bg' => 'bg-red-600', 'text' => 'text-white'],
                    'digunakan' => ['bg' => 'bg-blue-600', 'text' => 'text-white'],
                    'maintenance' => ['bg' => 'bg-yellow-600', 'text' => 'text-black'],
                ];
                $status = strtolower($armada['status_kendaraan'] ?? '');
                $bgClass = $statusColors[$status]['bg'] ?? 'bg-gray-500';
                $textClass = $statusColors[$status]['text'] ?? 'text-white';
            ?>
            <div class="absolute top-5 left-5 <?= $bgClass ?> <?= $textClass ?> text-sm px-4 py-1 rounded-full shadow-lg flex items-center font-medium">
                <i class="fa-solid"></i> <?= ucfirst($status) ?>
            </div>
        </div>

        <!-- Konten Deskripsi -->
        <div class="p-8 md:p-12 flex flex-col justify-center space-y-6">
            <!-- Judul dan Deskripsi -->
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2"><?= htmlspecialchars($armada['nama_kendaraan'] ?? 'Nama Armada') ?></h1>
            </div>

            <!-- Spesifikasi Detail -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-base text-gray-800">
                <div class="flex items-center"><i class="fa-solid fa-weight-hanging text-blue-600 mr-3"></i> <strong>Kapasitas:</strong> <?= htmlspecialchars($armada['kapasitas_kendaraan'] ?? '-') ?> Ton</div>
                <div class="flex items-center"><i class="fa-solid fa-money-bill-wave text-blue-600 mr-3"></i> <strong>Harga:</strong> Rp <?= number_format($armada['harga_sewa'] ?? 0, 0, ',', '.') ?> / hari</div>
                <div class="flex items-center space-x-3">
                    <strong class="text-gray-700">Status:</strong>
                    <span 
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                            <?= $statusColors[$status]['bg'] ?? 'bg-gray-300' ?> 
                            <?= $statusColors[$status]['text'] ?? 'text-white' ?>
                            ring-2 ring-offset-1 ring-white
                            animate-pulse"
                        title="Status kendaraan: <?= ucfirst($status) ?>"
                        aria-label="Status kendaraan <?= ucfirst($status) ?>">
                        <i class="fa-solid fa-circle mr-2"></i> <?= ucfirst($status) ?>
                    </span>
                </div>
            </div>
            <!-- Harga - Dinamis -->
            <?php
            $hargaHarian = $armada['harga_sewa'] ?? 0;
            $hargaMingguan = $hargaHarian * 6;
            $hargaBulanan = $hargaHarian * 22;
            ?>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-4">
            <!-- Harian -->
            <div class="relative group bg-white/80 border border-gray-200 rounded-2xl px-5 py-6 shadow-md hover:shadow-lg transition duration-300 ease-in-out transform hover:-translate-y-1">
                <div class="absolute top-3 right-4">
                <i class="fas fa-calendar-day text-indigo-500 text-base"></i>
                </div>
                <p class="text-xs font-semibold text-gray-400 tracking-wide mb-1">Harian</p>
                <p class="text-xl font-bold text-indigo-700 mb-1">Rp<?= number_format($hargaHarian, 0, ',', '.') ?></p>
                <p class="text-xs text-gray-500">Termasuk sopir & BBM</p>
            </div>

            <!-- Mingguan -->
            <div class="relative group bg-white/80 border border-gray-200 rounded-2xl px-5 py-6 shadow-md hover:shadow-lg transition duration-300 ease-in-out transform hover:-translate-y-1">
                <div class="absolute top-3 right-4">
                <i class="fas fa-calendar-week text-indigo-500 text-base"></i>
                </div>
                <p class="text-xs font-semibold text-gray-400 tracking-wide mb-1">Mingguan</p>
                <p class="text-xl font-bold text-indigo-700 mb-1">Rp<?= number_format($hargaMingguan, 0, ',', '.') ?></p>
                <p class="text-xs text-gray-500">Proyek jangka pendek</p>
            </div>

            <!-- Bulanan -->
            <div class="relative group bg-white/80 border border-gray-200 rounded-2xl px-5 py-6 shadow-md hover:shadow-lg transition duration-300 ease-in-out transform hover:-translate-y-1">
                <div class="absolute top-3 right-4">
                <i class="fas fa-calendar-alt text-indigo-500 text-base"></i>
                </div>
                <p class="text-xs font-semibold text-gray-400 tracking-wide mb-1">Bulanan</p>
                <p class="text-xl font-bold text-indigo-700 mb-1">Rp<?= number_format($hargaBulanan, 0, ',', '.') ?></p>
                <p class="text-xs text-gray-500">Solusi ekonomis</p>
            </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex flex-col sm:flex-row gap-4 pt-4">
                <a href="https://wa.me/6289696499238?text=<?= urlencode('Halo saya tertarik dengan armada ' . ($armada['nama_kendaraan'] ?? 'armada')) ?>"
                target="_blank"
                class="flex items-center justify-center px-6 py-3 bg-green-500 hover:bg-green-600 text-white rounded-full font-semibold text-base transition-all shadow-lg hover:scale-105">
                <i class="fa-brands fa-whatsapp mr-2"></i> Tanya via WhatsApp
            </a>

            <?php if (strtolower($status) === 'tersedia'): ?>
                <a href="/order/<?= urlencode($armada['id']) ?>"
                    class="flex items-center justify-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-full font-semibold text-base transition-all shadow-lg hover:scale-105">
                    <i class="fa-solid fa-calendar-check mr-2"></i> Pesan Sekarang
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
    </section>
</main>