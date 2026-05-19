// Axios global setup
if (window.axios) {
    window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

    // Add Bearer token to every request
    const token = localStorage.getItem('auth_token');
    if (token) {
        window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + token;
    }

    // Intercept 401 Unauthorized responses to handle token expiration
    window.axios.interceptors.response.use(response => response, error => {
        if (error.response && error.response.status === 401) {
            const errorType = error.response.data.error;

            if (['token_expired', 'token_invalid', 'token_not_found'].includes(errorType)) {
                localStorage.removeItem('auth_token');
                localStorage.removeItem('user_data');

                Swal.fire({
                    icon: 'warning',
                    title: 'Sesi Berakhir',
                    text: 'Sesi Anda telah berakhir. Silakan login kembali.',
                    confirmButtonText: 'Tutup'
                }).then(() => {
                    window.location.href = '/login';
                });
            }
        }
        return Promise.reject(error);
    });
}

function handleLogout() {
    Swal.fire({
        title: 'Konfirmasi Logout',
        text: 'Apakah Anda yakin ingin keluar?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Keluar',
        cancelButtonText: 'Batal'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                // We'll use fetch since axios setup might be scoped
                const token = localStorage.getItem('auth_token');

                const response = await fetch('/api/auth/logout', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });

                // Clear local storage regardless of API response (token might already be invalid)
                localStorage.removeItem('auth_token');
                localStorage.removeItem('user_data');

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Logout',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = '/login';
                });

            } catch (error) {
                console.error('Logout error:', error);
                // Still clear and redirect
                localStorage.removeItem('auth_token');
                localStorage.removeItem('user_data');
                window.location.href = '/login';
            }
        }
    });
}

window.handleLogout = handleLogout;
