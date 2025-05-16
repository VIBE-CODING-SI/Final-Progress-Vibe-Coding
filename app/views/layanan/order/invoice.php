<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-error mb-4">
        <?= $_SESSION['error'] ?>
        <?php unset($_SESSION['error']) ?>
    </div>
<?php endif; ?>

<main class="min-h-screen pt-20 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-xl p-8">
            <div class="flex justify-between items-center mb-8 border-b pb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Invoice #<?= $transaksi['nomor_referensi'] ?></h1>
                    <p class="text-gray-600 mt-2">Tanggal: <?= date('d M Y', strtotime($transaksi['tanggal'])) ?></p>
                </div>
                <span class="px-4 py-2 rounded-full text-sm font-medium <?= $transaksi['status_pembayaran'] === 'lunas' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                    <?= ucfirst(str_replace('_', ' ', $transaksi['status_pembayaran'])) ?>
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <!-- Detail Pelanggan -->
                <div class="space-y-4">
                    <h2 class="text-xl font-semibold text-gray-800">Detail Pelanggan</h2>
                    <div class="space-y-2">
                        <p class="text-gray-600"><?= $transaksi['customer'] ?></p>
                        <p class="text-gray-600"><?= $transaksi['nama_kapal'] ?></p>
                        <p class="text-gray-600"><?= $transaksi['lokasi_penjemputan'] ?></p>
                    </div>
                </div>

                <!-- Detail Penyewaan -->
                <div class="space-y-4">
                    <h2 class="text-xl font-semibold text-gray-800">Detail Penyewaan</h2>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Armada:</span>
                            <span class="font-medium">
                                <?= $armada['nama_kendaraan'] ?? 'N/A' ?> 
                                (<?= $armada['plat_kendaraan'] ?? 'N/A' ?>)
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Durasi:</span>
                            <span class="font-medium">
                                <?= date('d M Y', strtotime($transaksi['tanggal_mulai'])) ?> - 
                                <?= date('d M Y', strtotime($transaksi['tanggal_selesai'])) ?>
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Hari:</span>
                            <span class="font-medium"><?= $transaksi['durasi'] ?> Hari</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rincian Pembayaran -->
            <div class="bg-blue-50 rounded-lg p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Rincian Pembayaran</h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Harga per Hari:</span>
                        <span class="font-medium">Rp<?= number_format($armada['harga_sewa'] ?? 0, 0, ',', '.') ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">DPP (20%):</span>
                        <span class="font-medium">Rp<?= number_format($transaksi['dpp'], 0, ',', '.') ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">PPM (11%):</span>
                        <span class="font-medium">Rp<?= number_format($transaksi['ppm'], 0, ',', '.') ?></span>
                    </div>
                    <div class="flex justify-between pt-4 border-t border-blue-200">
                        <span class="text-lg font-bold text-gray-800">Total Pembayaran:</span>
                        <span class="text-lg font-bold text-blue-600">Rp<?= number_format($transaksi['nominal_yang_dibayarkan'], 0, ',', '.') ?></span>
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="https://wa.me/6281234567890?text=Konfirmasi%20Pembayaran%20<?= $transaksi['nomor_referensi'] ?>" 
                   target="_blank"
                   class="flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    Hubungi via WhatsApp
                </a>
                
                <a href="/" 
                   class="flex items-center justify-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-800 px-6 py-3 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</main>