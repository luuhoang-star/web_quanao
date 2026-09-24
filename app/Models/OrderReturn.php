<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'type',
        'reason',
        'note',
        'images',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'status',
        'admin_note',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'images' => 'array',
        'processed_at' => 'datetime',
    ];

    public const TYPE_LABELS = [
        'exchange' => 'Đổi Size / Màu',
        'return' => 'Trả Hàng Hoàn Tiền',
    ];

    public const STATUS_LABELS = [
        'pending' => 'Chờ duyệt',
        'approved' => 'Đã chấp thuận',
        'rejected' => 'Đã từ chối',
        'completed' => 'Đã hoàn tất',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPE_LABELS[$this->type] ?? $this->type;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'pending' => ['text' => 'Chờ duyệt', 'class' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle', 'icon' => 'fa-clock'],
            'approved' => ['text' => 'Đã chấp thuận', 'class' => 'bg-info-subtle text-info-emphasis border border-info-subtle', 'icon' => 'fa-clipboard-check'],
            'rejected' => ['text' => 'Bị từ chối', 'class' => 'bg-danger-subtle text-danger border border-danger-subtle', 'icon' => 'fa-circle-xmark'],
            'completed' => ['text' => 'Đã hoàn tất', 'class' => 'bg-success-subtle text-success border border-success-subtle', 'icon' => 'fa-circle-check'],
            default => ['text' => $this->status, 'class' => 'bg-light text-dark border', 'icon' => 'fa-tag'],
        };
    }
}
