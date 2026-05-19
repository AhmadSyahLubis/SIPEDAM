# 📋 PROJECT BRIEF — Sistem Pelayanan Digital Masyarakat (SIPEDAM)

> **Dinas Komunikasi dan Informatika (Diskominfo)**
> Aplikasi Pelayanan Publik Digital Pemerintah Daerah

---

## 1. Ringkasan Proyek

**Nama Aplikasi:** SIPEDAM (Sistem Pelayanan Digital Masyarakat)
**Jenis:** Aplikasi Web — Pelayanan Publik Online
**Tujuan:** Menyediakan platform digital bagi masyarakat untuk mengajukan laporan pengaduan dan permohonan pelayanan publik secara online, serta memudahkan pemerintah daerah dalam mengelola dan merespon setiap pengajuan secara efisien dan transparan.

---

## 2. Tech Stack

| Kategori         | Teknologi                          |
| ---------------- | ---------------------------------- |
| **Backend**      | Laravel 12 (PHP 8.2+)             |
| **Frontend**     | Blade Templates + Tailwind CSS v3  |
| **UI Components**| DaisyUI v4                         |
| **Alert/Dialog** | SweetAlert2                        |
| **Auth**         | JWT (tymon/jwt-auth)               |
| **Database**     | MySQL 8.0                          |
| **API Format**   | RESTful JSON API                   |
| **Icons**        | Heroicons / Font Awesome 6        |
| **HTTP Client**  | Axios                              |

---

## 3. Roles & Hak Akses

Hanya terdapat **2 role** dalam sistem:

### 3.1 User (Masyarakat)
- Registrasi akun baru
- Login & Logout
- Mengajukan laporan/pengaduan
- Mengajukan permohonan pelayanan publik
- Melihat status & riwayat pengajuan milik sendiri
- Menerima notifikasi perubahan status
- Mengupload dokumen pendukung
- Mengedit profil pribadi

### 3.2 Admin (Petugas Pemerintah)
- Login & Logout (tanpa registrasi — dibuat via seeder/manual)
- Dashboard statistik (total laporan, status, dll)
- Melihat & mengelola semua laporan masuk
- Melihat & mengelola semua permohonan pelayanan
- Mengubah status pengajuan (Diterima → Diproses → Selesai / Ditolak)
- Memberikan tanggapan/catatan pada setiap pengajuan
- Mengelola data kategori layanan
- Mengelola data pengguna

---

## 4. Fitur Utama

### 4.1 Autentikasi (JWT-Based)
- **Register** — Registrasi user baru (masyarakat)
- **Login** — Autentikasi dengan email & password, return JWT token
- **Logout** — Invalidasi JWT token
- **Password Hashing** — Menggunakan `bcrypt` bawaan Laravel
- **Token Expired Handling** — Middleware untuk mendeteksi token kadaluarsa, auto-refresh atau redirect ke login
- **Token Refresh** — Endpoint untuk memperbarui JWT token sebelum expired

### 4.2 Modul Laporan/Pengaduan (User)
- Form pengajuan laporan (judul, kategori, deskripsi, lokasi, lampiran foto)
- Daftar riwayat laporan dengan filter & search
- Detail laporan beserta timeline status
- Pembatalan laporan (jika masih berstatus "Menunggu")

### 4.3 Modul Permohonan Pelayanan (User)
- Daftar layanan publik yang tersedia
- Form permohonan (pilih jenis layanan, upload dokumen, isi data)
- Riwayat permohonan dengan tracking status
- Download bukti pengajuan (PDF)

### 4.4 Dashboard Admin
- Statistik ringkasan (total laporan, permohonan, status breakdown)
- Chart/Grafik interaktif
- Tabel data laporan & permohonan (sortable, filterable, paginated)
- Aksi: Terima, Proses, Selesaikan, atau Tolak pengajuan
- Form tanggapan/catatan admin

### 4.5 Profil & Akun
- Edit profil (nama, email, telepon, alamat, foto profil)
- Ganti password

---

## 5. Endpoint API — Autentikasi

### 5.1 Register
```
POST /api/auth/register
```
**Request Body:**
```json
{
    "name": "string|required|min:3|max:255",
    "email": "string|required|email|unique:users",
    "password": "string|required|min:8|confirmed",
    "password_confirmation": "string|required",
    "phone": "string|nullable|max:15",
    "address": "string|nullable|max:500",
    "nik": "string|required|digits:16|unique:users"
}
```
**Response (201):**
```json
{
    "success": true,
    "message": "Registrasi berhasil",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "nik": "3201234567890001",
            "role": "user"
        },
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOi...",
        "token_type": "bearer",
        "expires_in": 3600
    }
}
```

### 5.2 Login
```
POST /api/auth/login
```
**Request Body:**
```json
{
    "email": "string|required|email",
    "password": "string|required"
}
```
**Response (200):**
```json
{
    "success": true,
    "message": "Login berhasil",
    "data": {
        "user": {
            "id": 1,
            "name": "ahmad syah lubis",
            "email": "ahmadsyah@example.com",
            "role": "user"
        },
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOi...",
        "token_type": "bearer",
        "expires_in": 3600
    }
}
```
**Response (401):**
```json
{
    "success": false,
    "message": "Email atau password salah"
}
```

### 5.3 Logout
```
POST /api/auth/logout
```
**Headers:**
```
Authorization: Bearer {token}
```
**Response (200):**
```json
{
    "success": true,
    "message": "Logout berhasil"
}
```

### 5.4 Refresh Token
```
POST /api/auth/refresh
```
**Headers:**
```
Authorization: Bearer {token}
```
**Response (200):**
```json
{
    "success": true,
    "message": "Token berhasil diperbarui",
    "data": {
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOi...",
        "token_type": "bearer",
        "expires_in": 3600
    }
}
```

### 5.5 Get Authenticated User
```
GET /api/auth/me
```
**Headers:**
```
Authorization: Bearer {token}
```
**Response (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "ahmad syah lubis",
        "email": "ahmadsyah@example.com",
        "nik": "3201234567890001",
        "phone": "081234567890",
        "address": "Jl. Merdeka No. 1",
        "role": "user",
        "created_at": "2026-05-19T09:00:00.000000Z"
    }
}
```

### 5.6 Token Expired Handling
**Response (401) — Token Expired:**
```json
{
    "success": false,
    "message": "Token telah kadaluarsa, silakan login kembali",
    "error": "token_expired"
}
```
**Response (401) — Token Invalid:**
```json
{
    "success": false,
    "message": "Token tidak valid",
    "error": "token_invalid"
}
```
**Response (401) — Token Not Found:**
```json
{
    "success": false,
    "message": "Token tidak ditemukan, silakan login",
    "error": "token_not_found"
}
```

---

## 6. Database Schema (Tabel Utama)

### 6.1 `users`
| Kolom        | Tipe           | Keterangan                    |
| ------------ | -------------- | ----------------------------- |
| id           | bigint (PK)    | Auto increment                |
| name         | varchar(255)   | Nama lengkap                  |
| email        | varchar(255)   | Unique, untuk login           |
| password     | varchar(255)   | Bcrypt hashed                 |
| nik          | varchar(16)    | Nomor Induk Kependudukan      |
| phone        | varchar(15)    | Nomor telepon (nullable)      |
| address      | text           | Alamat (nullable)             |
| avatar       | varchar(255)   | Path foto profil (nullable)   |
| role         | enum           | 'admin', 'user' (default: user)|
| created_at   | timestamp      |                               |
| updated_at   | timestamp      |                               |

### 6.2 `categories`
| Kolom        | Tipe           | Keterangan                    |
| ------------ | -------------- | ----------------------------- |
| id           | bigint (PK)    | Auto increment                |
| name         | varchar(255)   | Nama kategori                 |
| type         | enum           | 'laporan', 'layanan'          |
| description  | text           | Deskripsi kategori (nullable) |
| icon         | varchar(255)   | Icon class (nullable)         |
| is_active    | boolean        | Default: true                 |
| created_at   | timestamp      |                               |
| updated_at   | timestamp      |                               |

### 6.3 `reports` (Laporan/Pengaduan)
| Kolom        | Tipe           | Keterangan                    |
| ------------ | -------------- | ----------------------------- |
| id           | bigint (PK)    | Auto increment                |
| user_id      | bigint (FK)    | Relasi ke users               |
| category_id  | bigint (FK)    | Relasi ke categories          |
| ticket_number| varchar(20)    | Nomor tiket unik (auto-gen)   |
| title        | varchar(255)   | Judul laporan                 |
| description  | text           | Isi laporan                   |
| location     | varchar(255)   | Lokasi kejadian (nullable)    |
| status       | enum           | 'menunggu','diproses','selesai','ditolak' |
| admin_notes  | text           | Catatan admin (nullable)      |
| created_at   | timestamp      |                               |
| updated_at   | timestamp      |                               |

### 6.4 `services` (Permohonan Layanan)
| Kolom        | Tipe           | Keterangan                    |
| ------------ | -------------- | ----------------------------- |
| id           | bigint (PK)    | Auto increment                |
| user_id      | bigint (FK)    | Relasi ke users               |
| category_id  | bigint (FK)    | Relasi ke categories          |
| ticket_number| varchar(20)    | Nomor tiket unik (auto-gen)   |
| description  | text           | Detail permohonan             |
| status       | enum           | 'menunggu','diproses','selesai','ditolak' |
| admin_notes  | text           | Catatan admin (nullable)      |
| created_at   | timestamp      |                               |
| updated_at   | timestamp      |                               |

### 6.5 `attachments`
| Kolom            | Tipe           | Keterangan                    |
| ---------------- | -------------- | ----------------------------- |
| id               | bigint (PK)    | Auto increment                |
| attachable_id    | bigint         | Polymorphic ID                |
| attachable_type  | varchar(255)   | Polymorphic type              |
| file_path        | varchar(255)   | Path file                     |
| file_name        | varchar(255)   | Nama asli file                |
| file_size        | bigint         | Ukuran file (bytes)           |
| mime_type        | varchar(100)   | Tipe MIME                     |
| created_at       | timestamp      |                               |
| updated_at       | timestamp      |                               |

### 6.6 `status_histories`
| Kolom             | Tipe           | Keterangan                    |
| ----------------- | -------------- | ----------------------------- |
| id                | bigint (PK)    | Auto increment                |
| trackable_id      | bigint         | Polymorphic ID                |
| trackable_type    | varchar(255)   | Polymorphic type              |
| status            | varchar(50)    | Status baru                   |
| notes             | text           | Catatan perubahan (nullable)  |
| changed_by        | bigint (FK)    | User yang mengubah            |
| created_at        | timestamp      |                               |

---

## 7. Struktur Folder Laravel

```
diskominfo/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   ├── RegisterController.php
│   │   │   │   ├── LoginController.php
│   │   │   │   └── LogoutController.php
│   │   │   ├── Admin/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── ReportController.php
│   │   │   │   ├── ServiceController.php
│   │   │   │   ├── CategoryController.php
│   │   │   │   └── UserController.php
│   │   │   └── User/
│   │   │       ├── DashboardController.php
│   │   │       ├── ReportController.php
│   │   │       ├── ServiceController.php
│   │   │       └── ProfileController.php
│   │   ├── Middleware/
│   │   │   ├── JwtMiddleware.php
│   │   │   ├── RoleMiddleware.php
│   │   │   └── TokenRefreshMiddleware.php
│   │   └── Requests/
│   │       ├── Auth/
│   │       │   ├── RegisterRequest.php
│   │       │   └── LoginRequest.php
│   │       ├── ReportRequest.php
│   │       └── ServiceRequest.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Category.php
│   │   ├── Report.php
│   │   ├── Service.php
│   │   ├── Attachment.php
│   │   └── StatusHistory.php
│   └── Services/
│       ├── AuthService.php
│       ├── ReportService.php
│       └── ServiceRequestService.php
├── database/
│   ├── migrations/
│   │   ├── xxxx_create_users_table.php
│   │   ├── xxxx_create_categories_table.php
│   │   ├── xxxx_create_reports_table.php
│   │   ├── xxxx_create_services_table.php
│   │   ├── xxxx_create_attachments_table.php
│   │   └── xxxx_create_status_histories_table.php
│   └── seeders/
│       ├── AdminSeeder.php
│       └── CategorySeeder.php
├── resources/
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php
│       │   ├── admin.blade.php
│       │   └── guest.blade.php
│       ├── auth/
│       │   ├── login.blade.php
│       │   └── register.blade.php
│       ├── admin/
│       │   ├── dashboard.blade.php
│       │   ├── reports/
│       │   ├── services/
│       │   ├── categories/
│       │   └── users/
│       └── user/
│           ├── dashboard.blade.php
│           ├── reports/
│           ├── services/
│           └── profile/
├── routes/
│   ├── api.php
│   └── web.php
└── config/
    └── jwt.php
```

---

## 8. Konfigurasi JWT

### 8.1 Package
```bash
composer require tymon/jwt-auth
```

### 8.2 Konfigurasi Utama (`config/jwt.php`)
- **TTL (Time-To-Live):** `60` menit (1 jam)
- **Refresh TTL:** `20160` menit (14 hari)
- **Algo:** `HS256`
- **Blacklist Enabled:** `true` (untuk logout/invalidasi token)

### 8.3 Middleware Flow
```
Request masuk
  → [JwtMiddleware] Cek apakah token ada
    → Token tidak ada → Return 401 (token_not_found)
    → Token ada → Validasi token
      → Token expired → Return 401 (token_expired)
      → Token invalid → Return 401 (token_invalid)
      → Token valid → Lanjutkan ke controller
        → [RoleMiddleware] Cek role user
          → Role tidak sesuai → Return 403 (forbidden)
          → Role sesuai → Proses request
```

---

## 9. Security Requirements

- ✅ Password di-hash menggunakan `bcrypt` (default Laravel)
- ✅ JWT token untuk autentikasi API
- ✅ Token blacklisting saat logout
- ✅ Token expired handling dengan response yang jelas
- ✅ Rate limiting pada endpoint auth (throttle)
- ✅ Validasi input ketat di setiap endpoint
- ✅ CSRF protection untuk form Blade
- ✅ File upload validation (tipe & ukuran file)
- ✅ SQL injection prevention via Eloquent ORM
- ✅ XSS prevention via Blade auto-escaping

---

## 10. UI/UX Guidelines

### 10.1 Design System
- **Framework CSS:** Tailwind CSS v3
- **Component Library:** DaisyUI v4
- **Theme:** Light & Dark mode support
- **Color Palette:**
  - Primary: Biru Pemerintah (`#1e40af` → DaisyUI primary)
  - Secondary: Hijau Pelayanan (`#059669`)
  - Accent: Kuning Emas (`#d97706`)
  - Neutral: Abu-abu modern

### 10.2 Alert & Notification
- **SweetAlert2** untuk:
  - Konfirmasi aksi (hapus, ubah status, dll)
  - Success/Error notification
  - Form dialog (jika diperlukan)
  - Session expired warning

### 10.3 Responsive
- Mobile-first design
- Sidebar collapsible di mobile
- Tabel menggunakan horizontal scroll atau card view di mobile

---

## 11. Prioritas Pengembangan

### Phase 1 — Foundation ⭐
1. Setup project Laravel 12 + Tailwind CSS + DaisyUI + SweetAlert2
2. Database migration & seeder (admin + kategori default)
3. Implementasi autentikasi JWT (Register, Login, Logout, Refresh, Token Handling)
4. Middleware JWT + Role
5. Halaman Login & Register (Blade + Tailwind + DaisyUI)

### Phase 2 — Core Features
6. Layout Admin & User (sidebar, topbar, footer)
7. Dashboard Admin (statistik + chart)
8. Dashboard User
9. CRUD Laporan/Pengaduan (User submit, Admin kelola)
10. CRUD Permohonan Layanan (User submit, Admin kelola)

### Phase 3 — Enhancement
11. Upload & manajemen attachment
12. Status history & timeline tracking
13. Profil user (edit + ganti password)
14. Manajemen kategori (Admin)
15. Manajemen user (Admin)

### Phase 4 — Polish
16. Notifikasi perubahan status
17. Export/Download bukti pengajuan (PDF)
18. Dark mode toggle
19. Performance optimization
20. Final testing & deployment

---

## 12. Catatan Tambahan

- Semua response API menggunakan format JSON yang konsisten (`success`, `message`, `data`)
- Semua pesan error & sukses dalam **Bahasa Indonesia**
- Gunakan **Form Request** untuk validasi
- Gunakan **Service Layer** untuk business logic
- Gunakan **Polymorphic Relationship** untuk attachments & status histories
- Seeder menyediakan akun admin default:
  - Email: `admin@diskominfo.go.id`
  - Password: `admin123`

---

> **Dokumen ini menjadi acuan utama dalam pengembangan Sistem Pelayanan Digital Masyarakat (SIPEDAM).**
> Setiap perubahan scope harus didiskusikan dan diperbarui pada dokumen ini.
