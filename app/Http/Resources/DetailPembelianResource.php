<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DetailPembelianResource extends JsonResource
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
            'pembelian_id' => $this->pembelian_id,
            'produk_id' => $this->produk_id,
            'diskon' => $this->diskon,
            'jumlah_barang' => $this->jumlah_barang,
            'total_harga' => $this->total_harga,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'produk' => new ProdukResource($this->produk),
        ];
    }
}
