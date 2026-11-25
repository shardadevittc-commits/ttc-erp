<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Weight;
use App\Models\Invoice;
use App\Models\GateAndGaadi;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
class DashboardController extends Controller
{
    /**
     * Display the main ERP dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $user->load('role');

        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        /*
        |--------------------------------------------------------------------------
        | 1. USER STATISTICS
        |--------------------------------------------------------------------------
        */

        $stats = [];

        $stats['total_users'] = User::count();

        $stats['total_roles'] = Role::count();

        $stats['active_users'] = User::where('status', 1)->count();

        /*
        |--------------------------------------------------------------------------
        | 2. TODAY'S MATERIAL SENT FOR WORK
        |--------------------------------------------------------------------------
        */

        $stats['today_material_sent'] = DB::table('work_orders_and_process')
            ->whereDate('created_at', $today)
            ->whereNull('delete_at')
            ->sum('material_send_for_work');

        /*
        |--------------------------------------------------------------------------
        | 3. TODAY'S RECEIVED MATERIAL
        |--------------------------------------------------------------------------
        */

        $stats['today_received_qty'] = DB::table('unload_weights')
            ->whereDate('created_at', $today)
            ->sum('received_qty');

        /*
        |--------------------------------------------------------------------------
        | 4. TODAY'S WEIGHBRIDGE SLIPS
        |--------------------------------------------------------------------------
        */

        $stats['today_weighbridge_slips'] = DB::table('weights')
            ->whereDate('created_at', $today)
            ->whereNull('deleted_at')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | 5. MONTHLY INVOICE AMOUNT
        |--------------------------------------------------------------------------
        */

        $stats['monthly_invoice_amount'] = DB::table('invoices')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->sum('total_amount');

        /*
        |--------------------------------------------------------------------------
        | 6. MONTHLY INVOICE COUNT
        |--------------------------------------------------------------------------
        */

        $stats['monthly_invoice_count'] = DB::table('invoices')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->count();

        /*
        |--------------------------------------------------------------------------
        | 7. LAST 7 DAYS CHART DATA
        |--------------------------------------------------------------------------
        */

        $startDate = Carbon::today()->subDays(6);

        // Material sent for work per day
        $materialSent = DB::table('work_orders_and_process')
            ->selectRaw('DATE(created_at) as day, SUM(material_send_for_work) as total')
            ->whereBetween('created_at', [
                $startDate->copy()->startOfDay(),
                Carbon::today()->endOfDay()
            ])
            ->whereNull('delete_at')
            ->groupByRaw('DATE(created_at)')
            ->pluck('total', 'day');

        // Finished production per day
        // finished_pcs_weight is stored in KG in the database.
        $finishedProduction = DB::table('work_orders_and_process')
            ->selectRaw('DATE(created_at) as day, SUM(finished_pcs_weight) as total')
            ->whereBetween('created_at', [
                $startDate->copy()->startOfDay(),
                Carbon::today()->endOfDay()
            ])
            ->whereNull('delete_at')
            ->groupByRaw('DATE(created_at)')
            ->pluck('total', 'day');

        $chartLabels = [];
        $chartMaterialSent = [];
        $chartFinishedProduction = [];

        for ($i = 0; $i < 7; $i++) {

            $date = $startDate->copy()->addDays($i);
            $dateKey = $date->format('Y-m-d');

            $chartLabels[] = $date->format('D');

            $chartMaterialSent[] = round(
                (float) ($materialSent[$dateKey] ?? 0),
                3
            );

            // Convert KG into MT
            $chartFinishedProduction[] = round(
                (float) ($finishedProduction[$dateKey] ?? 0) / 1000,
                3
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 8. RECENT VEHICLE TRANSACTIONS
        |--------------------------------------------------------------------------
        */

        $unloadedVehicles = DB::table('unload_weights')
            ->select('vehicle_id')
            ->groupBy('vehicle_id');

        $recentTransactions = DB::table('weights')
            ->leftJoin(
                'gate_and_gaadi',
                'weights.vehicle_id',
                '=',
                'gate_and_gaadi.id'
            )
            ->leftJoin(
                'customers',
                'weights.cust_id',
                '=',
                'customers.id'
            )
            ->leftJoinSub(
                $unloadedVehicles,
                'unloaded',
                function ($join) {
                    $join->on(
                        'weights.vehicle_id',
                        '=',
                        'unloaded.vehicle_id'
                    );
                }
            )
            ->whereNull('weights.deleted_at')
            ->select(
                'weights.id',
                'weights.truckno',
                'weights.formlist',
                'weights.loadedweight',
                'weights.tareweight',
                'weights.material_weight',
                'weights.first_weight',
                'weights.second_weight',
                'weights.created_at',
                'customers.company_name',
                'gate_and_gaadi.tokenno',
                DB::raw("
                    CASE
                        WHEN unloaded.vehicle_id IS NOT NULL
                            THEN 'Unloaded'
                        WHEN weights.first_weight = 'done'
                            AND weights.second_weight = 'done'
                            THEN 'Weighed'
                        WHEN weights.first_weight = 'done'
                            OR weights.second_weight = 'done'
                            THEN 'In Weighing'
                        ELSE 'Pending'
                    END AS transaction_status
                ")
            )
            ->orderByDesc('weights.created_at')
            ->limit(10)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 9. MONTHLY DISPATCH / INVOICE TREND
        |--------------------------------------------------------------------------
        */

        // Revenue is based on actual invoice total_amount.
        // Do not use hardcoded revenue figures.

        /*
        |--------------------------------------------------------------------------
        | 10. RETURN DATA TO DASHBOARD
        |--------------------------------------------------------------------------
        */

        return view('dashboard.index', compact(
            'user',
            'stats',
            'chartLabels',
            'chartMaterialSent',
            'chartFinishedProduction',
            'recentTransactions'
        ));
    }


    /**
     * Sale Dashboard
     */
    public function sale()
    {
        $user = Auth::user();

        $today = Carbon::today();

        // Financial Year: April to March
        $fyStart = Carbon::now()->month >= 4
            ? Carbon::now()->startOfYear()->month(4)->startOfDay()
            : Carbon::now()->subYear()->startOfYear()->month(4)->startOfDay();

        $fyEnd = $fyStart->copy()->addYear()->subDay()->endOfDay();

        /*
        |--------------------------------------------------------------------------
        | 1. TODAY SALES
        |--------------------------------------------------------------------------
        */

        $todaySales = DB::table('sale_invoices')
            ->whereDate('invoice_date', $today)
            ->sum('total_amount');

        $todayInvoices = DB::table('sale_invoices')
            ->whereDate('invoice_date', $today)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | 2. FINANCIAL YEAR SALES
        |--------------------------------------------------------------------------
        */

        $fySales = DB::table('sale_invoices')
            ->whereBetween('invoice_date', [$fyStart, $fyEnd])
            ->sum('total_amount');

        $totalInvoices = DB::table('sale_invoices')
            ->whereBetween('invoice_date', [$fyStart, $fyEnd])
            ->count();

        /*
        |--------------------------------------------------------------------------
        | 3. CUSTOMER OUTSTANDING
        |--------------------------------------------------------------------------
        | Based on invoice total minus recorded advance payment.
        | Not a complete payment ledger.
        */

        $outstanding = DB::table('sale_invoices')
            ->selectRaw('SUM(GREATEST(total_amount - advance_payment, 0)) as total')
            ->value('total') ?? 0;

        /*
        |--------------------------------------------------------------------------
        | 4. TODAY ADVANCE COLLECTION
        |--------------------------------------------------------------------------
        */

        $todayCollection = DB::table('sale_invoices')
            ->whereDate('invoice_date', $today)
            ->sum('advance_payment');

        /*
        |--------------------------------------------------------------------------
        | 5. SALES ORDERS
        |--------------------------------------------------------------------------
        */

        $salesOrders = DB::table('sale_orders as so')
            ->leftJoin('customers as c', 'c.id', '=', 'so.customer_id')
            ->leftJoin('sale_order_items as soi', function ($join) {
                $join->on('soi.sale_order_id', '=', 'so.id')
                     ->whereNull('soi.deleted_at');
            })
            ->whereNull('so.deleted_at')
            ->select(
                'so.id',
                'so.customer_id',
                'so.created_at',
                'c.company_name'
            )
            ->selectRaw('SUM(COALESCE(soi.item_qty, 0)) as ordered_qty')
            ->selectRaw('SUM(COALESCE(soi.dispatched_qty, 0)) as dispatched_qty')
            ->selectRaw('SUM(COALESCE(soi.pending_qty, COALESCE(soi.item_qty, 0) - COALESCE(soi.dispatched_qty, 0))) as pending_qty')
            ->selectRaw('SUM(COALESCE(soi.item_qty, 0) * COALESCE(soi.sale_item_prices, 0)) as order_value')
            ->groupBy(
                'so.id',
                'so.customer_id',
                'so.created_at',
                'c.company_name'
            )
            ->orderByDesc('so.id')
            ->get();

        $pendingOrders = $salesOrders->filter(function ($order) {
            return (float) $order->pending_qty > 0;
        });

        $totalOrders = $salesOrders->count();

        $pendingOrderCount = $pendingOrders->count();

        $pendingOrderValue = $pendingOrders->sum('order_value');

        $totalOrderValue = $salesOrders->sum('order_value');

        /*
        |--------------------------------------------------------------------------
        | 6. MONTHLY SALES CHART
        |--------------------------------------------------------------------------
        */

        $monthlySalesRows = DB::table('sale_invoices')
            ->selectRaw('MONTH(invoice_date) as month_no')
            ->selectRaw('SUM(total_amount) as total')
            ->whereBetween('invoice_date', [$fyStart, $fyEnd])
            ->groupByRaw('MONTH(invoice_date)')
            ->get()
            ->keyBy('month_no');

        $monthLabels = [];
        $monthSales = [];

        for ($i = 0; $i < 12; $i++) {

            $month = $fyStart->copy()->addMonths($i);

            $monthLabels[] = $month->format('M');

            $monthSales[] = round(
                (float) ($monthlySalesRows[$month->month]->total ?? 0) / 100000,
                2
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 7. PRODUCT WISE SALES CHART
        |--------------------------------------------------------------------------
        | Based on ordered quantity, not invoice revenue.
        */

        $productMix = DB::table('sale_order_items as soi')
            ->join('sale_orders as so', 'so.id', '=', 'soi.sale_order_id')
            ->leftJoin('products as p', 'p.id', '=', 'soi.product_id')
            ->whereNull('so.deleted_at')
            ->whereNull('soi.deleted_at')
            ->whereBetween('so.created_at', [$fyStart, $fyEnd])
            ->selectRaw("COALESCE(p.product_name, 'Other') as product_name")
            ->selectRaw('SUM(COALESCE(soi.item_qty, 0)) as total_qty')
            ->groupBy('p.product_name')
            ->orderByDesc('total_qty')
            ->get();

        $productLabels = $productMix->pluck('product_name')->values();

        $productValues = $productMix->pluck('total_qty')
            ->map(fn ($value) => round((float) $value, 3))
            ->values();

        /*
        |--------------------------------------------------------------------------
        | 8. RECENT SALES INVOICES
        |--------------------------------------------------------------------------
        */

        $dispatchSummary = DB::table('dispatch_material')
            ->select('gate_id')
            ->selectRaw('SUM(dispatch_qty) as dispatched_qty')
            ->groupBy('gate_id');

        $recentInvoices = DB::table('sale_invoices as si')
            ->leftJoin('customers as c', 'c.id', '=', 'si.customer_id')
            ->leftJoin('gate_and_gaadi as g', 'g.id', '=', 'si.gate_id')
            ->leftJoinSub($dispatchSummary, 'ds', function ($join) {
                $join->on('ds.gate_id', '=', 'si.gate_id');
            })
            ->select(
                'si.id',
                'si.invoice_no',
                'si.invoice_date',
                'si.total_amount',
                'si.advance_payment',
                'si.gst_amount',
                'c.company_name'
            )
            ->selectRaw('COALESCE(ds.dispatched_qty, 0) as dispatched_qty')
            ->orderByDesc('si.invoice_date')
            ->orderByDesc('si.id')
            ->limit(10)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 9. RECENT PENDING ORDERS
        |--------------------------------------------------------------------------
        */

        $recentPendingOrders = $pendingOrders
            ->sortByDesc('id')
            ->take(10);

        /*
        |--------------------------------------------------------------------------
        | 10. DASHBOARD DATA
        |--------------------------------------------------------------------------
        */

        $financialYear = $fyStart->format('Y') . '-' .
            $fyEnd->format('y');

        return view('dashboard.sale', compact(
            'user',
            'todaySales',
            'todayInvoices',
            'todayCollection',
            'outstanding',
            'fySales',
            'totalInvoices',
            'salesOrders',
            'pendingOrders',
            'totalOrders',
            'pendingOrderCount',
            'pendingOrderValue',
            'totalOrderValue',
            'monthLabels',
            'monthSales',
            'productLabels',
            'productValues',
            'recentInvoices',
            'recentPendingOrders',
            'financialYear'
        ));
    }
    /**
     * Purchase Dashboard
     */
    public function purchase()
    {
        $user = Auth::user();

        // Indian Financial Year: April to March
        $today = Carbon::today();

        $fyStartYear = $today->month >= 4
            ? $today->year
            : $today->year - 1;

        $fyStart = Carbon::create($fyStartYear, 4, 1)->startOfDay();
        $fyEnd = Carbon::create($fyStartYear + 1, 3, 31)->endOfDay();

        // Purchase order item receiving summary
        $receiving = DB::table('poitem_receiving')
            ->select('purchase_order_item_id')
            ->selectRaw('SUM(COALESCE(received_qty, 0)) as received_qty')
            ->whereNull('deleted_at')
            ->groupBy('purchase_order_item_id');

        // All purchase order items with received quantities
        $itemsQuery = DB::table('purchase_order_items as poi')
            ->leftJoinSub($receiving, 'pr', function ($join) {
                $join->on('pr.purchase_order_item_id', '=', 'poi.id');
            })
            ->join('purchase_orders as po', 'po.id', '=', 'poi.purchase_order_id')
            ->whereBetween('po.created_at', [$fyStart, $fyEnd]);

        // Financial year purchase value
        $purchaseSummary = (clone $itemsQuery)
            ->selectRaw('
                COALESCE(SUM(
                    COALESCE(poi.qty, 0) * COALESCE(poi.rate, 0)
                ), 0) as total_purchase
            ')
            ->selectRaw('COALESCE(SUM(COALESCE(poi.qty, 0)), 0) as ordered_qty')
            ->selectRaw('COALESCE(SUM(COALESCE(pr.received_qty, 0)), 0) as received_qty')
            ->selectRaw('COALESCE(SUM(
                GREATEST(
                    COALESCE(poi.qty, 0) - COALESCE(pr.received_qty, 0),
                    0
                )
            ), 0) as pending_qty')
            ->selectRaw('COALESCE(SUM(
                GREATEST(
                    COALESCE(poi.qty, 0) - COALESCE(pr.received_qty, 0),
                    0
                ) * COALESCE(poi.rate, 0)
            ), 0) as pending_value')
            ->first();

        // This month's purchase value
        $monthlyPurchase = (clone $itemsQuery)
            ->whereBetween('po.created_at', [
                $today->copy()->startOfMonth(),
                $today->copy()->endOfMonth()
            ])
            ->selectRaw('COALESCE(SUM(
                COALESCE(poi.qty, 0) * COALESCE(poi.rate, 0)
            ), 0) as total')
            ->value('total');

        // Total purchase orders in current FY
        $totalOrders = DB::table('purchase_orders')
            ->whereBetween('created_at', [$fyStart, $fyEnd])
            ->count();

        // Monthly purchase trend for financial year
        $monthlyData = DB::table('purchase_orders as po')
            ->join('purchase_order_items as poi', 'poi.purchase_order_id', '=', 'po.id')
            ->whereBetween('po.created_at', [$fyStart, $fyEnd])
            ->selectRaw('MONTH(po.created_at) as month_number')
            ->selectRaw('SUM(
                COALESCE(poi.qty, 0) * COALESCE(poi.rate, 0)
            ) as total')
            ->groupByRaw('MONTH(po.created_at)')
            ->pluck('total', 'month_number');

        $purchaseChartLabels = [];
        $purchaseChartValues = [];

        for ($month = 4; $month <= 15; $month++) {
            $actualMonth = (($month - 1) % 12) + 1;
            $year = $month <= 12 ? $fyStartYear : $fyStartYear + 1;

            $purchaseChartLabels[] = Carbon::create($year, $actualMonth, 1)
                ->format('M Y');

            $purchaseChartValues[] = (float) ($monthlyData[$actualMonth] ?? 0);
        }

        // Product-wise purchase mix
        $productMix = (clone $itemsQuery)
            ->leftJoin('products as p', 'p.id', '=', 'poi.product_id')
            ->selectRaw("COALESCE(p.product_name, 'Other') as product_name")
            ->selectRaw('SUM(
                COALESCE(poi.qty, 0) * COALESCE(poi.rate, 0)
            ) as total_value')
            ->groupBy('p.product_name')
            ->orderByDesc('total_value')
            ->limit(6)
            ->get();

        // Recent purchase orders
        $recentPurchases = DB::table('purchase_orders as po')
            ->leftJoin('customers as c', 'c.id', '=', 'po.cust_id')
            ->leftJoin('purchase_order_items as poi', 'poi.purchase_order_id', '=', 'po.id')
            ->leftJoinSub($receiving, 'pr', function ($join) {
                $join->on('pr.purchase_order_item_id', '=', 'poi.id');
            })
            ->whereBetween('po.created_at', [$fyStart, $fyEnd])
            ->select(
                'po.id',
                'po.created_at',
                'po.basis',
                'c.company_name'
            )
            ->selectRaw('SUM(COALESCE(poi.qty, 0)) as ordered_qty')
            ->selectRaw('SUM(COALESCE(pr.received_qty, 0)) as received_qty')
            ->selectRaw('SUM(
                COALESCE(poi.qty, 0) * COALESCE(poi.rate, 0)
            ) as order_value')
            ->groupBy(
                'po.id',
                'po.created_at',
                'po.basis',
                'c.company_name'
            )
            ->orderByDesc('po.created_at')
            ->limit(10)
            ->get();

        // Pending purchase orders
        $pendingPurchases = $recentPurchases->filter(function ($order) {
            return (float) $order->ordered_qty > (float) $order->received_qty;
        })->values();

        // Supplier count
        $supplierCount = DB::table('customers')
            ->where('supplier', 1)
            ->whereNull('deleted_at')
            ->count();

        return view('dashboard.purchase', compact(
            'user',
            'purchaseSummary',
            'monthlyPurchase',
            'totalOrders',
            'purchaseChartLabels',
            'purchaseChartValues',
            'productMix',
            'recentPurchases',
            'pendingPurchases',
            'supplierCount',
            'fyStartYear'
        ));
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
        $user = Auth::user();

        $records = Weight::query();

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

        // Base query
        $baseQuery = Weight::query()
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('poitem_receiving as pir')
                    ->whereColumn('pir.customer_id', 'weights.cust_id')
                    ->where(function ($sub) {
                        $sub->whereColumn('pir.vehical_id', 'weights.vehicle_id')
                            ->orWhereColumn('pir.weight_id', 'weights.id')
                            ->orWhereColumn('pir.vehical_id', 'weights.id');
                    });
            });

        // Total Vehicles
        $totalVehicles = (clone $baseQuery)->count();

        // Total Materials
        $totalMaterials = DB::table('poitem_receiving as pir')
            ->count();

        // Total Received Quantity
        $totalReceivedQty = DB::table('poitem_receiving as pir')
            ->sum('received_qty');

        // Last Receiving Date
        $lastReceiving = DB::table('poitem_receiving')
            ->max('created_at');

        // Recent Unloading History
        $recentUnloadings = Weight::with('customer:id,cust_code,company_name')
            ->select('weights.*')

            ->selectSub(function ($q) {
                $q->from('poitem_receiving as pir')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('pir.customer_id', 'weights.cust_id')
                    ->whereColumn('pir.weight_id', 'weights.id')
                    ->where(function ($sub) {
                        $sub->whereColumn('pir.vehical_id', 'weights.vehicle_id')
                            ->orWhereColumn('pir.weight_id', 'weights.id')
                            ->orWhereColumn('pir.vehical_id', 'weights.id');
                    });
            }, 'total_materials')

            ->selectSub(function ($q) {
                $q->from('poitem_receiving as pir')
                    ->selectRaw('COALESCE(SUM(pir.received_qty), 0)')
                    ->whereColumn('pir.customer_id', 'weights.cust_id')
                    ->whereColumn('pir.weight_id', 'weights.id')
                    ->where(function ($sub) {
                        $sub->whereColumn('pir.vehical_id', 'weights.vehicle_id')
                            ->orWhereColumn('pir.weight_id', 'weights.id')
                            ->orWhereColumn('pir.vehical_id', 'weights.id');
                    });
            }, 'total_received_qty')

            ->selectSub(function ($q) {
                $q->from('poitem_receiving as pir')
                    ->selectRaw('MAX(pir.created_at)')
                    ->whereColumn('pir.customer_id', 'weights.cust_id')
                    ->whereColumn('pir.weight_id', 'weights.id')
                    ->where(function ($sub) {
                        $sub->whereColumn('pir.vehical_id', 'weights.vehicle_id')
                            ->orWhereColumn('pir.weight_id', 'weights.id')
                            ->orWhereColumn('pir.vehical_id', 'weights.id');
                    });
            }, 'last_receiving_date')

            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('poitem_receiving as pir')
                    ->whereColumn('pir.customer_id', 'weights.cust_id')
                    ->whereColumn('pir.weight_id', 'weights.id')
                    ->where(function ($sub) {
                        $sub->whereColumn('pir.vehical_id', 'weights.vehicle_id')
                            ->orWhereColumn('pir.weight_id', 'weights.id')
                            ->orWhereColumn('pir.vehical_id', 'weights.id');
                    });
            })

            ->orderByDesc('weights.created_at')
            ->limit(10)
            ->get();

        return view('dashboard.unloader', compact(
            'user',
            'totalVehicles',
            'totalMaterials',
            'totalReceivedQty',
            'lastReceiving',
            'recentUnloadings'
        ));
    }

    /**
     * Dispatch Dashboard
     */
    public function dispatch()
    {
        $user = Auth::user();

        // Total dispatched orders
        $totalDispatches = DB::table('dispatch_material')
            ->where('status', 'dispatched')
            ->count();

        // Total dispatched quantity
        $totalDispatchQty = DB::table('dispatch_material')
            ->where('status', 'dispatched')
            ->sum('dispatch_qty');

        // Total dispatched vehicles
        $totalVehicles = DB::table('dispatch_material as dm')
            ->join('gate_and_gaadi as gate', 'gate.id', '=', 'dm.gate_id')
            ->where('dm.status', 'dispatched')
            ->distinct('gate.id')
            ->count('gate.id');

        // Total completed production KG
        $totalProduction = DB::table('work_orders_and_process as wop')
            ->whereNull('wop.delete_at')
            ->where('wop.status', 'completed')
            ->whereIn(
                'wop.sale_order_item_id',
                DB::table('dispatch_material')
                    ->select('sale_order_item_id')
                    ->where('status', 'dispatched')
            )
            ->selectRaw("
                SUM(
                    CASE
                        WHEN COALESCE(finished_pcs_weight, 0) > 0
                            THEN finished_pcs_weight

                        WHEN total_full_bundle_count IS NOT NULL
                            OR lose_bundle_weight IS NOT NULL
                        THEN (
                            COALESCE(total_full_bundle_count, 0)
                            * COALESCE(fs_bundle_weight, 0)
                            + COALESCE(lose_bundle_weight, 0)
                        ) * CASE
                            WHEN LOWER(COALESCE(fs_bundle_unit, ''))
                                IN ('2', 'ton', 'tons')
                            THEN 1000 ELSE 1
                        END

                        ELSE COALESCE(material_send_for_work, 0) * 1000
                    END
                ) as total_kg
            ")
            ->value('total_kg') ?? 0;

        // Recent dispatches
        $recentDispatches = DB::table('dispatch_material as dm')
            ->join('gate_and_gaadi as gate', 'gate.id', '=', 'dm.gate_id')
            ->join('customers as customer', 'customer.id', '=', 'gate.cust_id')
            ->join('sale_orders as so', 'so.id', '=', 'dm.sale_order_id')
            ->join('sale_order_items as soi', 'soi.id', '=', 'dm.sale_order_item_id')
            ->leftJoin('products as product', 'product.id', '=', 'soi.product_id')
            ->leftJoin('users as dispatch_user', 'dispatch_user.id', '=', 'dm.created_by')
            ->where('dm.status', 'dispatched')
            ->select([
                'dm.id',
                'dm.updated_at',
                'dm.dispatch_qty',
                'dm.dispatch_qty_unit',
                'gate.truckno',
                'customer.company_name',
                'so.customer_po_no',
                'product.product_name',
                'dispatch_user.first_name',
                'dispatch_user.last_name',
            ])
            ->latest('dm.updated_at')
            ->limit(10)
            ->get();

        return view('dashboard.dispatch', compact(
            'user',
            'totalDispatches',
            'totalDispatchQty',
            'totalVehicles',
            'totalProduction',
            'recentDispatches'
        ));
    }

    /**
     * Lab Dashboard
     */
    // public function lab()
    // {
    //     $user = Auth::user();

    //     return view('dashboard.lab', compact('user'));
    // }

    /**
     * Production Dashboard
     */
     public function production()
    {
        $user = Auth::user();

        $workTable = 'work_orders_and_process';
        $lossTable = 'work_order_loss';

        // Check tables
        if (
            !Schema::hasTable($workTable) ||
            !Schema::hasTable($lossTable)
        ) {
            abort(500, 'Production tables not found.');
        }

        // Get table columns
        $workColumns = Schema::getColumnListing($workTable);
        $lossColumns = Schema::getColumnListing($lossTable);

        // Find matching column
        $findColumn = function ($columns, $candidates) {
            foreach ($candidates as $candidate) {
                if (in_array($candidate, $columns)) {
                    return $candidate;
                }
            }

            return null;
        };

        // Work order columns
        $workQtyCol = $findColumn($workColumns, [
            'produced_qty',
            'production_qty',
            'process_qty',
            'quantity',
            'qty',
            'total_qty'
        ]);

        $workDateCol = $findColumn($workColumns, [
            'production_date',
            'process_date',
            'work_date',
            'date',
            'created_at'
        ]);

        $statusCol = $findColumn($workColumns, [
            'status',
            'work_status',
            'production_status'
        ]);

        $productCol = $findColumn($workColumns, [
            'product_name',
            'item_name',
            'product',
            'item'
        ]);

        $orderCol = $findColumn($workColumns, [
            'work_order_no',
            'work_order_number',
            'order_no',
            'wo_no'
        ]);

        // Scrap columns
        $scrapQtyCol = $findColumn($lossColumns, [
            'loss_qty',
            'scrap_qty',
            'waste_qty',
            'quantity',
            'qty',
            'loss'
        ]);

        $scrapDateCol = $findColumn($lossColumns, [
            'loss_date',
            'scrap_date',
            'date',
            'created_at'
        ]);

        // --------------------------------
        // PRODUCTION CALCULATIONS
        // --------------------------------

        // Total production
        $totalProduction = 0;

        if ($workQtyCol) {
            $totalProduction = (float) DB::table($workTable)
                ->sum($workQtyCol);
        }

        // Today's production
        $todayProduction = 0;

        if ($workQtyCol && $workDateCol) {
            $todayProduction = (float) DB::table($workTable)
                ->whereDate($workDateCol, Carbon::today())
                ->sum($workQtyCol);
        }

        // Pending production
        $pendingProduction = 0;

        if ($workQtyCol && $statusCol) {
            $pendingProduction = (float) DB::table($workTable)
                ->where(function ($query) use ($statusCol) {
                    $query->whereRaw(
                        "LOWER(`{$statusCol}`) LIKE ?",
                        ['%pending%']
                    )->orWhereRaw(
                        "LOWER(`{$statusCol}`) LIKE ?",
                        ['%process%']
                    );
                })
                ->sum($workQtyCol);
        }

        // --------------------------------
        // SCRAP CALCULATIONS
        // --------------------------------

        // Total scrap
        $totalScrap = 0;

        if ($scrapQtyCol) {
            $totalScrap = (float) DB::table($lossTable)
                ->sum($scrapQtyCol);
        }

        // Today's scrap
        $todayScrap = 0;

        if ($scrapQtyCol && $scrapDateCol) {
            $todayScrap = (float) DB::table($lossTable)
                ->whereDate($scrapDateCol, Carbon::today())
                ->sum($scrapQtyCol);
        }

        // --------------------------------
        // PRODUCTION EFFICIENCY
        // --------------------------------

        $efficiency = ($totalProduction + $totalScrap) > 0
            ? round(
                ($totalProduction / ($totalProduction + $totalScrap)) * 100,
                2
            )
            : 0;

        // --------------------------------
        // LAST 7 DAYS PRODUCTION CHART
        // --------------------------------

        $productionLabels = [];
        $productionData = [];

        for ($i = 6; $i >= 0; $i--) {

            $date = Carbon::today()->subDays($i);

            $productionLabels[] = $date->format('d M');

            $amount = 0;

            if ($workQtyCol && $workDateCol) {
                $amount = DB::table($workTable)
                    ->whereDate($workDateCol, $date->toDateString())
                    ->sum($workQtyCol);
            }

            $productionData[] = (float) $amount;
        }

        // --------------------------------
        // PRODUCT WISE PRODUCTION
        // --------------------------------

        // Always use Collection, never empty array
        $productWise = collect();

        if ($workQtyCol && $productCol) {

            $productWise = DB::table($workTable)
                ->select(
                    $productCol . ' as product',
                    DB::raw("SUM(`{$workQtyCol}`) as total")
                )
                ->whereNotNull($productCol)
                ->groupBy($productCol)
                ->orderByDesc('total')
                ->limit(6)
                ->get();
        }

        // --------------------------------
        // RECENT WORK ORDERS
        // --------------------------------

        $recentOrders = DB::table($workTable)
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        // --------------------------------
        // RECENT SCRAP RECORDS
        // --------------------------------

        $recentScrap = DB::table($lossTable)
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        // --------------------------------
        // DASHBOARD VIEW
        // --------------------------------

        return view('dashboard.production', compact(
            'user',
            'totalProduction',
            'todayProduction',
            'pendingProduction',
            'totalScrap',
            'todayScrap',
            'efficiency',
            'productionLabels',
            'productionData',
            'productWise',
            'recentOrders',
            'recentScrap',
            'workQtyCol',
            'workDateCol',
            'statusCol',
            'productCol',
            'orderCol',
            'scrapQtyCol',
            'scrapDateCol'
        ));
    }

    /**
     * Account Dashboard
     */
    public function account()
    {
        $user = Auth::user();

        $baseQuery = Invoice::query();

        // Financial Summary
        $totalInvoices = (clone $baseQuery)->count();

        $totalInvoiceAmount = (clone $baseQuery)->sum('total_amount');

        $totalGst = (clone $baseQuery)->sum('gst_amount');

        $totalTds = (clone $baseQuery)->sum('tds_amount');

        $totalTcs = (clone $baseQuery)->sum('tcs_amount');

        $totalTaxable = (clone $baseQuery)->sum('taxable_amount');

        $totalDiscount = (clone $baseQuery)->sum('cash_discount_amount');

        // Current Month
        $currentMonthInvoices = (clone $baseQuery)
            ->whereMonth('invoice_date', now()->month)
            ->whereYear('invoice_date', now()->year)
            ->count();

        $currentMonthAmount = (clone $baseQuery)
            ->whereMonth('invoice_date', now()->month)
            ->whereYear('invoice_date', now()->year)
            ->sum('total_amount');

        // Previous Month
        $previousMonthAmount = (clone $baseQuery)
            ->whereMonth('invoice_date', now()->subMonth()->month)
            ->whereYear('invoice_date', now()->subMonth()->year)
            ->sum('total_amount');

        // Monthly Invoice Chart - Last 6 Months
        $monthlyInvoices = Invoice::selectRaw("
                YEAR(invoice_date) as year,
                MONTH(invoice_date) as month,
                COUNT(*) as invoice_count,
                SUM(total_amount) as total_amount
            ")
            ->whereDate('invoice_date', '>=', now()->copy()->subMonths(5)->startOfMonth())
            ->whereDate('invoice_date', '<=', now()->endOfMonth())
            ->groupByRaw('YEAR(invoice_date), MONTH(invoice_date)')
            ->orderByRaw('YEAR(invoice_date), MONTH(invoice_date)')
            ->get()
            ->keyBy(function ($item) {
                return $item->year . '-' . $item->month;
            });

        $chartLabels = [];
        $chartAmounts = [];
        $chartCounts = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->copy()->subMonths($i);
            $key = $date->year . '-' . $date->month;

            $chartLabels[] = $date->format('M Y');

            $chartAmounts[] = round(
                (float) ($monthlyInvoices[$key]->total_amount ?? 0),
                2
            );

            $chartCounts[] = (int) ($monthlyInvoices[$key]->invoice_count ?? 0);
        }

        // Recent Invoices
        $invoices = Invoice::with([
            'gate.customer:id,company_name',
        ])
            ->latest('invoice_date')
            ->latest('id')
            ->paginate(10);

        return view('dashboard.account', compact(
            'user',
            'totalInvoices',
            'totalInvoiceAmount',
            'totalGst',
            'totalTds',
            'totalTcs',
            'totalTaxable',
            'totalDiscount',
            'currentMonthInvoices',
            'currentMonthAmount',
            'previousMonthAmount',
            'chartLabels',
            'chartAmounts',
            'chartCounts',
            'invoices'
        ));
    }
}
