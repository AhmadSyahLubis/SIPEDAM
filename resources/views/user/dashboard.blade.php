@extends('layouts.user')
@section('title', 'Beranda')
@section('page_title', 'Ringkasan Layanan Anda')

@section('content')

<!-- Welcome Banner -->
<div class="hero rounded-box bg-gradient-to-r from-primary to-primary-focus text-primary-content mb-8 shadow-xl">
    <div class="hero-content flex-col lg:flex-row w-full justify-between p-8">
        <div>
            <h1 class="text-3xl font-bold">Selamat Datang, <span id="welcome-name">User</span>!</h1>
            <p class="py-4 max-w-2xl text-lg">Pemerintah Kabupaten WOKA hadir melayani Anda. Ajukan laporan, usulan, maupun permohonan layanan publik secara aman dan pantau statusnya langsung dari perangkat Anda.</p>
            <div class="flex gap-4">
                <a href="{{ url('/user/reports') }}" class="btn btn-secondary bg-white text-primary border-none hover:bg-gray-100">Buat Pengaduan</a>
                <a href="{{ url('/user/services') }}" class="btn btn-outline border-white text-white hover:bg-white hover:text-primary">Minta Layanan</a>
            </div>
        </div>
        <div class="hidden lg:block">
            <i class="fa-solid fa-shield-halved text-8xl opacity-80"></i>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <!-- Reports Stats -->
    <div class="stat bg-base-100 shadow-xl rounded-box border border-base-200 p-4 lg:p-6 overflow-hidden">
        <div class="stat-figure text-primary w-12 h-12 lg:w-16 lg:h-16 flex items-center justify-center bg-primary/10 rounded-full">
            <i class="fa-solid fa-bullhorn text-2xl lg:text-3xl"></i>
        </div>
        <div class="stat-title font-semibold whitespace-normal leading-tight">Pengaduan Anda</div>
        <div class="stat-value text-primary mt-1 text-3xl lg:text-4xl" id="stat-reports-total">0</div>
        <div class="stat-desc mt-1 font-medium text-warning whitespace-normal"><span id="stat-reports-aktif">0</span> Dalam Proses</div>
    </div>

    <!-- Services Stats -->
    <div class="stat bg-base-100 shadow-xl rounded-box border border-base-200 p-4 lg:p-6 overflow-hidden">
        <div class="stat-figure text-secondary w-12 h-12 lg:w-16 lg:h-16 flex items-center justify-center bg-secondary/10 rounded-full">
            <i class="fa-solid fa-file-signature text-2xl lg:text-3xl"></i>
        </div>
        <div class="stat-title font-semibold whitespace-normal leading-tight">Permohonan Layanan</div>
        <div class="stat-value text-secondary mt-1 text-3xl lg:text-4xl" id="stat-services-total">0</div>
        <div class="stat-desc mt-1 font-medium text-warning whitespace-normal"><span id="stat-services-aktif">0</span> Dalam Proses</div>
    </div>
</div>

<!-- Recent Activities -->
<div class="card bg-base-100 shadow-xl border border-base-200">
    <div class="card-body">
        <h2 class="card-title text-xl mb-4 border-b border-base-200 pb-3"><i class="fa-solid fa-clock-rotate-left mr-2 text-primary"></i> Aktivitas Terakhir Anda</h2>
        
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr class="bg-base-200">
                        <th>Tipe</th>
                        <th>Nomor Tiket</th>
                        <th>Judul/Deskripsi</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="recent-activities">
                    <tr><td colspan="5" class="text-center py-8"><span class="loading loading-spinner text-primary"></span></td></tr>
                </tbody>
            </table>
        </div>
        
        <div class="mt-4 text-center hidden" id="empty-state">
            <div class="text-base-content/50 my-6">
                <i class="fa-solid fa-folder-open text-4xl mb-4"></i>
                <p>Belum ada aktivitas. Mulai buat laporan atau permohonan perdana Anda.</p>
            </div>
        </div>
    </div>
</div>

@endsection

@stack('scripts')
<script>
document.addEventListener('DOMContentLoaded', async function() {
    // Set Name
    try {
        const userData = JSON.parse(localStorage.getItem('user_data'));
        if (userData && userData.name) {
            document.getElementById('welcome-name').textContent = userData.name.split(' ')[0];
        }
    } catch(e) {}

    // Fetch Stats
    try {
        const response = await axios.get('/api/user/dashboard/stats');
        if (response.data.success) {
            const data = response.data.data;
            
            document.getElementById('stat-reports-total').textContent = data.reports.total;
            document.getElementById('stat-reports-aktif').textContent = data.reports.aktif;
            document.getElementById('stat-services-total').textContent = data.services.total;
            document.getElementById('stat-services-aktif').textContent = data.services.aktif;
            
            const tbody = document.getElementById('recent-activities');
            tbody.innerHTML = '';
            
            if (data.recent_activities.length === 0) {
                document.getElementById('empty-state').classList.remove('hidden');
            } else {
                data.recent_activities.forEach(item => {
                    let badgeClass = 'badge-warning';
                    if(item.status === 'diproses') badgeClass = 'badge-info';
                    if(item.status === 'selesai') badgeClass = 'badge-success';
                    if(item.status === 'ditolak') badgeClass = 'badge-error';
                    
                    const date = new Date(item.created_at).toLocaleDateString('id-ID', {
                        day: 'numeric', month: 'short', year: 'numeric'
                    });
                    
                    const titleText = item.title ? item.title : item.description.substring(0, 50) + '...';
                    
                    tbody.innerHTML += `
                        <tr class="hover">
                            <td>
                                <div class="badge ${item.type === 'Pengaduan' ? 'badge-primary' : 'badge-secondary'} gap-2 font-bold py-3 px-3 shadow-sm text-white">
                                    <i class="fa-solid ${item.type === 'Pengaduan' ? 'fa-bullhorn' : 'fa-file-signature'}"></i>
                                    ${item.type}
                                </div>
                            </td>
                            <td class="font-mono font-medium">${item.ticket_number}</td>
                            <td>
                                <div class="font-bold">${titleText}</div>
                                <div class="text-xs opacity-70">${item.category ? item.category.name : '-'}</div>
                            </td>
                            <td>${date}</td>
                            <td><span class="badge ${badgeClass} capitalize px-3 py-3 font-semibold">${item.status}</span></td>
                        </tr>
                    `;
                });
            }
        }
    } catch (error) {
        console.error('Failed to fetch user dashboard stats', error);
        document.getElementById('recent-activities').innerHTML = '<tr><td colspan="5" class="text-center text-error">Gagal memuat data</td></tr>';
    }
});
</script>
