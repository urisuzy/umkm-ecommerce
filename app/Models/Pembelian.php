<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    use HasFactory;
    protected $table = 'pembelian';
    protected $fillable = [
        'buyer_id',
        'seller_id',
        'no_kuitansi',
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
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
