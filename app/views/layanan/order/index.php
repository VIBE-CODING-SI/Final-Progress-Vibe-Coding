<main class="min-h-screen pt-20">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold mb-6">Pesan Armada <?= $armada['nama_kendaraan'] ?></h1>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 text-red-700 p-4 mb-4 rounded">
            <?= $_SESSION['error'] ?>
            <?php unset($_SESSION['error']) ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Detail Armada -->
            <div class="bg-gray-50 p-4 rounded-lg">
            <img src="/uploads/armada/<?= $armada['gambar_kendaraan'] ?>" 
                alt="<?= $armada['nama_kendaraan'] ?>" 
                class="w-full h-48 object-cover rounded-lg mb-4">
            <h3 class="text-xl font-semibold"><?= $armada['nama_kendaraan'] ?></h3>
            <p class="text-gray-600"><?= $armada['plat_kendaraan'] ?></p>
            <div class="mt-4">
                <p class="text-lg font-bold text-blue-600">
                Rp<?= number_format($armada['harga_sewa']) ?>/hari
                </p>
            </div>
            </div>

            <!-- Form Pemesanan -->
            <form action="/order/<?= $armada['id'] ?>/store" method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Nama Lengkap</label>
                    <input type="text" name="nama" required 
                        class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" required 
                            class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500"
                            min="<?= date('Y-m-d') ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" required 
                            class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500"
                            min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Lokasi Penjemputan</label>
                    <textarea name="lokasi_penjemputan" required 
                            class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Nomor Container</label>
                    <input type="text" name="nomor_container" required 
                        class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Nama Kapal</label>
                    <input type="text" name="nama_kapal" required 
                        class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500">
                </div>

                <button type="submit" 
                        class="w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition">
                    Lanjut ke Pembayaran
                </button>
            </form>
        </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const tanggalMulai = document.querySelector('input[name="tanggal_mulai"]');
    const tanggalSelesai = document.querySelector('input[name="tanggal_selesai"]');
    const errorDiv = document.createElement('div');
    errorDiv.className = 'text-red-600 text-sm mt-1 hidden';
    form.parentNode.insertBefore(errorDiv, form.nextSibling);

    function validateDates() {
        const start = new Date(tanggalMulai.value);
        const end = new Date(tanggalSelesai.value);
        
        if (start && end && end < start) {
            errorDiv.textContent = 'Tanggal selesai tidak boleh sebelum tanggal mulai';
            errorDiv.classList.remove('hidden');
            return false;
        }
        
        errorDiv.classList.add('hidden');
        return true;
    }

    tanggalMulai.addEventListener('change', validateDates);
    tanggalSelesai.addEventListener('change', validateDates);

    form.addEventListener('submit', function(e) {
        if (!validateDates()) {
            e.preventDefault();
        }
    });
});
</script>
