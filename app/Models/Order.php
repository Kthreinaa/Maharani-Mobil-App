<?php

namespace App\Models;

use App\Support\OrderCodeFormatter;
use App\Support\TransactionLabelFormatter;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public const REVIEW_WINDOW_DAYS = 7;

    public const HANDOVER_DOCUMENT_TEMPLATES = [
        'invoice' => 'Faktur / invoice pembelian',
        'receipt' => 'Kwitansi digital / bukti bayar',
        'handover_note' => 'Berita acara serah terima kendaraan',
        'stnk' => 'Status ketersediaan STNK',
        'bpkb' => 'Status ketersediaan BPKB',
    ];

    protected $fillable = [
        'order_code',
        'user_id',
        'car_id',
        'status',
        'total',
        'payment_method',
        'leasing_partner',
        'credit_dp_percentage',
        'credit_dp_amount',
        'credit_tenor_months',
        'credit_monthly_installment',
        'credit_interest_rate',
        'transaction_channel',
        'sales_flow',
        'notes',
        'cancel_reason',
        'customer_cancellation_reason',
        'customer_cancellation_requested_at',
        'follow_up_status',
        'next_follow_up_at',
        'import_source',
        'import_reference',
        'import_file_hash',
        'handled_by',
        'handled_role',
        'handled_at',
        'approved_by',
        'approved_at',
        'document_status',
    ];

    protected $casts = [
        'handled_at' => 'datetime',
        'next_follow_up_at' => 'datetime',
        'approved_at' => 'datetime',
        'customer_cancellation_requested_at' => 'datetime',
        'document_status' => 'array',
        'credit_dp_percentage' => 'decimal:2',
        'credit_dp_amount' => 'decimal:2',
        'credit_monthly_installment' => 'decimal:2',
        'credit_interest_rate' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::created(function (Order $order): void {
            $order->assignOrderCodeIfMissing();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function purchaseReview()
    {
        return $this->hasOne(ProductReview::class, 'source_id')
            ->where('source_type', 'purchase');
    }

    public function handledBy()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getOrderReferenceAttribute(): string
    {
        return $this->order_code ?: 'ORD' . str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }

    public function buildOrderCodeBase(?CarbonInterface $orderedAt = null): string
    {
        $orderedAt ??= $this->created_at ?? now();
        $this->loadMissing('car');

        return substr(OrderCodeFormatter::forOrderParts(
            $this->car?->tipe,
            $this->car?->merk,
            $orderedAt,
            $this->transaction_channel,
            $this->import_source,
            0
        ), 0, -2);
    }

    public function assignOrderCodeIfMissing(bool $force = false): void
    {
        if (!$force && filled($this->order_code)) {
            return;
        }

        $orderedAt = $this->created_at ?? now();
        $base = $this->buildOrderCodeBase($orderedAt);

        $sequence = static::query()
            ->where(function ($query) use ($orderedAt) {
                $query->where('created_at', '<', $orderedAt)
                    ->orWhere(function ($sameTimestampQuery) use ($orderedAt) {
                        $sameTimestampQuery->where('created_at', '=', $orderedAt)
                            ->where('id', '<=', $this->id);
                    });
            })
            ->count();

        $this->forceFill([
            'order_code' => $base . str_pad((string) $sequence, 2, '0', STR_PAD_LEFT),
        ])->saveQuietly();
    }

    public static function pendingDocumentStatuses(): array
    {
        return collect(self::HANDOVER_DOCUMENT_TEMPLATES)
            ->mapWithKeys(fn (string $label, string $key) => [$key => in_array($key, ['stnk', 'bpkb'], true) ? 'available' : 'pending'])
            ->all();
    }

    public function mergeDocumentStatuses(array $statuses): void
    {
        $this->forceFill([
            'document_status' => array_merge($this->document_status ?? self::pendingDocumentStatuses(), $statuses),
        ])->save();
    }

    public function issuePaymentDocuments(): void
    {
        $this->mergeDocumentStatuses([
            'invoice' => 'ready',
            'receipt' => 'ready',
        ]);
    }

    public function issueSettlementDocuments(): void
    {
        $this->mergeDocumentStatuses([
            'invoice' => 'ready',
            'receipt' => 'ready',
            'handover_note' => 'ready',
        ]);

        $this->syncCompletedStateFromHandover();
    }

    public function issueHandoverDocuments(): void
    {
        $this->issueSettlementDocuments();
    }

    public function resetTransactionDocuments(): void
    {
        $this->mergeDocumentStatuses([
            'invoice' => 'pending',
            'receipt' => 'pending',
            'handover_note' => 'pending',
        ]);
    }

    public function isFullyPaid(): bool
    {
        if (in_array($this->status, ['paid', 'completed'], true)) {
            return true;
        }

        $payment = $this->payment;

        if (!$payment || $payment->status !== 'verified') {
            return false;
        }

        return (float) $payment->amount >= (float) $this->total;
    }

    public function areTransactionDocumentsReady(): bool
    {
        return in_array($this->document_status['invoice'] ?? 'pending', ['ready', 'submitted', 'done'], true)
            && in_array($this->document_status['receipt'] ?? 'pending', ['ready', 'submitted', 'done'], true)
            && $this->isHandoverNoteReady();
    }

    public function isHandoverNoteReady(): bool
    {
        return in_array($this->document_status['handover_note'] ?? 'pending', ['ready', 'submitted', 'done'], true);
    }

    public function syncCompletedStateFromHandover(): void
    {
        if ($this->status === 'cancelled' || !$this->isFullyPaid() || !$this->isHandoverNoteReady()) {
            return;
        }

        $updates = [];

        if ($this->status !== 'completed') {
            $updates['status'] = 'completed';
        }

        if (($this->follow_up_status ?? null) !== 'closed_won') {
            $updates['follow_up_status'] = 'closed_won';
        }

        if (!$this->approved_at) {
            $updates['approved_at'] = now();
        }

        if ($updates !== []) {
            $this->forceFill($updates)->save();
        }

        if ($this->car && $this->car->status !== 'sold') {
            $this->car->update(['status' => 'sold']);
        }
    }

    public function getInternalPaymentMethodLabelAttribute(): string
    {
        return TransactionLabelFormatter::paymentMethod($this->payment?->method);
    }

    public function getIsCreditPurchaseAttribute(): bool
    {
        return in_array((string) $this->payment_method, ['credit', 'va'], true);
    }

    public function getLeasingPartnerLabelAttribute(): ?string
    {
        return $this->leasing_partner ?: null;
    }

    public function getTransactionChannelLabelAttribute(): string
    {
        return TransactionLabelFormatter::transactionChannel($this->transaction_channel);
    }

    public function getSalesFlowLabelAttribute(): string
    {
        return TransactionLabelFormatter::salesFlow($this->sales_flow);
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

    public function getPurchaseMethodLabelAttribute(): string
    {
        return TransactionLabelFormatter::purchaseMethod($this->payment_method);
    }

    public function getCustomerJourneyTitleAttribute(): string
    {
        return $this->transaction_channel === 'offline' || $this->sales_flow === 'offline_showroom'
            ? 'Datang ke Showroom'
            : 'Online Website';
    }

    public function getCustomerJourneyDetailAttribute(): string
    {
        return match ($this->sales_flow) {
            'after_test_drive' => 'Dengan Test Drive',
            'offline_showroom' => 'Test Drive di Showroom',
            default => 'Tanpa Test Drive',
        };
    }

    public function getCustomerJourneyLabelAttribute(): string
    {
        return match (true) {
            $this->sales_flow === 'after_test_drive' => 'Pesan online setelah test drive',
            $this->sales_flow === 'offline_showroom' || $this->transaction_channel === 'offline' => 'Datang ke showroom dan test drive',
            default => 'Pesan online tanpa test drive',
        };
    }

    public function getCustomerPurchaseStatusLabelAttribute(): string
    {
        $paymentStatus = (string) ($this->payment?->status ?? 'pending');
        $paidAmount = (float) ($this->payment?->amount ?? 0);
        $hasCreditDpPayment = $this->is_credit_purchase && $paymentStatus === 'verified' && $paidAmount > 0 && $paidAmount < (float) $this->total;

        if ($this->status === 'cancelled') {
            return 'Batal Membeli';
        }

        if ($this->has_pending_cancellation_request) {
            return 'Menunggu Persetujuan Pembatalan';
        }

        if ($this->status === 'completed') {
            return 'Transaksi Selesai';
        }

        if ($this->is_credit_purchase) {
            if ($this->status === 'pending') {
                return 'Menunggu Persetujuan Kredit';
            }

            if ($hasCreditDpPayment) {
                return 'DP Kredit Dibayar';
            }

            if (in_array($this->status, ['confirmed', 'paid'], true)) {
                return 'Pengajuan Kredit Disetujui';
            }
        }

        if ($this->sales_flow === 'after_test_drive' && $paymentStatus === 'pending' && $this->status === 'pending') {
            return 'Sedang Negosiasi';
        }

        if ($this->sales_flow !== 'after_test_drive' && $paymentStatus === 'pending' && $this->status === 'pending') {
            return 'Langsung Booking';
        }

        if (in_array($paymentStatus, ['pending', 'rejected'], true) || in_array($this->status, ['pending', 'confirmed'], true)) {
            return 'Menunggu Pembayaran';
        }

        if (in_array($paymentStatus, ['paid', 'verified'], true) || $this->status === 'paid') {
            return 'Transaksi Selesai';
        }

        return 'Guest';
    }

    public function getHasPurchaseReviewAttribute(): bool
    {
        if ($this->relationLoaded('purchaseReview')) {
            return $this->purchaseReview !== null;
        }

        return $this->purchaseReview()->exists();
    }

    public function getReviewStatusLabelAttribute(): string
    {
        if ($this->has_purchase_review) {
            return 'Sudah di Review';
        }

        if (!in_array((string) $this->status, ['paid', 'completed'], true)) {
            return 'Menunggu Selesai';
        }

        if ($this->can_submit_purchase_review) {
            return 'Review unit sekarang';
        }

        if ($this->review_window_expired) {
            return 'Batas review habis';
        }

        return 'Review belum tersedia';
    }

    public function getPurchaseReviewStartedAtAttribute(): ?CarbonInterface
    {
        if (!in_array((string) $this->status, ['paid', 'completed'], true)) {
            return null;
        }

        $this->loadMissing('payment');

        return $this->approved_at
            ?? $this->payment?->verified_at
            ?? $this->payment?->paid_at
            ?? $this->updated_at
            ?? $this->created_at;
    }

    public function getPurchaseReviewDeadlineAtAttribute(): ?CarbonInterface
    {
        $startedAt = $this->purchase_review_started_at;
        if (!$startedAt) {
            return null;
        }

        return Carbon::parse($startedAt)->copy()->addDays(self::REVIEW_WINDOW_DAYS)->endOfDay();
    }

    public function getCanSubmitPurchaseReviewAttribute(): bool
    {
        $deadlineAt = $this->purchase_review_deadline_at;

        return !$this->has_purchase_review
            && $deadlineAt !== null
            && now()->lessThanOrEqualTo($deadlineAt);
    }

    public function getReviewWindowExpiredAttribute(): bool
    {
        $deadlineAt = $this->purchase_review_deadline_at;

        return !$this->has_purchase_review
            && $deadlineAt !== null
            && now()->greaterThan($deadlineAt);
    }

    public function getHasPendingCancellationRequestAttribute(): bool
    {
        return $this->customer_cancellation_requested_at !== null
            && !in_array((string) $this->status, ['cancelled', 'completed'], true);
    }

    public function getCanCustomerRequestCancellationAttribute(): bool
    {
        return in_array((string) $this->status, ['pending', 'confirmed', 'paid'], true)
            && !$this->has_pending_cancellation_request;
    }

    public function getHandoverDocumentChecklistAttribute(): array
    {
        $savedStatuses = collect($this->document_status ?? []);

        return collect(self::HANDOVER_DOCUMENT_TEMPLATES)
            ->map(function (string $label, string $key) use ($savedStatuses) {
                $rawStatus = $savedStatuses->get($key, 'pending');
                $status = match (true) {
                    in_array($key, ['stnk', 'bpkb'], true) && in_array($rawStatus, ['ready', 'submitted', 'done', 'available', 'tersedia', 'pending'], true) => 'tersedia',
                    in_array($key, ['stnk', 'bpkb'], true) && in_array($rawStatus, ['missing', 'rejected', 'unavailable', 'tidak_tersedia'], true) => 'tidak tersedia',
                    in_array($rawStatus, ['ready', 'submitted', 'done'], true) => 'siap',
                    in_array($rawStatus, ['missing', 'rejected'], true) => 'belum',
                    default => 'menunggu',
                };

                return [
                    'key' => $key,
                    'label' => $label,
                    'status' => $status,
                ];
            })
            ->values()
            ->all();
    }
}
