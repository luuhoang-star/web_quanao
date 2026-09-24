<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Category;
use Illuminate\Contracts\Console\Kernel;

// Re-index root categories
$roots = Category::whereNull('parent_id')->orderBy('sort_order', 'asc')->orderBy('id', 'asc')->get();
$parentOrder = 1;

foreach ($roots as $root) {
    $root->update(['sort_order' => $parentOrder++]);

    // Re-index child categories per parent_id
    $childOrder = 1;
    foreach ($root->children()->orderBy('sort_order', 'asc')->orderBy('id', 'asc')->get() as $child) {
        $child->update(['sort_order' => $childOrder++]);
    }
}

echo "Successfully updated per-parent sort orders for all categories!\n";
