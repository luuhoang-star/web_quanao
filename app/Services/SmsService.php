<?php

namespace App\Services;

use App\Mail\OtpVerificationMail;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SmsService
{
    /**
     * Gửi mã OTP 6 số tới người dùng qua Gmail.
     */
    public static function sendOtp(User $user, string $otpCode): bool
    {
        return self::sendEmailOtp($user, $otpCode);
    }

    /**
     * Gửi OTP qua Gmail thực tế của người dùng.
     */
    public static function sendEmailOtp(User $user, string $otpCode): bool
    {
        if (empty($user->email)) {
            return false;
        }

        try {
            Mail::to($user->email)->send(new OtpVerificationMail($user, $otpCode));
            Log::info("Successfully sent OTP email to: {$user->email} (Code: {$otpCode})");

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send OTP email to {$user->email}: ".$e->getMessage());

            return false;
        }
    }
}
