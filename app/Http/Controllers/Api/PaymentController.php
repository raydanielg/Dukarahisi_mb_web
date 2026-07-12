<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\PaymentGatewayService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    /**
     * Initiate payment for an order.
     */
    public function initiate(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'phone_number' => 'required|string',
        ]);

        $user = $request->user();
        $order = Order::where('id', $request->order_id)
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if (!$order) {
            throw ValidationException::withMessages(['order_id' => 'Order not found or already paid.']);
        }

        $result = PaymentGatewayService::initiate($order, $request->phone_number);

        if (!$result['success']) {
            return response()->json([
                'status' => 'error',
                'message' => $result['message'],
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => $result['message'],
            'data' => [
                'order_id' => $order->id,
                'reference' => $result['reference'],
            ],
        ]);
    }

    /**
     * Handle payment gateway webhook.
     */
    public function webhook(Request $request)
    {
        $payload = $request->all();
        $signature = $request->header('X-Webhook-Signature');

        if ($signature && !PaymentGatewayService::verifyWebhookSignature($payload, $signature)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid signature.'], 400);
        }

        $payment = PaymentGatewayService::processWebhook($payload);

        if (!$payment) {
            return response()->json(['status' => 'error', 'message' => 'Order not found.'], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Webhook processed successfully.',
        ]);
    }
}
