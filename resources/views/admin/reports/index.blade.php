@extends('layouts.admin')
@section('title', 'Kelola Pengaduan')
@section('page_title', 'Kelola Pengaduan Masyarakat')

@section('content')
<div class="card bg-base-100 shadow-sm border border-base-200">
    <div class="card-body">
        
        <!-- Filters -->
        <div class="flex flex-col md:flex-row gap-4 mb-6 justify-between">
            <div class="form-control w-full md:w-96">
                <div class="join">
                    <input type="text" id="search-input" placeholder="Cari tiket, judul, atau pelapor..." class="input input-bordered join-item w-full bg-white text-black" onkeydown="if(event.key === 'Enter') loadAdminReports()" />
                    <button class="btn btn-primary join-item" onclick="loadAdminReports()"><i class="fa-solid fa-search"></i> Cari</button>
                </div>
            </div>
            <select id="status-filter" class="select select-bordered w-full md:w-48 bg-white text-black" onchange="loadAdminReports()">
                <option value="">Semua Status</option>
                <option value="menunggu">Menunggu</option>
                <option value="diproses">Diproses</option>
                <option value="selesai">Selesai</option>
                <option value="ditolak">Ditolak</option>
            </select>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full bg-white text-black text-sm">
                <thead>
                    <tr class="bg-base-200 text-black">
                        <th>No Tiket / Pelapor</th>
                        <th>Kategori & Lokasi</th>
                        <th>Judul Laporan</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="reports-table">
                    <tr><td colspan="6" class="text-center py-10"><span class="loading loading-spinner text-primary loading-lg"></span></td></tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="flex justify-between items-center mt-6">
            <div class="text-sm text-gray-500" id="pagination-info">Menampilkan -</div>
            <div id="pagination-container"></div>
        </div>
    </div>
</div>

<!-- Modal Detail & Update Status -->
<dialog id="modal_report_detail" class="modal">
    <div class="modal-box w-11/12 max-w-4xl bg-white text-black">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>
        <h3 class="font-bold text-xl mb-4 border-b border-gray-200 pb-2">Detail Pengaduan <span id="detail-ticket" class="text-primary font-mono ml-2"></span></h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Informasi Laporan -->
            <div>
                <h4 class="font-bold text-gray-700 mb-3 uppercase text-xs tracking-wider">Informasi Pelapor</h4>
                <div class="bg-gray-50 p-4 rounded-lg flex items-center gap-4 mb-4 border border-gray-100">
                    <div class="avatar">
                        <div class="w-12 h-12 rounded-full border border-gray-300">
                            <img id="detail-user-avatar" src="" alt="Avatar" />
                        </div>
                    </div>
                    <div>
                        <div class="font-bold whitespace-nowrap" id="detail-user-name">Nama</div>
                        <div class="text-xs text-gray-500 font-mono" id="detail-user-nik">NIK: -</div>
                        <div class="text-xs text-gray-500" id="detail-user-contact">Email/Phone</div>
                    </div>
                </div>

                <h4 class="font-bold text-gray-700 mb-3 uppercase text-xs tracking-wider">Isi Laporan</h4>
                <div class="mb-2">
                    <span class="badge badge-outline" id="detail-category">Kategori</span>
                    <span class="text-xs text-gray-500 ml-2" id="detail-date">Tanggal</span>
                </div>
                <h5 class="text-lg font-bold" id="detail-title">Judul</h5>
                <p class="text-sm text-gray-600 mt-2 mb-3 bg-gray-50 p-3 rounded border border-gray-100 whitespace-pre-line" id="detail-description"></p>
                
                <div class="text-sm"><i class="fa-solid fa-map-location-dot text-primary mr-2"></i> <span id="detail-location">-</span></div>
                
                <div class="mt-4" id="detail-attachment-container">
                    <h4 class="font-bold text-gray-700 mb-2 uppercase text-xs tracking-wider">Lampiran</h4>
                    <a id="detail-attachment-link" href="#" target="_blank" class="btn btn-sm btn-outline btn-primary">
                        <i class="fa-solid fa-paperclip"></i> Lihat Lampiran
                    </a>
                </div>
            </div>

            <!-- Panel Tindak Lanjut -->
            <div class="border-l border-gray-200 pl-0 md:pl-6">
                <h4 class="font-bold text-gray-700 mb-3 uppercase text-xs tracking-wider">Tindak Lanjut Laporan</h4>
                
                <div class="bg-base-100 border border-base-200 p-4 rounded-lg mb-4 max-h-[250px] overflow-y-auto">
                    <ul class="timeline timeline-vertical" id="detail-timeline">
                        <!-- Timeline generated by JS -->
                    </ul>
                </div>

                <h4 class="font-bold text-gray-700 mb-3 mt-6 uppercase text-xs tracking-wider">Update Status Baru</h4>
                <form id="form-update-status" onsubmit="updateStatus(event)">
                    <input type="hidden" id="report_id" />
                    
                    <div class="form-control mb-4">
                        <label class="label"><span class="label-text font-bold text-black">Ubah Status Menjadi</span></label>
                        <select class="select select-bordered w-full bg-white" id="update_status" required>
                            <option value="" disabled selected>Pilih Status Baru</option>
                            <option value="diproses">Proses Laporan Ini</option>
                            <option value="selesai">Tandai Selesai (Tuntas)</option>
                            <option value="ditolak">Tolak Laporan (Tidak Valid)</option>
                        </select>
                    </div>

                    <div class="form-control mb-4">
                        <label class="label"><span class="label-text font-bold text-black">Catatan Admin / Balasan</span></label>
                        <textarea class="textarea textarea-bordered h-24 w-full bg-white" id="update_notes" placeholder="Berikan catatan, alasan, atau solusi kepada masyarakat..."></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-full" id="btn-update-status">
                        <i class="fa-solid fa-check mr-2"></i> Simpan Perubahan Status
                    </button>
                </form>
            </div>
        </div>
    </div>
</dialog>

@endsection

@stack('scripts')
<script>
let currentPage = 1;

document.addEventListener('DOMContentLoaded', function() {
    loadAdminReports();
});

async function loadAdminReports(page = 1) {
    currentPage = page;
    const search = document.getElementById('search-input').value;
    const status = document.getElementById('status-filter').value;
    
    const tbody = document.getElementById('reports-table');
    tbody.innerHTML = '<tr><td colspan="6" class="text-center py-10"><span class="loading loading-spinner text-primary loading-lg"></span></td></tr>';
    
    try {
        const response = await axios.get(`/api/admin/reports?page=${page}&search=${search}&status=${status}`);
        if (response.data.success) {
            renderTable(response.data.data.data);
            renderPagination(response.data.data);
        }
    } catch (error) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-error">Gagal mengambil data laporan. Pastikan Anda punya akses Admin.</td></tr>';
    }
}

function renderTable(data) {
    const tbody = document.getElementById('reports-table');
    tbody.innerHTML = '';
    
    if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-8 text-gray-500">Tidak ada pengaduan ditemukan.</td></tr>';
        return;
    }
    
    data.forEach(item => {
        let badgeClass = 'badge-warning';
        if(item.status === 'diproses') badgeClass = 'badge-info';
        if(item.status === 'selesai') badgeClass = 'badge-success text-white';
        if(item.status === 'ditolak') badgeClass = 'badge-error text-white';
        
        const date = new Date(item.created_at).toLocaleDateString('id-ID', {
            day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
        });
        
        const userName = item.user ? item.user.name : 'NN';
        const userNik = item.user ? item.user.nik : '-';
        const catName = item.category ? item.category.name : '-';
        
        // Serialize item for onclick attribute to pass to modal
        const itemJson = encodeURIComponent(JSON.stringify(item));
        
        tbody.innerHTML += `
            <tr class="hover">
                <td>
                    <div class="font-mono font-bold text-primary">${item.ticket_number}</div>
                    <div class="text-xs font-semibold mt-1">${userName}</div>
                    <div class="text-[10px] text-gray-500">${userNik}</div>
                </td>
                <td>
                    <span class="font-semibold text-xs border border-gray-200 px-2 py-1 rounded bg-gray-50">${catName}</span>
                    <div class="text-xs mt-1 truncate max-w-[150px]" title="${item.location || '-'}">${item.location || '-'}</div>
                </td>
                <td class="max-w-[200px]">
                    <div class="font-bold truncate" title="${item.title}">${item.title}</div>
                    <div class="text-xs text-gray-500 truncate" title="${item.description}">${item.description}</div>
                </td>
                <td class="text-xs">${date}</td>
                <td><span class="badge ${badgeClass} font-semibold capitalize badge-sm">${item.status}</span></td>
                <td class="text-center">
                    <button class="btn btn-sm btn-primary" onclick="openDetailModal('${itemJson}')">
                        <i class="fa-solid fa-list-check"></i> Proses
                    </button>
                </td>
            </tr>
        `;
    });
}

function renderPagination(meta) {
    document.getElementById('pagination-info').textContent = `Menampilkan ${meta.from || 0} - ${meta.to || 0} dari ${meta.total} data`;
    
    const container = document.getElementById('pagination-container');
    container.innerHTML = '';
    if (meta.last_page <= 1) return;
    
    let html = '<div class="join shadow-sm border border-gray-200 rounded-lg overflow-hidden">';
    html += `<button class="join-item btn btn-sm bg-white border-0 ${meta.current_page === 1 ? 'btn-disabled' : 'hover:bg-gray-100 text-black'}" onclick="loadAdminReports(${meta.current_page - 1})">«</button>`;
    
    for(let i = 1; i <= meta.last_page; i++) {
        if (i === 1 || i === meta.last_page || (i >= meta.current_page - 1 && i <= meta.current_page + 1)) {
            const activeClass = meta.current_page === i ? 'bg-primary text-white hover:bg-primary' : 'bg-white text-black hover:bg-gray-100';
            html += `<button class="join-item btn btn-sm border-0 ${activeClass}" onclick="loadAdminReports(${i})">${i}</button>`;
        } else if (i === meta.current_page - 2 || i === meta.current_page + 2) {
            html += `<button class="join-item btn btn-sm bg-white text-gray-400 border-0 btn-disabled">...</button>`;
        }
    }
    
    html += `<button class="join-item btn btn-sm bg-white border-0 ${meta.current_page === meta.last_page ? 'btn-disabled' : 'hover:bg-gray-100 text-black'}" onclick="loadAdminReports(${meta.current_page + 1})">»</button>`;
    html += '</div>';
    container.innerHTML = html;
}

function openDetailModal(itemJsonEncoded) {
    const item = JSON.parse(decodeURIComponent(itemJsonEncoded));
    
    // Set Detail Data
    document.getElementById('detail-ticket').textContent = item.ticket_number;
    document.getElementById('detail-user-avatar').src = item.user && item.user.avatar ? '/storage/' + item.user.avatar : `https://ui-avatars.com/api/?name=${encodeURIComponent(item.user ? item.user.name : 'N')}&background=random`;
    document.getElementById('detail-user-name').textContent = item.user ? item.user.name : 'Unknown User';
    document.getElementById('detail-user-nik').textContent = 'NIK: ' + (item.user ? item.user.nik : '-');
    document.getElementById('detail-user-contact').textContent = (item.user ? item.user.email : '') + ' | ' + (item.user ? item.user.phone : '');
    
    document.getElementById('detail-category').textContent = item.category ? item.category.name : '-';
    document.getElementById('detail-date').textContent = new Date(item.created_at).toLocaleString('id-ID');
    document.getElementById('detail-title').textContent = item.title;
    document.getElementById('detail-description').textContent = item.description;
    document.getElementById('detail-location').textContent = item.location || 'Tidak ada lokasi';
    
    // Attachment
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
            const changedBy = history.changed_by ? (history.changed_by.name || 'Admin') : 'User / Sistem';
            
            timelineHtml += `
            <li>
                ${index > 0 ? `<hr class="${bgClass}" />` : ''}
                <div class="timeline-start text-xs text-gray-500 pt-1">${date}</div>
                <div class="timeline-middle ${colorClass}">
                    ${iconText}
                </div>
                <div class="timeline-end timeline-box w-full bg-base-100 shadow-sm border-base-200">
                    <div class="font-bold text-sm capitalize ${colorClass.replace('text-', 'text-')}">${history.status}</div>
                    <div class="text-xs mt-1 text-gray-600">${history.notes || '-'}</div>
                    <div class="text-[10px] text-gray-400 mt-2 uppercase">Oleh: ${changedBy}</div>
                </div>
                ${!isLast ? `<hr class="${bgClass}" />` : ''}
            </li>
            `;
        });
        timelineContainer.innerHTML = timelineHtml;
    } else {
        timelineContainer.innerHTML = '<li class="text-sm text-gray-500 text-center w-full pb-4">Belum ada jejak status</li>';
    }
    
    // Form prepopulate
    document.getElementById('report_id').value = item.id;
    document.getElementById('update_status').value = ''; 
    document.getElementById('update_notes').value = item.admin_notes || '';
    
    document.getElementById('modal_report_detail').showModal();
}

async function updateStatus(e) {
    e.preventDefault();
    const btn = document.getElementById('btn-update-status');
    const id = document.getElementById('report_id').value;
    const status = document.getElementById('update_status').value;
    const notes = document.getElementById('update_notes').value;
    
    btn.innerHTML = '<span class="loading loading-spinner loading-sm"></span> Menyimpan...';
    btn.disabled = true;
    
    try {
        const response = await axios.put(`/api/admin/reports/${id}/status`, {
            status: status,
            admin_notes: notes
        });
        
        if (response.data.success) {
            document.getElementById('modal_report_detail').close();
            Swal.fire({
                icon: 'success',
                title: 'Tersimpan',
                text: 'Status laporan berhasil diperbarui.',
                timer: 1500,
                showConfirmButton: false
            });
            loadAdminReports(currentPage);
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: error.response?.data?.message || 'Gagal menyimpan status'
        });
    } finally {
        btn.innerHTML = '<i class="fa-solid fa-check mr-2"></i> Simpan Perubahan Status';
        btn.disabled = false;
    }
}
</script>
