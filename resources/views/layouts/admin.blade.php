<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SIPEDAM') }} - @yield('title', 'Admin')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        const token = localStorage.getItem('auth_token');
        if (token) {
            axios.defaults.headers.common['Authorization'] = 'Bearer ' + token;
        }
    </script>
</head>
<body class="font-sans antialiased text-base-content bg-base-100 hidden" id="body-content">
    <div class="drawer lg:drawer-open">
        <input id="admin-drawer" type="checkbox" class="drawer-toggle" />
        
        <div class="drawer-content flex flex-col min-h-screen">
            <!-- Navbar -->
            <div class="w-full navbar bg-base-100 border-b border-base-200">
                <div class="flex-none lg:hidden">
                    <label for="admin-drawer" class="btn btn-square btn-ghost">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </label>
                </div>
                <div class="flex-1 px-2 mx-2">
                    <span class="text-xl font-bold">@yield('page_title', 'Dashboard')</span>
                </div>
                <div class="flex-none hidden lg:block">
                    <div class="dropdown dropdown-end">
                        <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar border border-base-300">
                            <div class="w-10 rounded-full">
                                <img alt="User Avatar" src="https://ui-avatars.com/api/?name=Admin&background=random" id="user-avatar" />
                            </div>
                        </div>
                        <ul tabindex="0" class="mt-3 z-[1] p-2 shadow menu menu-sm dropdown-content bg-base-100 rounded-box w-52 border border-base-200">
                            <li><span class="font-bold border-b border-base-200 pb-2 mb-2" id="user-name-display">Admin</span></li>
                            <li><a href="#" onclick="handleLogout()"><i class="fa-solid fa-right-from-bracket mr-2 text-error"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <main class="flex-1 p-4 md:p-6 bg-base-200/50">
                @yield('content')
            </main>
        </div> 
        
        <!-- Sidebar -->
        <div class="drawer-side z-40">
            <label for="admin-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
            <div class="w-72 min-h-full bg-base-100 text-base-content border-r border-base-200 flex flex-col">
                <div class="p-4 border-b border-base-200 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-building-flag text-primary text-2xl"></i>
                    <span class="text-2xl font-bold text-primary">SIPEDAM</span>
                </div>
                
                <ul class="menu p-4 w-full flex-1 md:text-base">
                    <li class="menu-title text-base-content/50">Menu Utama</li>
                    <li>
                        <a href="{{ url('/admin/dashboard') }}" class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">
                            <i class="fa-solid fa-chart-pie w-5"></i> Dashboard
                        </a>
                    </li>
                    
                    <li class="menu-title mt-4 text-base-content/50">Manajemen Pengajuan</li>
                    <li>
                        <a href="{{ url('/admin/reports') }}" class="{{ request()->is('admin/reports*') ? 'active' : '' }}">
                            <i class="fa-solid fa-bullhorn w-5"></i> Pengaduan Masyarakat
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/admin/services') }}" class="{{ request()->is('admin/services*') ? 'active' : '' }}">
                            <i class="fa-solid fa-file-signature w-5"></i> Permohonan Layanan
                        </a>
                    </li>
                    
                    <li class="menu-title mt-4 text-base-content/50">Manajemen Master</li>
                    <li>
                        <a href="{{ url('/admin/categories') }}" class="{{ request()->is('admin/categories*') ? 'active' : '' }}">
                            <i class="fa-solid fa-tags w-5"></i> Kategori Layanan
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/admin/users') }}" class="{{ request()->is('admin/users*') ? 'active' : '' }}">
                            <i class="fa-solid fa-users w-5"></i> Data Pengguna
                        </a>
                    </li>
                </ul>
                
                <div class="p-4 border-t border-base-200 lg:hidden">
                    <button class="btn btn-error btn-outline w-full gap-2" onclick="handleLogout()">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Auth check script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const token = localStorage.getItem('auth_token');
            const userDataStr = localStorage.getItem('user_data');
            
            if (!token || !userDataStr) {
                window.location.href = '/login';
                return;
            }
            
            try {
                const userData = JSON.parse(userDataStr);
                if (userData.role !== 'admin') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Akses Ditolak',
                        text: 'Anda bukan Admin.'
                    }).then(() => {
                        window.location.href = '/user/dashboard';
                    });
                    return;
                }
                
                document.getElementById('user-name-display').textContent = userData.name;
                document.getElementById('user-avatar').src = `https://ui-avatars.com/api/?name=${encodeURIComponent(userData.name)}&background=random`;
                
                document.getElementById('body-content').classList.remove('hidden');
            } catch (e) {
                window.location.href = '/login';
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>
