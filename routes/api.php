<?php

use Illuminate\Http\Request;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
    ProfilePesertaController,
};

Route::post('/login',  [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/email/verify-now', [AuthController::class, 'verifyNow']);
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])
    ->middleware(['signed'])->name('verification.verify');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::put('/change-password', [AuthController::class, 'changePassword']);


Route::apiResource('berita', BeritaController::class)->only(['index', 'show']);
Route::apiResource('banner', BannerController::class)->only(['index', 'show']);
Route::apiResource('slideshow', SlideshowController::class)->only(['index', 'show']);
Route::apiResource('mentor', MentorController::class)->only(['index', 'show']);
Route::apiResource('informasi-galeri', InformasiGaleriController::class)->only(['index', 'show']);
Route::apiResource('informasi-kontak', InformasiKontakController::class)->only(['index']);
Route::apiResource('informasi-lembaga', InformasiLembagaController::class)->only(['index']);

Route::get('/pelatihan', [PelatihanController::class, 'index']);
Route::get('/pelatihan/{id}', [PelatihanController::class, 'show']);

Route::apiResource('pendidikan', PendidikanController::class)->only(['index', 'show']);

Route::apiResource('profile-lkp', ProfileLKPController::class)->only(['index', 'show']);
Route::apiResource('profile-lpk', ProfileLPKController::class)->only(['index', 'show']);
Route::apiResource('profile-yayasan', ProfileYayasanController::class)->only(['index', 'show']);

Route::get('/peserta-alumni', [PesertaController::class, 'getPublicProfiles']);

Route::get('/kategori-pelatihan', [KategoriPelatihanController::class, 'index']);

// Rute untuk memberitahu pengguna bahwa mereka harus verifikasi email
Route::get('/email/verify', function () {
    return response()->json(['message' => 'Email belum diverifikasi. Silakan cek email Anda atau minta kirim ulang link verifikasi.'], 403);
})->middleware('auth:api')->name('verification.notice');


Route::middleware(['jwt.auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']); // Rute untuk mendapatkan detail user yang sedang login

    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('/documents/{filename}', [FileController::class, 'downloadDocument']);

    Route::post('/email/resend', [AuthController::class, 'resend'])
        ->middleware(['throttle:6,1'])->name('verification.send');
    
    Route::middleware(['peran:peserta'])->group(function () {
        Route::post('/daftar-pelatihan', [DaftarPelatihanController::class, 'store']);
        Route::get('/daftar-pelatihan/current-user', [DaftarPelatihanController::class, 'indexForCurrentUser']);
        Route::get('/profil-saya', [PesertaController::class, 'showMyProfile']);
        Route::put('/profil-saya', [PesertaController::class, 'updateMyProfile']);
        Route::post('/feedback/{id}', [FeedbackController::class, 'store']);
        Route::get('/notifikasi-saya', [NotifikasiController::class, 'indexForCurrentUser']);
        Route::get('/notifikasi-saya/{notifikasi_id}', [NotifikasiController::class, 'showForCurrentUser']);
        Route::put('/notifikasi-saya/{notifikasi_id}', [NotifikasiController::class, 'updateStatusForCurrentUser']);
        Route::delete('/notifikasi-saya/{notifikasi_id}', [NotifikasiController::class, 'destroyForCurrentUser']);
        Route::post('/email/resend', [AuthController::class, 'resend'])->name('verification.send');
            
    });


    Route::middleware(['peran:admin'])->group(function(){

        Route::get('/admin', [DaftarPelatihanController::class, 'index']);
        Route::get('/admin/{id}', [DaftarPelatihanController::class, 'show']);


        Route::get('/ketua', [DaftarPelatihanController::class, 'index']);
        Route::get('/ketua/{id}', [DaftarPelatihanController::class, 'show']);
        // Daftar Pelatihan
        Route::get('/daftar-pelatihan', [DaftarPelatihanController::class, 'index']);
        Route::put('/daftar-pelatihan/{id}', [DaftarPelatihanController::class, 'update']);
        Route::get('/daftar-pelatihan/{id}', [DaftarPelatihanController::class, 'show']);
        Route::delete('/daftar-pelatihan/{id}', [DaftarPelatihanController::class, 'destroy']);

        // Peserta
        Route::post('/peserta/{id}', [PesertaController::class, 'store']);
        Route::put('/peserta/{id}', [PesertaController::class, 'update']);
        Route::get('/peserta', [PesertaController::class, 'index']);
        Route::get('/peserta/{id}', [PesertaController::class, 'show']);
        Route::delete('/peserta/{id}', [PesertaController::class, 'destroy']);

        // Feedback
        Route::get('/feedback', [FeedbackController::class, 'index']);
        Route::get('/feedback/{id}', [FeedbackController::class, 'show']);
        Route::put('/feedback/{id}', [FeedbackController::class, 'update']);
        Route::delete('/feedback/{id}', [FeedbackController::class, 'destroy']);
        

        // Berita
        Route::post('/berita', [BeritaController::class, 'store']);
        Route::put('/berita/{id}', [BeritaController::class, 'update']);
        Route::delete('/berita/{id}', [BeritaController::class, 'destroy']);

        // Banner
        Route::post('/banner', [BannerController::class, 'store']);
        Route::put('/banner/{id}', [BannerController::class, 'update']); 
        Route::delete('/banner/{id}', [BannerController::class, 'destroy']);

        // Slideshow
        Route::post('/slideshow', [SlideshowController::class, 'store']);
        Route::put('/slideshow/{id}', [SlideshowController::class, 'update']); 
        Route::delete('/slideshow/{id}', [SlideshowController::class, 'destroy']);

        // Mentor
        Route::post('/mentor', [MentorController::class, 'store']);
        Route::put('/mentor/{id}', [MentorController::class, 'update']); 
        Route::delete('/mentor/{id}', [MentorController::class, 'destroy']);

        // Informasi Galeri
        Route::post('/informasi-galeri', [InformasiGaleriController::class, 'store']);
        Route::put('/informasi-galeri/{id}', [InformasiGaleriController::class, 'update']);
        Route::delete('/informasi-galeri/{id}', [InformasiGaleriController::class, 'destroy']);

        // Informasi Kontak
        Route::post('/informasi-kontak', [InformasiKontakController::class, 'store']);

        // Informasi Lembaga
        Route::post('/informasi-lembaga', [InformasiLembagaController::class, 'store']);
        Route::put('/informasi-lembaga/{id}', [InformasiLembagaController::class, 'update']);

        // Laporan Admin
        Route::apiResource('laporan-admin', LaporanAdminController::class)->only(['index', 'show', 'destroy']);
        Route::post('/my-laporan-admin', [LaporanAdminController::class, 'storeOrUpdateMyLaporan']);
        Route::get('/my-laporan-admin', [LaporanAdminController::class, 'showMyLaporan']);

        // Notifikasi announcement
        Route::post('/notifikasi-pengumuman', [NotifikasiController::class, 'sendAnnouncementToAllPeserta']);

        // Pelatihan
        Route::post('/pelatihan', [PelatihanController::class, 'store']);
        Route::put('/pelatihan/{id}', [PelatihanController::class, 'update']);
        Route::delete('/pelatihan/{id}', [PelatihanController::class, 'destroy']);

        // Pendidikan
        Route::post('/pendidikan', [PendidikanController::class, 'store']);
        Route::put('/pendidikan/{id}', [PendidikanController::class, 'update']);
        Route::delete('/pendidikan/{id}', [PendidikanController::class, 'destroy']);

        // Profile Entitas Tunggal
        Route::post('/profile-yayasan', [ProfileYayasanController::class, 'store']);
        Route::post('/profile-lkp', [ProfileLKPController::class, 'store']);
        Route::post('/profile-lpk', [ProfileLPKController::class, 'store']);

        Route::apiResource('kategori-pelatihan', KategoriPelatihanController::class)->except(['index']);
    });  
    

    Route::middleware(['peran:ketua'])->group(function () {
        Route::get('/laporan-admin', [LaporanAdminController::class, 'index']);
        Route::get('/laporan-admin/{id}', [LaporanAdminController::class, 'showLaporanByIdForKetua']);
        Route::delete('/laporan-admin/{id}', [LaporanAdminController::class, 'destroy']);
    });

});



