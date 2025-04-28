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
    StatusLamaranController
};
Route::get('/peserta', [PesertaController::class, 'index']);
Route::get('/admin', [AdminController::class, 'index']);
Route::get('/daftar-pelatihan', [DaftarPelatihanController::class, 'index']);
Route::get('/feedback', [FeedbackController::class, 'index']);
Route::get('/informasi-galeri', [InformasiGaleriController::class, 'index']);
Route::get('/informasi-kontak', [InformasiKontakController::class, 'index']);
Route::get('/informasi-lembaga', [InformasiLembagaController::class, 'index']);
Route::get('/ketua', [KetuaController::class, 'index']);
Route::get('/laporan-admin', [LaporanAdminController::class, 'index']);
Route::get('/notifikasi', [NotifikasiController::class, 'index']);
Route::get('/pelatihan', [PelatihanController::class, 'index']);
Route::get('/pendidikan', [PendidikanController::class, 'index']);
Route::get('/profile-yayasan', [ProfileYayasanController::class, 'index']);
Route::get('/profile-lkp', [ProfileLKPController::class, 'index']);
Route::get('/profile-lpk', [ProfileLPKController::class, 'index']);
Route::get('/status-lamaran', [StatusLamaranController::class, 'index']);

