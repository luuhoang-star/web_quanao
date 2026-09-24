<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlashSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sale_price',
        'start_time',
        'end_time',
        'quantity',
        'sold_quantity',
        'is_active',
    ];

    protected $casts = [
        'sale_price' => 'decimal:2',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'quantity' => 'integer',
        'sold_quantity' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Sản phẩm được áp dụng Flash Sale.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope lấy các chương trình Flash Sale đang thực sự có hiệu lực.
     */
    public function scopeActive(Builder $query): Builder
    {
        $now = now();

        return $query->where('is_active', true)
            ->where('start_time', '<=', $now)
            ->where('end_time', '>=', $now)
            ->whereRaw('(quantity - sold_quantity) > 0')
            ->whereHas('product', function ($q) {
                $q->where('is_active', true)
                    ->where('price', '>', 0)
                    ->whereColumn('flash_sales.sale_price', '<', 'products.price')
                    ->where('flash_sales.sale_price', '>', 0);
            });
    }

    /**
     * Số lượng Flash Sale còn lại.
     */
    public function getRemainingQuantityAttribute(): int
    {
        return max(0, (int) $this->quantity - (int) $this->sold_quantity);
    }

    /**
     * Phần trăm giảm Flash Sale chính xác từ giá gốc.
     */
    public function getDiscountPercentAttribute(): int
    {
        if ($this->product && $this->product->price > 0 && $this->sale_price < $this->product->price) {
            return (int) round((($this->product->price - $this->sale_price) / $this->product->price) * 100);
        }

        return 0;
    }

    /**
     * Kiểm tra chương trình Flash Sale có đang diễn ra hay không.
     */
    public function getIsOngoingAttribute(): bool
    {
        $now = now();

        return $this->is_active
            && $this->start_time <= $now
            && $this->end_time >= $now
            && $this->remaining_quantity > 0
            && $this->product
            && $this->product->is_active
            && $this->sale_price > 0
            && $this->sale_price < $this->product->price;
    }
}
