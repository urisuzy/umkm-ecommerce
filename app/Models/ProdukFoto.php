<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukFoto extends Model
{
    use HasFactory;
    protected $table = 'produk';
    protected $fillable = [
        'produk_id',
        'path_foto',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
