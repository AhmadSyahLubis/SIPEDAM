@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page_title', 'Dashboard Statistik')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <!-- Reports Stats -->
    <div class="stat bg-base-100 shadow rounded-box border border-base-200 p-4 overflow-hidden">
        <div class="stat-figure text-primary w-12 h-12 flex items-center justify-center bg-primary/10 rounded-full">
            <i class="fa-solid fa-bullhorn text-2xl"></i>
        </div>
        <div class="stat-title whitespace-normal text-sm leading-tight text-gray-500 font-semibold mb-1">Total Pengaduan</div>
        <div class="stat-value text-primary text-3xl" id="stat-reports-total">0</div>
        <div class="stat-desc whitespace-normal text-xs mt-1">Seluruh laporan masuk</div>
    </div>
    
    <div class="stat bg-base-100 shadow rounded-box border border-base-200 p-4 overflow-hidden">
        <div class="stat-figure text-warning w-12 h-12 flex items-center justify-center bg-warning/10 rounded-full">
            <i class="fa-solid fa-hourglass-half text-2xl"></i>
        </div>
        <div class="stat-title whitespace-normal text-sm leading-tight text-gray-500 font-semibold mb-1">Pengaduan Menunggu</div>
        <div class="stat-value text-warning text-3xl" id="stat-reports-menunggu">0</div>
        <div class="stat-desc whitespace-normal text-xs mt-1">Perlu segera diproses</div>
    </div>

    <!-- Services Stats -->
    <div class="stat bg-base-100 shadow rounded-box border border-base-200 p-4 overflow-hidden">
        <div class="stat-figure text-secondary w-12 h-12 flex items-center justify-center bg-secondary/10 rounded-full">
            <i class="fa-solid fa-file-signature text-2xl"></i>
        </div>
        <div class="stat-title whitespace-normal text-sm leading-tight text-gray-500 font-semibold mb-1">Total Permohonan</div>
        <div class="stat-value text-secondary text-3xl" id="stat-services-total">0</div>
        <div class="stat-desc whitespace-normal text-xs mt-1">Seluruh permohonan layanan</div>
    </div>
    
    <div class="stat bg-base-100 shadow rounded-box border border-base-200 p-4 overflow-hidden">
        <div class="stat-figure text-success w-12 h-12 flex items-center justify-center bg-success/10 rounded-full">
            <i class="fa-solid fa-check-circle text-2xl"></i>
        </div>
        <div class="stat-title whitespace-normal text-sm leading-tight text-gray-500 font-semibold mb-1">Layanan Selesai</div>
        <div class="stat-value text-success text-3xl" id="stat-services-selesai">0</div>
        <div class="stat-desc whitespace-normal text-xs mt-1">Layanan berhasil diproses</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="card bg-base-100 shadow-xl border border-base-200">
        <div class="card-body">
            <h2 class="card-title text-lg border-b border-base-200 pb-2"><i class="fa-solid fa-chart-bar mr-2"></i> Status Pengaduan</h2>
            <div class="overflow-x-auto mt-4">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Jumlah</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody id="reports-breakdown">
                        <tr><td colspan="3" class="text-center"><span class="loading loading-spinner"></span></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow-xl border border-base-200">
        <div class="card-body">
            <h2 class="card-title text-lg border-b border-base-200 pb-2"><i class="fa-solid fa-chart-bar mr-2"></i> Status Permohonan</h2>
            <div class="overflow-x-auto mt-4">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Jumlah</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody id="services-breakdown">
                        <tr><td colspan="3" class="text-center"><span class="loading loading-spinner"></span></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@stack('scripts')
<script>
document.addEventListener('DOMContentLoaded', async function() {
    try {
        const response = await axios.get('/api/admin/dashboard/stats');
        if (response.data.success) {
            const data = response.data.data;
            const reports = data.reports;
            const services = data.services;
            
            document.getElementById('stat-reports-total').textContent = reports.total;
            document.getElementById('stat-reports-menunggu').textContent = reports.menunggu;
            document.getElementById('stat-services-total').textContent = services.total;
            document.getElementById('stat-services-selesai').textContent = services.selesai;
            
            const repsTbody = document.getElementById('reports-breakdown');
            repsTbody.innerHTML = '';
            const repStatuses = ['menunggu', 'diproses', 'selesai'];
            repStatuses.forEach(st => {
                const count = reports[st] || 0;
                const perc = reports.total > 0 ? Math.round((count / reports.total) * 100) : 0;
                let badgeClass = st === 'menunggu' ? 'badge-warning' : (st === 'diproses' ? 'badge-info' : 'badge-success');
                repsTbody.innerHTML += `
                    <tr>
                        <td><span class="badge ${badgeClass} badge-sm capitalize">${st}</span></td>
                        <td class="font-bold">${count}</td>
                        <td><progress class="progress progress-primary w-full" value="${perc}" max="100"></progress></td>
                    </tr>
                `;
            });
            
            const servTbody = document.getElementById('services-breakdown');
            servTbody.innerHTML = '';
            repStatuses.forEach(st => {
                const count = services[st] || 0;
                const perc = services.total > 0 ? Math.round((count / services.total) * 100) : 0;
                let badgeClass = st === 'menunggu' ? 'badge-warning' : (st === 'diproses' ? 'badge-info' : 'badge-success');
                servTbody.innerHTML += `
                    <tr>
                        <td><span class="badge ${badgeClass} badge-sm capitalize">${st}</span></td>
                        <td class="font-bold">${count}</td>
                        <td><progress class="progress progress-secondary w-full" value="${perc}" max="100"></progress></td>
                    </tr>
                `;
            });
        }
    } catch (error) {
        console.error('Failed to fetch dashboard stats', error);
    }
});
</script>
