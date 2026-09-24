<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('admin')->check()) {
            return redirect()->route('admin.login')->with('error', 'Vui lòng đăng nhập tài khoản Quản trị viên.');
        }

        $user = Auth::guard('admin')->user();

        // Kiểm tra xem người dùng có vai trò admin hay không
        if (! $user->role || $user->role->name !== 'admin') {
            Auth::guard('admin')->logout();

            return redirect()->route('admin.login')->with('error', 'Tài khoản này không có quyền truy cập vào khu vực Quản trị viên.');
        }

        // Kiểm tra tài khoản có bị khóa hay không
        if ($user->status === 'banned') {
            Auth::guard('admin')->logout();

            return redirect()->route('admin.login')->with('error', 'Tài khoản của bạn đã bị khóa.');
        }

        return $next($request);
    }
}
