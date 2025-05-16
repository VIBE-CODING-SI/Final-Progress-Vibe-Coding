<aside
    class="bg-white shadow-lg fixed h-full transition-all duration-300"
    :class="sidebarOpen ? 'w-64' : 'w-16'"
>
    <div class="p-4 flex items-center">
        <h2
            class="text-2xl font-bold text-gray-800 truncate"
            :class="!sidebarOpen && 'hidden'"
        >
            <i class="fas fa-truck-moving mr-2"></i>
            CPS Haulage
        </h2>
        <h2
            class="text-2xl font-bold text-gray-800"
            :class="sidebarOpen && 'hidden'"
        >
            <i class="fas fa-truck-moving"></i>
        </h2>
    </div>

    <nav class="mt-6">
        <div class="px-4">
            <h3
                class="text-xs uppercase text-gray-500 font-bold mb-4"
                :class="!sidebarOpen && 'hidden'"
            >
                Menu
            </h3>
            <ul class="space-y-2">
                <li>
                    <a
                        href="/dashboard"
                        class="flex items-center p-2 text-gray-700 hover:bg-blue-100 rounded-lg menu-item"
                    >
                        <i class="fas fa-home w-5 mr-3"></i>
                        <span :class="!sidebarOpen && 'hidden'">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a
                        href="/dashboard/users"
                        class="flex items-center p-2 text-gray-700 hover:bg-blue-100 rounded-lg menu-item"
                    >
                        <i class="fas fa-users-cog w-5 mr-3"></i>
                        <span :class="!sidebarOpen && 'hidden'">User</span>
                    </a>
                </li>
                <li>
                    <a
                        href="/dashboard/armada"
                        class="flex items-center p-2 text-gray-700 hover:bg-blue-100 rounded-lg menu-item"
                    >
                        <i class="fas fa-truck w-5 mr-3"></i>
                        <span :class="!sidebarOpen && 'hidden'">Armada</span>
                    </a>
                </li>
                <li>
                    <a
                        href="/dashboard/transaksi"
                        class="flex items-center p-2 text-gray-700 hover:bg-blue-100 rounded-lg menu-item"
                    >
                        <i class="fas fa-exchange-alt w-5 mr-3"></i>
                        <span :class="!sidebarOpen && 'hidden'">Transaksi</span>
                    </a>
                </li>
                <li>
                    <a
                        href="/dashboard/penjualan"
                        class="flex items-center p-2 text-gray-700 hover:bg-blue-100 rounded-lg menu-item"
                    >
                        <i class="fas fa-chart-line w-5 mr-3"></i>
                        <span :class="!sidebarOpen && 'hidden'">Penjualan</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</aside>
