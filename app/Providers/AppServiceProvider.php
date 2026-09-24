<?php

namespace App\Providers;

use App\Http\Controllers\CartController;
use App\Models\FlashSale;
use Database\Seeders\FlashSaleAndFeaturedSeeder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        try {
            if (! Schema::hasColumn('products', 'is_featured')) {
                Schema::table('products', function ($table) {
                    $table->boolean('is_featured')->default(false)->after('is_active');
                });
            }

            if (! Schema::hasColumn('flash_sales', 'quantity')) {
                Schema::table('flash_sales', function ($table) {
                    $table->integer('quantity')->default(10)->after('end_time');
                    $table->integer('sold_quantity')->default(0)->after('quantity');
                });
            }

            if (FlashSale::count() === 0) {
                (new FlashSaleAndFeaturedSeeder)->run();
            }
        } catch (\Throwable $e) {
            // Silence exception
        }

        // Chia sẻ số lượng sản phẩm giỏ hàng với tất cả Blade Views
        View::composer('*', function ($view) {
            $view->with('cartCount', CartController::getCartCount());
        });
    }
}
