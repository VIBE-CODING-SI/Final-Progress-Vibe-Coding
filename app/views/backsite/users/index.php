<?php
    $users_json = json_encode($users);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $users_json = '[]';
        error_log('JSON Error: ' . json_last_error_msg());
    }
?>

<div
    class="bg-white rounded-lg shadow-sm p-6 mt-4"
    x-data="userTable(<?= htmlspecialchars(json_encode($users), ENT_QUOTES, 'UTF-8') ?>)"
    x-init="console.log('Component initialized')"
>
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Manajemen User</h3>
        <button
            @click="openCreate()"
            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
        >
            <i class="fas fa-plus mr-2"></i>Tambah User
        </button>
    </div>

    <table class="w-full table-auto">
        <thead>
            <tr class="bg-gray-50">
                <th class="px-4 py-2 text-left">Nama</th>
                <th class="px-4 py-2 text-left">No. Telp</th>
                <th class="px-4 py-2 text-left">Alamat</th>
                <th class="px-4 py-2 text-left">Email</th>
                <th class="px-4 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <template x-for="user in users" :key="user.id">
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-2" x-text="user.nama"></td>
                    <td class="px-4 py-2" x-text="user.no_telp"></td>
                    <td class="px-4 py-2" x-text="user.alamat"></td>
                    <td class="px-4 py-2" x-text="user.email"></td>
                    <td class="px-4 py-2 text-center">
                        <button
                            class="text-blue-500 border border-blue-500 hover:bg-blue-50 rounded p-1.5 mr-2 transition-all"
                            @click="openEdit(user)"
                        >
                            <i class="far fa-pen-to-square text-sm"></i>
                        </button>
                        <button
                            class="text-red-500 border border-red-500 hover:bg-red-50 rounded p-1.5 transition-all"
                            @click="openDelete(user);"
                        >
                            <i class="far fa-trash-can text-sm"></i>
                        </button>
                    </td>
                </tr>
            </template>
        </tbody>
    </table>

    <!-- Modal Create -->
    <div
        x-show="modalCreate"
        x-cloak
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
        <div
            class="bg-white rounded-lg w-1/3"
            @click.outside="modalCreate = false"
        >
            <form action="/dashboard/users/store" method="POST" class="space-y-4">
                <div class="p-4 border-b flex justify-between items-center">
                    <h3 class="text-lg font-semibold">Tambah User Baru</h3>
                    <button type="button" @click="modalCreate = false" class="text-gray-500 hover:text-gray-700">
                      <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Nama Lengkap</label>
                        <input name="nama" type="text" required class="w-full px-3 py-2 border rounded" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">No. Telp</label>
                        <input name="no_telp" type="text" required class="w-full px-3 py-2 border rounded" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Alamat</label>
                        <input name="alamat" type="text" required class="w-full px-3 py-2 border rounded" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Email</label>
                        <input name="email" type="email" required class="w-full px-3 py-2 border rounded" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Password</label>
                        <input name="password" type="password" required class="w-full px-3 py-2 border rounded" />
                    </div>
                </div>
                <div class="p-4 border-t flex justify-end space-x-2">
                    <button type="button" @click="modalCreate = false" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div
        x-show="modalEdit"
        x-cloak
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
        <div
            class="bg-white rounded-lg w-1/3"
            @click.outside="modalEdit = false"
        >
            <form :action="`/dashboard/users/update/${selected.id}`" method="POST" class="space-y-4" x-data>
                <div class="p-4 border-b flex justify-between items-center">
                    <h3 class="text-lg font-semibold">Edit User</h3>
                    <button type="button" @click="modalEdit = false" class="text-gray-500 hover:text-gray-700">
                      <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Nama Lengkap</label>
                        <input name="nama" type="text" x-model="selected.nama" required class="w-full px-3 py-2 border rounded" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">No. Telp</label>
                        <input name="no_telp" type="text" x-model="selected.no_telp" required class="w-full px-3 py-2 border rounded" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Alamat</label>
                        <input name="alamat" type="text" x-model="selected.alamat" required class="w-full px-3 py-2 border rounded" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Email</label>
                        <input name="email" type="email" x-model="selected.email" required class="w-full px-3 py-2 border rounded" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Password (kosongkan jika tidak diubah)</label>
                        <input name="password" type="password" class="w-full px-3 py-2 border rounded" />
                    </div>
                </div>
                <div class="p-4 border-t flex justify-end space-x-2">
                    <button type="button" @click="modalEdit = false" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Delete -->
    <div
        x-show="modalDelete"
        x-cloak
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
        <div
            class="bg-white rounded-lg w-1/3"
            @click.outside="modalDelete = false"
        >
            <div class="p-4 border-b flex justify-between items-center">
                <h3 class="text-lg font-semibold">Konfirmasi Hapus</h3>
                <button type="button" @click="modalDelete = false" class="text-gray-500 hover:text-gray-700">
                  <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-4">
                <p class="text-gray-600">Apakah Anda yakin ingin menghapus:</p>
                <p class="font-medium mt-2" x-text="selected.nama"></p>
            </div>
            <div class="p-4 border-t flex justify-end space-x-2">
                <button type="button" @click="modalDelete = false" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded">Batal</button>
                <form :action="`/dashboard/users/destroy/${selected.id}`" method="POST">
                  <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    function userTable(initialUsers) {
    try {
        if(!Array.isArray(initialUsers)) {
            console.error('Invalid initial users data:', initialUsers);
            initialUsers = [];
        }
        
        return {
            users: initialUsers,
            selected: null,
            modalCreate: false,
            modalEdit: false,
            modalDelete: false,

            openCreate() {
                this.modalCreate = true;
            },
            openEdit(user) {
                this.selected = Object.assign({}, user);
                this.modalEdit = true;
            },
            openDelete(user) {
                this.selected = user;
                this.modalDelete = true;
            },
        };
    } catch (error) {
        console.error('Error initializing user table:', error);
        return {
            users: [],
            selected: null,
            modalCreate: false,
            modalEdit: false,
            modalDelete: false,
            
            openCreate() {
                this.modalCreate = true;
            },
            openEdit(user) {
                this.selected = { ...user };
                this.modalEdit = true;
            },
            openDelete(user) {
                this.selected = user;
                this.modalDelete = true;
            },
        };
    }
}
</script>
