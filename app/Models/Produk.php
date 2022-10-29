<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;
    protected $table = 'produk';
    protected $fillable = [
        'umkm_id',
        'nama',
        'harga',
        'diskon',
    ];

    public function umkm()
    {
        return $this->belongsTo(Umkm::class);
    }
}
