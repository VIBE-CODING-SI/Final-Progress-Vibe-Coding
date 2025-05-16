<!-- Main Content -->
<div
    class="flex-1 transition-all duration-300"
    :style="sidebarOpen ? 'margin-left: 16rem' : 'margin-left: 4rem'"
>
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="flex items-center justify-between px-6 py-4">
            <div class="flex items-center">
                <button
                    class="text-gray-600 mr-4"
                    @click="sidebarOpen = !sidebarOpen"
                >
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <h1 class="text-xl font-semibold text-gray-800">Dashboard</h1>
            </div>

            <!-- User Menu -->
            <div class="relative" x-data="{ profileOpen: false }">
                <button
                    class="flex items-center space-x-2"
                    @click="profileOpen = !profileOpen"
                >
                    <div
                        class="h-8 w-8 bg-blue-500 rounded-full flex items-center justify-center text-white"
                    >
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="text-left">
                        <p class="text-gray-700 font-medium">Admin</p>
                    </div>
                    <i class="fas fa-chevron-down text-sm"></i>
                </button>

                <!-- Dropdown Menu -->
                <div
                    x-show="profileOpen"
                    @click.outside="profileOpen = false"
                    class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50"
                >
                    <div class="px-4 py-2 border-b">
                        <p class="text-sm font-medium text-gray-800">John Doe</p>
                        <p class="text-xs text-gray-500 truncate">
                            john.doe@example.com
                        </p>
                    </div>
                    <a
                        href="/logout"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    >
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </header>