<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Size;
use Illuminate\Database\Seeder;

class CategorySizeSeeder extends Seeder
{
    public function run(): void
    {
        $sizes = Size::all()->keyBy('name');

        $clothingSizes = Size::whereIn('name', ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'FREE SIZE', 'FREESIZE'])->pluck('id')->toArray();
        $pantSizes = Size::whereIn('name', ['28', '29', '30', '31', '32', '33', '34'])->pluck('id')->toArray();
        $shoeSizes = Size::whereIn('name', ['38', '39', '40', '41', '42', '43'])->pluck('id')->toArray();
        $allSizeIds = Size::pluck('id')->toArray();

        foreach (Category::all() as $category) {
            $nameLower = mb_strtolower($category->name);

            if (str_contains($nameLower, 'quần')) {
                $category->sizes()->sync($pantSizes ?: $allSizeIds);
            } elseif (str_contains($nameLower, 'giày') || str_contains($nameLower, 'dép')) {
                $category->sizes()->sync($shoeSizes ?: $allSizeIds);
            } elseif (str_contains($nameLower, 'áo') || str_contains($nameLower, 'váy') || str_contains($nameLower, 'đầm') || str_contains($nameLower, 'thời trang')) {
                $category->sizes()->sync($clothingSizes ?: $allSizeIds);
            } else {
                $category->sizes()->sync($allSizeIds);
            }
        }
    }
}
