<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestDrive extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'car_id',
        'booking_date',
        'booking_time',
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
        'booking_date' => 'date',
        'handled_at' => 'datetime',
        'next_follow_up_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function handledBy()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function getCustomerChannelLabelAttribute(): string
    {
        return match ($this->customer_channel) {
            'offline' => 'Datang ke Showroom',
            default => 'Booking Online',
        };
    }

    public function getFollowUpStatusLabelAttribute(): string
    {
        return match ($this->follow_up_status) {
            'needs_follow_up' => 'Perlu Follow-up',
            'followed_up' => 'Sudah Test Drive',
            'closed_won' => 'Lanjut ke Transaksi',
            'closed_lost' => 'Tidak Lanjut',
            default => 'Booking Test Drive',
        };
    }

    public function getProcessOutcomeLabelAttribute(): string
    {
        if ($this->order) {
            return 'Masuk ke transaksi ' . $this->order->order_reference;
        }

        return match ($this->status) {
            'completed' => 'Test drive selesai, belum masuk transaksi.',
            'rejected', 'cancelled' => 'Test drive tidak dilanjutkan ke transaksi.',
            default => 'Booking test drive terpisah dari transaksi.',
        };
    }
}
