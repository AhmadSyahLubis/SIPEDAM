# SIPEDAM — Sistem Pelayanan Digital Masyarakat

SIPEDAM adalah platform e-Government berbasis Laravel dan Tailwind/DaisyUI yang diproyeksikan untuk mengakomodasi kebutuhan Dinas Komunikasi dan Informatika dalam memberikan layanan aduan dan administratif untuk masyarakat secara real-time. Aplikasi ini dibangun dengan struktur Headless API untuk fleksibilitas masa depan dan Single Page Application (*SPA-like*) di sisi tampilan Blade.

## Fitur Utama

- **Otentikasi & Autorisasi Terpusat**: Login dengan JSON Web Token (JWT) yang tersimpan aman, serta pemisahan hak akses antara Administrator dan Masyarakat (User).
- **Modul Pengaduan Publik**: Masyarakat dapat mengajukan keluhan/laporan beserta lampiran bukti. Status laporan dapat dilacak melalui Timeline History.
- **Modul Pelayanan Administratif**: Permohonan layanan terstruktur yang dilengkapi dengan unggahan syarat dokumen format PDF/Gambar. Admin memberikan verifikasi melalui panel khusus.
- **Dashboard Analitik**: Cuplikan data (Total Laporan, Laporan Menunggu, Sedang Diproses, dan Selesai) yang di-render secara interaktif untuk Admin.
- **Manajemen Kategori & Pengguna (Master Data)**: Fitur admin untuk melakukan operasi pengelolaan (CRUD) kategori layanan, serta memantau data seluruh penduduk dalam sistem.

## Alur Kerja Singkat (Workflow)

1. **Pendaftaran Akun:** Warga/pengguna mendaftarkan diri secara mandiri melalui form Registrasi. Akun mereka secara otomatis mendapat *role* "user".
2. **Pengajuan:** User memilih menu Laporan atau Layanan, lalu mengisi detail dan melampirkan file dokumen bila diperlukan. Status tiket awal adalah **Menunggu**.
3. **Verifikasi Admin:** Admin log in dan membuka Manajemen Laporan/Layanan. Admin meninjau berkas, lalu memperbarui status menjadi **Diproses**, **Ditolak**, atau **Selesai** sambil memberikan catatan (Feedback).
4. **Notifikasi Timeline:** Setiap perubahan status langsung terekam pada fungsi Jejak Status (*Timeline*) yang dapat dipantau User sewaktu-waktu.

## Prasyarat Server (Requirement)
- PHP 8.2 atau lebih baru
- Composer 2.x
- Node.js & npm (v18+)
- MySQL
- Git

## Panduan Instalasi (Development)

Ikuti langkah-langkah di bawah ini untuk menjalankan aplikasi SIPEDAM di sistem lokal Anda:

1. **Clone repositori ini:**
   ```bash
   git clone https://github.com/AhmadSyahLubis/SIPEDAM.git
   cd SIPEDAM
   ```

2. **Instal seluruh *Dependency* Backend & Frontend:**
   ```bash
   composer install
   npm install
   ```

3. **Duplikat file konfigurasi *Environment*:**
   ```bash
   cp .env.example .env
   ```

4. **Koneksikan ke basis data:**
   Buka file `.env` di teks editor Anda dan sesuaikan koneksi database.
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=diskominfo
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   *Pastikan Anda telah membuat database dengan nama `diskominfo` di MySQL Anda.*

5. **Generate Kunci Aplikasi dan Rahasia JWT:**
   ```bash
   php artisan key:generate
   php artisan jwt:secret
   ```

6. **Migrasi Tabel Basis Data & Seed Data Awal:**
   Jalankan perintah ini untuk menciptakan tabel sekaligus memasukkan akun Admin bawaan dan kategori awal.
   ```bash
   php artisan migrate --seed
   ```

7. **Tautkan Folder Penyimpanan (*Storage*):**
   Ini penting supaya sistem bisa membaca file gambar/dokumen yang diunggah.
   ```bash
   php artisan storage:link
   ```

8. **Kompilasi Aset Frontend (Tailwind/DaisyUI/Vite):**
   ```bash
   npm run build
   ```

9. **Jalankan *Development Server* Laravel:**
   ```bash
   php artisan serve
   ```
   Aplikasi Anda kini sudah bisa diakses di `http://127.0.0.1:8000`

---

## Informasi Akun Bawaan (Default Seeder)

Untuk mulai mengetes panel kontrol administratif, Anda bisa login menggunakan kredensial standar berikut:

- **Email:** `admin@diskominfo.go.id`
- **Password:** `password`
