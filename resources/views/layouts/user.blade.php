<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SIPEDAM') }} - @yield('title', 'Portal Masyarakat')</title>

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
        <input id="user-drawer" type="checkbox" class="drawer-toggle" />
        
        <div class="drawer-content flex flex-col min-h-screen">
            <!-- Navbar -->
            <div class="w-full navbar bg-primary text-primary-content">
                <div class="flex-none lg:hidden">
                    <label for="user-drawer" class="btn btn-square btn-ghost">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </label>
                </div>
                <div class="flex-1 px-2 mx-2">
                    <span class="text-xl font-bold">@yield('page_title', 'Dashboard Masyarakat')</span>
                </div>
                <div class="flex-none hidden lg:block">
                    <div class="dropdown dropdown-end">
                        <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar bg-white text-primary">
                            <div class="w-10 rounded-full">
                                <img alt="User Avatar" src="https://ui-avatars.com/api/?name=User&background=random" id="user-avatar" />
                            </div>
                        </div>
                        <ul tabindex="0" class="mt-3 z-[1] p-2 shadow menu menu-sm dropdown-content bg-base-100 rounded-box w-52 border border-base-200 text-base-content">
                            <li><span class="font-bold border-b border-base-200 pb-2 mb-2" id="user-name-display">User</span></li>
                            <li><a href="{{ url('/user/profile') }}"><i class="fa-solid fa-user mr-2"></i> Profil Saya</a></li>
                            <li><a href="#" onclick="handleLogout()"><i class="fa-solid fa-right-from-bracket mr-2 text-error"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <main class="flex-1 p-4 lg:p-8 bg-base-200 w-full max-w-7xl mx-auto">
                @yield('content')
            </main>
            
            <!-- Footer -->
            <footer class="footer footer-center p-4 bg-base-100 text-base-content border-t border-base-200">
                <aside>
                    <p>Copyright © {{ date('Y') }} - Dinas Komunikasi dan Informatika</p>
                </aside>
            </footer>
        </div> 
        
        <!-- Sidebar -->
        <div class="drawer-side z-40">
            <label for="user-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
            <div class="w-72 min-h-full bg-base-100 text-base-content flex flex-col border-r border-base-200">
                <div class="p-4 bg-primary text-primary-content flex items-center gap-3">
                    <div class="avatar">
                        <div class="w-12 rounded-full bg-white text-primary flex items-center justify-center font-bold text-xl">
                            SI
                        </div>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold">SIPEDAM</h2>
                        <span class="text-xs opacity-80" id="user-nik-display">NIK: -</span>
                    </div>
                </div>
                
                <ul class="menu p-4 w-full flex-1 md:text-base gap-1">
                    <li class="menu-title text-base-content/50 px-0 mt-2">Menu Utama</li>
                    <li>
                        <a href="{{ url('/user/dashboard') }}" class="{{ request()->is('user/dashboard') ? 'active' : '' }}">
                            <i class="fa-solid fa-home w-6 text-center"></i> Beranda
                        </a>
                    </li>
                    
                    <li class="menu-title text-base-content/50 px-0 mt-4">Layanan Anda</li>
                    <li>
                        <a href="{{ url('/user/reports') }}" class="{{ request()->is('user/reports*') ? 'active' : '' }}">
                            <i class="fa-solid fa-bullhorn w-6 text-center"></i> Laporan Pengaduan
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/user/services') }}" class="{{ request()->is('user/services*') ? 'active' : '' }}">
                            <i class="fa-solid fa-file-signature w-6 text-center"></i> Permohonan Layanan
                        </a>
                    </li>
                    <li class="menu-title text-base-content/50 px-0 mt-4">Pengaturan</li>
                    <li>
                        <a href="{{ url('/user/profile') }}" class="{{ request()->is('user/profile*') ? 'active' : '' }}">
                            <i class="fa-solid fa-user-pen w-6 text-center"></i> Profil Saya
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
                
                document.getElementById('user-name-display').textContent = userData.name;
                document.getElementById('user-nik-display').textContent = 'NIK: ' + userData.nik;
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
