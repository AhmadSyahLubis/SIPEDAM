<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Kategori Pengaduan (Laporan)
            ['name' => 'Fasilitas Umum & Infrastruktur', 'type' => 'laporan', 'description' => 'Kerusakan jalan, jembatan, fasilitas taman terjengkal, dll.'],
            ['name' => 'Pelayanan Publik / ASN', 'type' => 'laporan', 'description' => 'Pelanggaran disiplin ASN, pungli, pelayanan buruk instansi.'],
            ['name' => 'Kebersihan & Lingkungan Hidup', 'type' => 'laporan', 'description' => 'Masalah sampah menumpuk, pencemaran lingkungan.'],
            ['name' => 'Ketertiban & Keamanan', 'type' => 'laporan', 'description' => 'Gangguan ketertiban umum, balap liar, keramaian ilegal.'],
            ['name' => 'Kesehatan Masyarakat', 'type' => 'laporan', 'description' => 'Pelayanan puskesmas/RSUD, gizi buruk, wabah penyakit.'],
            
            // Kategori Layanan
            ['name' => 'Pembuatan KTP / KK', 'type' => 'layanan', 'description' => 'Permohonan pembuatan administrasi kependudukan.'],
            ['name' => 'Layanan Kesehatan Keluarga', 'type' => 'layanan', 'description' => 'Pendaftaran BPJS kesehatan, vaksinasi, dll.'],
            ['name' => 'Perizinan Usaha (UMKM)', 'type' => 'layanan', 'description' => 'Permohonan izin usaha mikro kecil.'],
            ['name' => 'Permohonan Data / Informasi', 'type' => 'layanan', 'description' => 'Permohonan transparansi data pemerintahan.'],
            ['name' => 'Bantuan Sosial', 'type' => 'layanan', 'description' => 'Pendaftaran dan pengusulan penerima dana bansos.'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['name' => $category['name']], $category);
        }
    }
}
