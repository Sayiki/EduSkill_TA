<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PesertaPublicResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // Hanya sertakan data yang aman untuk publik
            'nama_peserta' => $this->user->name, // Ambil nama dari relasi user
            'foto_peserta' => $this->daftarPelatihan?->foto, 
            'tempat_kerja' => $this->feedback?->tempat_kerja,
   
        ];
    }
}
