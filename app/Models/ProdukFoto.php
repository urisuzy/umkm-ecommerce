<?php

namespace App\Models;

use App\Enums\DiskEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProdukFoto extends Model
{
    use HasFactory;
    protected $table = 'produk_foto';
    protected $fillable = [
        'produk_id',
        'path_foto',
    ];

    protected function pathFoto(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Storage::disk(DiskEnum::IMAGE)->url($value),
            set: fn ($value) => $value,
        );
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
