<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InformasiLembagaResource extends JsonResource
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
            'visi' => $this->visi,
            'misi' => $this->misi,
            'terakhir_diperbarui' => $this->updated_at->format('d M Y H:i'),
        ];
    }
}
