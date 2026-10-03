<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'collection_id',
        'outfit',
        'name',
        'slug',
        'sku',
        'price',
        'sale_price',
        'description',
        'material',
        'care_instructions',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->orderByDesc('is_thumbnail')->orderBy('sort_order');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function flashSales(): HasMany
    {
        return $this->hasMany(FlashSale::class);
    }

    public function activeFlashSale()
    {
        $now = now();

        return $this->hasOne(FlashSale::class)
            ->where('is_active', true)
            ->where('start_time', '<=', $now)
            ->where('end_time', '>=', $now)
            ->whereRaw('(quantity - sold_quantity) > 0')
            ->where('sale_price', '>', 0);
    }

    public function favoredByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'wishlists')->withTimestamps();
    }

    /**
     * Lấy giá khuyến mãi thực tế của sản phẩm.
     */
    public function getEffectiveSalePriceAttribute(): ?float
    {
        if ($this->sale_price && $this->sale_price > 0 && $this->sale_price < $this->price) {
            return (float) $this->sale_price;
        }

        return null;
    }

    /**
     * Kiểm tra sản phẩm có đang giảm giá hay không (bao gồm cả sản phẩm chính & biến thể).
     */
    public function getHasDiscountAttribute(): bool
    {
        return ! is_null($this->effective_sale_price) && $this->effective_sale_price < $this->price;
    }

    /**
     * Phần trăm giảm giá tối đa.
     */
    public function getDiscountPercentAttribute(): int
    {
        $salePrice = $this->effective_sale_price;
        if ($salePrice && $salePrice < $this->price && $this->price > 0) {
            return (int) round((($this->price - $salePrice) / $this->price) * 100);
        }

        return 0;
    }

    /**
     * Relationship với các Lookbook có chứa sản phẩm này.
     */
    public function lookbooks(): BelongsToMany
    {
        return $this->belongsToMany(Lookbook::class, 'lookbook_product')
            ->withPivot('sort_order')
            ->withTimestamps();
    }

    /**
     * Relationship với các Outfit có chứa sản phẩm này.
     */
    public function outfits(): BelongsToMany
    {
        return $this->belongsToMany(Outfit::class, 'outfit_product')
            ->withPivot('sort_order')
            ->withTimestamps();
    }

    /**
     * Lấy URL ảnh đại diện hiển thị của sản phẩm (hỗ trợ cả local storage và URL ngoài).
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->relationLoaded('images') && $this->images->isNotEmpty()) {
            $thumb = $this->images->firstWhere('is_thumbnail', true) ?: $this->images->first();

            return $thumb->display_url;
        }

        if ($this->relationLoaded('primaryImage') && $this->primaryImage) {
            return $this->primaryImage->display_url;
        }

        $thumb = $this->primaryImage ?: $this->images->first();
        if ($thumb && method_exists($thumb, 'getDisplayUrlAttribute')) {
            return $thumb->display_url;
        }

        return 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=800&q=80';
    }

    /**
     * Alias cho getImageUrlAttribute.
     */
    public function getDisplayImageUrlAttribute(): string
    {
        return $this->image_url;
    }
}
