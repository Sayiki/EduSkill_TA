<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\{
    AdminController,
    DaftarPelatihanController,
    FeedbackController,
    InformasiGaleriController,
    InformasiKontakController,
    InformasiLembagaController,
    KetuaController,
    LaporanAdminController,
    NotifikasiController,
    PelatihanController,
    PendidikanController,
    PesertaController,
    ProfileYayasanController,
    ProfileLKPController,
    ProfileLPKController,
    StatusLamaranController,
    AuthController,
    BeritaController,
    BannerController,
    SlideshowController,
    MentorController,
    FileController,
    ProfilePesertaController, // Pastikan ini diimpor jika digunakan di rute peserta
};

// --- Rute Autentikasi Publik (Tidak memerlukan JWT) ---
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// --- Rute API Publik (Hanya GET untuk tampilan, tidak memerlukan JWT) ---
Route::apiResource('profile-lkp', ProfileLKPController::class)->only(['index', 'show']);
Route::apiResource('profile-lpk', ProfileLPKController::class)->only(['index', 'show']);
Route::apiResource('profile-yayasan', ProfileYayasanController::class)->only(['index', 'show']);

Route::apiResource('berita', BeritaController::class)->only(['index', 'show']);
Route::apiResource('banner', BannerController::class)->only(['index', 'show']);
Route::apiResource('feedback', FeedbackController::class)->only(['index', 'show']);
Route::apiResource('slideshow', SlideshowController::class)->only(['index', 'show']);
Route::apiResource('mentor', MentorController::class)->only(['index', 'show']);
Route::apiResource('informasi-galeri', InformasiGaleriController::class)->only(['index', 'show']);
Route::apiResource('informasi-kontak', InformasiKontakController::class)->only(['index']);
Route::apiResource('informasi-lembaga', InformasiLembagaController::class)->only(['index']);

Route::get('/pelatihan', [PelatihanController::class, 'index']);
Route::get('/pelatihan/{id}', [PelatihanController::class, 'show']);

Route::apiResource('pendidikan', PendidikanController::class)->only(['index', 'show']);

Route::get('/peserta', [PesertaController::class, 'index']);
Route::get('/peserta/{id}', [PesertaController::class, 'show']);


// --- Rute API Terproteksi (Memerlukan JWT Token) ---
Route::middleware(['jwt.auth'])->group(function(){
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']); // Rute untuk mendapatkan detail user yang sedang login

    // --- Rute Khusus Peran Peserta (Memerlukan JWT Token DAN peran 'peserta') ---
    Route::middleware(['peran:peserta'])->group(function () {
        Route::get('daftar-pelatihan/current-user', [DaftarPelatihanController::class, 'indexForCurrentUser']);      
        Route::post('/daftar-pelatihan', [DaftarPelatihanController::class, 'store']); 
        Route::put('/profile-peserta/{peserta}', [ProfilePesertaController::class, 'update']);
        Route::post('/feedback', [FeedbackController::class, 'store']);
        // Route::get('/peserta/{id}', [PesertaController::class, 'show']); // Sudah dihandle di apiResource publik di atas
        Route::put('/peserta/{id}', [PesertaController::class, 'update']);
        Route::get('/notifikasi-saya', [NotifikasiController::class, 'indexForCurrentUser']);
        Route::get('/notifikasi-saya/{id}', [NotifikasiController::class, 'showForCurrentUser']);
        Route::put('/notifikasi-saya/{id}', [NotifikasiController::class, 'updateStatusForCurrentUser']);
        Route::delete('/notifikasi-saya/{id}', [NotifikasiController::class, 'destroyForCurrentUser']);
    });

    // --- Rute Khusus Peran Admin (Memerlukan JWT Token DAN peran 'admin') ---
    Route::middleware(['peran:admin'])->group(function(){
        Route::apiResource('admin', AdminController::class);
        Route::apiResource('ketua', KetuaController::class);

        Route::apiResource('profile-yayasan', ProfileYayasanController::class)->except(['index', 'show']);
        Route::apiResource('profile-lkp', ProfileLKPController::class)->except(['index', 'show']);
        Route::apiResource('profile-lpk', ProfileLPKController::class)->except(['index', 'show']);

        Route::apiResource('berita', BeritaController::class)->except(['index', 'show']);
        Route::apiResource('banner', BannerController::class)->except(['index', 'show']);
        Route::apiResource('slideshow', SlideshowController::class)->except(['index', 'show']);
        Route::apiResource('mentor', MentorController::class)->except(['index', 'show']);
        Route::apiResource('informasi-galeri', InformasiGaleriController::class)->except(['index', 'show']);
        Route::apiResource('informasi-kontak', InformasiKontakController::class)->except(['index', 'show']);
        Route::apiResource('informasi-lembaga', InformasiLembagaController::class)->except(['index', 'show']);
        // Pelatihan
        Route::post('/pelatihan', [PelatihanController::class, 'store']);
        Route::put('/pelatihan/{id}', [PelatihanController::class, 'update']);
        Route::delete('/pelatihan/{id}', [PelatihanController::class, 'destroy']);

        Route::apiResource('pendidikan', PendidikanController::class)->except(['index', 'show']);
        Route::apiResource('feedback', FeedbackController::class)->except(['index', 'show', 'store']);

        Route::get('/daftar-pelatihan', [DaftarPelatihanController::class, 'index']);
        Route::put('/daftar-pelatihan/{id}', [DaftarPelatihanController::class, 'update']);
        Route::get('/daftar-pelatihan/{id}', [DaftarPelatihanController::class, 'show']);
        Route::delete('/daftar-pelatihan/{id}', [DaftarPelatihanController::class, 'destroy']);
        
        Route::post('/peserta/{id}', [PesertaController::class, 'store']);
        Route::put('/peserta/{id}', [PesertaController::class, 'update']);
        Route::delete('/peserta/{id}', [PesertaController::class, 'destroy']);

        Route::apiResource('laporan-admin', LaporanAdminController::class)->only(['index', 'show', 'destroy']);
        Route::post('/my-laporan-admin', [LaporanAdminController::class, 'storeOrUpdateMyLaporan']);
        Route::get('/my-laporan-admin', [LaporanAdminController::class, 'showMyLaporan']);
        Route::post('/notifikasi-pengumuman', [NotifikasiController::class, 'sendAnnouncementToAllPeserta']);
        Route::apiResource('notifikasi', NotifikasiController::class)->except(['index', 'show', 'store']);

        Route::post('/informasi-lembaga', [InformasiLembagaController::class, 'store']);
        Route::put('/informasi-lembaga/{id}', [InformasiLembagaController::class, 'update']);

        // Mentor
        Route::post('/mentor', [MentorController::class, 'store']);
        Route::put('/mentor/{id}', [MentorController::class, 'update']); 
        Route::delete('/mentor/{id}', [MentorController::class, 'destroy']);

        Route::get('/documents/{filename}', [FileController::class, 'downloadDocument']);
    });

    // --- Rute Khusus Peran Ketua (Memerlukan JWT Token DAN peran 'ketua') ---
    Route::middleware(['peran:ketua'])->group(function () {
        Route::get('/laporan-admin', [LaporanAdminController::class, 'index']);
        Route::get('/laporan-admin/{id}', [LaporanAdminController::class, 'showLaporanByIdForKetua']);
        Route::delete('/laporan-admin/{id}', [LaporanAdminController::class, 'destroy']);
    });
});