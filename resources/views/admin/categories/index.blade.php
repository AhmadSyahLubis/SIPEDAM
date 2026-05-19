@extends('layouts.admin')
@section('title', 'Manajemen Kategori')
@section('page_title', 'Kelola Kategori')

@section('content')
<div class="mb-6 flex justify-between items-center flex-wrap gap-4">
    <div>
        <h2 class="text-2xl font-bold">Kategori Master</h2>
        <p class="text-base-content/70">Kelola kategori untuk Laporan Pengaduan dan Permohonan Layanan.</p>
    </div>
    <button class="btn btn-primary" onclick="openModalCreate()">
        <i class="fa-solid fa-plus mr-2"></i> Tambah Kategori
    </button>
</div>

<div class="card bg-base-100 shadow-xl border border-base-200">
    <div class="card-body p-0 sm:p-6">
        <div class="flex flex-col md:flex-row justify-between mb-4 gap-4 px-4 sm:px-0 pt-4 sm:pt-0">
            <div class="form-control w-full max-w-xs">
                <select id="type-filter" class="select select-bordered" onchange="loadCategories()">
                    <option value="">Semua Tipe</option>
                    <option value="laporan">Laporan Pengaduan</option>
                    <option value="layanan">Permohonan Layanan</option>
                </select>
            </div>
            <div class="form-control w-full max-w-xs">
                <input type="text" id="search-input" placeholder="Cari kategori..." class="input input-bordered w-full" onkeyup="filterLocalData()" />
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr class="bg-base-200">
                        <th>No</th>
                        <th>Ikon</th>
                        <th>Nama Kategori</th>
                        <th>Tipe</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="category-table-body">
                    <tr><td colspan="6" class="text-center py-8"><span class="loading loading-spinner text-primary"></span></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form Kategori -->
<dialog id="modal_category" class="modal">
    <div class="modal-box">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>
        <h3 class="font-bold text-lg mb-4 border-b pb-2" id="modal-title">Tambah Kategori Baru</h3>
        
        <form id="form-category" onsubmit="submitCategory(event)">
            <input type="hidden" id="category_id">
            
            <div class="form-control mb-3">
                <label class="label"><span class="label-text font-semibold">Tipe *</span></label>
                <select id="type" name="type" class="select select-bordered w-full" required>
                    <option value="laporan">Laporan Pengaduan</option>
                    <option value="layanan">Permohonan Layanan</option>
                </select>
            </div>

            <div class="form-control mb-3">
                <label class="label"><span class="label-text font-semibold">Nama Kategori *</span></label>
                <input type="text" id="name" name="name" class="input input-bordered w-full" required />
            </div>
            
            <div class="form-control mb-3">
                <label class="label">
                    <span class="label-text font-semibold">Icon Class (opsional)</span>
                    <span class="label-text-alt text-gray-500">FontAwesome e.g. fa-solid fa-bolt</span>
                </label>
                <input type="text" id="icon" name="icon" class="input input-bordered w-full" placeholder="fa-solid fa-circle" />
            </div>

            <div class="form-control mb-5">
                <label class="label"><span class="label-text font-semibold">Deskripsi</span></label>
                <textarea id="description" name="description" class="textarea textarea-bordered h-20" placeholder="Deskripsi opsional..."></textarea>
            </div>

            <div class="modal-action">
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('modal_category').close()">Batal</button>
                <button type="submit" class="btn btn-primary" id="btn-submit">Simpan</button>
            </div>
        </form>
    </div>
</dialog>
@endsection

@stack('scripts')
<script>
let allCategories = [];

document.addEventListener('DOMContentLoaded', function() {
    loadCategories();
});

async function loadCategories() {
    try {
        const typeFilter = document.getElementById('type-filter').value;
        const url = `/api/categories?all=true${typeFilter ? '&type='+typeFilter : ''}`;
        
        const response = await axios.get(url);
        if (response.data.success) {
            allCategories = response.data.data;
            renderTable(allCategories);
        }
    } catch (error) {
        document.getElementById('category-table-body').innerHTML = '<tr><td colspan="6" class="text-center text-error">Gagal mengambil data kategori.</td></tr>';
    }
}

function filterLocalData() {
    const search = document.getElementById('search-input').value.toLowerCase();
    const filtered = allCategories.filter(cat => cat.name.toLowerCase().includes(search));
    renderTable(filtered);
}

function renderTable(data) {
    const tbody = document.getElementById('category-table-body');
    tbody.innerHTML = '';
    
    if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-8">Tidak ada kategori.</td></tr>';
        return;
    }
    
    data.forEach((item, index) => {
        const iconHtml = item.icon ? `<i class="${item.icon} text-xl w-8 text-center text-gray-600"></i>` : '-';
        const typeBadge = item.type === 'laporan' ? 'badge-primary' : 'badge-secondary';
        const activeBadge = item.is_active ? 'badge-success text-white' : 'badge-error text-white';
        const itemJson = encodeURIComponent(JSON.stringify(item));
        
        tbody.innerHTML += `
            <tr class="hover">
                <td>${index + 1}</td>
                <td>${iconHtml}</td>
                <td class="font-bold">${item.name}</td>
                <td><span class="badge ${typeBadge} font-semibold capitalize">${item.type}</span></td>
                <td><span class="badge ${activeBadge} badge-sm font-semibold">${item.is_active ? 'Aktif' : 'Nonaktif'}</span></td>
                <td>
                    <div class="flex gap-2">
                        <button class="btn btn-sm btn-circle btn-info btn-outline" onclick="openModalEdit('${itemJson}')" title="Edit">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button class="btn btn-sm ${item.is_active ? 'btn-warning' : 'btn-success'} btn-outline w-24" onclick="toggleActive(${item.id})">
                            ${item.is_active ? 'Nonaktifkan' : 'Aktifkan'}
                        </button>
                        <button class="btn btn-sm btn-circle btn-error btn-outline" onclick="deleteCategory(${item.id})" title="Hapus">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
}

function openModalCreate() {
    document.getElementById('form-category').reset();
    document.getElementById('category_id').value = '';
    document.getElementById('modal-title').textContent = 'Tambah Kategori Baru';
    document.getElementById('modal_category').showModal();
}

function openModalEdit(itemJsonEncoded) {
    const item = JSON.parse(decodeURIComponent(itemJsonEncoded));
    document.getElementById('category_id').value = item.id;
    document.getElementById('name').value = item.name;
    document.getElementById('type').value = item.type;
    document.getElementById('icon').value = item.icon || '';
    document.getElementById('description').value = item.description || '';
    
    document.getElementById('modal-title').textContent = 'Edit Kategori';
    document.getElementById('modal_category').showModal();
}

async function submitCategory(e) {
    e.preventDefault();
    const btn = document.getElementById('btn-submit');
    const id = document.getElementById('category_id').value;
    const body = {
        name: document.getElementById('name').value,
        type: document.getElementById('type').value,
        icon: document.getElementById('icon').value,
        description: document.getElementById('description').value
    };
    
    btn.disabled = true;
    btn.innerHTML = '<span class="loading loading-spinner loading-sm"></span> Menyimpan...';
    
    try {
        let response;
        if (id) {
            response = await axios.put(`/api/admin/categories/${id}`, body);
        } else {
            response = await axios.post('/api/admin/categories', body);
        }
        
        if (response.data.success) {
            document.getElementById('modal_category').close();
            Swal.fire('Berhasil!', response.data.message, 'success');
            loadCategories();
        }
    } catch (error) {
        Swal.fire('Gagal!', error.response?.data?.message || 'Terjadi kesalahan', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = 'Simpan';
    }
}

async function toggleActive(id) {
    try {
        const response = await axios.put(`/api/admin/categories/${id}/toggle`);
        if (response.data.success) {
            loadCategories();
        }
    } catch (error) {
        Swal.fire('Error!', error.response?.data?.message || 'Gagal mengubah status', 'error');
    }
}

function deleteCategory(id) {
    Swal.fire({
        title: 'Hapus Kategori?',
        text: "Kategori tidak dapat dihapus jika sudah digunakan di pelaporan.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await axios.delete(`/api/admin/categories/${id}`);
                if (response.data.success) {
                    Swal.fire('Terhapus!', response.data.message, 'success');
                    loadCategories();
                }
            } catch (error) {
                Swal.fire('Gagal!', error.response?.data?.message || 'Kategori ini sudah terpakai', 'error');
            }
        }
    });
}
</script>
