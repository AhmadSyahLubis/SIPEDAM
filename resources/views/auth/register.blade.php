@extends('layouts.guest')
@section('title', 'Register')

@section('content')
<div class="card w-full max-w-lg bg-base-100 shadow-xl border border-base-200 m-4">
    <div class="card-body">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-primary">Daftar Akun Baru</h2>
            <p class="text-sm text-base-content/70 mt-1">Lengkapi data diri Anda untuk menggunakan layanan SIPEDAM</p>
        </div>

        <form id="registerForm" method="POST" action="javascript:void(0);">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text font-medium">Nama Lengkap <span class="text-error">*</span></span>
                    </label>
                    <input type="text" id="name" name="name" class="input input-bordered w-full" required />
                </div>
                
                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text font-medium">NIK <span class="text-error">*</span></span>
                    </label>
                    <input type="text" id="nik" name="nik" maxlength="16" class="input input-bordered w-full" required />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text font-medium">Email <span class="text-error">*</span></span>
                    </label>
                    <input type="email" id="email" name="email" class="input input-bordered w-full" required />
                </div>
                
                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text font-medium">No. Telepon</span>
                    </label>
                    <input type="text" id="phone" name="phone" class="input input-bordered w-full" />
                </div>
            </div>

            <div class="form-control w-full mt-2">
                <label class="label">
                    <span class="label-text font-medium">Alamat</span>
                </label>
                <textarea id="address" name="address" class="textarea textarea-bordered h-24" placeholder="Alamat lengkap..."></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2 mb-6">
                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text font-medium">Password <span class="text-error">*</span></span>
                    </label>
                    <input type="password" id="password" name="password" class="input input-bordered w-full" required minlength="8" />
                </div>
                
                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text font-medium">Konfirmasi Password <span class="text-error">*</span></span>
                    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="input input-bordered w-full" required minlength="8" />
                </div>
            </div>

            <div class="form-control w-full">
                <button type="submit" id="submitBtn" class="btn btn-primary w-full shadow-sm text-white">Daftar</button>
            </div>
            
            <div class="text-center mt-6 text-sm">
                Sudah punya akun? <a href="{{ url('/login') }}" class="link link-primary font-medium hover:text-primary-focus">Login di sini</a>
            </div>
        </form>
    </div>
</div>

@endsection

@stack('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('registerForm');
    const submitBtn = document.getElementById('submitBtn');

    if (registerForm) {
        registerForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const password = document.getElementById('password').value;
            const password_confirmation = document.getElementById('password_confirmation').value;
            
            if (password !== password_confirmation) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Password Tidak Sama',
                    text: 'Konfirmasi password tidak cocok dengan password yang diketik.'
                });
                return;
            }
            
            const payload = {
                name: document.getElementById('name').value,
                nik: document.getElementById('nik').value,
                email: document.getElementById('email').value,
                phone: document.getElementById('phone').value,
                address: document.getElementById('address').value,
                password: password,
                password_confirmation: password_confirmation
            };
            
            submitBtn.innerHTML = '<span class="loading loading-spinner loading-sm"></span> Loading...';
            submitBtn.disabled = true;

            try {
                const response = await fetch('/api/auth/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload)
                });
                
                const data = await response.json();
                
                if (response.ok && data.success) {
                    localStorage.setItem('auth_token', data.data.token);
                    localStorage.setItem('user_data', JSON.stringify(data.data.user));
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Registrasi Berhasil!',
                        text: 'Akun Anda telah berhasil dibuat.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = '/user/dashboard';
                    });
                } else {
                    let errorMessage = data.message || 'Registrasi gagal.';
                    
                    if (data.errors) {
                        errorMessage = Object.values(data.errors).map(err => err.join('<br>')).join('<br><br>');
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Registrasi Gagal',
                        html: errorMessage
                    });
                    
                    submitBtn.innerHTML = 'Daftar';
                    submitBtn.disabled = false;
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    text: 'Tidak dapat terhubung ke server.'
                });
                submitBtn.innerHTML = 'Daftar';
                submitBtn.disabled = false;
            }
        });
    }
});
</script>
