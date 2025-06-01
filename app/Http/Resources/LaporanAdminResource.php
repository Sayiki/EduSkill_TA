<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LaporanAdminResource extends JsonResource
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
            'admin_id' => $this->admin_id,
            'nama_admin_pembuat' => $this->whenLoaded('admin', function() {
                return $this->admin->user ? $this->admin->user->name : 'N/A'; // Asumsi relasi Admin->User
            }),
            'waktu_upload' => $this->waktu_upload ? $this->waktu_upload->format('Y-m-d H:i:s') : null,
            'jumlah_peserta' => $this->jumlah_peserta,
            'jumlah_lulusan_bekerja' => $this->jumlah_lulusan_bekerja,
            'jumlah_pendaftar' => $this->jumlah_pendaftar,
            'pelatihan_dibuka' => $this->pelatihan_dibuka,
            'pelatihan_berjalan' => $this->pelatihan_berjalan,
            'dibuat_pada' => $this->created_at->format('Y-m-d H:i:s'),
            'diperbarui_pada' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
