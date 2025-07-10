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
            'laporan_file' => $this->laporan_file,
            'laporan_deskripsi' => $this->laporan_deskripsi,
            'dibuat_pada' => $this->created_at->format('Y-m-d H:i:s'),
            'diperbarui_pada' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
