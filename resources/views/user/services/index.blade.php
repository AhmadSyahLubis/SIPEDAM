@extends('layouts.user')
@section('title', 'Permohonan Layanan')
@section('page_title', 'Permohonan Layanan')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold">Layanan Publik</h2>
        <p class="text-base-content/70">Jelajahi dan ajukan permohonan layanan administratif.</p>
    </div>
    <button class="btn btn-secondary text-white shadow-lg" onclick="document.getElementById('modal_create_service').showModal()">
        <i class="fa-solid fa-file-signature mr-2"></i> Ajukan Layanan
    </button>
</div>

<div class="card bg-base-100 shadow-xl border border-base-200">
    <div class="card-body">
        <!-- Filters -->
        <div class="flex flex-col md:flex-row gap-4 mb-4 justify-between">
            <div class="form-control w-full max-w-xs">
                <div class="join">
                    <input type="text" id="search-input" placeholder="Cari layanan (nomor tiket)..." class="input input-bordered join-item w-full" />
                    <button class="btn btn-primary join-item" onclick="loadServices()"><i class="fa-solid fa-search"></i></button>
                </div>
            </div>
            <select id="status-filter" class="select select-bordered w-full max-w-xs" onchange="loadServices()">
                <option value="">Semua Status</option>
                <option value="menunggu">Menunggu</option>
                <option value="diproses">Diproses</option>
                <option value="selesai">Selesai</option>
                <option value="ditolak">Ditolak</option>
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="services-grid">
            <div class="col-span-full text-center py-10 opacity-70">
                <span class="loading loading-spinner text-secondary loading-lg"></span>
            </div>
        </div>
        
        <!-- Pagination -->
        <div class="flex justify-center mt-8" id="pagination-container">
        </div>
    </div>
</div>

<!-- Modal Create Service -->
<dialog id="modal_create_service" class="modal">
    <div class="modal-box w-11/12 max-w-2xl">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>
        <h3 class="font-bold text-xl mb-4 border-b border-base-200 pb-2">Formulir Permohonan Layanan</h3>
        
        <form id="form-create-service" onsubmit="submitService(event)">
            <div class="form-control">
                <label class="label"><span class="label-text font-semibold">Jenis Layanan Yang Dibutuhkan *</span></label>
                <select name="category_id" id="category_id" class="select select-bordered w-full" required>
                    <option value="" disabled selected>Pilih Jenis Layanan</option>
                </select>
            </div>

            <div class="form-control mt-4">
                <label class="label"><span class="label-text font-semibold">Deskripsi / Catatan Permohonan *</span></label>
                <textarea name="description" class="textarea textarea-bordered h-24 w-full" placeholder="Jelaskan kebutuhan layanan Anda secara spesifik..." required></textarea>
            </div>

            <div class="form-control mt-4">
                <label class="label">
                    <span class="label-text font-semibold">Dokumen Persyaratan *</span>
                    <span class="label-text-alt text-error font-medium">Wajib, Max 5MB (PDF/JPG/PNG)</span>
                </label>
                <div class="alert alert-info py-2 shadow-sm mb-2 rounded-lg text-sm bg-blue-50 text-blue-800 border bg-opacity-40">
                    <i class="fa-solid fa-circle-info"></i> Gabungkan seluruh dokumen persyaratan dalam satu file PDF jika lebih dari satu halaman.
                </div>
                <input type="file" name="attachment" class="file-input file-input-bordered file-input-secondary w-full" accept=".jpg,.jpeg,.png,.pdf" required />
            </div>

            <div class="modal-action border-t border-base-200 pt-4 mt-6">
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('modal_create_service').close()">Batal</button>
                <button type="submit" class="btn btn-secondary shadow text-white" id="btn-submit">
                    <i class="fa-solid fa-paper-plane mr-2"></i> Ajukan Permohonan
                </button>
            </div>
        </form>
    </div>
</dialog>

<!-- Modal Detail Permohonan -->
<dialog id="modal_service_detail" class="modal">
    <div class="modal-box w-11/12 max-w-3xl">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>
        <h3 class="font-bold text-xl mb-4 border-b border-base-200 pb-2">Detail Permohonan: <span id="detail-ticket" class="text-primary font-mono ml-2"></span></h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-bold text-gray-700 mb-3 uppercase text-xs tracking-wider">Isi Permohonan Anda</h4>
                <div class="mb-2">
                    <span class="text-xs font-semibold px-2 py-1 bg-base-200 rounded border border-base-300 shadow-sm" id="detail-category">Kategori</span>
                    <span class="text-xs text-gray-500 ml-2" id="detail-date">Tanggal</span>
                </div>
                <h5 class="text-sm font-semibold mt-4 mb-2">Keperluan:</h5>
                <p class="text-sm text-gray-600 mb-3 bg-gray-50 p-3 rounded border border-gray-100 whitespace-pre-line" id="detail-description"></p>
                
                <div class="mt-4" id="detail-attachment-container">
                    <h4 class="font-bold text-gray-700 mb-2 uppercase text-xs tracking-wider">Lampiran Dokumen</h4>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-file-pdf text-error text-xl"></i>
                        <span id="detail-attachment-name" class="text-sm truncate max-w-[150px]"></span>
                        <a id="detail-attachment-link" href="#" target="_blank" class="btn btn-xs btn-outline btn-primary ml-auto">
                            Tampilkan
                        </a>
                    </div>
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
    loadServices();
    loadCategories();
});

async function loadCategories() {
    try {
        const token = localStorage.getItem('auth_token');
        const response = await axios.get('/api/categories?type=layanan', {
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

async function loadServices(page = 1) {
    currentPage = page;
    const search = document.getElementById('search-input').value;
    const status = document.getElementById('status-filter').value;
    
    const container = document.getElementById('services-grid');
    container.innerHTML = '<div class="col-span-full text-center py-10 opacity-70"><span class="loading loading-spinner text-secondary loading-lg"></span></div>';
    
    try {
        const response = await axios.get(`/api/user/services?page=${page}&search=${search}&status=${status}`);
        if (response.data.success) {
            renderGrid(response.data.data.data);
            renderPagination(response.data.data);
        }
    } catch (error) {
        container.innerHTML = '<div class="col-span-full text-center text-error">Gagal mengambil data layanan.</div>';
    }
}

function renderGrid(data) {
    const container = document.getElementById('services-grid');
    container.innerHTML = '';
    
    if (data.length === 0) {
        container.innerHTML = '<div class="col-span-full text-center py-12 text-base-content/60"><i class="fa-regular fa-folder-open text-4xl mb-3 block"></i> Belum ada permohonan layanan.</div>';
        return;
    }
    
    data.forEach(item => {
        let badgeClass = 'badge-warning';
        if (item.status === 'diproses') badgeClass = 'badge-info';
        if (item.status === 'selesai') badgeClass = 'badge-success text-white';
        if (item.status === 'ditolak') badgeClass = 'badge-error text-white';
        
        const date = new Date(item.created_at).toLocaleDateString('id-ID', {
            day: 'numeric', month: 'short', year: 'numeric'
        });
        
        const catName = item.category ? item.category.name : '-';
        const itemJson = encodeURIComponent(JSON.stringify(item));
        
        container.innerHTML += `
            <div class="card bg-base-100 border border-base-200 shadow hover:shadow-lg transition-shadow">
                <div class="card-body p-5">
                    <div class="flex justify-between items-start mb-2">
                        <span class="badge ${badgeClass} font-semibold capitalize px-3 py-3">${item.status}</span>
                        <span class="text-xs font-semibold opacity-60">${date}</span>
                    </div>
                    <h3 class="card-title text-primary font-mono text-lg mt-1">${item.ticket_number}</h3>
                    <div class="text-[11px] font-semibold bg-base-200 px-2 py-1 rounded-md mb-2 inline-block leading-tight border border-base-300 shadow-sm">${catName}</div>
                    <p class="text-sm line-clamp-3 my-2 text-gray-600">${item.description}</p>
                    
                    <div class="flex justify-end gap-2 mt-4 pt-4 border-t border-base-100">
                        <button class="btn btn-sm btn-outline btn-primary" onclick="showDetail('${itemJson}')" title="Detail">
                            <i class="fa-solid fa-eye mr-1"></i> Detail
                        </button>
                        ${item.status === 'menunggu' ? `
                        <button class="btn btn-sm btn-outline btn-error" onclick="cancelService(${item.id})" title="Batalkan">
                            <i class="fa-solid fa-trash mr-1"></i> Batal
                        </button>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
    });
}

function renderPagination(meta) {
    const container = document.getElementById('pagination-container');
    container.innerHTML = '';
    
    if (meta.last_page <= 1) return;
    
    let html = '<div class="join shadow-sm">';
    
    html += `<button class="join-item btn btn-sm ${meta.current_page === 1 ? 'btn-disabled' : ''}" onclick="loadServices(${meta.current_page - 1})">«</button>`;
    
    for(let i = 1; i <= meta.last_page; i++) {
        if (i === 1 || i === meta.last_page || (i >= meta.current_page - 1 && i <= meta.current_page + 1)) {
            html += `<button class="join-item btn btn-sm ${meta.current_page === i ? 'btn-active' : ''}" onclick="loadServices(${i})">${i}</button>`;
        } else if (i === meta.current_page - 2 || i === meta.current_page + 2) {
            html += `<button class="join-item btn btn-sm btn-disabled">...</button>`;
        }
    }
    
    html += `<button class="join-item btn btn-sm ${meta.current_page === meta.last_page ? 'btn-disabled' : ''}" onclick="loadServices(${meta.current_page + 1})">»</button>`;
    
    html += '</div>';
    container.innerHTML = html;
}

async function submitService(e) {
    e.preventDefault();
    const btn = document.getElementById('btn-submit');
    const form = document.getElementById('form-create-service');
    
    btn.innerHTML = '<span class="loading loading-spinner loading-sm"></span> Diproses...';
    btn.disabled = true;
    
    const formData = new FormData(form);
    
    try {
        const response = await axios.post('/api/user/services', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        
        if (response.data.success) {
            document.getElementById('modal_create_service').close();
            form.reset();
            
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: response.data.message,
                confirmButtonColor: '#3085d6'
            });
            
            loadServices();
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
        btn.innerHTML = '<i class="fa-solid fa-paper-plane mr-2"></i> Ajukan Permohonan';
        btn.disabled = false;
    }
}

async function cancelService(id) {
    Swal.fire({
        title: 'Batalkan Permohonan?',
        text: "Anda yakin ingin membatalkan permohonan layanan ini?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Batalkan!'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await axios.delete(`/api/user/services/${id}`);
                if (response.data.success) {
                    Swal.fire('Dibatalkan!', response.data.message, 'success');
                    loadServices(currentPage);
                }
            } catch (error) {
                Swal.fire('Error!', error.response?.data?.message || 'Gagal membatalkan permohonan', 'error');
            }
        }
    });
}

function showDetail(itemJsonEncoded) {
    const item = JSON.parse(decodeURIComponent(itemJsonEncoded));
    
    document.getElementById('detail-ticket').textContent = item.ticket_number;
    document.getElementById('detail-category').textContent = item.category ? item.category.name : '-';
    document.getElementById('detail-date').textContent = new Date(item.created_at).toLocaleString('id-ID');
    document.getElementById('detail-description').textContent = item.description;
    
    const attContainer = document.getElementById('detail-attachment-container');
    if (item.attachments && item.attachments.length > 0) {
        attContainer.style.display = 'block';
        document.getElementById('detail-attachment-link').href = '/storage/' + item.attachments[0].file_path;
        document.getElementById('detail-attachment-name').textContent = item.attachments[0].file_name;
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
    
    document.getElementById('modal_service_detail').showModal();
}
</script>
