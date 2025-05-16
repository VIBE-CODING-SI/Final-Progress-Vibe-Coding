<div
    class="bg-white rounded-lg shadow-sm p-6 mt-4"
    x-data="armadaTable(<?= htmlspecialchars(json_encode($armada), ENT_QUOTES, 'UTF-8') ?>)"
    x-cloak
>
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Manajemen Armada</h3>
        <button
            @click="openCreate()"
            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
        >
            <i class="fas fa-plus mr-2"></i>Tambah Armada
        </button>
    </div>

    <table class="w-full table-auto">
        <thead>
            <tr class="bg-gray-50">
                <th class="px-4 py-2 text-left">Gambar</th>
                <th class="px-4 py-2 text-left">Plat</th>
                <th class="px-4 py-2 text-left">Nama</th>
                <th class="px-4 py-2 text-left">Tipe</th>
                <th class="px-4 py-2 text-left">Kapasitas</th>
                <th class="px-4 py-2 text-left">Harga Sewa</th>
                <th class="px-4 py-2 text-left">Status</th>
                <th class="px-4 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <template x-for="a in armada" :key="a.id">
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-2">
                        <img 
                            :src="a.gambar_kendaraan ? `/uploads/armada/${a.gambar_kendaraan}` : '/assets/no-image.png'"
                            class="w-16 h-16 object-cover rounded"
                            alt="Gambar Kendaraan"
                        >
                    </td>
                    <td class="px-4 py-2" x-text="a.plat_kendaraan"></td>
                    <td class="px-4 py-2" x-text="a.nama_kendaraan"></td>
                    <td class="px-4 py-2 capitalize" x-text="a.tipe_kendaraan"></td>
                    <td class="px-4 py-2" x-text="`${formatKapasitas(a.kapasitas_kendaraan)} Ton`"></td>
                    <td class="px-4 py-2" x-text="`Rp. ${new Intl.NumberFormat('id-ID').format(a.harga_sewa)}/hari`"></td>
                    <td class="px-4 py-2">
                        <span 
                            class="px-2 py-1 rounded text-xs capitalize"
                            :class="{
                                'bg-green-100 text-green-800': a.status_kendaraan === 'tersedia',
                                'bg-blue-100 text-blue-800': a.status_kendaraan === 'digunakan',
                                'bg-yellow-100 text-yellow-800': a.status_kendaraan === 'maintenance',
                                'bg-red-100 text-red-800': a.status_kendaraan === 'nonaktif'
                            }"
                            x-text="a.status_kendaraan"
                        ></span>
                    </td>
                    <td class="px-4 py-2 text-center">
                        <button
                            class="text-blue-500 border border-blue-500 hover:bg-blue-50 rounded p-1.5 mr-2 transition-all"
                            @click="openEdit(a)"
                        >
                            <i class="far fa-pen-to-square text-sm"></i>
                        </button>
                        <button
                            class="text-red-500 border border-red-500 hover:bg-red-50 rounded p-1.5 transition-all"
                            @click="openDelete(a)"
                        >
                            <i class="far fa-trash-can text-sm"></i>
                        </button>
                    </td>
                </tr>
            </template>
        </tbody>
    </table>

    <!-- Modal Create -->
    <div x-show="modalCreate"
        x-cloak
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        >
        <div class="bg-white rounded-lg w-1/3" @click.outside="modalCreate = false">
            <form action="/dashboard/armada/store" method="POST" enctype="multipart/form-data" class="space-y-4">
                <div class="p-4 space-y-4">
                    <!-- Plat -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Plat Kendaraan</label>
                        <input name="plat_kendaraan" type="text" required 
                            class="w-full px-3 py-2 border rounded">
                    </div>
                    
                    <!-- Nama Kendaraan -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Nama Kendaraan</label>
                        <input name="nama_kendaraan" type="text" required 
                            class="w-full px-3 py-2 border rounded">
                    </div>
                    
                    <!-- Tipe Kendaraan -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Tipe Kendaraan</label>
                        <select name="tipe_kendaraan" required class="w-full px-3 py-2 border rounded">
                            <option value="tronton">Tronton</option>
                            <option value="trailer">Trailer</option>
                        </select>
                    </div>
                    
                    <!-- Kapasitas -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Kapasitas (Ton)</label>
                        <input name="kapasitas_kendaraan" type="number" step="0.1" required 
                            class="w-full px-3 py-2 border rounded">
                    </div>
                    
                    <!-- Harga Sewa -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Harga Sewa per Hari</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-500">Rp</span>
                            <input name="harga_sewa" type="number" required 
                                class="w-full pl-8 pr-3 py-2 border rounded">
                        </div>
                    </div>
                    
                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Status</label>
                        <select name="status_kendaraan" required class="w-full px-3 py-2 border rounded">
                            <option value="tersedia">Tersedia</option>
                            <option value="digunakan">Digunakan</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                    
                    <!-- Gambar -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Gambar Kendaraan</label>
                        <input type="file" name="gambar_kendaraan" 
                            class="w-full px-3 py-2 border rounded"
                            accept="image/jpeg, image/png, image/webp">
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="p-4 border-t flex justify-end space-x-2">
                        <button type="button" @click="modalCreate = false" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div
        x-show="modalEdit"
        x-cloak
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[999]"
    >
        <form class="bg-white rounded-lg w-11/12 md:w-2/3 lg:w-1/2 xl:w-1/3 flex flex-col max-h-[95vh]"
                :action="`/dashboard/armada/update/${selected.id}`" 
                method="POST" 
                enctype="multipart/form-data"
        >
            <!-- Header -->
            <div class="p-4 border-b flex-shrink-0">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold">Edit Armada</h3>
                    <button 
                        @click="modalEdit = false" 
                        class="text-gray-500 hover:text-gray-700"
                    >
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto p-4 space-y-4">
                <!-- Preview Gambar -->
                <div class="text-center">
                    <img 
                        :src="selected.gambar_kendaraan ? `/uploads/armada/${selected.gambar_kendaraan}` : '/assets/no-image.png'"
                        class="w-32 h-32 object-cover rounded-lg mx-auto mb-4 border"
                    >
                </div>

                <!-- Form Fields -->
                <div class="space-y-4">
                    <!-- Plat Kendaraan -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Plat Kendaraan</label>
                        <input name="plat_kendaraan" type="text" x-model="selected.plat_kendaraan" required class="w-full px-3 py-2 border rounded">
                    </div>

                    <!-- Nama Kendaraan -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Nama Kendaraan</label>
                        <input name="nama_kendaraan" type="text" x-model="selected.nama_kendaraan" required class="w-full px-3 py-2 border rounded">
                    </div>
                    
                    <!-- Tipe Kendaraan -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Tipe Kendaraan</label>
                        <select name="tipe_kendaraan" x-model="selected.tipe_kendaraan" required class="w-full px-3 py-2 border rounded">
                            <option value="tronton">Tronton</option>
                            <option value="trailer">Trailer</option>
                        </select>
                    </div>
                    
                    <!-- Kapasitas -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Kapasitas (Ton)</label>
                        <input name="kapasitas_kendaraan" type="number" step="0.1" x-model="selected.kapasitas_kendaraan" required class="w-full px-3 py-2 border rounded">
                    </div>
                    
                    <!-- Harga Sewa -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Harga Sewa per Hari</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-500">Rp</span>
                            <input name="harga_sewa" type="number" x-model="selected.harga_sewa" required class="w-full pl-8 pr-3 py-2 border rounded">
                        </div>
                    </div>
                    
                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Status</label>
                        <select name="status_kendaraan" x-model="selected.status_kendaraan" required class="w-full px-3 py-2 border rounded">
                            <option value="tersedia">Tersedia</option>
                            <option value="digunakan">Digunakan</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                    
                    <!-- Gambar -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Gambar Kendaraan</label>
                        <input type="file" name="gambar_kendaraan" 
                            class="w-full px-3 py-2 border rounded"
                            accept="image/jpeg, image/png, image/webp">
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="p-4 border-t flex-shrink-0">
                <div class="flex justify-end space-x-2">
                    <button 
                        type="button" 
                        @click="modalEdit = false" 
                        class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 focus:ring-2 focus:ring-blue-500"
                    >
                        Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Modal Delete -->
    <div
        x-show="modalDelete"
        x-cloak
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
        <div class="bg-white rounded-lg w-1/3" @click.outside="modalDelete = false">
            <div class="p-4 border-b flex justify-between items-center">
                <h3 class="text-lg font-semibold">Konfirmasi Hapus</h3>
                <button type="button" @click="modalDelete = false" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-4">
                <p class="text-gray-600">Apakah Anda yakin ingin menghapus armada:</p>
                <p class="font-medium mt-2" x-text="selected.plat_kendaraan"></p>
            </div>
            <div class="p-4 border-t flex justify-end space-x-2">
                <button type="button" @click="modalDelete = false" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded">Batal</button>
                <form :action="`/dashboard/armada/destroy/${selected.id}`" method="POST">
                    <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function armadaTable(initialArmada) {
    return {
        armada: Array.isArray(initialArmada) ? initialArmada : [],
        selected: null,
        modalCreate: false,
        modalEdit: false,
        modalDelete: false,
        
        formatKapasitas(value) {
        const num = parseFloat(value);
        return num % 1 === 0 ? 
            num.toString() : 
            num.toFixed(2).replace(/0+$/, '').replace(/\.$/, '');
        },
        openCreate() {
            this.modalCreate = true;
        },
        openEdit(armada) {
            this.selected = { ...armada };
            this.modalEdit = true;
        },
        openDelete(armada) {
            this.selected = armada;
            this.modalDelete = true;
        }
    };
}
</script>