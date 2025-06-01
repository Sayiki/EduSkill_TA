<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileYayasanResource extends JsonResource
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
            'nama_yayasan' => $this->nama_yayasan,
            'deskripsi_yayasan' => $this->deskripsi_yayasan,
            'url_foto_yayasan' => $this->foto_yayasan ? (filter_var($this->foto_yayasan, FILTER_VALIDATE_URL) ? $this->foto_yayasan : asset('storage/' . $this->foto_yayasan)) : null,
            'terakhir_diperbarui' => $this->updated_at->format('d M Y H:i'),
        ];
    }
}
