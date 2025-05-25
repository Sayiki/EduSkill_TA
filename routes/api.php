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
    AuthController
};

Route::post('/login',  [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout']);


Route::middleware(['jwt.auth'])->group(function () {
    Route::middleware(['peran:peserta'])->group(function () {
        Route::post('/daftar-pelatihan', [DaftarPelatihanController::class, 'store']);
    });


    Route::middleware(['auth:admin'])->group(function(){
        Route::apiResources([
            'peserta'          => PesertaController::class,
            'admin'            => AdminController::class,
            'feedback'         => FeedbackController::class,
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
    });  

});



