<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotifikasiResource extends JsonResource
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
            'peserta_id' => $this->id_peserta,
            'judul' => $this->judul,
            'pesan' => $this->pesan,
            'status' => $this->status, // misal: 'belum dibaca', 'dibaca'
            'dikirim_pada' => $this->created_at->format('d M Y H:i:s'),
            'diperbarui_pada' => $this->updated_at->format('d M Y H:i:s'),
        ];
    }
}
