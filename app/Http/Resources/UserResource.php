<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'nama' => $this->profile->nama,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'balance' => $this->balance,
            'alamat' => $this->profile->alamat,
            'no_telp' => $this->profile->no_telp,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
