<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-error mb-4">
        <?= $_SESSION['error'] ?>
        <?php unset($_SESSION['error']) ?>
    </div>
<?php endif; ?>

<?php
$nominal = $transaksi['nominal_yang_dibayarkan'] ?? 0;
$dpp = $transaksi['dpp'] ?? ($nominal * 0.2);
$ppm = $transaksi['ppm'] ?? ($nominal * 0.11);
?>

<main class="min-h-screen pt-20">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold mb-6">Konfirmasi Pembayaran</h1>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 text-red-700 p-4 mb-4 rounded">
            <?= $_SESSION['error'] ?>
            <?php unset($_SESSION['error']) ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Detail Pesanan -->
            <div class="bg-gray-50 p-4 rounded-lg">
            <h2 class="text-lg font-semibold mb-4">Detail Pesanan</h2>
            
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Kode Booking:</span>
                    <span class="font-medium"><?= $transaksi['nomor_referensi'] ?></span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600">Armada:</span>
                    <span class="font-medium">
                        <?= $armada['nama_kendaraan'] ?> (<?= $armada['plat_kendaraan'] ?>)
                    </span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600">Durasi:</span>
                    <span class="font-medium">
                        <?php if(isset($transaksi['tanggal_mulai']) && isset($transaksi['tanggal_selesai'])): ?>
                        <?= date('d M Y', strtotime($transaksi['tanggal_mulai'])) ?> - 
                        <?= date('d M Y', strtotime($transaksi['tanggal_selesai'])) ?>
                        
                        <?php else: ?>
                        Durasi tidak tersedia
                        <?php endif; ?>
                    </span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600">Lokasi Penjemputan:</span>
                    <span class="font-medium">
                        <?= $transaksi['lokasi_penjemputan'] ?? 'Belum ditentukan' ?>
                    </span>
                </div>
            </div>
            </div>

            <!-- Form Pembayaran -->
            <form action="/payment/<?= $transaksi['nomor_referensi'] ?>/process" method="POST" class="space-y-4" enctype="multipart/form-data">>
            <div class="bg-blue-50 p-4 rounded-lg">
                <h2 class="text-lg font-semibold mb-2">Rincian Pembayaran</h2>
                
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span>Total Pembayaran:</span>
                    <span class="font-bold text-blue-600">
                    Rp<?= number_format($transaksi['nominal_yang_dibayarkan']) ?>
                    </span>
                </div>
                
                <div class="flex justify-between text-sm">
                    <span>DPP (20%):</span>
                    <span>Rp<?= number_format($dpp) ?></span>
                </div>
                
                <div class="flex justify-between text-sm">
                    <span>PPM (11%):</span>
                    <span>Rp<?= number_format($ppm) ?></span>
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="font-medium">Pilih Metode Pembayaran:</h3>
                
                <div class="flex items-center p-3 border rounded hover:bg-gray-50 cursor-pointer">
                <input type="radio" name="metode" value="cash" id="cash" required 
                        class="h-4 w-4 text-blue-600" checked>
                <label for="cash" class="ml-2">
                    <span class="block font-medium">Bayar Cash di Lokasi</span>
                    <span class="text-sm text-gray-600">Bayar langsung saat penjemputan armada</span>
                </label>
                </div>

                <div class="flex items-center p-3 border rounded hover:bg-gray-50 cursor-pointer">
                <input type="radio" name="metode" value="transfer" id="transfer" 
                        class="h-4 w-4 text-blue-600">
                <label for="transfer" class="ml-2">
                    <span class="block font-medium">Transfer Bank</span>
                    <span class="text-sm text-gray-600">
                    Transfer ke rekening BCA 123 456 7890 a.n PT Catur Pratama Sukses
                    </span>
                </label>
                </div>
            </div>

            <div id="transferSection" class="hidden">
                <div class="mt-4">
                <label class="block text-sm font-medium mb-1">Upload Bukti Transfer</label>
                <input type="file" name="bukti_pembayaran" 
                        class="w-full px-3 py-2 border rounded"
                        accept="image/*,.pdf">
                <p class="text-sm text-gray-500 mt-1">Format: JPG, PNG, PDF (maks. 2MB)</p>
                </div>
            </div>
            
            <button type="submit" 
                    class="w-full bg-green-600 text-white py-3 px-6 rounded-lg hover:bg-green-700 transition">
                Konfirmasi Pembayaran
            </button>
            </form>
        </div>
        </div>
    </div>
</main>

<script>
document.querySelectorAll('input[name="metode"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const transferSection = document.getElementById('transferSection');
        transferSection.style.display = this.value === 'transfer' ? 'block' : 'none';
    });
});
</script>