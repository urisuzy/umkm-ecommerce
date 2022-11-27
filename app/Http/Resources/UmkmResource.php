<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UmkmResource extends JsonResource
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
            'nama_umkm' => $this->nama_umkm,
            'alamat' => $this->alamat,
            'no_telp_umkm' => $this->no_telp_umkm,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'produks_count' => $this->produks_count ?? 0
        ];
    }
}
