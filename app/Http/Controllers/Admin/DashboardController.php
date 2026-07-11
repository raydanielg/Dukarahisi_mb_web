<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\Note;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show admin dashboard.
     */
    public function index(Request $request)
    {
        $totalNotes = Note::count();
        $publishedNotes = Note::where('status', 'published')->count();
        $totalOrders = Order::count();
        $totalRevenue = Order::where('status', 'paid')->sum('total_amount');
        $totalUsers = User::where('role', 'customer')->count();
        $pendingOrders = Order::where('status', 'pending')->count();

        $recentOrders = Order::with('user', 'items.note')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recentUsers = User::where('role', 'customer')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $topNotes = Note::withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->limit(5)
            ->get();

        $dailySales = [];
        for ($i = 13; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dailySales[] = [
                'date' => $date->format('d M'),
                'amount' => Order::where('status', 'paid')
                    ->whereDate('created_at', $date)
                    ->sum('total_amount'),
                'count' => Order::where('status', 'paid')
                    ->whereDate('created_at', $date)
                    ->count(),
            ];
        }

        return view('admin.dashboard', compact(
            'totalNotes', 'publishedNotes', 'totalOrders', 'totalRevenue',
            'totalUsers', 'pendingOrders', 'recentOrders', 'recentUsers',
            'topNotes', 'dailySales'
        ));
    }
}
