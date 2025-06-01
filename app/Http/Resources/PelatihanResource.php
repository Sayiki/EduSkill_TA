<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PelatihanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama_pelatihan' => $this->nama_pelatihan,
            'keterangan_pelatihan' => $this->keterangan_pelatihan,
            'jumlah_kuota' => $this->jumlah_kuota,
            'peserta_terdaftar' => $this->jumlah_peserta, // Jumlah peserta yang sudah diterima
            'sisa_kuota' => $this->jumlah_kuota - $this->jumlah_peserta,
            'deadline_pendaftaran' => $this->waktu_pengumpulan ? $this->waktu_pengumpulan->format('d M Y H:i') : null,
            'mentor_pelatihan' => new MentorResource($this->whenLoaded('mentor')), // Menampilkan info mentor
            'tanggal_dibuat' => $this->created_at->format('d M Y'),
        ];
    }
}
