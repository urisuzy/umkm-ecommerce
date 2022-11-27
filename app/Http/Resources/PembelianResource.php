<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PembelianResource extends JsonResource
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
            'umkm_id' => $this->umkm_id,
            'no_kuitansi' => $this->no_kuitansi,
            'total_harga' => $this->total_harga,
            'status' => $this->status,
            'payment_code' => $this->payment_code,
            'no_resi' => $this->no_resi,
            'review' => $this->review,
            'paid_at' => $this->paid_at,
            'sent_at' => $this->sent_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'details_count' => $this->details_count ?? 0,
            'details' => DetailPembelianResource::collection($this->details),
            'buyer' => new UserResource($this->buyer),
            'seller' => new UmkmResource($this->seller)
        ];
    }
}
