<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'offer_id',
        'actor_role',
        'action',
        'offered_price',
        'note',
    ];

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function getActionLabelAttribute(): string
    {
        $actor = match ($this->actor_role) {
            'marketing' => 'Marketing',
            'supervisor' => 'Supervisor',
            'customer' => 'Customer',
            default => 'Tim',
        };

        return match ($this->action) {
            'submitted' => 'Customer mengajukan penawaran',
            'countered' => $actor . ' mengirim tawar balik',
            'accepted' => 'Harga disepakati',
            'rejected' => 'Negosiasi dihentikan',
            default => 'Update negosiasi',
        };
    }
}
