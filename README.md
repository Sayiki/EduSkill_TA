# 🎓 EduSkill Backend API

<div align="center">

### 🚀 RESTful API untuk Aplikasi Web EduSkill
*Layanan backend untuk Sistem Informasi Lembaga Pendidikan Non-Formal Bina Essa*

[![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![JWT](https://img.shields.io/badge/JWT-Auth-000000?style=for-the-badge&logo=jsonwebtokens&logoColor=white)](https://jwt.io)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)

[🌟 Fitur Utama](#-fitur-utama) • [🚀 Mulai Cepat](#-instalasi-cepat) • [📚 Dokumentasi API](#-dokumentasi-api) • [🤝 Kontribusi](#-kontribusi)

---

*Dikembangkan sebagai Tugas Akhir untuk Program Studi S1 Rekayasa Perangkat Lunak  
Fakultas Informatika, Universitas Telkom*

**Penulis:** [Arzaq Ajradika](https://github.com/Sayiki) (1302210096)

</div>

## 🌟 Fitur Utama

<table>
<tr>
<td width="50%">

### 👥 Manajemen Pengguna
- **Autentikasi Multi-Peran** dengan JWT
- **Kontrol Akses Berbasis Peran** (Admin, Ketua, Peserta)
- Sistem registrasi & login yang aman

### 🎯 Pengelolaan Pelatihan
- **Operasi CRUD Lengkap** untuk program pelatihan
- **Manajemen Mentor & Kategori**
- **Kontrol Kuota & Kapasitas**

</td>
<td width="50%">

### 📋 Sistem Pendaftaran
- **Proses Pendaftaran yang Mudah**
- **Update Status Real-time**
- **Notifikasi Otomatis**

### 📊 Manajemen Konten
- **Update Konten Dinamis**
- **Berita & Pengumuman**
- **Manajemen Galeri & Banner**
- **Pelaporan Komprehensif**

</td>
</tr>
</table>

## 🛠️ Teknologi yang Digunakan

| Kategori | Teknologi |
|----------|-----------|
| **Framework** | ![Laravel](https://img.shields.io/badge/Laravel-10-red?logo=laravel) |
| **Bahasa** | ![PHP](https://img.shields.io/badge/PHP-8.1%2B-blue?logo=php) |
| **Database** | ![MySQL](https://img.shields.io/badge/MySQL-8.0-orange?logo=mysql) |
| **Autentikasi** | ![JWT](https://img.shields.io/badge/JWT-Auth-black?logo=jsonwebtokens) |
| **Pengujian API** | ![Postman](https://img.shields.io/badge/Postman-Testing-orange?logo=postman) |

## 🚀 Instalasi Cepat

### 📋 Prasyarat

Pastikan Anda memiliki software berikut:
- ![PHP](https://img.shields.io/badge/PHP-≥8.1-blue?style=flat-square&logo=php) PHP 12 atau lebih tinggi
- ![Composer](https://img.shields.io/badge/Composer-Terbaru-brown?style=flat-square&logo=composer) Composer
- ![MySQL](https://img.shields.io/badge/MySQL-8.0-blue?style=flat-square&logo=mysql) MySQL Server
- ![Git](https://img.shields.io/badge/Git-Terbaru-red?style=flat-square&logo=git) Git

### ⚡ Langkah-langkah Instalasi

```bash
# 1️⃣ Clone repositori
git clone https://github.com/Sayiki/EduSkill_TA.git
cd EduSkillBE

# 2️⃣ Install dependensi PHP
composer install

# 3️⃣ Setup file environment
cp .env.example .env

# 4️⃣ Generate kunci aplikasi
php artisan key:generate

# 5️⃣ Konfigurasi database di file .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=eduskill
# DB_USERNAME=root
# DB_PASSWORD=

# 6️⃣ Jalankan migrasi dan seed data
php artisan migrate --seed

# 7️⃣ Jalankan server development
php artisan serve
```

🎉 **Selesai!** API Anda sekarang berjalan di `http://127.0.0.1:8000`

## 📚 Dokumentasi API

### 🔐 Endpoint Autentikasi

| Metode | Endpoint | Deskripsi | Akses |
|--------|----------|-----------|-------|
| `POST` | `/api/login` | Autentikasi pengguna | 🌐 Publik |
| `POST` | `/api/register` | Buat akun peserta | 🌐 Publik |

### 🎓 Endpoint Pelatihan

| Metode | Endpoint | Deskripsi | Akses |
|--------|----------|-----------|-------|
| `GET` | `/api/pelatihan` | Ambil semua program pelatihan | 🌐 Publik |
| `POST` | `/api/daftar-pelatihan/{id}` | Daftar pelatihan | 🔒 Perlu Auth sebagai peserta |

> 📖 **Dokumentasi API Lengkap** tersedia dalam dokumen Tugas Akhir dengan contoh request/response yang detail.


## 🚦 Status Proyek

| Status | Deskripsi |
|--------|-----------|
| ✅ **Selesai** | Fase pengembangan telah selesai |
| ✅ **Teruji** | Semua fitur telah diuji dengan Postman |
| ✅ **Terdokumentasi** | Dokumentasi lengkap tersedia |
| 🎓 **Akademik** | Diserahkan sebagai Tugas Akhir |

