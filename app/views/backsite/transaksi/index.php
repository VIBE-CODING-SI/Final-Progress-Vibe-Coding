<div
    class="bg-white rounded-lg shadow-sm p-6 mt-4"
    x-data="transaksiTable()"
    x-cloak
    x-init="initDataTable()"
>
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Manajemen Transaksi</h3>
    </div>
    <div class="flex gap-3 items-center py-4">
        <div class="flex items-center gap-2 bg-white border rounded-lg px-3 py-2">
            <i class="fas fa-calendar-alt text-gray-400"></i>
            <span class="text-sm text-gray-600"><?= date('d M Y', strtotime('-2 months')) ?> - <?= date('d M Y') ?></span>
        </div>
    </div>

    <table id="transaksiTable" class="w-full table-auto stripe hover" style="width:100%">
        <thead>
            <tr class="bg-gray-50">
                <th class="px-4 py-2 text-left">Tanggal</th>
                <th class="px-4 py-2 text-left">Customer</th>
                <th class="px-4 py-2 text-left">Container</th>
                <th class="px-4 py-2 text-left">Status Bayar</th>
                <th class="px-4 py-2 text-left">Status Transaksi</th>
                <th class="px-4 py-2 text-left">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data diisi via DataTables -->
        </tbody>
    </table>

    <!-- Modal Detail -->
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
function transaksiTable() {
    return {
        showDetailModal: false,
        detailContent: '',
        selectedTransaksi: null,
        
        initDataTable() {
            $('#transaksiTable').DataTable({
                serverSide: true,
                processing: true,
                ajax: {
                    url: '/dashboard/transaksi/datatable',
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
                        render: (data, type, row) => `
                            <form method="POST" action="/dashboard/transaksi/update-payment-status/${row.id}">
                                <select 
                                    name="status_pembayaran" 
                                    class="px-2 py-1 rounded text-xs ${data === 'lunas' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}"
                                    onchange="this.form.submit()"
                                >
                                    <option value="lunas" ${data === 'lunas' ? 'selected' : ''}>Lunas</option>
                                    <option value="belum_lunas" ${data === 'belum_lunas' ? 'selected' : ''}>Belum Lunas</option>
                                </select>
                            </form>`
                    },
                    {
                        data: 'status_transaksi',
                        render: (data, type, row) => `
                            <form method="POST" action="/dashboard/transaksi/update-transaction-status/${row.id}">
                                <select 
                                    name="status_transaksi" 
                                    class="px-2 py-1 rounded text-xs ${{
                                        'selesai': 'bg-green-100 text-green-800',
                                        'diproses': 'bg-yellow-100 text-yellow-800',
                                        'belum_dimulai': 'bg-gray-100 text-gray-800'
                                    }[data] || ''}"
                                    onchange="this.form.submit()"
                                >
                                    <option value="belum_dimulai" ${data === 'belum_dimulai' ? 'selected' : ''}>Belum Dimulai</option>
                                    <option value="diproses" ${data === 'diproses' ? 'selected' : ''}>Diproses</option>
                                    <option value="selesai" ${data === 'selesai' ? 'selected' : ''}>Selesai</option>
                                </select>
                            </form>`
                    },
                    { 
                        data: null,
                        render: (data, type, row) => `
                            <button 
                                @click="showDetail(${row.id})"
                                class="text-blue-500 hover:text-blue-700"
                            >
                                <i class="fas fa-eye"></i>
                            </button>`
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
                    }
                }
            });
        },

        async showDetail(id) {
            try {
                const response = await fetch(`/dashboard/transaksi/${id}`);
                if (!response.ok) throw new Error('Gagal memuat data');
                
                const data = await response.json();
                
                this.detailContent = `
                    <div class="p-8 space-y-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Kolom Kiri -->
                            <div class="space-y-6">
                                <div class="bg-white p-6 rounded-lg shadow-sm">
                                    <h4 class="text-lg font-semibold text-blue-600 mb-4">
                                        <i class="fas fa-file-invoice mr-2"></i>Informasi Transaksi
                                    </h4>
                                    <div class="space-y-3">
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600 font-medium">Kode Referensi</span>
                                            <span class="text-gray-800">${data.nomor_referensi || '-'}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600 font-medium">Tanggal Transaksi</span>
                                            <span class="text-gray-800">${new Date(data.tanggal).toLocaleDateString('id-ID')}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600 font-medium">Durasi Sewa</span>
                                            <span class="text-gray-800">${data.durasi || 0} Hari</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white p-6 rounded-lg shadow-sm">
                                    <h4 class="text-lg font-semibold text-blue-600 mb-4">
                                        <i class="fas fa-calendar-alt mr-2"></i>Jadwal
                                    </h4>
                                    <div class="space-y-3">
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600 font-medium">Mulai</span>
                                            <span class="text-gray-800">${new Date(data.tanggal_mulai).toLocaleDateString('id-ID')}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600 font-medium">Selesai</span>
                                            <span class="text-gray-800">${new Date(data.tanggal_selesai).toLocaleDateString('id-ID')}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Kolom Kanan -->
                            <div class="space-y-6">
                                <div class="bg-white p-6 rounded-lg shadow-sm">
                                    <h4 class="text-lg font-semibold text-blue-600 mb-4">
                                        <i class="fas fa-truck-moving mr-2"></i>Detail Armada
                                    </h4>
                                    <div class="space-y-3">
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600 font-medium">Nama Armada</span>
                                            <span class="text-gray-800">${data.nama_kendaraan || 'Belum ditentukan'}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600 font-medium">Plat Nomor</span>
                                            <span class="text-gray-800">${data.plat_kendaraan || 'Belum ditentukan'}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600 font-medium">Harga/Hari</span>
                                            <span class="text-gray-800">Rp${Number(data.harga_sewa || 0).toLocaleString('id-ID')}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white p-6 rounded-lg shadow-sm">
                                    <h4 class="text-lg font-semibold text-blue-600 mb-4">
                                        <i class="fas fa-money-bill-wave mr-2"></i>Rincian Pembayaran
                                    </h4>
                                    <div class="space-y-3">
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600 font-medium">Subtotal</span>
                                            <span class="text-gray-800">Rp${Number(data.nominal_yang_dibayarkan || 0).toLocaleString('id-ID')}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600 font-medium">DPP (90%)</span>
                                            <span class="text-gray-800">Rp${Number(data.dpp || 0).toLocaleString('id-ID')}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600 font-medium">PPM (10%)</span>
                                            <span class="text-gray-800">Rp${Number(data.ppm || 0).toLocaleString('id-ID')}</span>
                                        </div>
                                        <div class="flex justify-between items-center pt-3 border-t">
                                            <span class="text-lg font-bold text-gray-800">Total</span>
                                            <span class="text-lg font-bold text-blue-600">
                                                Rp${Number(data.nominal_yang_dibayarkan || 0).toLocaleString('id-ID')}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`;

                this.showDetailModal = true;
                
            } catch (error) {
                console.error('Error:', error);
                Swal.fire('Error', 'Gagal memuat detail transaksi', 'error');
            }
        }
    };
}
</script>