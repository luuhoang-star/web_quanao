<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để thực hiện.');
        }

        $user = Auth::user();

        // Kiểm tra xem người dùng có vai trò user hay không
        if (! $user->role || $user->role->name !== 'user') {
            return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập vào trang này.');
        }

        // Kiểm tra tài khoản có bị khóa hay không
        if ($user->status === 'banned') {
            Auth::logout();

            return redirect()->route('login')->with('error', 'Tài khoản của bạn đã bị khóa.');
        }

        return $next($request);
    }
}
