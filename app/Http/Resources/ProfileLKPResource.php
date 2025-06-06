<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileLKPResource extends JsonResource
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
            'nama_lkp' => $this->nama_lkp,
            'deskripsi_lkp' => $this->deskripsi_lkp,
            'foto_lkp' => $this->foto_lkp ? (filter_var($this->foto_lkp, FILTER_VALIDATE_URL) ? $this->foto_lkp : asset('storage/' . $this->foto_lkp)) : null,
        ];
    }
}
