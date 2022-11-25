<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    use HasFactory;
    protected $table = 'pembelian';
    protected $fillable = [
        'user_id',
        'umkm_id',
        'no_kuitansi',
        'total_harga',
        'status',
        'payment_code',
        'no_resi',
        'review',
        'paid_at',
        'sent_at',
    ];

    public function details()
    {
        return $this->hasMany(DetailPembelian::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function seller()
    {
        return $this->belongsTo(Umkm::class, 'umkm_id');
    }
}
