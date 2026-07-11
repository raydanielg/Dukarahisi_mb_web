<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function ordersIndex(Request $request)
    {
        $query = Order::with('user', 'items.note', 'payment')->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('reference', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->get();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'orders' => $orders]);
        }

        return view('admin.sales.orders', compact('orders'));
    }

    public function customersIndex(Request $request)
    {
        $query = User::where('role', 'customer')->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        $customers = $query->get();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'customers' => $customers]);
        }

        return view('admin.sales.customers', compact('customers'));
    }

    public function paymentsIndex(Request $request)
    {
        $query = Payment::with('order.user')->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('reference', 'like', "%{$search}%")
                  ->orWhereHas('order.user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->get();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'payments' => $payments]);
        }

        return view('admin.sales.payments', compact('payments'));
    }

    public function reviewsIndex(Request $request)
    {
        $query = Review::with('note', 'user')->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('comment', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('note', function ($q) use ($search) {
                      $q->where('title', 'like', "%{$search}%");
                  });
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        $reviews = $query->get();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'reviews' => $reviews]);
        }

        return view('admin.sales.reviews', compact('reviews'));
    }

    public function ordersUpdateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,completed,cancelled,refunded',
        ]);

        $order->update(['status' => $validated['status']]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Order status updated.',
                'order' => $order->load('user', 'payment'),
            ]);
        }

        return redirect()->route('admin.sales.orders')->with('status', 'Order status updated.');
    }

    public function paymentsUpdateStatus(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,completed,failed,refunded',
        ]);

        $payment->update(['status' => $validated['status']]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment status updated.',
                'payment' => $payment->load('order.user'),
            ]);
        }

        return redirect()->route('admin.sales.payments')->with('status', 'Payment status updated.');
    }

    public function reviewsDestroy(Review $review)
    {
        $review->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Review deleted.']);
        }

        return redirect()->route('admin.sales.reviews')->with('status', 'Review deleted.');
    }
}
