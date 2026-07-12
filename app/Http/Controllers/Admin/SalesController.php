<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
        $query = User::where('role', 'customer')->with('orders')->orderBy('created_at', 'desc');

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

    public function customersResetPassword(Request $request, User $customer)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $customer->update(['password' => Hash::make($validated['password'])]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Password reset successfully.']);
        }

        return redirect()->route('admin.sales.customers')->with('status', 'Password reset successfully.');
    }

    public function customersBulkResetPassword(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:users,id',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $password = Hash::make($validated['password']);
        $count = User::whereIn('id', $validated['ids'])->where('role', 'customer')->update(['password' => $password]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => "Password reset for {$count} customer(s)."]);
        }

        return redirect()->route('admin.sales.customers')->with('status', "Password reset for {$count} customer(s).");
    }

    public function customersDestroy(Request $request, User $customer)
    {
        if ($customer->role !== 'customer') {
            return response()->json(['success' => false, 'message' => 'Only customers can be deleted here.'], 403);
        }

        if (auth()->id() === $customer->id) {
            return response()->json(['success' => false, 'message' => 'You cannot delete your own account.'], 403);
        }

        $customer->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Customer deleted.']);
        }

        return redirect()->route('admin.sales.customers')->with('status', 'Customer deleted.');
    }

    public function customersBulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:users,id',
        ]);

        $ids = array_diff($validated['ids'], [auth()->id()]);

        User::whereIn('id', $ids)->where('role', 'customer')->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Selected customers deleted.']);
        }

        return redirect()->route('admin.sales.customers')->with('status', 'Selected customers deleted.');
    }

    public function customersBulkUpload(Request $request)
    {
        $validated = $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        $handle = fopen($path, 'r');

        if (! $handle) {
            return response()->json(['success' => false, 'message' => 'Unable to read CSV file.'], 422);
        }

        $headers = fgetcsv($handle);
        if (! $headers) {
            fclose($handle);
            return response()->json(['success' => false, 'message' => 'CSV file is empty.'], 422);
        }

        $headers = array_map('strtolower', array_map('trim', $headers));
        $required = ['name', 'email', 'phone_number', 'password'];
        $missing = array_diff($required, $headers);

        if (! empty($missing)) {
            fclose($handle);
            return response()->json(['success' => false, 'message' => 'Missing columns: ' . implode(', ', $missing)], 422);
        }

        $created = 0;
        $errors = [];
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            if (count($row) < count($headers)) {
                continue;
            }

            $data = array_combine($headers, $row);
            if (! is_array($data)) {
                continue;
            }

            $data = array_map('trim', $data);

            $validator = Validator::make($data, [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone_number' => 'required|string|max:20|unique:users,phone_number',
                'password' => 'required|string|min:8',
            ]);

            if ($validator->fails()) {
                $errors[] = "Row {$rowNumber}: " . $validator->errors()->first();
                continue;
            }

            User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone_number' => $data['phone_number'],
                'password' => Hash::make($data['password']),
                'role' => 'customer',
            ]);

            $created++;
        }

        fclose($handle);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => empty($errors),
                'message' => $created . ' customer' . ($created === 1 ? '' : 's') . ' imported.',
                'created' => $created,
                'errors' => $errors,
            ]);
        }

        return redirect()->route('admin.sales.customers')->with('status', $created . ' customer' . ($created === 1 ? '' : 's') . ' imported.');
    }
}
