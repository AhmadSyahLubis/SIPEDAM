@extends('layouts.guest')
@section('title', 'Login')

@section('content')
<div class="card w-96 bg-base-100 shadow-xl border border-base-200">
    <div class="card-body">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-primary">SIPEDAM</h2>
            <p class="text-sm text-base-content/70 mt-1">Sistem Pelayanan Digital Masyarakat</p>
        </div>

        <form id="loginForm" method="POST" action="javascript:void(0);">
            @csrf
            
            <div class="form-control w-full mb-4">
                <label class="label">
                    <span class="label-text font-medium">Email</span>
                </label>
                <input type="email" id="email" name="email" placeholder="email@contoh.com" class="input input-bordered w-full" required />
            </div>

            <div class="form-control w-full mb-6">
                <label class="label">
                    <span class="label-text font-medium">Password</span>
                </label>
                <input type="password" id="password" name="password" placeholder="••••••••" class="input input-bordered w-full" required />
            </div>

            <div class="form-control w-full mt-2">
                <button type="submit" id="submitBtn" class="btn btn-primary w-full shadow-sm text-white">Login</button>
            </div>
            
            <div class="text-center mt-6 text-sm">
                Belum punya akun? <a href="{{ url('/register') }}" class="link link-primary font-medium hover:text-primary-focus">Daftar sekarang</a>
            </div>
        </form>
    </div>
</div>

@endsection

@stack('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const submitBtn = document.getElementById('submitBtn');

    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            submitBtn.innerHTML = '<span class="loading loading-spinner loading-sm"></span> Loading...';
            submitBtn.disabled = true;

            try {
                const response = await fetch('/api/auth/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ email, password })
                });
                
                const data = await response.json();
                
                if (response.ok && data.success) {
                    // Save token
                    localStorage.setItem('auth_token', data.data.token);
                    localStorage.setItem('user_data', JSON.stringify(data.data.user));
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Login Berhasil!',
                        text: 'Selamat datang kembali.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = data.data.user.role === 'admin' ? '/admin/dashboard' : '/user/dashboard';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Login Gagal',
                        text: data.message || 'Email atau password salah.'
                    });
                    submitBtn.innerHTML = 'Login';
                    submitBtn.disabled = false;
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    text: 'Tidak dapat terhubung ke server.'
                });
                submitBtn.innerHTML = 'Login';
                submitBtn.disabled = false;
            }
        });
    }
});
</script>
