<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProdukResource extends JsonResource
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
            'umkm_id' => $this->umkm_id,
            'nama' => $this->nama,
            'harga' => $this->harga,
            'diskon' => $this->diskon,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'foto' => ProdukFotoResource::collection($this->fotos),
            'umkm' => new UmkmResource($this->umkm)
        ];
    }
}
