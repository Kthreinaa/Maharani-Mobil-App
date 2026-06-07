<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'method',
        'gateway_provider',
        'gateway_reference',
        'gateway_external_id',
        'gateway_checkout_url',
        'gateway_status',
        'gateway_channel',
        'gateway_payload',
        'amount',
        'paid_at',
        'proof_file',
        'status',
        'verified_by',
        'verified_at',
        'handled_by',
        'handled_role',
        'handled_at',
    ];

    protected $casts = [
        'gateway_payload' => 'array',
        'paid_at' => 'datetime',
        'verified_at' => 'datetime',
        'handled_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function handledBy()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function getInternalMethodLabelAttribute(): string
    {
        return match ($this->method) {
            'cash' => 'Tunai',
            'credit', 'transfer', 'va' => 'Transfer',
            default => strtoupper((string) $this->method),
        };
    }

    public function getStatusBadgeClassesAttribute(): string
    {
        return match ($this->status) {
            'verified' => 'bg-emerald-100 text-emerald-700',
            'rejected' => 'bg-rose-100 text-rose-700',
            default => 'bg-amber-100 text-amber-700',
        };
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->handled_role) {
            'marketing' => 'Marketing',
            'supervisor' => 'Supervisor',
            'gateway' => 'Gateway Otomatis',
            default => 'Belum Diproses',
        };
    }

    public function getRoleBadgeClassesAttribute(): string
    {
        return match ($this->handled_role) {
            'marketing' => 'border-amber-200 bg-amber-50 text-amber-700',
            'supervisor' => 'border-sky-200 bg-sky-50 text-sky-700',
            'gateway' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
            default => 'border-slate-200 bg-slate-50 text-slate-600',
        };
    }

    public function getGatewayChannelLabelAttribute(): string
    {
        return match ($this->gateway_channel) {
            'BCA' => 'BCA Virtual Account',
            'BNI' => 'BNI Virtual Account',
            'BRI' => 'BRI Virtual Account',
            'MANDIRI' => 'Mandiri Virtual Account',
            'PERMATA' => 'Permata Virtual Account',
            'BANK_TRANSFER' => 'Transfer Bank',
            'EWALLET' => 'E-Wallet',
            'QRIS' => 'QRIS',
            default => filled($this->gateway_channel) ? (string) $this->gateway_channel : 'Belum dipilih',
        };
    }

    public function getRequiresProofAttribute(): bool
    {
        return in_array($this->method, ['transfer', 'va'], true);
    }

    public function getHasProofAttribute(): bool
    {
        return filled($this->proof_file);
    }

    public function getCoversFullAmountAttribute(): bool
    {
        $orderTotal = (float) ($this->order?->total ?? 0);

        if ($orderTotal <= 0) {
            return false;
        }

        return (float) $this->amount >= $orderTotal;
    }
}
