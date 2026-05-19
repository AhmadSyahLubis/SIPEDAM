if (window.axios) {
    window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

    const token = localStorage.getItem('auth_token');
    if (token) {
        window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + token;
    }

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
                const token = localStorage.getItem('auth_token');

                const response = await fetch('/api/auth/logout', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });

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
                localStorage.removeItem('auth_token');
                localStorage.removeItem('user_data');
                window.location.href = '/login';
            }
        }
    });
}

window.handleLogout = handleLogout;
