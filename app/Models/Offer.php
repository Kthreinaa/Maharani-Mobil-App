<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'car_id',
        'offer_price',
        'counter_price',
        'final_price',
        'negotiation_round',
        'last_offer_by',
        'status',
        'customer_channel',
        'notes',
        'follow_up_status',
        'lost_reason',
        'next_follow_up_at',
        'handled_by',
        'handled_role',
        'handled_at',
    ];

    protected $casts = [
        'offer_price' => 'decimal:2',
        'counter_price' => 'decimal:2',
        'final_price' => 'decimal:2',
        'handled_at' => 'datetime',
        'next_follow_up_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function handledBy()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function histories()
    {
        return $this->hasMany(OfferHistory::class)->latest();
    }

    public function getCustomerChannelLabelAttribute(): string
    {
        return match ($this->customer_channel) {
            'offline' => 'Datang ke Showroom',
            default => 'Online Website',
        };
    }

    public function getFollowUpStatusLabelAttribute(): string
    {
        return match ($this->follow_up_status) {
            'needs_follow_up' => 'Perlu Follow-up',
            'followed_up' => 'Sudah Follow-up',
            'closed_won' => 'Deal',
            'closed_lost' => 'Batal',
            default => 'Menunggu Tindak Lanjut',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'countered' => 'Tawar Balik Supervisor',
            'accepted' => 'Harga Disepakati',
            'rejected' => 'Negosiasi Ditutup',
            default => 'Menunggu Tanggapan',
        };
    }

    public function getStatusBadgeClassesAttribute(): string
    {
        return match ($this->status) {
            'countered' => 'bg-sky-100 text-sky-700 border border-sky-200',
            'accepted' => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
            'rejected' => 'bg-rose-100 text-rose-700 border border-rose-200',
            default => 'bg-amber-100 text-amber-700 border border-amber-200',
        };
    }

    public function getNegotiatedPriceAttribute(): float
    {
        return (float) ($this->final_price ?? $this->counter_price ?? $this->offer_price ?? 0);
    }

    public function getCanCheckoutAttribute(): bool
    {
        return $this->status === 'accepted' && $this->negotiated_price > 0;
    }
}
