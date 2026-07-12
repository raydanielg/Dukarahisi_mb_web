<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * Create a new order from selected notes.
     */
    public function store(Request $request)
    {
        $request->validate([
            'note_ids' => 'required|array|min:1',
            'note_ids.*' => 'integer|exists:notes,id',
        ]);

        $user = $request->user();
        $noteIds = $request->note_ids;

        $notes = Note::whereIn('id', $noteIds)
            ->where('status', 'published')
            ->where('is_active', true)
            ->get();

        if ($notes->count() !== count($noteIds)) {
            throw ValidationException::withMessages(['note_ids' => 'Some notes are not available.']);
        }

        $alreadyPurchased = OrderItem::whereHas('order', function ($q) use ($user) {
            $q->where('user_id', $user->id)->where('status', 'paid');
        })->whereIn('note_id', $noteIds)->pluck('note_id')->toArray();

        if (!empty($alreadyPurchased)) {
            throw ValidationException::withMessages(['note_ids' => 'You have already purchased some of these notes.']);
        }

        return DB::transaction(function () use ($user, $notes) {
            $total = $notes->sum(fn (Note $note) => $note->final_price);

            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $total,
                'status' => 'pending',
                'reference' => 'ORD-' . strtoupper(uniqid()),
            ]);

            foreach ($notes as $note) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'note_id' => $note->id,
                    'price_at_purchase' => $note->final_price,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Order created successfully.',
                'data' => [
                    'order_id' => $order->id,
                    'reference' => $order->reference,
                    'total_amount' => $order->total_amount,
                    'items' => $order->items->load('note'),
                ],
            ], 201);
        });
    }

    /**
     * List user orders.
     */
    public function index(Request $request)
    {
        $orders = $request->user()->orders()
            ->with('items.note')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $orders,
        ]);
    }

    /**
     * Create a single material order.
     */
    public function storeSingle(Request $request)
    {
        $request->validate([
            'material_type' => 'required|string|in:notes,books,lesson-plans,syllabus,scheme-of-work,logbooks',
            'material_id' => 'required|integer',
        ]);

        $user = $request->user();
        $type = $request->material_type;
        $id = $request->material_id;

        $models = [
            'notes' => \App\Models\Note::class,
            'books' => \App\Models\Book::class,
            'lesson-plans' => \App\Models\LessonPlan::class,
            'syllabus' => \App\Models\Syllabus::class,
            'scheme-of-work' => \App\Models\SchemeOfWork::class,
            'logbooks' => \App\Models\Logbook::class,
        ];

        $modelClass = $models[$type];
        $item = $modelClass::findOrFail($id);

        if ($item->is_free || $item->final_price <= 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'This material is free.',
            ], 422);
        }

        $alreadyPurchased = OrderItem::whereHas('order', function ($q) use ($user) {
            $q->where('user_id', $user->id)->where('status', 'paid');
        })->where('note_id', $id)->exists();

        if ($alreadyPurchased) {
            return response()->json([
                'status' => 'error',
                'message' => 'You have already purchased this material.',
            ], 422);
        }

        return DB::transaction(function () use ($user, $item, $id) {
            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $item->final_price,
                'status' => 'pending',
                'reference' => 'ORD-' . strtoupper(uniqid()),
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'note_id' => $id,
                'price_at_purchase' => $item->final_price,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Order created successfully.',
                'data' => [
                    'order_id' => $order->id,
                    'reference' => $order->reference,
                    'total_amount' => $order->total_amount,
                ],
            ], 201);
        });
    }

    /**
     * Check order payment status.
     */
    public function status(Request $request, Order $order)
    {
        try {
            $authUser = $request->user();

            if ($order->user_id !== $authUser->id) {
                Log::warning('Order status unauthorized access attempt', [
                    'order_id' => $order->id,
                    'order_user_id' => $order->user_id,
                    'auth_user_id' => $authUser->id,
                ]);
                return response()->json(['status' => 'error', 'message' => 'Unauthorized.'], 403);
            }

            $payment = $order->payment;

            return response()->json([
                'status' => 'success',
                'data' => [
                    'order_id' => $order->id,
                    'reference' => $order->reference,
                    'order_status' => $order->status,
                    'payment_status' => $payment?->status ?? 'pending',
                    'amount' => $order->total_amount,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Order status error: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['status' => 'error', 'message' => 'Server error.'], 500);
        }
    }
}
