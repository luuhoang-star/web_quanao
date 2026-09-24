<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Subscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Hướng dẫn mua hàng.
     */
    public function buyingGuide(): View
    {
        return view('pages.buying-guide');
    }

    /**
     * Phương thức thanh toán.
     */
    public function paymentMethods(): View
    {
        return view('pages.payment-methods');
    }

    /**
     * Tra cứu đơn hàng dành cho khách hàng.
     */
    public function orderLookup(Request $request): View
    {
        $keyword = trim($request->get('search', ''));
        $orders = collect();

        if ($keyword !== '') {
            $orders = Order::with([
                'items.productVariant.product.primaryImage',
                'items.productVariant.color',
                'items.productVariant.size',
                'payment',
                'statusHistories',
            ])
                ->where('order_code', $keyword)
                ->orWhere('recipient_phone', $keyword)
                ->latest()
                ->take(5)
                ->get();
        }

        return view('pages.order-lookup', compact('keyword', 'orders'));
    }

    /**
     * Chăm sóc khách hàng & Trung tâm trợ giúp.
     */
    public function customerCare(): View
    {
        return view('pages.customer-care');
    }

    /**
     * Chính sách bảo mật.
     */
    public function privacyPolicy(): View
    {
        return view('pages.privacy-policy');
    }

    /**
     * Chính sách vận chuyển.
     */
    public function shippingPolicy(): View
    {
        return view('pages.shipping-policy');
    }

    /**
     * Chính sách đổi trả 30 ngày.
     */
    public function returnPolicy(): View
    {
        return view('pages.return-policy');
    }

    /**
     * Chính sách kiểm hàng khi nhận.
     */
    public function inspectionPolicy(): View
    {
        return view('pages.inspection-policy');
    }

    /**
     * Điều khoản dịch vụ.
     */
    public function termsOfService(): View
    {
        return view('pages.terms-of-service');
    }

    /**
     * Xử lý đăng ký nhận bản tin khuyến mãi.
     */
    public function subscribeNewsletter(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'max:255'],
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email của bạn.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'email.max' => 'Địa chỉ email không được vượt quá 255 ký tự.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $email = trim(mb_strtolower($request->email));

        $existing = Subscriber::where('email', $email)->first();
        if ($existing) {
            return response()->json([
                'success' => true,
                'message' => 'Email của bạn đã được đăng ký nhận tin trên hệ thống từ trước!',
                'already_subscribed' => true,
            ]);
        }

        Subscriber::create([
            'email' => $email,
            'ip_address' => $request->ip(),
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Chúc mừng bạn đã đăng ký nhận bản tin và ưu đãi độc quyền từ Atino thành công!',
        ]);
    }
}
