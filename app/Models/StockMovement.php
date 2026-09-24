<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_variant_id',
        'user_id',
        'type',
        'quantity_change',
        'before_quantity',
        'after_quantity',
        'cost_price',
        'supplier',
        'reference_code',
        'note',
    ];

    protected $casts = [
        'quantity_change' => 'integer',
        'before_quantity' => 'integer',
        'after_quantity' => 'integer',
        'cost_price' => 'decimal:2',
    ];

    public const TYPES = [
        'import' => 'Nhập kho',
        'export' => 'Xuất kho',
        'adjustment' => 'Điều chỉnh kiểm kê',
        'order_sale' => 'Bán hàng (Đơn hàng)',
        'order_cancel' => 'Hủy đơn (Hoàn kho)',
        'order_return' => 'Trả hàng (Hoàn kho)',
    ];

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getTypeBadgeClassAttribute(): string
    {
        return match ($this->type) {
            'import' => 'bg-success-subtle text-success border-success-subtle',
            'export' => 'bg-danger-subtle text-danger border-danger-subtle',
            'adjustment' => 'bg-warning-subtle text-warning border-warning-subtle',
            'order_sale' => 'bg-primary-subtle text-primary border-primary-subtle',
            'order_cancel', 'order_return' => 'bg-info-subtle text-info border-info-subtle',
            default => 'bg-secondary-subtle text-secondary border-secondary-subtle',
        };
    }
}
