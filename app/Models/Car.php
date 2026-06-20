<?php

namespace App\Models;

use App\Support\CarUnitCodeSuggester;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    public const IMPORT_ARCHIVE_DESCRIPTIONS = [
        'Unit arsip hasil import penjualan Excel.',
        'Unit arsip hasil import penjualan 2025.',
    ];

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

    public function productReviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function getUnitCodeSequenceAttribute(): int
    {
        return CarUnitCodeSuggester::extractSequence($this->kode_unit);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'available' => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
            'reserved' => 'bg-amber-100 text-amber-700 border border-amber-200',
            'sold' => 'bg-rose-100 text-rose-700 border border-rose-200',
            default => 'bg-slate-100 text-slate-700 border border-slate-200',
        };
    }

    public function getStatusDisplayLabelAttribute(): string
    {
        return strtoupper((string) $this->status);
    }

    public function scopeImportedArchive($query)
    {
        return $query
            ->where('cars.status', 'sold')
            ->whereIn('cars.deskripsi', self::IMPORT_ARCHIVE_DESCRIPTIONS)
            ->whereExists(function ($subQuery) {
                $subQuery
                    ->selectRaw('1')
                    ->from('orders')
                    ->whereColumn('orders.car_id', 'cars.id')
                    ->whereNotNull('orders.import_reference');
            });
    }

    public function scopeOperationalDataset($query)
    {
        return $query->where(function ($subQuery) {
            $subQuery->where('cars.status', '!=', 'sold')
                ->orWhereNotIn('cars.deskripsi', self::IMPORT_ARCHIVE_DESCRIPTIONS);
        });
    }

    public function scopeManagedCatalog($query)
    {
        return $query->where(function ($subQuery) {
            $subQuery->where(function ($operationalQuery) {
                $operationalQuery->where('cars.status', '!=', 'sold')
                    ->orWhereNotIn('cars.deskripsi', self::IMPORT_ARCHIVE_DESCRIPTIONS);
            })->orWhere(function ($archiveQuery) {
                $archiveQuery->where('cars.status', 'sold')
                    ->whereIn('cars.deskripsi', self::IMPORT_ARCHIVE_DESCRIPTIONS)
                    ->whereExists(function ($orderQuery) {
                        $orderQuery
                            ->selectRaw('1')
                            ->from('orders')
                            ->whereColumn('orders.car_id', 'cars.id')
                            ->whereNotNull('orders.import_reference');
                    });
            });
        });
    }
}
