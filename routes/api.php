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
};

Route::post('/login',  [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::get('/berita', [BeritaController::class, 'index']);
Route::get('/berita/{id}', [BeritaController::class, 'show']);

Route::get('/banner', [BannerController::class, 'index']);
Route::get('/banner/{id}', [BannerController::class, 'show']);

Route::get('/slideshow', [SlideshowController::class, 'index']);
Route::get('/slideshow/{id}', [SlideshowController::class, 'show']);

Route::get('/mentor', [MentorController::class, 'index']);
Route::get('/mentor/{id}', [MentorController::class, 'show']);


Route::middleware(['jwt.auth'])->group(function () {
    Route::middleware(['peran:peserta'])->group(function () {
        Route::post('/daftar-pelatihan', [DaftarPelatihanController::class, 'store']);
        Route::get('/peserta/{id}', [PesertaController::class, 'show']);
        Route::put('/peserta/{id}', [PesertaController::class, 'update']);
        Route::post('/feedback/{id}', [FeedbackController::class, 'store']);
        
    });


    Route::middleware(['peran:admin'])->group(function(){
        Route::apiResources([
            'admin'            => AdminController::class,
            'informasi-galeri' => InformasiGaleriController::class,
            'informasi-kontak' => InformasiKontakController::class,
            'informasi-lembaga'=> InformasiLembagaController::class,
            'ketua'            => KetuaController::class,
            'laporan-admin'    => LaporanAdminController::class,
            'notifikasi'       => NotifikasiController::class,
            'pelatihan'        => PelatihanController::class,
            'pendidikan'       => PendidikanController::class,
            'profile-yayasan'  => ProfileYayasanController::class,
            'profile-lkp'      => ProfileLKPController::class,
            'profile-lpk'      => ProfileLPKController::class,
        ]);
        Route::get('/daftar-pelatihan', [DaftarPelatihanController::class, 'index']);
        Route::put('/daftar-pelatihan/{id}', [DaftarPelatihanController::class, 'update']);
        Route::get('/daftar-pelatihan/{id}', [DaftarPelatihanController::class, 'show']);
        Route::delete('/daftar-pelatihan/{id}', [DaftarPelatihanController::class, 'destroy']);
        Route::delete('/peserta/{id}', [PesertaController::class, 'destroy']);
        Route::delete('/feedback/{id}', [FeedbackController::class, 'destroy']);
        Route::get('/feedback/{id}', [FeedbackController::class, 'show']);
        Route::post('/berita', [BeritaController::class, 'store']);
        Route::put('/berita/{id}', [BeritaController::class, 'update']);
        Route::delete('/berita/{id}', [BeritaController::class, 'destroy']);
        Route::post('/banner', [BannerController::class, 'store']);
        Route::put('/banner/{id}', [BannerController::class, 'update']); 
        Route::delete('/banner/{id}', [BannerController::class, 'destroy']);
        Route::post('/slideshow', [SlideshowController::class, 'store']);
        Route::put('/slideshow/{id}', [SlideshowController::class, 'update']); 
        Route::delete('/slideshow/{id}', [SlideshowController::class, 'destroy']);
        Route::post('/mentor', [MentorController::class, 'store']);
        Route::put('/mentor/{id}', [MentorController::class, 'update']); 
        Route::delete('/mentor/{id}', [MentorController::class, 'destroy']);
    });  

});



