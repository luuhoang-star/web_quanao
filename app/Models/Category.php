<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'image',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function sizes(): BelongsToMany
    {
        return $this->belongsToMany(Size::class, 'category_size');
    }

    public function colors(): BelongsToMany
    {
        return $this->belongsToMany(Color::class, 'category_color');
    }

    /**
     * Trả về danh sách tất cả ID danh mục con và cháu của danh mục này (Chống circular parent).
     */
    public function getDescendantIds(): array
    {
        $ids = [];
        foreach ($this->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $child->getDescendantIds());
        }

        return $ids;
    }

    /**
     * Lấy danh sách danh mục theo cấu trúc Cây phân cấp (Cha đứng trước, các Con đứng ngay bên dưới).
     */
    public static function getTreeList(): Collection
    {
        $roots = static::with(['children' => fn ($q) => $q->orderBy('sort_order', 'asc')->orderBy('name', 'asc')])
            ->whereNull('parent_id')
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        $categories = collect();
        foreach ($roots as $root) {
            $categories->push($root);
            foreach ($root->children as $child) {
                $categories->push($child);
            }
        }

        $existingIds = $categories->pluck('id')->toArray();
        $orphans = static::whereNotIn('id', $existingIds)->orderBy('name', 'asc')->get();
        foreach ($orphans as $orphan) {
            $categories->push($orphan);
        }

        return $categories;
    }
}
