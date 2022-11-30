<?php

namespace App\Http\Resources;

use App\Enums\DiskEnum;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class HoldingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'nama' => $this->nama,
            'foto' => $this->foto ? Storage::disk(DiskEnum::IMAGE)->url($this->foto) : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'umkms_count' => $this->umkms_count ?? 0
        ];
    }
}
