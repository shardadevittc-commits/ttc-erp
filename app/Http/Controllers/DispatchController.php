<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\GateAndGaadi;
use App\Models\Customer;

class DispatchController extends Controller
{
    public function index(Request $request)
    {
        $gate = GateAndGaadi::with('customer')->where('formlist', 'Material Out')->get();

        $parties = Customer::select('id', 'company_name')->orderBy('company_name')->get();

        //echo"<pre>";print_r($gate->toArray());die;
        return view('dispatch.index', compact('gate', 'parties'));
    }

    /*public function pendingOrders($cust_id)
    {
        $orders = DB::table('sale_orders')->where('customer_id', $cust_id)->orderByDesc('id')->get();

        foreach ($orders as $order) {
            $order->items = DB::table('sale_order_items')->where('sale_order_id', $order->id)->get();
        }
        //echo"<pre>";print_r($orders);die;
        return response()->json(['success' => true,'orders' => $orders,]);
    }*/

    public function pendingOrders($cust_id)
    {
        $orders = DB::table('sale_orders')->where('customer_id', $cust_id)->orderByDesc('id')->get();

        foreach ($orders as $order) {

            $order->items = DB::table('sale_order_items as soi')
                ->leftJoin('grades as g', 'g.id', '=', 'soi.grade_id')
                ->leftJoin('sizes as s', 's.id', '=', 'soi.size_id')
                ->leftJoin('units as u', 'u.id', '=', 'soi.fs_bundle_unit')
                ->where('soi.sale_order_id', $order->id)
                ->select('soi.id', 'soi.sale_order_id', 'soi.grade_id','g.grade_name as grade_name',
                    'soi.size_id', 's.size_name as size_name', 'soi.sale_item_prices', 'soi.item_qty',
                    'soi.fs_bundle_weight', 'soi.fs_bundle_unit')->get();

            foreach ($order->items as $item) {
                $item->basic_rate = $order->basic_rate;
            }
        }
        return response()->json(['success' => true, 'orders' => $orders,]);
    }

    public function selectedOrders(Request $request)
    {
        $validated = $request->validate([
            'order_ids' => ['required', 'array', 'min:1'],
            'order_ids.*' => ['required', 'integer', 'distinct'],
        ]);

        $orderIds = $validated['order_ids'];

        // Fetch actual database records, not browser-submitted rates.
        $orders = SaleOrder::with('items')
            ->whereIn('id', $orderIds)
            ->get();

        // Ensure every submitted order ID exists.
        if ($orders->count() !== count(array_unique($orderIds))) {
            abort(422, 'One or more selected orders are invalid.');
        }

        // Preserve the user's selection order.
        $orders = $orders->sortBy(function ($order) use ($orderIds) {
            return array_search($order->id, $orderIds);
        })->values();

        return view('dispatch.selected-orders', compact('orders'));
    }

}
