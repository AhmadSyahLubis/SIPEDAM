@extends('layouts.user')
@section('title', 'Profil Saya')
@section('page_title', 'Pengaturan Akun')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col md:flex-row gap-6">
        <!-- Profil Card -->
        <div class="card bg-base-100 shadow-xl border border-base-200 md:w-1/3 h-fit">
            <div class="card-body items-center text-center">
                <div class="avatar mb-4 indicator">
                    <span class="indicator-item badge badge-success badge-sm" id="role-badge">User</span>
                    <div class="w-32 rounded-full ring ring-primary ring-offset-base-100 ring-offset-2">
                        <img id="profile-avatar" src="" alt="Avatar" />
                    </div>
                </div>
                <h2 class="card-title text-2xl" id="profile-name-display">-</h2>
                <div class="text-sm font-mono text-gray-500 mb-2">NIK: <span id="profile-nik">-</span></div>
                <p class="text-sm text-gray-500" id="profile-email">-</p>
                
                <div class="divider before:bg-base-300 after:bg-base-300"></div>
                
                <div class="w-full text-left space-y-2">
                    <div class="text-sm"><i class="fa-solid fa-phone w-5 text-gray-400"></i> <span id="profile-phone-display">-</span></div>
                    <div class="text-sm"><i class="fa-solid fa-location-dot w-5 text-gray-400"></i> <span id="profile-address-display">-</span></div>
                </div>
            </div>
        </div>

        <div class="md:w-2/3 space-y-6">
            <!-- Edit Profile Form -->
            <div class="card bg-base-100 shadow-xl border border-base-200">
                <div class="card-body">
                    <h3 class="card-title text-xl mb-4"><i class="fa-solid fa-user-pen text-primary"></i> Edit Biodata</h3>
                    <form id="form-profile" onsubmit="updateProfile(event)">
                        <div class="form-control mb-3">
                            <label class="label"><span class="label-text font-semibold">Nama Lengkap</span></label>
                            <input type="text" id="name" name="name" class="input input-bordered w-full" required />
                        </div>
                        
                        <div class="form-control mb-3">
                            <label class="label"><span class="label-text font-semibold">No HP / WhatsApp</span></label>
                            <input type="tel" id="phone" name="phone" class="input input-bordered w-full" placeholder="08..." />
                        </div>
                        
                        <div class="form-control mb-5">
                            <label class="label"><span class="label-text font-semibold">Alamat Domisili</span></label>
                            <textarea id="address" name="address" class="textarea textarea-bordered h-24" placeholder="Jalan..."></textarea>
                        </div>
                        
                        <div class="form-control">
                            <button type="submit" class="btn btn-primary w-full sm:w-auto" id="btn-update-profile">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Ganti Password Form -->
            <div class="card bg-base-100 shadow-xl border border-base-200">
                <div class="card-body">
                    <h3 class="card-title text-xl mb-4"><i class="fa-solid fa-lock text-warning"></i> Keamanan & Sandi</h3>
                    <form id="form-password" onsubmit="updatePassword(event)">
                        <div class="form-control mb-3">
                            <label class="label"><span class="label-text font-semibold">Password Saat Ini</span></label>
                            <input type="password" id="current_password" name="current_password" class="input input-bordered w-full" required />
                        </div>
                        
                        <div class="form-control mb-3">
                            <label class="label"><span class="label-text font-semibold">Password Baru</span></label>
                            <input type="password" id="new_password" name="new_password" class="input input-bordered w-full" required minlength="8" />
                        </div>
                        
                        <div class="form-control mb-5">
                            <label class="label"><span class="label-text font-semibold">Konfirmasi Password Baru</span></label>
                            <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="input input-bordered w-full" required minlength="8" />
                        </div>
                        
                        <div class="form-control">
                            <button type="submit" class="btn btn-warning w-full sm:w-auto" id="btn-update-password">Ganti Password</button>
                        </div>
                    </form>
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection

@stack('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadProfile();
});

async function loadProfile() {
    try {
        const response = await axios.get('/api/user/profile');
        if (response.data.success) {
            const user = response.data.data;
            
            document.getElementById('name').value = user.name;
            document.getElementById('phone').value = user.phone || '';
            document.getElementById('address').value = user.address || '';
            
            document.getElementById('profile-name-display').textContent = user.name;
            document.getElementById('profile-nik').textContent = user.nik;
            document.getElementById('profile-email').textContent = user.email;
            document.getElementById('profile-phone-display').textContent = user.phone || 'Belum diatur';
            document.getElementById('profile-address-display').textContent = user.address || 'Belum diatur';
            
            const avatarUrl = user.avatar ? user.avatar : `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=random`;
            document.getElementById('profile-avatar').src = avatarUrl;
        }
    } catch (error) {
        console.error(error);
        Swal.fire('Error', 'Gagal memuat profil', 'error');
    }
}

async function updateProfile(e) {
    e.preventDefault();
    const btn = document.getElementById('btn-update-profile');
    
    const body = {
        name: document.getElementById('name').value,
        phone: document.getElementById('phone').value,
        address: document.getElementById('address').value
    };
    
    btn.disabled = true;
    btn.innerHTML = '<span class="loading loading-spinner loading-sm"></span> Menyimpan...';
    
    try {
        const response = await axios.put('/api/user/profile', body);
        if (response.data.success) {
            let lsData = JSON.parse(localStorage.getItem('user_data') || '{}');
            lsData.name = response.data.data.name;
            localStorage.setItem('user_data', JSON.stringify(lsData));
            
            Swal.fire('Berhasil!', response.data.message, 'success');
            loadProfile(); // reload UI
        }
    } catch (error) {
        Swal.fire('Gagal!', error.response?.data?.message || 'Terjadi kesalahan', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = 'Simpan Perubahan';
    }
}

async function updatePassword(e) {
    e.preventDefault();
    const btn = document.getElementById('btn-update-password');
    
    const body = {
        current_password: document.getElementById('current_password').value,
        password: document.getElementById('new_password').value,
        password_confirmation: document.getElementById('new_password_confirmation').value
    };
    
    btn.disabled = true;
    btn.innerHTML = '<span class="loading loading-spinner loading-sm"></span> Memproses...';
    
    try {
        const response = await axios.put('/api/user/profile/password', body);
        if (response.data.success) {
            document.getElementById('form-password').reset();
            Swal.fire({
                title: 'Berhasil!', 
                text: 'Sandi Anda telah diganti, silakan masuk kembali dengan password baru.', 
                icon: 'success'
            }).then(() => {
                handleLogout(); // force logout
            });
        }
    } catch (error) {
        let errorMsg = error.response?.data?.message || 'Gagal mengubah password';
        if (error.response?.data?.errors?.password) {
            errorMsg = error.response.data.errors.password[0];
        }
        Swal.fire('Gagal!', errorMsg, 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = 'Ganti Password';
    }
}
</script>
