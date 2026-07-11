<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentGatewayService
{
    /**
     * Initiate payment for an order.
     */
    public static function initiate(Order $order, string $phoneNumber): array
    {
        $provider = config('services.payment.provider', 'selcom');

        try {
            if ($provider === 'selcom') {
                return self::initiateSelcom($order, $phoneNumber);
            }

            // Fallback for testing
            return [
                'success' => true,
                'reference' => 'TEST-' . time(),
                'message' => 'Payment initiated (test mode)',
            ];
        } catch (\Exception $e) {
            Log::error('Payment initiation failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Payment initiation failed.',
            ];
        }
    }

    /**
     * Initiate Selcom payment.
     */
    protected static function initiateSelcom(Order $order, string $phoneNumber): array
    {
        $apiKey = config('services.payment.api_key');
        $apiSecret = config('services.payment.api_secret');
        $baseUrl = config('services.payment.base_url', 'https://api.selcommobile.com');

        if (!$apiKey || !$apiSecret) {
            return [
                'success' => false,
                'message' => 'Payment gateway not configured.',
            ];
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . base64_encode($apiKey . ':' . $apiSecret),
            'Content-Type' => 'application/json',
        ])->post($baseUrl . '/v1/payment/checkout', [
            'order_id' => $order->reference,
            'amount' => $order->total_amount,
            'msisdn' => $phoneNumber,
            'currency' => 'TZS',
            'callback_url' => route('payments.webhook'),
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return [
                'success' => true,
                'reference' => $data['reference'] ?? $order->reference,
                'message' => $data['message'] ?? 'Payment initiated successfully',
            ];
        }

        return [
            'success' => false,
            'message' => 'Payment gateway error.',
        ];
    }

    /**
     * Verify webhook signature.
     */
    public static function verifyWebhookSignature(array $payload, string $signature): bool
    {
        $secret = config('services.payment.webhook_secret');
        if (!$secret) {
            return false;
        }

        $computed = hash_hmac('sha256', json_encode($payload), $secret);
        return hash_equals($computed, $signature);
    }

    /**
     * Process successful payment webhook.
     */
    public static function processWebhook(array $payload): ?Payment
    {
        $orderReference = $payload['order_id'] ?? null;
        if (!$orderReference) {
            return null;
        }

        $order = Order::where('reference', $orderReference)->first();
        if (!$order) {
            return null;
        }

        $status = ($payload['status'] ?? '') === 'success' ? 'success' : 'failed';

        $payment = Payment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'method' => $payload['method'] ?? 'mobile_money',
                'reference' => $payload['reference'] ?? $orderReference,
                'status' => $status,
                'raw_response' => $payload,
            ]
        );

        $order->status = $status === 'success' ? 'paid' : 'failed';
        $order->save();

        return $payment;
    }
}
