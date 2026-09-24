<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Outfit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'badge',
        'tagline',
        'gradient',
        'hero_image',
        'description',
        'items_included',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'items_included' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Relationship với các sản phẩm thuộc Outfit này.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'outfit_product')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order', 'asc');
    }

    /**
     * Helper lấy đường dẫn hình ảnh hero của Outfit.
     */
    public function getHeroImageUrlAttribute(): string
    {
        if (! $this->hero_image) {
            return 'https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=1200&q=80';
        }

        if (str_starts_with($this->hero_image, 'http://') || str_starts_with($this->hero_image, 'https://')) {
            return $this->hero_image;
        }

        return asset('storage/'.$this->hero_image);
    }
}
