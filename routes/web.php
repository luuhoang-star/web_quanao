<?php

use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CollectionController;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FlashSaleController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\LookbookController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\OrderReturnController as AdminOrderReturnController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\SizeController;
use App\Http\Controllers\Api\PaymentWebhookController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MoMoPaymentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OutfitController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\UserAddressController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

// Trang chủ Website
Route::get('/', [HomeController::class, 'index'])->name('home');

// Thông tin Hỗ trợ Khách hàng & Chính sách (Hỗ trợ cả dạng /duong-dan và /duong-dan.html)
Route::get('/huong-dan-mua-hang{ext?}', [PageController::class, 'buyingGuide'])->where('ext', '\.html')->name('pages.buying-guide');
Route::get('/phuong-thuc-thanh-toan{ext?}', [PageController::class, 'paymentMethods'])->where('ext', '\.html')->name('pages.payment-methods');
Route::get('/tra-cuu-don-hang{ext?}', [PageController::class, 'orderLookup'])->where('ext', '\.html')->name('pages.order-lookup');
Route::get('/cham-soc-khach-hang{ext?}', [PageController::class, 'customerCare'])->where('ext', '\.html')->name('pages.customer-care');

Route::get('/chinh-sach-bao-mat{ext?}', [PageController::class, 'privacyPolicy'])->where('ext', '\.html')->name('pages.privacy-policy');
Route::get('/chinh-sach-van-chuyen{ext?}', [PageController::class, 'shippingPolicy'])->where('ext', '\.html')->name('pages.shipping-policy');
Route::get('/chinh-sach-doi-tra{ext?}', [PageController::class, 'returnPolicy'])->where('ext', '\.html')->name('pages.return-policy');
Route::get('/chinh-sach-kiem-hang{ext?}', [PageController::class, 'inspectionPolicy'])->where('ext', '\.html')->name('pages.inspection-policy');
Route::get('/dieu-khoan-dich-vu{ext?}', [PageController::class, 'termsOfService'])->where('ext', '\.html')->name('pages.terms-of-service');

// Đăng ký nhận bản tin khuyến mãi (Newsletter)
Route::post('/newsletter/subscribe', [PageController::class, 'subscribeNewsletter'])->name('newsletter.subscribe');

// Webhook Tự Động Xác Nhận Thanh Toán Chuyển Khoản (SePay, Casso, Ngân hàng)
Route::match(['get', 'post'], '/api/webhook/payment', [PaymentWebhookController::class, 'handle'])->name('api.webhook.payment');

// Cổng thanh toán MoMo (Return Callback & IPN Webhook)
Route::get('/payment/momo/callback', [MoMoPaymentController::class, 'callback'])->name('payment.momo.callback');
Route::post('/api/webhook/momo', [MoMoPaymentController::class, 'ipn'])->name('api.webhook.momo');

// SEO Routes (Sitemap XML & Robots.txt)
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('seo.sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('seo.robots');

// Trang danh sách sản phẩm cửa hàng (Shop Listing: Search, Filter, Sort, Pagination)
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/search-suggestions', [ShopController::class, 'searchSuggestions'])->name('shop.searchSuggestions');
Route::get('/shop/quick-view/{product}', [ShopController::class, 'quickView'])->name('shop.quickView');

// Trang Flash Sale giảm giá (/flash-sale)
Route::get('/flash-sale', [ShopController::class, 'flashSale'])->name('shop.flashSale');

// Trang gợi ý Outfit theo slug (/outfits/office, /outfits/summer, /outfits/sport)
Route::get('/outfits/{slug}', [OutfitController::class, 'show'])->name('outfits.show');

// Trang chi tiết sản phẩm (Gallery, Review, Related Products)
Route::get('/product/{slug}', [ShopController::class, 'show'])->name('shop.show');

// Authentication routes dành cho khách (guest người dùng)
Route::middleware('guest:web')->group(function () {
    // Đăng ký Khách hàng
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Đăng nhập Khách hàng (/login -> Về trang chủ /)
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    // Quên mật khẩu & Đặt lại mật khẩu qua Gmail thực tế
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');
});

// Authentication routes dành cho khách Admin (/admin/login)
Route::middleware('guest:admin')->group(function () {
    Route::get('/admin/login', [AuthController::class, 'showAdminLoginForm'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'adminLogin'])->middleware('throttle:5,1')->name('admin.processLogin');
});

// Quản lý Giỏ hàng (Cart: Add, Update, Remove, Clear) - Cho phép cả Khách & Thành viên
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/cart/json', [CartController::class, 'jsonContent'])->name('cart.json');
Route::post('/cart/add', [CartController::class, 'store'])->name('cart.store');
Route::put('/cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::delete('/cart/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');

// Authentication routes dành cho thành viên người dùng (auth:web & check.banned)
Route::middleware(['auth:web', 'check.banned'])->group(function () {
    // Đăng xuất
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Xác thực số điện thoại qua OTP 6 số
    Route::get('/verify-otp', [AuthController::class, 'showOtpForm'])->name('otp.show');
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('otp.verify');
    Route::post('/resend-otp', [AuthController::class, 'resendOtp'])->name('otp.resend');

    // Trang cá nhân người dùng (/user & /profile)
    Route::get('/user', [ProfileController::class, 'show'])->name('user.dashboard');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.changePassword');

    // Áp dụng & Hủy mã giảm giá (Coupons)
    Route::post('/coupon/apply', [OrderController::class, 'applyCoupon'])->name('coupon.apply');
    Route::post('/coupon/remove', [OrderController::class, 'removeCoupon'])->name('coupon.remove');

    // Quản lý Đặt hàng & Lịch sử Đơn hàng (Orders & Checkout)
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('orders.checkout');
    Route::post('/checkout', [OrderController::class, 'processCheckout'])->name('orders.processCheckout');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order_code}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order_code}/check-payment-status', [OrderController::class, 'checkPaymentStatus'])->name('orders.checkPaymentStatus');
    Route::get('/orders/{order_code}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{order}/reorder', [OrderController::class, 'reorder'])->name('orders.reorder');
    Route::post('/orders/{order}/return', [OrderController::class, 'requestReturn'])->name('orders.requestReturn');
    Route::post('/orders/{order}/momo-pay', [MoMoPaymentController::class, 'create'])->name('orders.momoPay');

    // Đánh giá sản phẩm (Reviews)
    Route::post('/product/{product}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Quản lý Địa chỉ giao hàng người dùng (User Addresses)
    Route::post('/user/addresses', [UserAddressController::class, 'store'])->name('user.addresses.store');
    Route::put('/user/addresses/{address}', [UserAddressController::class, 'update'])->name('user.addresses.update');
    Route::post('/user/addresses/{address}/set-default', [UserAddressController::class, 'setDefault'])->name('user.addresses.setDefault');
    Route::delete('/user/addresses/{address}', [UserAddressController::class, 'destroy'])->name('user.addresses.destroy');

    // Quản lý Sản phẩm Yêu thích (Wishlist: View, Toggle, Remove)
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{product}/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::delete('/wishlist/{wishlist}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
});

// Admin routes (Dành riêng cho Quản trị viên - admin)
Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('/logout', [AuthController::class, 'adminLogout'])->name('logout');
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Quản lý Đơn hàng Admin (Admin Orders)
    Route::get('orders/export', [AdminOrderController::class, 'export'])->name('orders.export');
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::get('orders/{order}/invoice', [AdminOrderController::class, 'invoice'])->name('orders.invoice');
    Route::put('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::put('orders/{order}/payment-status', [AdminOrderController::class, 'updatePaymentStatus'])->name('orders.updatePaymentStatus');

    // Quản lý Yêu Cầu Đổi / Trả Hàng (Admin Order Returns)
    Route::get('returns', [AdminOrderReturnController::class, 'index'])->name('returns.index');
    Route::get('returns/{return}', [AdminOrderReturnController::class, 'show'])->name('returns.show');
    Route::post('returns/{return}/approve', [AdminOrderReturnController::class, 'approve'])->name('returns.approve');
    Route::post('returns/{return}/reject', [AdminOrderReturnController::class, 'reject'])->name('returns.reject');

    // Quản lý Đánh Giá Admin (Admin Reviews)
    Route::get('reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::put('reviews/{review}/toggle-approval', [AdminReviewController::class, 'toggleApproval'])->name('reviews.toggleApproval');
    Route::put('reviews/{review}/reply', [AdminReviewController::class, 'reply'])->name('reviews.reply');
    Route::delete('reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

    // Quản lý Mã Giảm Giá Admin (Admin Coupons)
    Route::put('coupons/{coupon}/toggle-status', [CouponController::class, 'toggleStatus'])->name('coupons.toggleStatus');
    Route::resource('coupons', CouponController::class)->except(['show']);

    // Quản lý Khách hàng Admin (Admin Customers)
    Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::put('customers/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('customers.toggleStatus');

    // CRUD Sản phẩm (Products) & Biến thể sản phẩm (Product Variants)
    Route::post('products/bulk-action', [ProductController::class, 'bulkAction'])->name('products.bulkAction');
    Route::post('products/{product}/duplicate', [ProductController::class, 'duplicate'])->name('products.duplicate');
    Route::delete('products/images/{image}', [ProductController::class, 'deleteImage'])->name('products.images.destroy');
    Route::patch('products/images/{image}/color', [ProductController::class, 'updateImageColor'])->name('products.images.update-color');
    Route::put('products/{product}/toggle-featured', [ProductController::class, 'toggleFeatured'])->name('products.toggleFeatured');
    Route::put('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggleStatus');
    Route::post('products/{product}/variants/batch-generate', [ProductVariantController::class, 'generateBatch'])->name('products.variants.batchGenerate');
    Route::post('products/{product}/variants/bulk-action', [ProductVariantController::class, 'bulkAction'])->name('products.variants.bulkAction');
    Route::put('products/{product}/variants/{variant}/toggle-status', [ProductVariantController::class, 'toggleStatus'])->name('products.variants.toggleStatus');
    Route::resource('products', ProductController::class)->except(['show']);
    Route::resource('products.variants', ProductVariantController::class)->except(['show']);

    // CRUD Flash Sale (Admin Flash Sales)
    Route::post('flash-sales/bulk-action', [FlashSaleController::class, 'bulkAction'])->name('flash-sales.bulkAction');
    Route::put('flash-sales/{flashSale}/toggle-status', [FlashSaleController::class, 'toggleStatus'])->name('flash-sales.toggleStatus');
    Route::resource('flash-sales', FlashSaleController::class)->except(['show']);

    // CRUD Danh mục (Categories)
    Route::post('categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');
    Route::post('categories/bulk-action', [CategoryController::class, 'bulkAction'])->name('categories.bulkAction');
    Route::put('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggleStatus');
    Route::get('categories/{category}/products', [CategoryController::class, 'getProducts'])->name('categories.products');
    Route::post('categories/{category}/products/delete-selected', [CategoryController::class, 'deleteSelectedProducts'])->name('categories.products.deleteSelected');
    Route::delete('categories/{category}/products/{product}', [CategoryController::class, 'deleteProduct'])->name('categories.products.destroy');
    Route::delete('categories/{category}/products', [CategoryController::class, 'deleteAllProducts'])->name('categories.products.destroyAll');
    Route::resource('categories', CategoryController::class)->except(['show']);

    // CRUD Màu sắc (Colors)
    Route::post('colors/bulk-action', [ColorController::class, 'bulkAction'])->name('colors.bulkAction');
    Route::get('colors/{color}/variants', [ColorController::class, 'getVariants'])->name('colors.variants');
    Route::put('colors/{color}/toggle-status', [ColorController::class, 'toggleStatus'])->name('colors.toggleStatus');
    Route::post('colors/{color}/transfer', [ColorController::class, 'transferColor'])->name('colors.transfer');
    Route::delete('colors/{color}/detach-product/{product}', [ColorController::class, 'detachProduct'])->name('colors.detachProduct');
    Route::put('colors/{color}/toggle-product-status/{product}', [ColorController::class, 'toggleProductColorStatus'])->name('colors.toggleProductStatus');
    Route::resource('colors', ColorController::class)->except(['show']);

    // CRUD Kích cỡ (Sizes)
    Route::post('sizes/bulk-action', [SizeController::class, 'bulkAction'])->name('sizes.bulkAction');
    Route::get('sizes/{size}/variants', [SizeController::class, 'getVariants'])->name('sizes.variants');
    Route::match(['put', 'post'], 'sizes/{size}/toggle-status', [SizeController::class, 'toggleStatus'])->name('sizes.toggleStatus');
    Route::resource('sizes', SizeController::class)->except(['show']);

    // CRUD Lookbook (@ATINO_STYLE / Lookbook Gallery)
    Route::put('lookbooks/{lookbook}/toggle-status', [LookbookController::class, 'toggleStatus'])->name('lookbooks.toggleStatus');
    Route::resource('lookbooks', LookbookController::class)->except(['show']);

    // CRUD Outfit Phối Sẵn (Admin Outfits)
    Route::put('outfits/{outfit}/toggle-status', [App\Http\Controllers\Admin\OutfitController::class, 'toggleStatus'])->name('outfits.toggleStatus');
    Route::resource('outfits', App\Http\Controllers\Admin\OutfitController::class)->except(['show']);

    // CRUD Bộ Sưu Tập (Admin Collections)
    Route::put('collections/{collection}/toggle-status', [CollectionController::class, 'toggleStatus'])->name('collections.toggleStatus');
    Route::resource('collections', CollectionController::class)->except(['show']);

    // Quản lý Kho Hàng (Inventory Management)
    Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('inventory/import', [InventoryController::class, 'import'])->name('inventory.import');
    Route::post('inventory/export', [InventoryController::class, 'export'])->name('inventory.export');
    Route::post('inventory/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');
    Route::post('inventory/{variant}/threshold', [InventoryController::class, 'updateThreshold'])->name('inventory.threshold');
    Route::get('inventory/movements', [InventoryController::class, 'movements'])->name('inventory.movements');
    Route::get('inventory/export-excel', [InventoryController::class, 'exportExcel'])->name('inventory.exportExcel');

    // Thông tin cá nhân & Đổi mật khẩu Admin (Admin Profile)
    Route::get('profile', [AdminProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [AdminProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [AdminProfileController::class, 'changePassword'])->name('profile.changePassword');

    // Cấu hình Hệ thống (Admin Settings)
    Route::get('settings', [AdminSettingsController::class, 'index'])->name('settings.index');
    Route::post('settings', [AdminSettingsController::class, 'update'])->name('settings.update');
    Route::post('settings/test-telegram', [AdminSettingsController::class, 'testTelegram'])->name('settings.testTelegram');
});
