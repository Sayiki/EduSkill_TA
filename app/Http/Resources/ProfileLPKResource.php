<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileLPKResource extends JsonResource
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
            'lembaga_id' => $this->id_lembaga,
            'nama_lpk' => $this->nama_lpk,
            'deskripsi_lpk' => $this->deskripsi_lpk,
            'url_foto_lpk' => $this->foto_lpk ? (filter_var($this->foto_lpk, FILTER_VALIDATE_URL) ? $this->foto_lpk : asset('storage/' . $this->foto_lpk)) : null,
            'informasi_lembaga_induk' => new InformasiLembagaResource($this->whenLoaded('lembaga')),
            'terakhir_diperbarui' => $this->updated_at->format('d M Y H:i'),
        ];
    }
}
