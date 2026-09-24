<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\ReviewImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Lưu đánh giá sản phẩm.
     * CHỈ CHO PHÉP NGƯỜI ĐÃ MUA & ĐÃ NHẬN HÀNG THÀNH CÔNG (completed) ĐÁNH GIÁ.
     */
    public function store(Request $request, Product $product): RedirectResponse
    {
        $userId = Auth::id();

        // 1. KIỂM TRA ĐIỀU KIỆN: Người dùng đã nhận thành công (completed) sản phẩm này chưa?
        $hasPurchased = Order::where('user_id', $userId)
            ->where('status', 'completed')
            ->whereHas('items', function ($q) use ($product) {
                $q->where('product_id', $product->id)
                    ->orWhere('product_name', $product->name);
            })
            ->exists();

        if (! $hasPurchased) {
            return back()->with('error', 'Rất tiếc! Chỉ những khách hàng đã mua và nhận hàng thành công mới có quyền viết đánh giá.');
        }

        // 2. KIỂM TRA CHỐNG SPAM: Ngăn chặn gửi nhiều đánh giá cho cùng 1 sản phẩm
        $alreadyReviewed = Review::where('user_id', $userId)
            ->where('product_id', $product->id)
            ->exists();

        if ($alreadyReviewed) {
            return back()->with('error', 'Bạn đã gửi đánh giá cho sản phẩm này trước đó!');
        }

        // 3. Validate nội dung đánh giá
        $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:5', 'max:1000'],
            'images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'rating.required' => 'Vui lòng chọn số sao đánh giá (từ 1 đến 5 sao).',
            'comment.required' => 'Vui lòng nhập nội dung bình luận đánh giá.',
            'comment.min' => 'Nội dung bình luận phải có ít nhất 5 ký tự.',
            'images.*.image' => 'File tải lên phải là hình ảnh.',
        ]);

        // 4. Tạo đánh giá
        $review = Review::create([
            'user_id' => $userId,
            'product_id' => $product->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_approved' => true,
        ]);

        // 5. Lưu ảnh đính kèm (Review Images)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $imagePath = $file->store('reviews', 'public');
                ReviewImage::create([
                    'review_id' => $review->id,
                    'image_url' => $imagePath,
                ]);
            }
        }

        return back()->with('success', 'Cảm ơn bạn đã gửi đánh giá cho sản phẩm "'.$product->name.'"!');
    }

    /**
     * Cập nhật đánh giá của chính mình.
     */
    public function update(Request $request, Review $review): RedirectResponse
    {
        if ($review->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền chỉnh sửa đánh giá này.');
        }

        $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:5', 'max:1000'],
        ], [
            'rating.required' => 'Vui lòng chọn số sao đánh giá.',
            'comment.required' => 'Vui lòng nhập nội dung đánh giá.',
            'comment.min' => 'Nội dung đánh giá phải có ít nhất 5 ký tự.',
        ]);

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Cập nhật đánh giá thành công!');
    }

    /**
     * Xóa đánh giá của chính mình.
     */
    public function destroy(Review $review): RedirectResponse
    {
        if ($review->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền xóa đánh giá này.');
        }

        $review->delete();

        return back()->with('success', 'Đã xóa đánh giá thành công!');
    }
}
