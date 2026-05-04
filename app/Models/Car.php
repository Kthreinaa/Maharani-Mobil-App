<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_unit',
        'merk',
        'tipe',
        'tahun',
        'harga',
        'kilometer',
        'transmisi',
        'warna',
        'bahan_bakar',
        'status',
        'deskripsi',
        'photos',
        'created_by',
    ];

    protected $casts = [
        'photos' => 'array',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function favoredByUsers()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function testDrives()
    {
        return $this->hasMany(TestDrive::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }
}
