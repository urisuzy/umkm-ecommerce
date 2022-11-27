<?php

namespace App\Models;

use App\Enums\DiskEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Holding extends Model
{
    use HasFactory;
    protected $table = 'holdings';
    protected $fillable = [
        'user_id',
        'nama',
        'foto'
    ];

    protected $withCount = ['umkms'];

    protected function foto(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Storage::disk(DiskEnum::IMAGE)->url($value),
            set: fn ($value) => $value,
        );
    }

    public function umkms()
    {
        return $this->belongsToMany(Umkm::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
