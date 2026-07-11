<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', '30');
        $startDate = Carbon::now()->subDays((int) $period);

        // Total stats
        $totalRevenue = Order::where('status', 'paid')->sum('total_amount');
        $totalOrders = Order::count();
        $totalPaidOrders = Order::where('status', 'paid')->count();
        $totalUsers = User::where('role', 'customer')->count();
        $totalNotes = Note::count();
        $totalDownloads = Note::sum('downloads_count');

        // Period stats
        $periodRevenue = Order::where('status', 'paid')
            ->where('created_at', '>=', $startDate)
            ->sum('total_amount');
        $periodOrders = Order::where('created_at', '>=', $startDate)->count();
        $periodPaidOrders = Order::where('status', 'paid')
            ->where('created_at', '>=', $startDate)
            ->count();
        $periodNewUsers = User::where('role', 'customer')
            ->where('created_at', '>=', $startDate)
            ->count();

        // Daily data for period
        $dailyLabels = [];
        $dailyRevenue = [];
        $dailyOrders = [];
        $dailyUsers = [];
        $dailyDownloads = [];

        for ($i = (int) $period - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dailyLabels[] = $date->format('d M');
            $dailyRevenue[] = Order::where('status', 'paid')
                ->whereDate('created_at', $date)
                ->sum('total_amount');
            $dailyOrders[] = Order::whereDate('created_at', $date)->count();
            $dailyUsers[] = User::where('role', 'customer')
                ->whereDate('created_at', $date)
                ->count();
            $dailyDownloads[] = 0; // Placeholder, can be updated with real download tracking
        }

        // Order status breakdown
        $orderStatusData = [
            Order::where('status', 'paid')->count(),
            Order::where('status', 'pending')->count(),
            Order::where('status', 'failed')->count(),
            Order::where('status', 'cancelled')->count(),
        ];

        // Payment status breakdown
        $paymentStatusData = [
            Payment::where('status', 'success')->count(),
            Payment::where('status', 'pending')->count(),
            Payment::where('status', 'failed')->count(),
            Payment::where('status', 'refunded')->count(),
        ];

        // Top selling notes
        $topNotes = Note::withCount('orderItems')
            ->with('subject')
            ->orderBy('order_items_count', 'desc')
            ->limit(10)
            ->get();

        // Top subjects by revenue
        $topSubjects = Subject::withCount('notes')
            ->with(['notes.orderItems' => function ($q) {
                $q->whereHas('order', function ($o) {
                    $o->where('status', 'paid');
                });
            }])
            ->get()
            ->map(function ($subject) {
                $revenue = 0;
                $sales = 0;
                foreach ($subject->notes as $note) {
                    $revenue += $note->orderItems->sum('price_at_purchase');
                    $sales += $note->orderItems->count();
                }
                $subject->revenue = $revenue;
                $subject->sales = $sales;
                return $subject;
            })
            ->sortByDesc('revenue')
            ->take(8)
            ->values();

        // Recent sales
        $recentSales = Order::with('user', 'items.note')
            ->where('status', 'paid')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // User growth by month (last 6 months)
        $monthlyLabels = [];
        $monthlyUsers = [];
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthlyLabels[] = $month->format('M Y');
            $monthlyUsers[] = User::where('role', 'customer')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $monthlyRevenue[] = Order::where('status', 'paid')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('total_amount');
        }

        return view('admin.analytics', compact(
            'period', 'totalRevenue', 'totalOrders', 'totalPaidOrders', 'totalUsers',
            'totalNotes', 'totalDownloads', 'periodRevenue', 'periodOrders',
            'periodPaidOrders', 'periodNewUsers', 'dailyLabels', 'dailyRevenue',
            'dailyOrders', 'dailyUsers', 'dailyDownloads', 'orderStatusData',
            'paymentStatusData', 'topNotes', 'topSubjects', 'recentSales',
            'monthlyLabels', 'monthlyUsers', 'monthlyRevenue'
        ));
    }
}
