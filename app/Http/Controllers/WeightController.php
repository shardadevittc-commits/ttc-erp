<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Customer;
use App\Models\GateAndGaadi;
use App\Models\Weight;
use App\Models\PurchaseOrderitemReceiving;
use App\Models\UnloadWeight;
use App\Models\SaleOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class WeightController extends Controller
{

    public function index(Request $request)
    {
        $parties = Customer::orderBy('id')->get();

        $materialEntries = GateAndGaadi::with('customer')
            ->whereIn('formlist', ['Material In', 'Material Out', 'Return'])
            ->whereNotExists(function ($query) {
                $query->select(\Illuminate\Support\Facades\DB::raw(1))
                    ->from('unload_weights')
                    ->join('weights', function ($join) {
                        $join->on('weights.id', '=', 'unload_weights.weight_id')
                            ->on('weights.vehicle_id', '=', 'unload_weights.vehicle_id');
                    })
                    ->whereColumn('unload_weights.vehicle_id', 'gate_and_gaadi.id')
                    ->whereColumn('unload_weights.formlist', 'gate_and_gaadi.formlist')
                    ->whereColumn('weights.cust_id', 'gate_and_gaadi.cust_id');
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $materialEntries->getCollection()->each(function ($entry) {
            $weighRecord = Weight::where('vehicle_id', $entry->id)
                ->where('cust_id', $entry->cust_id)
                ->first();

            $entry->setAttribute(
                'weigh',
                $weighRecord ? $weighRecord->toArray() : null
            );
        });

        // Material In
        $vehiclesInYard = $materialEntries->getCollection()->where('formlist', 'Material In')->values();

        // Material Out
        $vehiclesOutsideYard = $materialEntries->getCollection()->where('formlist', 'Material Out')->values();

        // Return
        $scrapVehicles = $materialEntries->getCollection()->where('formlist', 'Return')->values();

        return view('weight.index', compact('parties', 'materialEntries', 'vehiclesInYard', 'vehiclesOutsideYard', 'scrapVehicles'));
    }


    public function add(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | POST: Save First Weight
        |--------------------------------------------------------------------------
        */

        if ($request->isMethod('post')) {

            $validated = $request->validate([
                // Accept either vehicle_id or id
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
                return back()
                    ->withInput()
                    ->withErrors(['id' => 'Selected vehicle ID does not exist.']);
            }

            $data = [
                // GateAndGaadi ID is saved in weights.vehicle_id
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
                return redirect()
                    ->route('material_weight', [
                        'id' => $vehicle->id,
                        'cust_id' => $request->cust_id,
                        'formlist' => $request->formlist,
                    ])
                    ->with('error', 'First weight record already exists.');
            }

            Weight::create($data);

            return redirect()
                ->route('material_weight', [
                    'id' => $vehicle->id,
                    'cust_id' => $request->cust_id,
                    'formlist' => $request->formlist,
                ])
                ->with('success', 'Vehicle first weight saved successfully.');
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

            $po_receiving = PurchaseOrderitemReceiving::query()
                ->leftJoin('purchase_order_items as poi','poitem_receiving.purchase_order_item_id','=','poi.id')
                ->leftJoin('products as p', 'poi.product_id', '=', 'p.id')
                ->leftJoin('grades as g', 'poitem_receiving.grade_id', '=', 'g.id')
                ->leftJoin('sizes as s', 'poitem_receiving.size_id', '=', 's.id')
                ->where('poitem_receiving.weight_id', $id)
                ->when($custId, function ($query) use ($custId) {
                    $query->where('poitem_receiving.customer_id', $custId);
                })
                ->select(
                    'poitem_receiving.size_id',
                    's.size_name as size_name',
                    'poitem_receiving.purchase_order_item_id',
                    'poi.product_id',
                    'p.product_name as product_name',
                    'poitem_receiving.grade_id',
                    'g.grade_name as grade_name',
                    'poitem_receiving.po_item_qty',
                    'poitem_receiving.received_qty'
                )->get()->toArray();
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

        return view('weight.material_weight', compact(
            'user',
            'parties',
            'materialIn',
            'id',
            'custId',
            'formlist',
            'weight',
            'weigh'
        ));
    }

    public function history(Request $request)
    {
        $wt = Weight::where('first_weight', 'done')->where('second_weight', 'done')->get();

        foreach ($wt as $weight) {

            // Customer name fetch
            $customer = Customer::where('id', $weight->cust_id)->first();
            $weight->customer_name = $customer->company_name ?? 'N/A';

            // Unload weight fetch
            $weight->unload_weight = UnloadWeight::where('weight_id', $weight->id)->where('vehicle_id', $weight->vehicle_id)->get()->toArray();
        }

        return view('weight.history', compact('wt'));
    }

    public function unload_weight(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer|exists:weights,id',
            'vehicle_id' => 'required|integer',
            'cust_id' => 'required|integer',
            'formlist' => 'required|string',

            'tareweight' => 'nullable|numeric|min:0',
            'loadedweight' => 'nullable|numeric|min:0',
            'unload_tare_weight' => 'nullable|numeric|min:0',
            'unload_loaded_weight' => 'nullable|numeric|min:0',
            'material_weight' => 'nullable|numeric|min:0',

            'po_receiving' => 'required|array|min:1',
            'po_receiving.*.size_id' => 'nullable|integer',
            'po_receiving.*.purchase_order_item_id' => 'nullable|integer',
            'po_receiving.*.product_id' => 'nullable|integer',
            'po_receiving.*.grade_id' => 'nullable|integer',
            'po_receiving.*.received_qty' => 'required|numeric|min:0',

            'actual_qty' => 'required|array',
            'actual_qty.*' => 'required|numeric|min:0',
        ]);
        $entries = $validated['po_receiving'];
        $actualQty = $validated['actual_qty'];

        // Har material entry ke liye actual quantity required hai.
        foreach ($entries as $index => $entry) {
            if (!array_key_exists($index, $actualQty)) {
                return back()
                    ->withInput()
                    ->withErrors([
                        "actual_qty.$index" => 'Actual quantity is required for every material entry.',
                    ]);
            }
        }

        if (isset($validated['id'])) {
            Weight::where('id', $validated['id'])
            ->update(['second_weight' => 'done', 'material_weight' => $validated['material_weight'],
            'unload_tare_weight' => $validated['unload_tare_weight'] ?? 0, 'unload_loaded_weight' => $validated['unload_loaded_weight'] ?? 0]);
        }


        DB::transaction(function () use ($validated, $entries, $actualQty) {

            foreach ($entries as $index => $entry) {

                UnloadWeight::create([
                    'weight_id'             => $validated['id'],
                    'vehicle_id'            => $validated['vehicle_id'],
                    'cust_id'               => $validated['cust_id'],
                    'formlist'              => $validated['formlist'],
                    'tareweight'            => $validated['tareweight'],
                    'loadedweight'          => $validated['loadedweight'],
                    'unload_tare_weight'    => $validated['unload_tare_weight'] ?? 0,
                    'unload_loaded_weight'  => $validated['unload_loaded_weight'] ?? 0,
                    'material_weight'       => $validated['material_weight'],
                    'size_id'               => $entry['size_id'] ?? null,
                    'purchase_order_item_id'=> $entry['purchase_order_item_id'] ?? null,
                    'product_id'            => $entry['product_id'] ?? null,
                    'grade_id'              => $entry['grade_id'] ?? null,
                    'received_qty'          => $entry['received_qty'],
                    'actual_qty'            => $actualQty[$index],
                ]);
            }
        });

        return back()->with('success', 'Unload weight records saved successfully.');
    }
}
