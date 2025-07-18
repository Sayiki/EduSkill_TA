<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InformasiGaleriResource extends JsonResource
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
            'judul_foto' => $this->judul_foto, 
            'file_foto' => $this->file_foto,
            'tanggal_upload' => $this->created_at->format('d M Y H:i'),
        ];
    }
}
