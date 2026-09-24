<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Size extends Model
{
    use HasFactory;

    public const TYPES = [
        'LETTER' => 'Size Chữ (Áo, Váy: S, M, L, XL...)',
        'NUMBER' => 'Size Số (Quần: Jean, Tây, Kaki...)',
        'FREE_SIZE' => 'Kích cỡ tự do (Freesize)',
        'OTHER' => 'Phụ kiện / Khác',
    ];

    protected $fillable = [
        'name',
        'code',
        'size_type',
        'specifications',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
        'specifications' => 'array',
    ];

    public function getTypeLabelAttribute(): string
    {
        $legacyMap = [
            'TOPS' => 'Size Chữ (Áo, Váy: S, M, L, XL...)',
            'BOTTOMS' => 'Size Số (Quần: Jean, Tây, Kaki...)',
            'FREE' => 'Kích cỡ tự do (Freesize)',
        ];

        return self::TYPES[$this->size_type] ?? $legacyMap[$this->size_type] ?? 'Phụ kiện / Khác';
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_size');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_variants', 'size_id', 'product_id')->distinct();
    }
}
