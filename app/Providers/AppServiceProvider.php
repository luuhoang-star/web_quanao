<?php

namespace App\Providers;

use App\Http\Controllers\CartController;
use Illuminate\Pagination\Paginator;
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

        // Chia sẻ số lượng sản phẩm giỏ hàng với tất cả Blade Views
        View::composer('*', function ($view) {
            $view->with('cartCount', CartController::getCartCount());
        });
    }
}
