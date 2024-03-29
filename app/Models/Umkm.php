<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Umkm extends Model
{
    use HasFactory;
    protected $table = 'umkm';
    protected $fillable = [
        'user_id',
        'nama_umkm',
        'alamat',
        'no_telp_umkm',
    ];

    protected $withCount = ['produks'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function holdings()
    {
        return $this->belongsToMany(Holding::class);
    }

    public function produks()
    {
        return $this->hasMany(Produk::class);
    }
}
