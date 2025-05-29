<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
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
            'nama_banner' => $this->nama_banner,
            'url_gambar' => $this->gambar ? asset('storage/' . $this->gambar) : null,
            'tanggal_dibuat' => $this->created_at->format('d M Y H:i'),
        ];
    }
}