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
     * Verify Snippe webhook signature.
     *
     * Signature = hex(HMAC-SHA256(signing_key, "{timestamp}.{raw_body}"))
     */
    public static function verifyWebhookSignature(string $rawBody, string $timestamp, string $signature): bool
    {
        $secret = config('services.payment.webhook_secret');
        if (!$secret) {
            return false;
        }

        // Optional: reject stale events older than 5 minutes
        if (abs(time() - (int) $timestamp) > 300) {
            Log::warning('Snippe webhook timestamp too old', ['timestamp' => $timestamp]);
            return false;
        }

        $message = $timestamp . '.' . $rawBody;
        $computed = hash_hmac('sha256', $message, $secret);
        return hash_equals($computed, $signature);
    }

    /**
     * Process Snippe webhook payload.
     */
    public static function processWebhook(array $payload): ?Payment
    {
        $data = $payload['data'] ?? [];
        $gatewayReference = $data['reference'] ?? ($data['external_reference'] ?? null);

        if (!$gatewayReference) {
            Log::warning('Snippe webhook: no gateway reference found', ['payload' => $payload]);
            return null;
        }

        $payment = Payment::where('reference', $gatewayReference)
            ->orWhere('reference', $data['external_reference'] ?? null)
            ->first();

        if (!$payment) {
            $metadata = $data['metadata'] ?? ($payload['metadata'] ?? []);
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
                Log::warning('Snippe webhook: payment/order not found', ['payload' => $payload]);
                return null;
            }

            $payment = new Payment(['order_id' => $order->id]);
        }

        $eventType = $payload['type'] ?? '';
        $paymentStatus = 'pending';
        $orderStatus = 'pending';

        if (str_contains($eventType, 'completed')) {
            $paymentStatus = 'success';
            $orderStatus = 'paid';
        } elseif (str_contains($eventType, 'failed')) {
            $paymentStatus = 'failed';
            $orderStatus = 'failed';
        } elseif (str_contains($eventType, 'expired')) {
            $paymentStatus = 'failed';
            $orderStatus = 'cancelled';
        } elseif (str_contains($eventType, 'voided')) {
            $paymentStatus = 'refunded';
            $orderStatus = 'cancelled';
        }

        $payment->method = $data['channel']['type'] ?? 'mobile_money';
        $payment->reference = $data['reference'] ?? ($data['external_reference'] ?? $payment->reference);
        $payment->status = $paymentStatus;
        $payment->raw_response = $payload;
        $payment->save();

        $order = $payment->order;
        if ($order) {
            $order->status = $orderStatus;
            $order->save();
        }

        return $payment;
    }
}
