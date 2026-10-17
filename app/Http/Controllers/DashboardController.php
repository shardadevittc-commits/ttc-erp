<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Weight;
use App\Models\GateAndGaadi;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the main ERP dashboard.
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        $user->load('role');

        $stats = [
            'total_users'   => User::count(),
            'total_roles'   => Role::count(),
            'active_users'  => User::where('status', 1)->count(),
        ];

        return view('dashboard.index', compact('user', 'stats'));
    }


    /**
     * Sale Dashboard
     */
    public function sale()
    {
        $user = Auth::user();

        return view('dashboard.sale', compact('user'));
    }

    /**
     * Purchase Dashboard
     */
    public function purchase()
    {
        $user = Auth::user();

        return view('dashboard.purchase', compact('user'));
    }

    /**
     * Gate Dashboard
     */
    public function gate()
    {
        $user = Auth::user();

        $today = Carbon::today();

        // Today's vehicle entries
        $vehicleInToday = GateAndGaadi::whereDate('created_at', $today)->count();

        // Today's weighments
        $weighmentsToday = Weight::whereDate('created_at', $today)->count();

        // Latest vehicle entries
        $recentVehicles = GateAndGaadi::with('customer')
            ->latest('id')
            ->take(5)
            ->get();

        // Vehicle entries with weighment records
        $weighedMaterialIds = Weight::whereNotNull('vehicle_id')
            ->pluck('vehicle_id');

        // Entries that have not yet been weighed
        $pendingWeighment = GateAndGaadi::whereNotIn('id', $weighedMaterialIds)
            ->count();

        return view('dashboard.gate', compact(
            'user',
            'vehicleInToday',
            'weighmentsToday',
            'recentVehicles',
            'pendingWeighment'
        ));
    }

    /**
     * Weight Dashboard
     */
    public function weight()
    {
        $user = Auth::user();$records = Weight::query();

        $totalVehicles = (clone $records)->count();

        $totalGateAndGaadi = (clone $records)
            ->where('formlist', 'Material In')->count();

        $totalMaterialOut = (clone $records)
            ->where('formlist', 'Material Out')->count();

        $totalReturns = (clone $records)
            ->where('formlist', 'Return')->count();

        $totalCustomers = Customer::count();

        $recentRecords = (clone $records)
            ->with('customer')
            ->latest('created_at')
            ->take(10)
            ->get();

        // Last 7 days: number of weighment records per day.
        $daily = (clone $records)
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day');

        $weighmentChartLabels = collect(range(6, 0))
            ->map(fn ($days) => now()->subDays($days)->format('d M'))
            ->values();

        $weighmentChartValues = collect(range(6, 0))
            ->map(fn ($days) => (int) $daily->get(
                now()->subDays($days)->toDateString(), 0
            ))
            ->values();

        $weightChartLabels = (clone $records)
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(created_at) as day')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('day')
            ->map(fn ($day) => \Carbon\Carbon::parse($day)->format('d M'))
            ->values();

        $weightRows = (clone $records)
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(created_at) as day,
                SUM(loadedweight) as loaded,
                SUM(tareweight) as tare')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $loadedWeightValues = $weightRows->pluck('loaded')
            ->map(fn ($value) => (float) $value)->values();

        $tareWeightValues = $weightRows->pluck('tare')
            ->map(fn ($value) => (float) $value)->values();

        $todayVehicles = (clone $records)
            ->whereDate('created_at', today())->count();

        $todayLoadedWeight = (clone $records)
            ->whereDate('created_at', today())->sum('loadedweight');

        $todayTareWeight = (clone $records)
            ->whereDate('created_at', today())->sum('tareweight');

        // Supply these arrays to support the Monthly chart filter.
        $monthChartLabels = [];
        $monthChartValues = [];

        return view('dashboard.weight', compact(
            'totalVehicles',
            'totalGateAndGaadi',
            'totalMaterialOut',
            'totalReturns',
            'totalCustomers',
            'recentRecords',
            'weighmentChartLabels',
            'weighmentChartValues',
            'weightChartLabels',
            'loadedWeightValues',
            'tareWeightValues',
            'todayVehicles',
            'todayLoadedWeight',
            'todayTareWeight',
            'monthChartLabels',
            'monthChartValues'
        ));
    }

    /**
     * Unloader Dashboard
     */
    public function unloader()
    {
        $user = Auth::user();

        return view('dashboard.unloader', compact('user'));
    }

    /**
     * Dispatch Dashboard
     */
    public function dispatch()
    {
        $user = Auth::user();

        return view('dashboard.dispatch', compact('user'));
    }

    /**
     * Lab Dashboard
     */
    public function lab()
    {
        $user = Auth::user();

        return view('dashboard.lab', compact('user'));
    }

    /**
     * Production Dashboard
     */
    public function production()
    {
        $user = Auth::user();

        return view('dashboard.production', compact('user'));
    }

    /**
     * Lab Production Dashboard
     */
    public function labProduction()
    {
        $user = Auth::user();

        return view('dashboard.lab-production', compact('user'));
    }

    /**
     * Account Dashboard
     */
    public function account()
    {
        $user = Auth::user();

        return view('dashboard.account', compact('user'));
    }
}
