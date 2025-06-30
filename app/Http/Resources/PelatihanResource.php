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
            'kategori' => $this->kategori, 
            'biaya' => $this->biaya,
            'jumlah_kuota' => $this->jumlah_kuota,
            'jumlah_peserta' => $this->jumlah_peserta, // Used for 'jumlah_peserta' in frontend
            // Ensure waktu_pengumpulan is formatted to be easily consumed by JavaScript's Date or direct datetime-local input
            'waktu_pengumpulan' => $this->waktu_pengumpulan ? $this->waktu_pengumpulan->format('Y-m-d H:i:s') : null,
            'mentor_id' => $this->mentor_id, // Added for frontend mentor dropdown selection
            'status_pelatihan' => $this->status_pelatihan, // Added for frontend
            'post_status' => $this->post_status, // Added for frontend
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
            'mentor' => new MentorResource($this->whenLoaded('mentor')), // Ensure MentorResource exists
            'admin' => new AdminResource($this->whenLoaded('admin')),   // Ensure AdminResource exists
        ];
    }
}
