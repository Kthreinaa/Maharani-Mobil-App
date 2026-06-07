<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'car_id',
        'source_type',
        'source_id',
        'rating',
        'review_text',
        'media_path',
        'media_paths',
        'status',
        'admin_notes',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'media_paths' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getReviewPhotosAttribute(): array
    {
        $photos = collect($this->media_paths ?? []);

        if ($photos->isEmpty() && !empty($this->media_path)) {
            $photos->push($this->media_path);
        }

        return $photos
            ->filter(fn ($path) => filled($path))
            ->values()
            ->all();
    }

    public function getPrimaryPhotoAttribute(): ?string
    {
        return $this->review_photos[0] ?? null;
    }
}
