<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    /**
     * Hiển thị form yêu cầu quên mật khẩu.
     */
    public function showLinkRequestForm(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Gửi email khôi phục mật khẩu qua Gmail / SMTP.
     */
    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
        ]);

        $email = strtolower(trim($request->email));
        $user = User::where('email', $email)->first();

        if (! $user) {
            return back()->with('status', 'Nếu địa chỉ email tồn tại trên hệ thống, chúng tôi đã gửi liên kết đặt lại mật khẩu đến hộp thư của bạn.');
        }

        // Tạo token ngẫu nhiên 64 ký tự
        $token = Str::random(64);

        // Lưu thông tin token vào bảng password_reset_tokens
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'email' => $email,
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        $resetUrl = route('password.reset', [
            'token' => $token,
            'email' => $email,
        ]);

        try {
            // Gửi Email khôi phục thực tế qua Gmail SMTP
            Mail::to($email)->send(new ResetPasswordMail($user, $token, $resetUrl));

            Log::info("Reset password email successfully sent to: {$email}");

            return back()->with('status', 'Chúng tôi đã gửi link đặt lại mật khẩu đến địa chỉ email '.$email.'. Vui lòng kiểm tra hộp thư Gmail của bạn!');
        } catch (\Exception $e) {
            Log::error("Failed to send reset email to {$email}: ".$e->getMessage());

            if (app()->environment('local')) {
                session()->flash('simulated_reset_url', $resetUrl);
            }

            return back()->with('status', 'Chúng tôi đã tạo link đặt lại mật khẩu cho tài khoản '.$email.'!');
        }
    }

    /**
     * Hiển thị form thiết lập mật khẩu mới.
     */
    public function showResetForm(Request $request, string $token): View
    {
        $email = $request->query('email', '');

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Xử lý cập nhật mật khẩu mới.
     */
    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'token.required' => 'Mã xác thực không hợp lệ.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.exists' => 'Tài khoản email này không tồn tại.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu mới không trùng khớp.',
        ]);

        $email = strtolower(trim($request->email));

        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (! $record) {
            return back()->withInput()->withErrors([
                'email' => 'Yêu cầu đặt lại mật khẩu này không tồn tại hoặc đã hết hạn!',
            ]);
        }

        // Kiểm tra HASH Token hợp lệ
        if (! Hash::check($request->token, $record->token)) {
            return back()->withInput()->withErrors([
                'email' => 'Link đặt lại mật khẩu không hợp lệ hoặc đã bị thay đổi!',
            ]);
        }

        // Kiểm tra Hạn Token (Hết hạn sau 15 phút)
        if (Carbon::parse($record->created_at)->addMinutes(15)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();

            return back()->withInput()->withErrors([
                'email' => 'Link đặt lại mật khẩu đã hết hạn (quá 15 phút). Vui lòng gửi yêu cầu mới!',
            ]);
        }

        // Cập nhật mật khẩu mới cho User
        $user = User::where('email', $email)->first();
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();

            // Đăng xuất các thiết bị khác nếu user đang đăng nhập
            if (Auth::check() && Auth::id() === $user->id) {
                Auth::logoutOtherDevices($request->password);
            }
        }

        // Xóa Token khỏi database sau khi đổi thành công (chống dùng lại token)
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return redirect()->route('login')->with('success', 'Đã đặt lại mật khẩu thành công! Vui lòng đăng nhập với mật khẩu mới.');
    }
}
