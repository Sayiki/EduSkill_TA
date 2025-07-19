<div align="center">
<br />
<h1>Backend RESTful API untuk Aplikasi Web EduSkill</h1>
<p>
Layanan backend untuk sistem informasi Lembaga Pendidikan Non-Formal Bina Essa.
</p>
</div>

<p align="center">
<img src="https://img.shields.io/badge/PHP-8.1%2B-777BB4?style=for-the-badge&logo=php" alt="PHP">
<img src="https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel" alt="Laravel">
<img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql" alt="MySQL">
<img src="https://img.shields.io/badge/License-MIT-yellow.svg?style=for-the-badge" alt="License: MIT">
</p>

📝 Tentang Proyek
Ini adalah repositori untuk layanan backend dari aplikasi EduSkill, sebuah sistem informasi untuk Lembaga Pendidikan Non-Formal Bina Essa. Proyek ini dikembangkan sebagai bagian dari Tugas Akhir untuk memenuhi syarat kelulusan Program Studi S1 Rekayasa Perangkat Lunak, Fakultas Informatika, Universitas Telkom.

Penulis: Arzaq Ajradika (1302210096)

📋 Daftar Isi
Fitur Utama

Teknologi yang Digunakan

Panduan Instalasi

Dokumentasi API

Status Proyek

Kontribusi

Lisensi

✨ Fitur Utama
👤 Manajemen Pengguna & Peran: Sistem autentikasi dan otorisasi berbasis peran menggunakan JWT untuk tiga jenis pengguna: Admin, Ketua, dan Peserta.

🎓 Pengelolaan Pelatihan: Operasi CRUD (Create, Read, Update, Delete) untuk data pelatihan, termasuk informasi mentor, kategori, dan kuota.

🚀 Pendaftaran Peserta: Alur pendaftaran bagi peserta untuk mengikuti pelatihan yang tersedia.

🔔 Sistem Notifikasi: Mengirimkan notifikasi kepada pengguna terkait status pendaftaran atau pengumuman penting.

🖼️ Manajemen Konten: Pengelolaan konten dinamis seperti berita, banner, galeri, dan profil lembaga.

📊 Pelaporan: Fitur bagi admin untuk membuat laporan yang dapat diakses oleh ketua lembaga.

🛠️ Teknologi yang Digunakan
Framework: Laravel 10

Bahasa: PHP 8.1+

Database: MySQL

Keamanan: tymon/jwt-auth untuk autentikasi API

Pengujian API: Postman

🚀 Panduan Instalasi
Berikut adalah langkah-langkah untuk menjalankan proyek ini di lingkungan lokal.

Prasyarat
PHP >= 8.1

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

📚 Dokumentasi API
Seluruh endpoint API telah diuji menggunakan Postman. Dokumentasi lengkap untuk setiap endpoint, termasuk path, metode HTTP, dan contoh request/response, tersedia di dalam dokumen Tugas Akhir.

Contoh Endpoint
POST /api/login: Autentikasi pengguna (Admin, Ketua, Peserta).

POST /api/register: Membuat akun baru untuk Peserta.

GET /api/pelatihan: Mengambil semua data pelatihan (Akses Publik).

POST /api/daftar-pelatihan/{id}: Mendaftarkan peserta ke sebuah pelatihan (Membutuhkan Autentikasi).

📈 Status Proyek
Proyek ini telah selesai dikembangkan dan diuji fungsionalitasnya sebagai bagian dari pemenuhan Tugas Akhir.

🤝 Kontribusi
Kontribusi dalam bentuk pull request atau issue sangat diterima. Jika Anda ingin berkontribusi, silakan fork repositori ini dan buat pull request.

Fork repositori ini.

Buat branch fitur baru (git checkout -b fitur/FiturBaru).

Commit perubahan Anda (git commit -m 'Menambahkan FiturBaru').

Push ke branch tersebut (git push origin fitur/FiturBaru).

Buka sebuah Pull Request.

📄 Lisensi
Proyek ini dilisensikan di bawah Lisensi MIT. Lihat file LICENSE untuk detail lebih lanjut.
