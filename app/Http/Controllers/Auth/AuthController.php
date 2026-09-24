<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\CartController;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Role;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Hiển thị form đăng ký.
     */
    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    /**
     * Xử lý đăng ký tài khoản.
     */
    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone', 'regex:/^(0[3|5|7|8|9])[0-9]{8}$/'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không hợp lệ.',
            'email.unique' => 'Email này đã được sử dụng.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.unique' => 'Số điện thoại này đã được đăng ký tài khoản.',
            'phone.regex' => 'Số điện thoại không đúng định dạng (Ví dụ: 0987654321).',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải chứa ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $userRole = Role::where('name', 'user')->first();
        $roleId = $userRole ? $userRole->id : 2;

        // Tạo 6 số OTP ngẫu nhiên
        $rawOtp = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        $user = User::create([
            'role_id' => $roleId,
            'name' => $request->name,
            'email' => strtolower(trim($request->email)),
            'phone' => trim($request->phone),
            'phone_verified_at' => null,
            'otp_code_hash' => Hash::make($rawOtp),
            'otp_expires_at' => now()->addMinutes(5),
            'otp_sent_at' => now(),
            'otp_attempts' => 0,
            'password' => Hash::make($request->password),
            'status' => 'active',
        ]);

        // Gửi OTP thực tế qua Email Gmail
        SmsService::sendOtp($user, $rawOtp);

        // Tạo giỏ hàng mặc định cho người dùng mới
        Cart::create([
            'user_id' => $user->id,
        ]);

        // Đăng nhập người dùng và nạp giỏ hàng
        Auth::guard('web')->login($user);
        CartController::mergeSessionCartToUser($user->id);

        return redirect()->route('otp.show')->with('success', 'Đăng ký thành công! Mã OTP xác thực 6 số đã được gửi tới Gmail '.$user->email);
    }

    /**
     * Hiển thị giao diện Nhập Mã OTP 6 số qua Email.
     */
    public function showOtpForm(): View|RedirectResponse
    {
        $user = Auth::guard('web')->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('home')->with('info', 'Địa chỉ email của bạn đã được xác thực trước đó.');
        }

        return view('auth.otp', compact('user'));
    }

    /**
     * Xử lý xác thực OTP 6 số (Backend HASH check & Rate limit).
     */
    public function verifyOtp(Request $request): RedirectResponse
    {
        $user = Auth::guard('web')->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('home');
        }

        // Đọc mã OTP 6 số từ form (ghép từ 6 ô nhập hoặc input duy nhất)
        $inputOtp = $request->otp;
        if (is_array($request->otp_digits)) {
            $inputOtp = implode('', $request->otp_digits);
        }

        $inputOtp = trim((string) $inputOtp);

        if (strlen($inputOtp) !== 6 || ! ctype_digit($inputOtp)) {
            return back()->with('error', 'Vui lòng nhập đầy đủ 6 chữ số mã OTP.');
        }

        // 1. Kiểm tra giới hạn nhập sai (tối đa 5 lần)
        if ($user->otp_attempts >= 5) {
            return back()->with('error', 'Mã OTP đã bị nhập sai quá 5 lần. Vui lòng bấm "Gửi lại mã OTP" để lấy mã mới.');
        }

        // 2. Kiểm tra thời gian hết hạn (5 phút)
        if (! $user->otp_expires_at || now()->greaterThan($user->otp_expires_at)) {
            return back()->with('error', 'Mã OTP đã hết hạn (5 phút). Vui lòng bấm "Gửi lại mã OTP" để nhận mã mới.');
        }

        // 3. Kiểm tra tính đúng đắn của HASH mã OTP ở Backend
        if (! Hash::check($inputOtp, $user->otp_code_hash)) {
            $user->increment('otp_attempts');
            $remainingAttempts = 5 - $user->otp_attempts;

            if ($remainingAttempts <= 0) {
                return back()->with('error', 'Bạn đã nhập sai mã OTP 5 lần. Mã này đã bị vô hiệu hóa, vui lòng gửi lại mã mới!');
            }

            return back()->with('error', 'Mã OTP không chính xác! Bạn còn '.$remainingAttempts.' lần thử.');
        }

        // Xác thực thành công: Cập nhật email_verified_at, phone_verified_at & xóa thông tin OTP cũ
        $user->update([
            'email_verified_at' => now(),
            'phone_verified_at' => $user->phone_verified_at ?? now(),
            'otp_code_hash' => null,
            'otp_expires_at' => null,
            'otp_sent_at' => null,
            'otp_attempts' => 0,
        ]);

        return redirect()->route('home')->with('success', 'Xác thực tài khoản qua Gmail thành công! Tài khoản của bạn đã được kích hoạt hoàn toàn.');
    }

    /**
     * Gửi lại mã OTP qua Gmail (Rate Limit 60s cooldown).
     */
    public function resendOtp(Request $request): RedirectResponse
    {
        $user = Auth::guard('web')->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('home');
        }

        // Kiểm tra thời gian chờ 60 giây (Resend Cooldown)
        if ($user->otp_sent_at && now()->diffInSeconds($user->otp_sent_at) < 60) {
            $secondsLeft = 60 - (int) now()->diffInSeconds($user->otp_sent_at);

            return back()->with('error', 'Vui lòng chờ '.$secondsLeft.' giây trước khi yêu cầu gửi lại mã OTP mới.');
        }

        // Sinh mã OTP 6 số mới
        $rawOtp = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'otp_code_hash' => Hash::make($rawOtp),
            'otp_expires_at' => now()->addMinutes(5),
            'otp_sent_at' => now(),
            'otp_attempts' => 0,
        ]);

        // Gửi OTP thực tế qua Email Gmail
        SmsService::sendOtp($user, $rawOtp);

        return back()->with('success', 'Mã OTP 6 số mới đã được gửi thành công tới Gmail '.$user->email.'!');
    }

    /**
     * Hiển thị form đăng nhập Khách hàng (/login).
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('home');
        }

        return view('auth.login');
    }

    /**
     * Xử lý đăng nhập Khách hàng (/login) -> Về Trang Chủ (/).
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không hợp lệ.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $user = User::where('email', strtolower(trim($request->email)))->first();
        if ($user && $user->status === 'banned') {
            return back()->withInput()->with('error', 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ hỗ trợ.');
        }

        $remember = $request->has('remember');

        if (Auth::guard('web')->attempt(['email' => strtolower(trim($request->email)), 'password' => $request->password], $remember)) {
            $request->session()->regenerate();

            // Hợp nhất giỏ hàng guest session vào DB user cart
            CartController::mergeSessionCartToUser(Auth::guard('web')->id());

            return redirect()->route('home')->with('success', 'Đăng nhập thành công! Chào mừng bạn quay trở lại.');
        }

        return back()->withInput()->with('error', 'Email hoặc mật khẩu không chính xác.');
    }

    /**
     * Hiển thị form đăng nhập Admin (/admin/login).
     */
    public function showAdminLoginForm(): View|RedirectResponse
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.admin-login');
    }

    /**
     * Xử lý đăng nhập Admin (/admin/login) -> Về Trang Admin (/admin).
     */
    public function adminLogin(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email quản trị.',
            'email.email' => 'Địa chỉ email không hợp lệ.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $user = User::where('email', strtolower(trim($request->email)))->first();
        if (! $user || ! $user->role || $user->role->name !== 'admin') {
            return back()->withInput()->with('error', 'Email này không thuộc tài khoản Quản trị viên!');
        }

        if ($user->status === 'banned') {
            return back()->withInput()->with('error', 'Tài khoản Quản trị viên của bạn đã bị khóa.');
        }

        $remember = $request->has('remember');

        if (Auth::guard('admin')->attempt(['email' => strtolower(trim($request->email)), 'password' => $request->password], $remember)) {
            $request->session()->regenerate();

            return redirect()->route('admin.dashboard')->with('success', 'Chào mừng Quản trị viên đăng nhập hệ thống!');
        }

        return back()->withInput()->with('error', 'Email hoặc mật khẩu không chính xác.');
    }

    /**
     * Xử lý đăng xuất Người dùng (User).
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Đã đăng xuất tài khoản người dùng thành công!');
    }

    /**
     * Xử lý đăng xuất Quản trị viên (Admin).
     */
    public function adminLogout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Đã đăng xuất tài khoản Quản trị viên thành công!');
    }
}
