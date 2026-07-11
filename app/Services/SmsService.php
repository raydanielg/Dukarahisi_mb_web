<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Generate a random OTP code.
     */
    public static function generateOtp(int $length = 6): string
    {
        return str_pad((string) random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
    }

    /**
     * Send OTP via SMS using configured provider.
     */
    public static function sendOtp(string $phoneNumber, string $otp): bool
    {
        $provider = config('services.sms.provider', 'beem');
        $message = "Elimu Store: Namba yako ya uthibitisho ni {$otp}. Muda wake ni dakika 5.";

        try {
            if ($provider === 'beem') {
                return self::sendBeem($phoneNumber, $message);
            }

            // Fallback: log only for local development
            Log::info("SMS OTP to {$phoneNumber}: {$otp}");
            return true;
        } catch (\Exception $e) {
            Log::error('SMS sending failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send SMS via Beem Africa API.
     */
    protected static function sendBeem(string $phoneNumber, string $message): bool
    {
        $apiKey = config('services.sms.beem_api_key');
        $secretKey = config('services.sms.beem_secret_key');
        $senderId = config('services.sms.beem_sender_id', 'ELIMUSTORE');

        if (!$apiKey || !$secretKey) {
            Log::warning('Beem SMS credentials not configured.');
            return false;
        }

        $response = Http::withBasicAuth($apiKey, $secretKey)
            ->post('https://apisms.beem.africa/v1/send', [
                'source_addr' => $senderId,
                'schedule_time' => '',
                'encoding' => 0,
                'message' => $message,
                'recipients' => [
                    ['recipient_id' => '1', 'dest_addr' => $phoneNumber],
                ],
            ]);

        return $response->successful();
    }
}
