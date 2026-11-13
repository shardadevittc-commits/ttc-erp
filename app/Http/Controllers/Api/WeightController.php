<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\GateAndGaadi;
use App\Models\Weight;
use App\Models\Customer;
use App\Models\PurchaseOrderitemReceiving;

class WeightController extends Controller
{
    public function add(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | POST: Save First Weight
        |--------------------------------------------------------------------------
        */

        if ($request->isMethod('post')) {

            $validated = $request->validate([
                'id' => 'nullable|integer|required_without:vehicle_id',
                'vehicle_id' => 'nullable|integer|required_without:id',

                'truckno' => 'required|string|max:20',
                'cust_id' => 'required|exists:customers,id',
                'formlist' => 'required|in:Material In,Material Out,Return',
                'rstno' => 'required|string|max:100',

                'loadedweight' => 'required_if:formlist,Material In|nullable|numeric|min:0',
                'tareweight' => 'required_if:formlist,Material Out,Return|nullable|numeric|min:0',

                'vehicleentryslip' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
                'weighmentslip' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
                'existing_vehicleentryslip' => 'nullable|string',
            ]);

            // Prefer vehicle_id; otherwise use id
            $vehicleId = $request->input('vehicle_id') ?: $request->input('id');

            // Confirm this vehicle exists in GateAndGaadi
            $vehicle = GateAndGaadi::find($vehicleId);

            if (!$vehicle) {
                return response()->json([
                    'status' => false,
                    'message' => 'Selected vehicle ID does not exist.',
                    'data' => null
                ], 404);
            }

            $data = [
                'vehicle_id' => $vehicle->id,

                'truckno' => strtoupper($request->truckno),
                'cust_id' => $request->cust_id,
                'formlist' => $request->formlist,
                'rstno' => $request->rstno,

                'loadedweight' => $request->formlist === 'Material In'
                    ? $request->loadedweight
                    : null,

                'tareweight' => in_array(
                    $request->formlist,
                    ['Material Out', 'Return']
                )
                    ? $request->tareweight
                    : null,

                'first_weight' => 'done',
                'second_weight' => 'pending',
                'created_by' => Auth::id(),
            ];

            // Vehicle entry slip
            if ($request->hasFile('vehicleentryslip')) {
                $data['vehicleentryslip'] = $request
                    ->file('vehicleentryslip')
                    ->store('vehicle-entry-slips', 'public');

            } elseif ($request->filled('existing_vehicleentryslip')) {
                $data['vehicleentryslip'] = $request->existing_vehicleentryslip;
            }

            // Weighment slip
            if ($request->hasFile('weighmentslip')) {
                $data['weighmentslip'] = $request
                    ->file('weighmentslip')
                    ->store('weighment-slips', 'public');
            }

            // Prevent duplicate first-weight record for same vehicle/customer/type
            $existingWeight = Weight::where('vehicle_id', $vehicle->id)
                ->where('cust_id', $request->cust_id)
                ->where('formlist', $request->formlist)
                ->first();

            if ($existingWeight) {
                return response()->json([
                    'status' => false,
                    'message' => 'First weight record already exists.',
                    'data' => $existingWeight
                ], 409);
            }

            $weight = Weight::create($data);

            return response()->json([
                'status' => true,
                'message' => 'Vehicle first weight saved successfully.',
                'data' => [
                    'vehicle' => $vehicle,
                    'weight' => $weight,
                ]
            ], 201);
        }


        /*
        |--------------------------------------------------------------------------
        | GET: Load Vehicle Weight Form
        |--------------------------------------------------------------------------
        */

        $user = Auth::user();
        $parties = Customer::all();

        // Accept id or vehicle_id in query string
        $id = $request->query('id') ?: $request->query('vehicle_id');

        $custId = $request->query('cust_id');
        $formlist = $request->query('formlist', 'Material In');

        $materialIn = $id ? GateAndGaadi::find($id) : null;

        $weight = null;
        $po_receiving = collect();

        /*
        |--------------------------------------------------------------------------
        | Get Existing Weight
        |--------------------------------------------------------------------------
        */

        if ($id) {

            $weight = Weight::where('vehicle_id', $id)
                ->when($custId, function ($query) use ($custId) {
                    $query->where('cust_id', $custId);
                })
                ->where('formlist', $formlist)
                ->first();

            /*
            |--------------------------------------------------------------------------
            | Material In Receiving Items
            |--------------------------------------------------------------------------
            */

            if ($formlist === 'Material In') {

                $po_receiving = PurchaseOrderitemReceiving::query()
                    ->leftJoin(
                        'purchase_order_items as poi',
                        'poitem_receiving.purchase_order_item_id',
                        '=',
                        'poi.id'
                    )
                    ->leftJoin('products as p', 'poi.product_id', '=', 'p.id')
                    ->leftJoin('grades as g', 'poitem_receiving.grade_id', '=', 'g.id')
                    ->leftJoin('sizes as s', 'poitem_receiving.size_id', '=', 's.id')
                    ->where('poitem_receiving.weight_id', $id)
                    ->when($custId, function ($query) use ($custId) {
                        $query->where('poitem_receiving.customer_id', $custId);
                    })
                    ->select(
                        'poitem_receiving.size_id',
                        's.size_name',
                        'poitem_receiving.purchase_order_item_id',
                        'poi.product_id',
                        'p.product_name',
                        'poitem_receiving.grade_id',
                        'g.grade_name',
                        'poitem_receiving.po_item_qty',
                        'poitem_receiving.received_qty'
                    )
                    ->get();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Material Out Items
        |--------------------------------------------------------------------------
        */

        if ($formlist === 'Material Out') {

            $po_receiving = DB::table('work_orders_and_process as wop')
                ->join('sale_order_items as soi', function ($join) {
                    $join->on('wop.grade_id', '=', 'soi.grade_id')
                        ->on('wop.size_id', '=', 'soi.size_id');
                })
                ->leftJoin('products as p', 'soi.product_id', '=', 'p.id')
                ->leftJoin('grades as g', 'wop.grade_id', '=', 'g.id')
                ->leftJoin('sizes as s', 'wop.size_id', '=', 's.id')
                ->leftJoin('brands as b', 'soi.brand_id', '=', 'b.id')
                ->select(
                    'wop.size_id',
                    's.size_name',
                    'soi.id as sale_order_item_id',
                    'soi.product_id',
                    'p.product_name',
                    'wop.grade_id',
                    'g.grade_name',
                    'soi.brand_id',
                    'b.brand_name',
                    'soi.item_qty as received_qty',
                    'wop.material_send_for_work as po_item_qty',
                    'soi.dispatched_qty',
                    'soi.pending_qty'
                )
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | Attach Weight Details
        |--------------------------------------------------------------------------
        */

        if ($materialIn) {

            $materialIn->loadedweight = $weight->loadedweight
                ?? $materialIn->loadedweight
                ?? null;

            $materialIn->tareweight = $weight->tareweight
                ?? $materialIn->tareweight
                ?? null;

            $materialIn->weighmentslip = $weight->weighmentslip
                ?? $materialIn->weighmentslip
                ?? null;

            $materialIn->vehicleentryslip = $weight->vehicleentryslip
                ?? $materialIn->vehicleentryslip
                ?? null;

            $materialIn->rstno = $weight->rstno
                ?? $materialIn->rstno
                ?? null;

            // This is the Weight table primary key
            $materialIn->vehicle_weighId = $weight->id ?? null;

            $materialIn->material_weight = $weight->material_weight ?? null;
            $materialIn->unload_tare_weight = $weight->unload_tare_weight ?? null;
            $materialIn->unload_loaded_weight = $weight->unload_loaded_weight ?? null;
            $materialIn->first_weight = $weight->first_weight ?? null;
            $materialIn->second_weight = $weight->second_weight ?? null;

            $materialIn->po_receiving = $po_receiving;
        }

        $weigh = $weight;

        // JSON Response instead of Blade View
        return response()->json([
            'status' => true,
            'message' => 'Vehicle weight details fetched successfully.',
            'data' => [
                'user' => $user,
                'parties' => $parties,
                'materialIn' => $materialIn,
                'id' => $id,
                'custId' => $custId,
                'formlist' => $formlist,
                'weight' => $weight,
                'weigh' => $weigh,
                'po_receiving' => $po_receiving,
            ]
        ], 200);
    }
}
