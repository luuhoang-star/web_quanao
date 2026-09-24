<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Color;
use Illuminate\Database\Seeder;

class CategoryColorSeeder extends Seeder
{
    public function run(): void
    {
        $allColorIds = Color::pluck('id')->toArray();

        foreach (Category::all() as $category) {
            $category->colors()->sync($allColorIds);
        }
    }
}
