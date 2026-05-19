@extends('layouts.user')
@section('title', 'Laporan Pengaduan')
@section('page_title', 'Laporan Pengaduan')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold">Laporan Pengaduan Anda</h2>
        <p class="text-base-content/70">Kelola dan pantau status laporan yang Anda ajukan.</p>
    </div>
    <button class="btn btn-primary shadow-lg" onclick="document.getElementById('modal_create_report').showModal()">
        <i class="fa-solid fa-plus mr-2"></i> Buat Laporan
    </button>
</div>

<div class="card bg-base-100 shadow-xl border border-base-200">
    <div class="card-body">
        <!-- Filters -->
        <div class="flex flex-col md:flex-row gap-4 mb-4 justify-between">
            <div class="form-control w-full max-w-xs">
                <div class="join">
                    <input type="text" id="search-input" placeholder="Cari berdasarkan tiket/judul..." class="input input-bordered join-item w-full" />
                    <button class="btn btn-secondary join-item" onclick="loadReports()"><i class="fa-solid fa-search"></i></button>
                </div>
            </div>
            <select id="status-filter" class="select select-bordered w-full max-w-xs" onchange="loadReports()">
                <option value="">Semua Status</option>
                <option value="menunggu">Menunggu</option>
                <option value="diproses">Diproses</option>
                <option value="selesai">Selesai</option>
                <option value="ditolak">Ditolak</option>
            </select>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr class="bg-base-200">
                        <th>No Tiket</th>
                        <th>Kategori</th>
                        <th>Judul Laporan</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="reports-table">
                    <tr><td colspan="6" class="text-center py-10"><span class="loading loading-spinner text-primary loading-lg"></span></td></tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="flex justify-center mt-6" id="pagination-container">
        </div>
    </div>
</div>

<!-- Modal Create Report -->
<dialog id="modal_create_report" class="modal">
    <div class="modal-box w-11/12 max-w-3xl">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>
        <h3 class="font-bold text-xl mb-4 border-b border-base-200 pb-2">Formulir Pengaduan Baru</h3>
        
        <form id="form-create-report" onsubmit="submitReport(event)">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-control">
                    <label class="label"><span class="label-text font-semibold">Kategori Pengaduan *</span></label>
                    <select name="category_id" id="category_id" class="select select-bordered w-full" required>
                        <option value="" disabled selected>Pilih Kategori</option>
                        <!-- Options loaded via JS -->
                    </select>
                </div>
                
                <div class="form-control">
                    <label class="label"><span class="label-text font-semibold">Lokasi Kejadian</span></label>
                    <input type="text" name="location" class="input input-bordered w-full" placeholder="Cth: Jl. Sudirman No 123" />
                </div>
            </div>

            <div class="form-control mt-4">
                <label class="label"><span class="label-text font-semibold">Judul Laporan *</span></label>
                <input type="text" name="title" class="input input-bordered w-full" placeholder="Ringkasan singkat kejadian" required />
            </div>

            <div class="form-control mt-4">
                <label class="label"><span class="label-text font-semibold">Deskripsi Lengkap *</span></label>
                <textarea name="description" class="textarea textarea-bordered h-32 w-full" placeholder="Jelaskan detail kejadian/keluhan Anda..." required></textarea>
            </div>

            <div class="form-control mt-4">
                <label class="label">
                    <span class="label-text font-semibold">Lampiran Bukti (Opsional)</span>
                    <span class="label-text-alt text-error">Max 2MB (JPG, PNG, PDF)</span>
                </label>
                <input type="file" name="attachment" class="file-input file-input-bordered file-input-primary w-full" accept=".jpg,.jpeg,.png,.pdf" />
            </div>

            <div class="modal-action border-t border-base-200 pt-4 mt-6">
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('modal_create_report').close()">Batal</button>
                <button type="submit" class="btn btn-primary shadow" id="btn-submit">
                    <i class="fa-solid fa-paper-plane mr-2"></i> Kirim Laporan
                </button>
            </div>
        </form>
    </div>
</dialog>

<!-- Modal Detail Laporan -->
<dialog id="modal_report_detail" class="modal">
    <div class="modal-box w-11/12 max-w-3xl">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>
        <h3 class="font-bold text-xl mb-4 border-b border-base-200 pb-2">Detail Laporan: <span id="detail-ticket" class="text-primary font-mono ml-2"></span></h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-bold text-gray-700 mb-3 uppercase text-xs tracking-wider">Isi Laporan Anda</h4>
                <div class="mb-2">
                    <span class="text-xs font-semibold px-2 py-1 bg-base-200 rounded border border-base-300 shadow-sm" id="detail-category">Kategori</span>
                    <span class="text-xs text-gray-500 ml-2" id="detail-date">Tanggal</span>
                </div>
                <h5 class="text-lg font-bold" id="detail-title">Judul</h5>
                <p class="text-sm text-gray-600 mt-2 mb-3 bg-gray-50 p-3 rounded border border-gray-100 whitespace-pre-line" id="detail-description"></p>
                <div class="text-sm"><i class="fa-solid fa-map-location-dot text-primary mr-2"></i> <span id="detail-location">-</span></div>
                
                <div class="mt-4" id="detail-attachment-container">
                    <h4 class="font-bold text-gray-700 mb-2 uppercase text-xs tracking-wider">Lampiran Bukti</h4>
                    <a id="detail-attachment-link" href="#" target="_blank" class="btn btn-sm btn-outline btn-primary">
                        <i class="fa-solid fa-paperclip"></i> Unduh / Lihat Lampiran
                    </a>
                </div>
            </div>

            <div class="border-l border-base-200 pl-0 md:pl-6">
                <h4 class="font-bold text-gray-700 mb-3 uppercase text-xs tracking-wider">Status & Tanggapan Sistem</h4>
                
                <h4 class="font-bold text-gray-700 mb-3 uppercase text-xs tracking-wider">Jejak Status (Timeline)</h4>
                
                <div class="bg-base-100 border border-base-200 p-4 rounded-lg mb-4 max-h-[300px] overflow-y-auto">
                    <ul class="timeline timeline-vertical" id="detail-timeline">
                        <!-- Timeline generated by JS -->
                    </ul>
                </div>
            </div>
        </div>
    </div>
</dialog>

@endsection

@stack('scripts')
<script>
let currentPage = 1;

document.addEventListener('DOMContentLoaded', function() {
    loadReports();
    loadCategories();
});

async function loadCategories() {
    try {
        const token = localStorage.getItem('auth_token');
        const response = await axios.get('/api/categories?type=laporan', {
            headers: { 'Authorization': 'Bearer ' + token }
        });
        const select = document.getElementById('category_id');
        
        if (response.data.success) {
            response.data.data.forEach(cat => {
                select.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
            });
        }
    } catch (error) {
        console.error('Failed to load categories', error);
    }
}

async function loadReports(page = 1) {
    currentPage = page;
    const search = document.getElementById('search-input').value;
    const status = document.getElementById('status-filter').value;
    
    const tbody = document.getElementById('reports-table');
    tbody.innerHTML = '<tr><td colspan="6" class="text-center py-10"><span class="loading loading-spinner text-primary loading-lg"></span></td></tr>';
    
    try {
        const response = await axios.get(`/api/user/reports?page=${page}&search=${search}&status=${status}`);
        if (response.data.success) {
            renderTable(response.data.data.data);
            renderPagination(response.data.data);
        }
    } catch (error) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-error">Gagal mengambil data laporan.</td></tr>';
    }
}

function renderTable(data) {
    const tbody = document.getElementById('reports-table');
    tbody.innerHTML = '';
    
    if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-8 text-base-content/60">Tidak ada laporan ditemukan.</td></tr>';
        return;
    }
    
    data.forEach(item => {
        let badgeClass = 'badge-warning';
        if(item.status === 'diproses') badgeClass = 'badge-info';
        if(item.status === 'selesai') badgeClass = 'badge-success text-white';
        if(item.status === 'ditolak') badgeClass = 'badge-error text-white';
        
        const date = new Date(item.created_at).toLocaleDateString('id-ID', {
            day: 'numeric', month: 'short', year: 'numeric'
        });
        
        const catName = item.category ? item.category.name : '-';
        const itemJson = encodeURIComponent(JSON.stringify(item));
        
        tbody.innerHTML += `
            <tr class="hover">
                <td class="font-mono font-bold text-primary text-sm">${item.ticket_number}</td>
                <td>
                    <div class="text-[11px] font-semibold bg-base-200 px-2 py-1 rounded-md max-w-[160px] whitespace-normal break-words leading-tight border border-base-300 shadow-sm text-center">${catName}</div>
                </td>
                <td class="max-w-[200px]">
                    <div class="font-bold text-sm truncate" title="${item.title}">${item.title}</div>
                    <div class="text-xs opacity-75 truncate" title="${item.location || '-'}">${item.location || '-'}</div>
                </td>
                <td class="text-xs whitespace-nowrap">${date}</td>
                <td><span class="badge ${badgeClass} badge-sm font-semibold capitalize">${item.status}</span></td>
                <td>
                    <div class="flex flex-nowrap gap-1">
                        <button class="btn btn-sm btn-circle btn-primary btn-outline" onclick="showDetail('${itemJson}')" title="Detail">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                        ${item.status === 'menunggu' ? `
                        <button class="btn btn-sm btn-circle btn-error btn-outline" onclick="cancelReport(${item.id})" title="Batalkan">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                        ` : ''}
                    </div>
                </td>
            </tr>
        `;
    });
}

function renderPagination(meta) {
    const container = document.getElementById('pagination-container');
    container.innerHTML = '';
    
    if (meta.last_page <= 1) return;
    
    let html = '<div class="join shadow-sm">';
    
    // Prev
    html += `<button class="join-item btn btn-sm ${meta.current_page === 1 ? 'btn-disabled' : ''}" onclick="loadReports(${meta.current_page - 1})">«</button>`;
    
    // Pages
    for(let i = 1; i <= meta.last_page; i++) {
        if (i === 1 || i === meta.last_page || (i >= meta.current_page - 1 && i <= meta.current_page + 1)) {
            html += `<button class="join-item btn btn-sm ${meta.current_page === i ? 'btn-active' : ''}" onclick="loadReports(${i})">${i}</button>`;
        } else if (i === meta.current_page - 2 || i === meta.current_page + 2) {
            html += `<button class="join-item btn btn-sm btn-disabled">...</button>`;
        }
    }
    
    // Next
    html += `<button class="join-item btn btn-sm ${meta.current_page === meta.last_page ? 'btn-disabled' : ''}" onclick="loadReports(${meta.current_page + 1})">»</button>`;
    
    html += '</div>';
    container.innerHTML = html;
}

async function submitReport(e) {
    e.preventDefault();
    const btn = document.getElementById('btn-submit');
    const form = document.getElementById('form-create-report');
    
    btn.innerHTML = '<span class="loading loading-spinner loading-sm"></span> Mengirim...';
    btn.disabled = true;
    
    const formData = new FormData(form);
    
    try {
        const response = await axios.post('/api/user/reports', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        
        if (response.data.success) {
            document.getElementById('modal_create_report').close();
            form.reset();
            
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: response.data.message,
                confirmButtonColor: '#3085d6'
            });
            
            loadReports();
        }
    } catch (error) {
        let errorMsg = 'Terjadi kesalahan sistem.';
        if (error.response && error.response.data && error.response.data.errors) {
            errorMsg = Object.values(error.response.data.errors)[0][0];
        } else if (error.response && error.response.data) {
            errorMsg = error.response.data.message;
        }
        
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: errorMsg,
            confirmButtonColor: '#d33'
        });
    } finally {
        btn.innerHTML = '<i class="fa-solid fa-paper-plane mr-2"></i> Kirim Laporan';
        btn.disabled = false;
    }
}

async function cancelReport(id) {
    Swal.fire({
        title: 'Batalkan Laporan?',
        text: "Anda yakin ingin membatalkan laporan ini?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Batalkan!',
        cancelButtonText: 'Kembali'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await axios.delete(`/api/user/reports/${id}`);
                if (response.data.success) {
                    Swal.fire('Dibatalkan!', response.data.message, 'success');
                    loadReports(currentPage);
                }
            } catch (error) {
                Swal.fire('Error!', error.response?.data?.message || 'Gagal membatalkan laporan', 'error');
            }
        }
    });
}
function showDetail(itemJsonEncoded) {
    const item = JSON.parse(decodeURIComponent(itemJsonEncoded));
    
    document.getElementById('detail-ticket').textContent = item.ticket_number;
    document.getElementById('detail-category').textContent = item.category ? item.category.name : '-';
    document.getElementById('detail-date').textContent = new Date(item.created_at).toLocaleString('id-ID');
    document.getElementById('detail-title').textContent = item.title;
    document.getElementById('detail-description').textContent = item.description;
    document.getElementById('detail-location').textContent = item.location || 'Tidak ada lokasi spesifik';
    
    const attContainer = document.getElementById('detail-attachment-container');
    if (item.attachments && item.attachments.length > 0) {
        attContainer.style.display = 'block';
        document.getElementById('detail-attachment-link').href = '/storage/' + item.attachments[0].file_path;
    } else {
        attContainer.style.display = 'none';
    }
    
    const timelineContainer = document.getElementById('detail-timeline');
    timelineContainer.innerHTML = '';
    
    if (item.status_histories && item.status_histories.length > 0) {
        let timelineHtml = '';
        item.status_histories.forEach((history, index) => {
            const date = new Date(history.created_at).toLocaleString('id-ID', {
                day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
            });
            
            let colorClass = 'text-base-300';
            let bgClass = 'bg-base-300';
            let iconText = '<i class="fa-solid fa-circle text-[10px]"></i>';
            
            if (history.status === 'menunggu') { colorClass = 'text-warning'; bgClass = 'bg-warning'; }
            else if (history.status === 'diproses') { colorClass = 'text-info'; bgClass = 'bg-info'; }
            else if (history.status === 'selesai') { colorClass = 'text-success'; bgClass = 'bg-success'; iconText = '<i class="fa-solid fa-check text-[10px]"></i>'; }
            else if (history.status === 'ditolak') { colorClass = 'text-error'; bgClass = 'bg-error'; iconText = '<i class="fa-solid fa-xmark text-[10px]"></i>'; }
            
            const isLast = index === item.status_histories.length - 1;
            const changedBy = history.changed_by ? (history.changed_by.name || 'Admin') : 'Sistem';
            
            timelineHtml += `
            <li>
                ${index > 0 ? `<hr class="${bgClass}" />` : ''}
                <div class="timeline-start text-xs text-gray-500 pt-1">${date}</div>
                <div class="timeline-middle ${colorClass}">
                    ${iconText}
                </div>
                <div class="timeline-end timeline-box w-full bg-base-100 shadow-sm border-base-200">
                    <div class="font-bold text-sm capitalize ${colorClass.replace('text-', 'text-')}">${history.status}</div>
                    <div class="text-xs mt-1 text-gray-600">${history.notes || 'Status diperbarui'}</div>
                    <div class="text-[10px] text-gray-400 mt-2 uppercase">Oleh: ${history.status === 'menunggu' ? 'Anda' : 'Admin'}</div>
                </div>
                ${!isLast ? `<hr class="${bgClass}" />` : ''}
            </li>
            `;
        });
        timelineContainer.innerHTML = timelineHtml;
    } else {
        timelineContainer.innerHTML = '<li class="text-sm text-gray-500 text-center w-full pb-4">Belum ada jejak status</li>';
    }
    
    document.getElementById('modal_report_detail').showModal();
}
</script>
