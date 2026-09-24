<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    /**
     * Danh sách tất cả đánh giá sản phẩm phía Admin.
     */
    public function index(Request $request): View
    {
        $query = Review::with(['user', 'product', 'images'])->latest();

        // Lọc theo số sao đánh giá (1 - 5 sao)
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Lọc theo trạng thái ẩn / hiển thị
        if ($request->filled('status')) {
            if ($request->status === 'approved') {
                $query->where('is_approved', true);
            } elseif ($request->status === 'hidden') {
                $query->where('is_approved', false);
            }
        }

        // Tìm kiếm theo tên sản phẩm, tên khách hàng hoặc nội dung nhận xét
        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);
            $query->where(function ($q) use ($keyword) {
                $q->where('comment', 'like', "%{$keyword}%")
                    ->orWhereHas('user', function ($uq) use ($keyword) {
                        $uq->where('name', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('product', function ($pq) use ($keyword) {
                        $pq->where('name', 'like', "%{$keyword}%");
                    });
            });
        }

        $reviews = $query->paginate(10)->withQueryString();

        // Thống kê chung
        $totalReviews = Review::count();
        $averageRating = round(Review::avg('rating') ?: 5, 1);
        $approvedCount = Review::where('is_approved', true)->count();
        $hiddenCount = Review::where('is_approved', false)->count();

        return view('admin.reviews.index', compact('reviews', 'totalReviews', 'averageRating', 'approvedCount', 'hiddenCount'));
    }

    /**
     * Bật / Tắt trạng thái hiển thị đánh giá (Toggle Approval).
     */
    public function toggleApproval(Request $request, Review $review)
    {
        $newStatus = ! $review->is_approved;
        $review->update(['is_approved' => $newStatus]);

        $message = $newStatus
            ? 'Đã duyệt hiển thị đánh giá thành công!'
            : 'Đã ẩn đánh giá khỏi trang web thành công!';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_approved' => $newStatus,
                'message' => $message,
                'approvedCount' => Review::where('is_approved', true)->count(),
                'hiddenCount' => Review::where('is_approved', false)->count(),
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Quản trị viên phản hồi đánh giá của khách hàng.
     */
    public function reply(Request $request, Review $review): RedirectResponse
    {
        $request->validate([
            'admin_reply' => ['nullable', 'string', 'max:1000'],
        ]);

        $replyContent = trim((string) $request->input('admin_reply', ''));

        $review->update([
            'admin_reply' => $replyContent ?: null,
            'replied_at' => $replyContent ? now() : null,
        ]);

        $msg = $replyContent ? 'Đã lưu phản hồi đánh giá của khách hàng thành công!' : 'Đã xóa phản hồi đánh giá!';

        return back()->with('success', $msg);
    }

    /**
     * Xóa đánh giá sản phẩm.
     */
    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return back()->with('success', 'Đã xóa đánh giá sản phẩm thành công!');
    }
}
