<!-- Container utama -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- Metrics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">

        <!-- Transaksi Penjualan -->
        <div class="bg-white rounded-lg shadow-sm p-6 overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Transaksi Penjualan</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">
                        <?= count($transaksi ?? []) ?>
                    </p>
                </div>
                <div class="bg-blue-100 p-4 rounded-full shrink-0">
                    <i class="fas fa-file-invoice-dollar text-blue-500 text-2xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-green-500 text-sm">
                <i class="fas fa-arrow-up mr-1"></i>
                +<?= rand(1, 10) ?>%
            </div>
        </div>

        <!-- Jumlah Tronton -->
        <div class="bg-white rounded-lg shadow-sm p-6 overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Jumlah Tronton</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2"><?= count($trontonList ?? []) ?></p>
                </div>
                <div class="bg-orange-100 p-4 rounded-full shrink-0">
                    <i class="fas fa-truck-moving text-orange-500 text-2xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-gray-500 text-sm">
                <i class="fas fa-minus mr-1"></i> Unit
            </div>
        </div>

        <!-- Jumlah Trailer -->
        <div class="bg-white rounded-lg shadow-sm p-6 overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Jumlah Trailer</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2"><?= count($trailerList ?? []) ?></p>
                </div>
                <div class="bg-green-100 p-4 rounded-full shrink-0">
                    <i class="fas fa-truck text-green-500 text-2xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-gray-500 text-sm">
                <i class="fas fa-minus mr-1"></i> Unit
            </div>
        </div>

    </div>

    <!-- Chart Section -->
    <div class="bg-white rounded-lg shadow-sm p-6 overflow-x-auto">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold">Grafik Penjualan</h3>
            <button class="text-gray-500">
                <i class="fas fa-ellipsis-v"></i>
            </button>
        </div>

    <!-- Chart Canvas -->
    <?php include __DIR__ . '/year_charts.php'; ?>
    <?php include __DIR__ . '/month_charts.php'; ?>
</div>