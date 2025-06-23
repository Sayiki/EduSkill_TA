<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DaftarPelatihan;
use App\Models\Peserta;
use App\Models\Notifikasi; 
use App\Models\Pelatihan; 
use Illuminate\Validation\Rule; 

class DaftarPelatihanController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);

        // Eager load relasi yang mungkin dibutuhkan di frontend
        $entries = DaftarPelatihan::with(['peserta.user', 'pelatihan'])
                                  ->latest() // Urutkan berdasarkan yang terbaru
                                  ->paginate($perPage);

        return response()->json($entries);
    }

    public function store(Request $request)
    {
        Log::info('Registration attempt by User ID: ' . $request->user()->id);

        $data = $request->validate([
            'pelatihan_id' => 'required|integer|exists:pelatihan,id',
            'nik'          => ['required', 'string', 'digits:16'],
            'kk'           => 'nullable|string|max:255', // Asumsi ini path file atau teks
            'ktp'          => 'nullable|string|max:255', // Asumsi ini path file atau teks
            'ijazah'       => 'nullable|string|max:255', // Asumsi ini path file atau teks
            'foto'         => 'nullable|string|max:255', // Asumsi ini path file atau teks
        ]);

        $peserta = Peserta::firstOrCreate(
            ['user_id' => $request->user()->id]
            // Anda bisa menambahkan data default lain untuk peserta di sini jika perlu
        );

        if (empty($peserta->nik_peserta)) {
            $isNikTaken = Peserta::where('nik_peserta', $data['nik'])->where('id', '!=', $peserta->id)->exists();
            if ($isNikTaken) {
                return response()->json(['message' => 'NIK sudah terdaftar oleh peserta lain.'], 422);
            }
            $peserta->nik_peserta = $data['nik'];
            $peserta->save();
        } 
        elseif ($peserta->nik_peserta !== $data['nik']) {
            return response()->json([
                'message' => 'NIK yang Anda masukkan tidak sesuai dengan data yang sudah terdaftar.',
            ], 422);
        }
        
        $hasActiveRegistration = DaftarPelatihan::where('peserta_id', $peserta->id)
                                              ->whereIn('status', ['ditinjau', 'diterima'])
                                              ->exists();

        if ($hasActiveRegistration) {
            return response()->json([
                'message' => 'Anda sudah memiliki pendaftaran yang sedang ditinjau atau sudah diterima. Anda tidak dapat mendaftar lagi saat ini.'
            ], 409); 
        }

        $entryData = [
            'peserta_id'   => $peserta->id,
            'pelatihan_id' => $data['pelatihan_id'],
            'kk'           => $data['kk'] ?? null,
            'ktp'          => $data['ktp'] ?? null,
            'ijazah'       => $data['ijazah'] ?? null,
            'foto'         => $data['foto'] ?? null,
            'status'       => 'ditinjau',
        ];
        
        $entry = DaftarPelatihan::create($entryData);

        return response()->json($entry->load(['peserta.user', 'pelatihan']), 201);
    }

    public function show($id)
    {
        $entry = DaftarPelatihan::with(['peserta.user', 'pelatihan'])->findOrFail($id);
        return response()->json($entry, 200);
    }

    public function update(Request $request, $id)
    {
        // 1. Temukan entri pendaftaran, pastikan memuat relasi yang dibutuhkan untuk notifikasi
        $entry = DaftarPelatihan::with(['pelatihan', 'peserta'])->findOrFail($id); // Tambahkan 'peserta'

        // 2. Validasi input yang masuk. Fokus utama pada perubahan status.
        $validatedData = $request->validate([
            'status' => ['required', Rule::in(['ditinjau', 'diterima', 'ditolak'])],
        ]);

        $newStatus = $validatedData['status'];
        $originalStatus = $entry->status;

        // Jangan lakukan apa-apa jika status tidak berubah.
        if ($newStatus === $originalStatus) {
            return response()->json($entry->load(['peserta.user', 'pelatihan']), 200);
        }

        $pelatihan = $entry->pelatihan; // Ambil objek pelatihan dari relasi

        // 3. LOGIKA BISNIS: Periksa kuota SEBELUM menerima peserta baru.
        if ($newStatus === 'diterima') {
            if ($pelatihan->jumlah_peserta >= $pelatihan->jumlah_kuota) {
                return response()->json([
                    'message' => 'Gagal menerima peserta. Kuota untuk pelatihan ini sudah penuh.',
                    'kuota' => $pelatihan->jumlah_kuota,
                    'peserta_saat_ini' => $pelatihan->jumlah_peserta,
                ], 409);
            }
        }

        // 4. Lakukan update pada entri pendaftaran.
        // Hanya update status karena itu yang divalidasi dari request.
        $entry->status = $newStatus;
        $entry->save();

        // 5. LOGIKA BISNIS: Update jumlah peserta pada pelatihan terkait DAN KIRIM NOTIFIKASI.
        $notificationMessage = '';

        if ($newStatus === 'diterima' && $originalStatus !== 'diterima') {
            $pelatihan->increment('jumlah_peserta');
            $notificationMessage = 'Selamat! Pendaftaran Anda untuk pelatihan "' . $pelatihan->nama_pelatihan . '" telah diterima.';
        } 
        elseif ($newStatus === 'ditolak' && $originalStatus !== 'ditolak') {
            // Hanya decrement jika status sebelumnya adalah 'diterima'
            if ($originalStatus === 'diterima') {
                $pelatihan->decrement('jumlah_peserta');
            }
            $notificationMessage = 'Mohon maaf, pendaftaran Anda untuk pelatihan "' . $pelatihan->nama_pelatihan . '" telah ditolak. Silakan hubungi admin untuk informasi lebih lanjut atau periksa detail di profil Anda.';
        }
        elseif ($originalStatus === 'diterima' && ($newStatus === 'ditinjau' || $newStatus === 'ditolak')) {
            // Jika status diubah dari 'diterima' ke status lain (misalnya, pembatalan penerimaan)
            $pelatihan->decrement('jumlah_peserta');
            if ($newStatus === 'ditinjau') {
                 $notificationMessage = 'Status pendaftaran Anda untuk pelatihan "' . $pelatihan->nama_pelatihan . '" diubah menjadi sedang ditinjau kembali.';
            }
            // Notifikasi untuk 'ditolak' dari 'diterima' sudah ditangani di blok elseif sebelumnya.
        }

        // Buat notifikasi jika ada pesan yang dihasilkan
        if (!empty($notificationMessage) && $entry->peserta_id) {
            Notifikasi::create([
                'peserta_id' => $entry->peserta_id,
                'pesan' => $notificationMessage,
                'status' => 'belum dibaca', // Status notifikasi default
            ]);
        }

        // 6. Kembalikan respons dengan data yang sudah diperbarui.
        return response()->json($entry->fresh()->load(['peserta.user', 'pelatihan']), 200);
    }

    public function destroy($id)
    {
        $entry = DaftarPelatihan::with('pelatihan')->findOrFail($id);

        // Jika pendaftaran yang dihapus statusnya 'diterima', kurangi jumlah peserta
        if ($entry->status === 'diterima' && $entry->pelatihan) {
            $entry->pelatihan->decrement('jumlah_peserta');
        }
        
        $entry->delete(); 

        return response()->json(['message' => 'Pendaftaran berhasil dihapus.'], 200); // Mengembalikan pesan sukses
    }
}
