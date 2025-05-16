<div
    class="bg-white rounded-lg shadow-sm p-6 mt-4"
    x-data="penjualanTable(<?= htmlspecialchars(json_encode($transaksi), ENT_QUOTES, 'UTF-8') ?>)"
    x-cloak
    x-init="initDataTable()"
>
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Manajemen Penjualan</h3>
        <button
            @click="showModal = true; modalType = 'import'; modalTitle = 'Import Data CSV'"
            class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600"
        >
            <i class="fas fa-file-import mr-2"></i>Import Data
        </button>
    </div>

    <table id="penjualanTable" class="w-full table-auto stripe hover" style="width:100%">
        <thead>
            <tr class="bg-gray-50">
                <th class="px-4 py-2 text-left">Tanggal</th>
                <th class="px-4 py-2 text-left">Customer</th>
                <th class="px-4 py-2 text-left">Container</th>
                <th class="px-4 py-2 text-left">Status Bayar</th>
                <th class="px-4 py-2 text-left">Status Transaksi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data diisi via DataTables -->
        </tbody>
    </table>

    <!-- Modal Import -->
    <div
        x-show="showModal"
        x-cloak
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
        <div class="bg-white rounded-lg w-1/3" @click.outside="showModal = false">
            <div class="p-4 border-b flex justify-between items-center">
                <h3 class="text-lg font-semibold" x-text="modalTitle"></h3>
                <button @click="showModal = false" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="p-4">
                <form action="/dashboard/penjualan/import" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">File CSV</label>
                        <input 
                            type="file" 
                            name="csv_file" 
                            accept=".csv" 
                            required 
                            class="w-full px-3 py-2 border rounded"
                        >
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" @click="showModal = false" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                            Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tambahkan modal detail di bawah modal import -->
    <div
        x-show="showDetailModal"
        x-cloak
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
        <div class="bg-white rounded-lg w-1/3" @click.outside="showDetailModal = false">
            <div class="p-4 border-b flex justify-between items-center">
                <h3 class="text-lg font-semibold">Detail Transaksi</h3>
                <button @click="showDetailModal = false" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="p-4 space-y-3" x-html="detailContent"></div>
        </div>
    </div>
</div>

<?php ob_start() ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
function penjualanTable() {
    return {
        showModal: false,
        showDetailModal: false,
        modalType: '',
        modalTitle: '',
        
        initDataTable() {
            $('#penjualanTable').DataTable({
                serverSide: true,
                processing: true,
                ajax: {
                    url: '/dashboard/penjualan/datatable',
                    type: 'GET',
                    error: function(xhr) {
                        console.error('Gagal memuat data:', xhr.responseText);
                    }
                },
                columns: [
                    { 
                        data: 'tanggal',
                        render: data => data ? new Date(data).toLocaleDateString('id-ID') : '-'
                    },
                    { 
                        data: 'customer',
                        render: data => data || '-'
                    },
                    { 
                        data: 'nomor_container',
                        render: data => data || '-'
                    },
                    { 
                        data: 'status_pembayaran',
                        render: data => `
                            <span class="px-2 py-1 rounded text-xs ${
                                data === 'lunas' 
                                    ? 'bg-green-100 text-green-800' 
                                    : 'bg-red-100 text-red-800'
                            }">
                                ${data ? data.replace('_', ' ') : '-'}
                            </span>`
                    },
                    { 
                        data: 'status_transaksi',
                        render: data => `
                            <span class="px-2 py-1 rounded text-xs ${
                                data === 'selesai' 
                                    ? 'bg-green-100 text-green-800' 
                                    : data === 'diproses' 
                                        ? 'bg-yellow-100 text-yellow-800' 
                                        : 'bg-gray-100 text-gray-800'
                            }">
                                ${data ? data.replace('_', ' ') : '-'}
                            </span>`
                    },
                    { 
                        data: null,
                        render: function(data, type, row) {
                            return `<button 
                                @click="showDetail(${row.id})"
                                class="text-blue-500 hover:text-blue-700">
                                <i class="fas fa-eye"></i>
                            </button>`;
                        }
                    }
                ],
                order: [[0, 'desc']],
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ entri",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    },
                    emptyTable: "Tidak ada data yang tersedia",
                    processing: "Memproses...",
                    loadingRecords: "Memuat..."
                }
            });
        },
        async showDetail(id) {
            try {
                const response = await fetch(`/dashboard/penjualan/${id}`);
                const data = await response.json();
                
                this.selectedTransaksi = data;
                this.detailContent = `
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="font-medium">Tanggal:</label><p>${new Date(data.tanggal).toLocaleDateString('id-ID')}</p></div>
                        <div><label class="font-medium">Customer:</label><p>${data.customer}</p></div>
                        <div><label class="font-medium">Container:</label><p>${data.nomor_container}</p></div>
                        <div><label class="font-medium">Nama Kapal:</label><p>${data.nama_kapal}</p></div>
                        <div><label class="font-medium">Nominal:</label><p>Rp ${Number(data.nominal_yang_dibayarkan).toLocaleString('id-ID')}</p></div>
                        <div><label class="font-medium">DPP:</label><p>Rp ${Number(data.dpp).toLocaleString('id-ID')}</p></div>
                        <div><label class="font-medium">PPM:</label><p>Rp ${Number(data.ppm).toLocaleString('id-ID')}</p></div>
                        <div><label class="font-medium">Referensi:</label><p>${data.nomor_referensi}</p></div>
                    </div>
                `;
                this.showDetailModal = true;
                
            } catch (error) {
                console.error('Gagal memuat detail:', error);
                Swal.fire('Error', 'Gagal memuat detail transaksi', 'error');
            }
        }
    };
}
</script>