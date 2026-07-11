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
}
