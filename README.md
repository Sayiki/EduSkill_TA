<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

Backend RESTful API untuk Aplikasi Web EduSkill
Ini adalah repositori untuk layanan backend dari aplikasi EduSkill, sebuah sistem informasi untuk Lembaga Pendidikan Non-Formal Bina Essa. Proyek ini dikembangkan sebagai bagian dari Tugas Akhir untuk memenuhi syarat kelulusan Program Studi S1 Rekayasa Perangkat Lunak, Fakultas Informatika, Universitas Telkom.

Penulis: Arzaq Ajradika (1302210096)

Fitur Utama
Manajemen Pengguna & Peran: Sistem autentikasi dan otorisasi berbasis peran menggunakan JWT untuk tiga jenis pengguna: Admin, Ketua, dan Peserta.

Pengelolaan Pelatihan: Operasi CRUD (Create, Read, Update, Delete) untuk data pelatihan, termasuk informasi mentor, kategori, dan dll.

Pendaftaran Peserta: Alur pendaftaran bagi peserta untuk mengikuti pelatihan yang tersedia.

Sistem Notifikasi: Mengirimkan notifikasi kepada pengguna terkait status pendaftaran atau pengumuman penting.

Manajemen Konten: Pengelolaan konten dinamis seperti berita, banner, galeri, dan profil lembaga.

Pelaporan: Fitur bagi admin untuk membuat laporan yang dapat diakses oleh ketua lembaga.

Panduan Instalasi
Berikut adalah langkah-langkah untuk menjalankan proyek ini di lingkungan lokal.

Prasyarat
PHP >= 12

Composer

Server Database (MySQL)

Git

Langkah-langkah Instalasi
Clone repositori ini:

git clone https://github.com/Sayiki/EduSkill_TA
cd EduSkillBE

Install dependensi PHP menggunakan Composer:

composer install

Salin file .env.example menjadi .env:

cp .env.example .env

Buat kunci aplikasi (APP_KEY):

php artisan key:generate

Konfigurasi koneksi database di dalam file .env:
Sesuaikan variabel berikut dengan konfigurasi database lokal Anda.

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=eduskill
DB_USERNAME=root
DB_PASSWORD=

Jalankan migrasi untuk membuat tabel dan isi data awal (seeding):

php artisan migrate --seed

Jalankan server pengembangan lokal:

php artisan serve

Aplikasi sekarang akan berjalan di http://127.0.0.1:8000.

Dokumentasi API
Seluruh endpoint API telah diuji menggunakan Postman. Dokumentasi lengkap untuk setiap endpoint, termasuk path, metode HTTP, dan contoh request/response, tersedia di dalam dokumen Tugas Akhir.

Contoh Endpoint
POST /api/login: Autentikasi pengguna (Admin, Ketua, Peserta).

POST /api/register: Membuat akun baru untuk Peserta.

GET /api/pelatihan: Mengambil semua data pelatihan (Akses Publik).

POST /api/daftar-pelatihan/{id}: Mendaftarkan peserta ke sebuah pelatihan (Membutuhkan Autentikasi).

Status Proyek
Proyek ini telah selesai dikembangkan dan diuji fungsionalitasnya sebagai bagian dari pemenuhan Tugas Akhir.
