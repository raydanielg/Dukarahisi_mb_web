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
        $provider = config('services.payment.provider', 'snippe');

        try {
            if ($provider === 'snippe') {
                return self::initiateSnippe($order, $phoneNumber);
            }

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
     * Initiate Snippe mobile money payment.
     */
    protected static function initiateSnippe(Order $order, string $phoneNumber): array
    {
        $apiKey = config('services.payment.api_key');
        $baseUrl = rtrim(config('services.payment.base_url', 'https://api.snippe.sh'), '/');

        if (!$apiKey) {
            return [
                'success' => false,
                'message' => 'Payment gateway not configured.',
            ];
        }

        $cleanPhone = ltrim($phoneNumber, '+');
        if (!str_starts_with($cleanPhone, '255')) {
            $cleanPhone = '255' . ltrim($cleanPhone, '0');
        }

        $user = $order->user;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
            'Idempotency-Key' => substr('ord-' . $order->reference, 0, 30),
        ])->post($baseUrl . '/v1/payments', [
            'payment_type' => 'mobile',
            'details' => [
                'amount' => (int) $order->total_amount,
                'currency' => 'TZS',
            ],
            'phone_number' => $cleanPhone,
            'customer' => [
                'firstname' => $user?->first_name ?? 'Customer',
                'lastname' => $user?->last_name ?? 'User',
                'email' => $user?->email ?? 'customer@darasahurutz.com',
            ],
            'webhook_url' => route('payments.webhook'),
            'metadata' => [
                'order_id' => $order->id,
                'order_reference' => $order->reference,
            ],
        ]);

        $data = $response->json();

        if ($response->successful() && ($data['status'] ?? '') === 'success') {
            $paymentData = $data['data'] ?? [];

            Payment::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'method' => 'mobile_money',
                    'reference' => $paymentData['reference'] ?? null,
                    'status' => 'pending',
                    'raw_response' => $data,
                    'phone_number' => $cleanPhone,
                ]
            );

            return [
                'success' => true,
                'reference' => $paymentData['reference'] ?? $order->reference,
                'message' => 'Payment initiated. Please authorize on your phone.',
            ];
        }

        Log::error('Snippe payment initiation error', ['response' => $data]);

        return [
            'success' => false,
            'message' => $data['message'] ?? 'Payment gateway error.',
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
     * Process Snippe webhook payload.
     */
    public static function processWebhook(array $payload): ?Payment
    {
        $metadata = $payload['data']['metadata'] ?? ($payload['metadata'] ?? []);
        $orderId = $metadata['order_id'] ?? null;
        $orderReference = $metadata['order_reference'] ?? null;

        $order = null;
        if ($orderId) {
            $order = Order::find($orderId);
        }
        if (!$order && $orderReference) {
            $order = Order::where('reference', $orderReference)->first();
        }

        if (!$order) {
            Log::warning('Snippe webhook: order not found', ['payload' => $payload]);
            return null;
        }

        $eventType = $payload['type'] ?? '';
        $paymentStatus = 'pending';

        if (str_contains($eventType, 'completed')) {
            $paymentStatus = 'success';
        } elseif (str_contains($eventType, 'failed')) {
            $paymentStatus = 'failed';
        } elseif (str_contains($eventType, 'expired')) {
            $paymentStatus = 'expired';
        } elseif (str_contains($eventType, 'voided')) {
            $paymentStatus = 'voided';
        }

        $payment = Payment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'method' => $payload['data']['channel']['type'] ?? 'mobile_money',
                'reference' => $payload['data']['reference'] ?? ($payload['data']['external_reference'] ?? $order->reference),
                'status' => $paymentStatus,
                'raw_response' => $payload,
            ]
        );

        $order->status = $paymentStatus === 'success' ? 'paid' : $paymentStatus;
        $order->save();

        return $payment;
    }
}
