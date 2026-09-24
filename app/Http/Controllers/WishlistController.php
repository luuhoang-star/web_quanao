<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WishlistController extends Controller
{
    /**
     * Danh sách sản phẩm yêu thích của người dùng.
     */
    public function index(): View
    {
        $wishlists = Wishlist::with(['product.images', 'product.category'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(12);

        return view('wishlist.index', compact('wishlists'));
    }

    /**
     * Bật / Tắt trạng thái yêu thích sản phẩm (Toggle Wishlist).
     */
    public function toggle(Product $product, Request $request): RedirectResponse|JsonResponse
    {
        if (! Auth::check()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng đăng nhập để lưu sản phẩm yêu thích!',
                    'redirect' => route('login'),
                ], 401);
            }

            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để lưu sản phẩm yêu thích!');
        }

        $userId = Auth::id();

        $existing = Wishlist::where('user_id', $userId)
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $count = Wishlist::where('user_id', $userId)->count();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'added' => false,
                    'message' => 'Đã xóa sản phẩm "'.$product->name.'" khỏi danh sách yêu thích!',
                    'count' => $count,
                ]);
            }

            return back()->with('success', 'Đã xóa sản phẩm "'.$product->name.'" khỏi danh sách yêu thích!');
        }

        Wishlist::create([
            'user_id' => $userId,
            'product_id' => $product->id,
        ]);

        $count = Wishlist::where('user_id', $userId)->count();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'added' => true,
                'message' => 'Đã thêm sản phẩm "'.$product->name.'" vào danh sách yêu thích!',
                'count' => $count,
            ]);
        }

        return back()->with('success', 'Đã thêm sản phẩm "'.$product->name.'" vào danh sách yêu thích!');
    }

    /**
     * Xóa 1 mục khỏi danh sách yêu thích.
     */
    public function destroy(Wishlist $wishlist, Request $request): RedirectResponse|JsonResponse
    {
        if ($wishlist->user_id !== Auth::id()) {
            abort(403);
        }

        $wishlist->delete();
        $count = Wishlist::where('user_id', Auth::id())->count();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa sản phẩm khỏi danh sách yêu thích!',
                'count' => $count,
            ]);
        }

        return back()->with('success', 'Đã xóa sản phẩm khỏi danh sách yêu thích!');
    }
}
