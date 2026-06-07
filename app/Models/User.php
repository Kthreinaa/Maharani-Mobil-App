<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'provider',
        'provider_id',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Mendefinisikan relasi user ke daftar favorit.
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Mendefinisikan relasi many-to-many user ke mobil favorit.
     */
    public function favoriteCars()
    {
        return $this->belongsToMany(Car::class, 'favorites')->withTimestamps();
    }

    /**
     * Mendefinisikan relasi user ke pesanan.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Mendefinisikan relasi user ke test drive.
     */
    public function testDrives()
    {
        return $this->hasMany(TestDrive::class);
    }

    /**
     * Mendefinisikan relasi user ke penawaran.
     */
    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function productReviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    /**
     * Mengecek apakah user memiliki role customer.
     */
    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    /**
     * Mengecek apakah user memiliki role marketing.
     */
    public function isMarketing(): bool
    {
        return $this->role === 'marketing';
    }

    /**
     * Mengecek apakah user memiliki role supervisor.
     */
    public function isSupervisor(): bool
    {
        return $this->role === 'supervisor';
    }

    /**
     * Mengecek apakah user memiliki role owner.
     */
    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }
}
