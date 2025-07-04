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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DaftarPelatihanController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $pelatihanId = $request->query('pelatihan_id');

        $query = DaftarPelatihan::with(['peserta.user', 'pelatihan'])->latest();

        if ($pelatihanId) {
            $query->where('pelatihan_id', $pelatihanId);
        }

        $entries = $query->paginate($perPage);

        return response()->json($entries);
    }

    public function store(Request $request)
    {
        Log::info('Registration attempt by User ID: ' . $request->user()->id);
        Log::info('Request Data:', $request->all());

        $validatedData = $request->validate([
            'pelatihan_id' => 'required|integer|exists:pelatihan,id',
            'nik'          => ['required', 'string', 'digits:16'],
            'ktp'          => 'required|file|mimes:jpeg,png,pdf|max:2048',
            'kk'           => 'required|file|mimes:jpeg,png,pdf|max:2048',
            'ijazah'       => 'required|file|mimes:jpeg,png,pdf|max:2048',
            'foto'         => 'required|file|mimes:jpeg,png,pdf|max:2048',
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'nomor_telp'   => 'required|string|max:15',
            'pendidikan_id' => 'required|integer|exists:pendidikan,id',
            'alamat_peserta' => 'required|string|max:255',
            'jenis_kelamin' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'tanggal_lahir' => ['required', 'date'],
        ]);

        DB::beginTransaction();

        try {
            $pelatihan = Pelatihan::lockForUpdate()->find($validatedData['pelatihan_id']);

            if (!$pelatihan) {
                DB::rollBack();
                return response()->json(['message' => 'Pelatihan tidak valid.'], 404);
            }

            // Cek kuota SEBELUM melakukan apapun
            if ($pelatihan->jumlah_peserta >= $pelatihan->jumlah_kuota) {
                DB::rollBack();
                return response()->json(['message' => 'Maaf, kuota untuk pelatihan ini sudah penuh.'], 409);
            }

            $user = $request->user();

            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];
            $user->save();

            $peserta = Peserta::firstOrCreate(
                ['user_id' => $user->id],
                [ // Data untuk pembuatan pertama kali
                    'nik_peserta' => $validatedData['nik'],
                    'nomor_telp' => $validatedData['nomor_telp'],
                    'pendidikan_id' => $validatedData['pendidikan_id'],
                    'alamat_peserta' => $validatedData['alamat_peserta'],
                    'jenis_kelamin' => $validatedData['jenis_kelamin'], // Isi di sini
                    'tanggal_lahir' => $validatedData['tanggal_lahir'], // Isi di sini
                ]
            );

            if ($peserta->wasRecentlyCreated === false) {
                if (empty($peserta->nik_peserta)) {
                    $isNikTaken = Peserta::where('nik_peserta', $validatedData['nik'])
                                         ->where('id', '!=', $peserta->id)
                                         ->exists();
                    if ($isNikTaken) {
                        DB::rollBack();
                        return response()->json(['message' => 'NIK sudah terdaftar oleh peserta lain.'], 422);
                    }
                    $peserta->nik_peserta = $validatedData['nik'];
                } elseif ($peserta->nik_peserta !== $validatedData['nik']) {
                    DB::rollBack();
                    return response()->json(['message' => 'NIK yang Anda masukkan tidak sesuai dengan data yang sudah terdaftar.'], 422);
                }

                $peserta->nomor_telp = $validatedData['nomor_telp'];
                $peserta->pendidikan_id = $validatedData['pendidikan_id'];
                $peserta->alamat_peserta = $validatedData['alamat_peserta'];
                $peserta->jenis_kelamin = $validatedData['jenis_kelamin']; // Update di sini
                $peserta->tanggal_lahir = $validatedData['tanggal_lahir']; // Update di sini
                $peserta->save();
            }

            $uploadedPaths = [];
            $files = ['ktp', 'kk', 'ijazah', 'foto'];
            foreach ($files as $fileField) {
                if ($request->hasFile($fileField)) {
                    $path = $request->file($fileField)->store('documents/daftar_pelatihan', 'public');
                    $uploadedPaths[$fileField] = $path;
                    Log::info("File uploaded: {$fileField} -> {$path}");
                } else {
                    Log::warning("File field missing: {$fileField}");
                }
            }

            $hasActiveRegistration = DaftarPelatihan::where('peserta_id', $peserta->id)
                                                  ->whereIn('status', ['ditinjau', 'diterima'])
                                                  ->exists();

            if ($hasActiveRegistration) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Anda sudah memiliki pendaftaran yang sedang ditinjau atau sudah diterima untuk pelatihan lain atau pelatihan ini.'
                ], 409);
            }

            $hasAlreadyRegisteredForThisPelatihan = DaftarPelatihan::where('peserta_id', $peserta->id)
                                                                    ->where('pelatihan_id', $validatedData['pelatihan_id'])
                                                                    ->exists();
            if ($hasAlreadyRegisteredForThisPelatihan) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Anda sudah terdaftar untuk pelatihan ini.'
                ], 409);
            }


            $entryData = [
                'peserta_id'   => $peserta->id,
                'pelatihan_id' => $validatedData['pelatihan_id'],
                'ktp'          => $uploadedPaths['ktp'] ?? null,
                'kk'           => $uploadedPaths['kk'] ?? null,
                'ijazah'       => $uploadedPaths['ijazah'] ?? null,
                'foto'         => $uploadedPaths['foto'] ?? null,
                'status'       => 'ditinjau',
            ];

            $entry = DaftarPelatihan::create($entryData);

            $pelatihan->increment('jumlah_peserta');

            DB::commit();
            return response()->json($entry->load(['peserta.user', 'pelatihan']), 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation Error during registration:', ['errors' => $e->errors()]);
            return response()->json(['message' => 'Validasi gagal', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error during registration:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Terjadi kesalahan server saat mendaftar.', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $entry = DaftarPelatihan::with(['peserta.user', 'pelatihan'])->findOrFail($id);
        return response()->json($entry, 200);
    }

    public function indexForCurrentUser(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $perPage = $request->query('per_page', 10);
        $entries = DaftarPelatihan::with(['peserta.user', 'pelatihan'])
                                ->whereHas('peserta', function ($query) use ($user) {
                                    $query->where('user_id', $user->id);
                                })
                                ->latest()
                                ->paginate($perPage);

        return response()->json($entries);
    }

    public function update(Request $request, $id)
    {
        $entry = DaftarPelatihan::with('pelatihan')->findOrFail($id);
        $validatedData = $request->validate([
            'status' => ['required', Rule::in(['ditinjau', 'diterima', 'ditolak'])],
        ]);

        $newStatus = $validatedData['status'];
        $originalStatus = $entry->status;
        $pelatihan = $entry->pelatihan;

        if ($newStatus === $originalStatus) {
            return response()->json($entry->load(['peserta.user', 'pelatihan']), 200);
        }
        
        DB::transaction(function () use ($entry, $pelatihan, $originalStatus, $newStatus) {
            // HANYA KEMBALIKAN KUOTA JIKA PENDAFTAR DITOLAK
            if ($newStatus === 'ditolak' && ($originalStatus === 'ditinjau' || $originalStatus === 'diterima')) {
                $pelatihan->decrement('jumlah_peserta');
            } 
            // JIKA SEBELUMNYA DITOLAK, LALU DITERIMA KEMBALI (kasus langka)
            elseif (($newStatus === 'diterima' || $newStatus === 'ditinjau') && $originalStatus === 'ditolak') {
                // Cek kuota lagi sebelum menerima kembali
                $pelatihan->refresh(); // Ambil data terbaru dari DB
                if ($pelatihan->jumlah_peserta >= $pelatihan->jumlah_kuota) {
                    // Throw exception untuk membatalkan transaksi dan memberi error
                    throw new \Exception('Gagal mengubah status. Kuota sudah penuh oleh pendaftar lain.');
                }
                $pelatihan->increment('jumlah_peserta');
            }

            // Simpan status baru pendaftaran
            $entry->status = $newStatus;
            $entry->save();
        });

        $notificationMessage = '';

        if ($newStatus === 'diterima' && $originalStatus !== 'diterima') {
            $pelatihan->increment('jumlah_peserta');
            $notificationMessage = 'Selamat! Pendaftaran Anda untuk pelatihan "' . $pelatihan->nama_pelatihan . '" telah diterima.';
        }
        elseif ($newStatus === 'ditolak' && $originalStatus !== 'ditolak') {
            if ($originalStatus === 'diterima') {
                $pelatihan->decrement('jumlah_peserta');
            }
            $notificationMessage = 'Mohon maaf, pendaftaran Anda untuk pelatihan "' . $pelatihan->nama_pelatihan . '" telah ditolak. Silakan hubungi admin untuk informasi lebih lanjut atau periksa detail di profil Anda.';
        }
        elseif ($originalStatus === 'diterima' && ($newStatus === 'ditinjau' || $newStatus === 'ditolak')) {
            $pelatihan->decrement('jumlah_peserta');
            if ($newStatus === 'ditinjau') {
                 $notificationMessage = 'Status pendaftaran Anda untuk pelatihan "' . $pelatihan->nama_pelatihan . '" diubah menjadi sedang ditinjau kembali.';
            }
        }

        if (!empty($notificationMessage) && $entry->peserta_id) {
            Notifikasi::create([
                'peserta_id' => $entry->peserta_id,
                'pesan' => $notificationMessage,
                'status' => 'belum dibaca',
            ]);
        }

        return response()->json($entry->fresh()->load(['peserta.user', 'pelatihan']), 200);
    }


    public function destroy($id)
    {
        $entry = DaftarPelatihan::with('pelatihan')->findOrFail($id);

        // Kembalikan kuota jika pendaftaran yang dihapus berstatus 'diterima' atau 'ditinjau'
        if ($entry->pelatihan && ($entry->status === 'diterima' || $entry->status === 'ditinjau')) {
            $entry->pelatihan->decrement('jumlah_peserta');
        }
        
        $entry->delete(); 

        return response()->json(['message' => 'Pendaftaran berhasil dihapus.'], 200); 
    }
}
