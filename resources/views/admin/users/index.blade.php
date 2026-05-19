@extends('layouts.admin')
@section('title', 'Manajemen Pengguna')
@section('page_title', 'Data Pengguna')

@section('content')
<div class="mb-6 flex justify-between items-center flex-wrap gap-4">
    <div>
        <h2 class="text-2xl font-bold">Daftar Pengguna / Masyarakat</h2>
        <p class="text-base-content/70">Kelola dan pantau seluruh pengguna terdaftar dalam sistem.</p>
    </div>
</div>

<div class="card bg-base-100 shadow-xl border border-base-200">
    <div class="card-body p-0 sm:p-6">
        <div class="flex flex-col md:flex-row justify-between mb-4 gap-4 px-4 sm:px-0 pt-4 sm:pt-0">
            <div class="form-control w-full max-w-xs">
                <select id="role-filter" class="select select-bordered" onchange="currentPage=1; loadUsers()">
                    <option value="">Semua Role</option>
                    <option value="user">Masyarakat (User)</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="form-control w-full max-w-xs relative">
                <input type="text" id="search-input" placeholder="Cari NIK, nama atau email..." class="input input-bordered w-full pr-10" onkeypress="if(event.key === 'Enter') { currentPage=1; loadUsers(); }" />
                <button class="btn btn-ghost btn-circle btn-sm absolute right-1 top-2" onclick="currentPage=1; loadUsers()">
                    <i class="fa-solid fa-search"></i>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full whitespace-nowrap">
                <thead>
                    <tr class="bg-base-200">
                        <th>Profil</th>
                        <th>NIK</th>
                        <th>Kontak</th>
                        <th>Bergabung</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="user-table-body">
                    <tr><td colspan="6" class="text-center py-8"><span class="loading loading-spinner text-primary"></span></td></tr>
                </tbody>
            </table>
        </div>
        
        <div class="flex justify-between items-center mt-6 px-4 sm:px-0 pb-4 sm:pb-0">
            <div class="text-sm text-gray-500" id="pagination-info">Menampilkan 0 data</div>
            <div class="join" id="pagination-controls">
                <!-- Pagination buttons generated via JS -->
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail User -->
<dialog id="modal_detail_user" class="modal">
    <div class="modal-box w-11/12 max-w-2xl">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>
        <h3 class="font-bold text-lg mb-4 border-b pb-2">Detail Pengguna</h3>
        
        <div class="flex flex-col sm:flex-row gap-6 mb-6">
            <div class="avatar">
                <div class="w-24 rounded-full ring ring-primary ring-offset-base-100 ring-offset-2">
                    <img id="detail-avatar" src="" alt="Avatar" />
                </div>
            </div>
            <div class="flex-1 space-y-2">
                <h4 class="text-2xl font-bold" id="detail-name">-</h4>
                <div class="badge badge-primary badge-outline" id="detail-role">-</div>
                <div class="text-sm text-gray-500"><i class="fa-solid fa-id-card w-5"></i> NIK: <span id="detail-nik" class="font-semibold text-base-content">-</span></div>
                <div class="text-sm text-gray-500"><i class="fa-regular fa-calendar w-5"></i> Bergabung: <span id="detail-joined">-</span></div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-base-200 rounded-box p-4">
            <div>
                <div class="text-xs text-gray-500 mb-1">Email</div>
                <div class="font-medium" id="detail-email">-</div>
            </div>
            <div>
                <div class="text-xs text-gray-500 mb-1">No. Telepon / WhatsApp</div>
                <div class="font-medium" id="detail-phone">-</div>
            </div>
            <div class="sm:col-span-2">
                <div class="text-xs text-gray-500 mb-1">Alamat Domisili</div>
                <div class="font-medium" id="detail-address">-</div>
            </div>
        </div>
        
        <div class="modal-action">
            <button class="btn" onclick="document.getElementById('modal_detail_user').close()">Tutup</button>
        </div>
    </div>
</dialog>

@endsection

@stack('scripts')
<script>
let currentPage = 1;
let lastPage = 1;

document.addEventListener('DOMContentLoaded', function() {
    loadUsers();
});

async function loadUsers() {
    const tbody = document.getElementById('user-table-body');
    tbody.innerHTML = '<tr><td colspan="6" class="text-center py-8"><span class="loading loading-spinner text-primary"></span></td></tr>';
    
    try {
        const role = document.getElementById('role-filter').value;
        const search = document.getElementById('search-input').value;
        
        let url = `/api/admin/users?page=${currentPage}`;
        if (role) url += `&role=${role}`;
        if (search) url += `&search=${search}`;
        
        const response = await axios.get(url);
        if (response.data.success) {
            renderTable(response.data.data.data);
            renderPagination(response.data.data);
        }
    } catch (error) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-error">Gagal mengambil data.</td></tr>';
    }
}

function renderTable(data) {
    const tbody = document.getElementById('user-table-body');
    tbody.innerHTML = '';
    
    if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-8">Data tidak ditemukan.</td></tr>';
        return;
    }
    
    data.forEach(item => {
        const roleBadge = item.role === 'admin' ? 'badge-secondary' : 'badge-primary';
        const dateObj = new Date(item.created_at);
        const joinedDate = dateObj.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
        const avatarUrl = item.avatar ? item.avatar : `https://ui-avatars.com/api/?name=${encodeURIComponent(item.name)}&background=random`;
        
        const itemJson = encodeURIComponent(JSON.stringify(item));
        
        tbody.innerHTML += `
            <tr class="hover">
                <td>
                    <div class="flex items-center gap-3">
                        <div class="avatar">
                            <div class="mask mask-squircle w-10 h-10">
                                <img src="${avatarUrl}" alt="Avatar" />
                            </div>
                        </div>
                        <div>
                            <div class="font-bold">${item.name}</div>
                            <div class="text-sm opacity-50">${item.email}</div>
                        </div>
                    </div>
                </td>
                <td class="font-mono text-sm">${item.nik || '-'}</td>
                <td>${item.phone || '-'}</td>
                <td>${joinedDate}</td>
                <td><span class="badge ${roleBadge} badge-sm capitalize">${item.role}</span></td>
                <td>
                    <button class="btn btn-sm btn-info btn-outline" onclick="showDetail('${itemJson}')">
                        <i class="fa-solid fa-eye"></i> Detail
                    </button>
                </td>
            </tr>
        `;
    });
}

function renderPagination(meta) {
    currentPage = meta.current_page;
    lastPage = meta.last_page;
    
    const info = document.getElementById('pagination-info');
    info.textContent = `Menampilkan ${meta.from || 0} - ${meta.to || 0} dari total ${meta.total} pengguna`;
    
    const controls = document.getElementById('pagination-controls');
    let html = '';
    
    html += `<button class="join-item btn btn-sm" ${currentPage === 1 ? 'disabled' : ''} onclick="changePage(${currentPage - 1})">«</button>`;
    html += `<button class="join-item btn btn-sm no-animation">Hal ${currentPage}</button>`;
    html += `<button class="join-item btn btn-sm" ${currentPage === lastPage ? 'disabled' : ''} onclick="changePage(${currentPage + 1})">»</button>`;
    
    controls.innerHTML = html;
}

function changePage(page) {
    if (page < 1 || page > lastPage) return;
    currentPage = page;
    loadUsers();
}

function showDetail(itemJsonEncoded) {
    const item = JSON.parse(decodeURIComponent(itemJsonEncoded));
    
    document.getElementById('detail-name').textContent = item.name;
    document.getElementById('detail-role').textContent = item.role.toUpperCase();
    document.getElementById('detail-nik').textContent = item.nik || '-';
    document.getElementById('detail-email').textContent = item.email;
    document.getElementById('detail-phone').textContent = item.phone || 'Belum diisi';
    document.getElementById('detail-address').textContent = item.address || 'Belum diisi';
    
    const dateObj = new Date(item.created_at);
    document.getElementById('detail-joined').textContent = dateObj.toLocaleDateString('id-ID', { 
        day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute:'2-digit' 
    });
    
    document.getElementById('detail-avatar').src = item.avatar ? item.avatar : `https://ui-avatars.com/api/?name=${encodeURIComponent(item.name)}&background=random`;
    
    document.getElementById('modal_detail_user').showModal();
}
</script>
