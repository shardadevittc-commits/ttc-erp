<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DispatchMaterial;
use App\Models\GateAndGaadi;
use App\Models\PurchaseOrderitemReceiving;
use App\Models\SaleOrderItem;
use App\Models\UnloadWeight;
use App\Models\Weight;
use App\Models\WorkOrderProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WeightController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureWeightDepartment();

        $materialEntries = GateAndGaadi::query()
            ->select(['id', 'cust_id', 'formlist', 'truckno', 'tokenno', 'created_at'])
            ->with('customer:id,company_name')
            ->whereIn('formlist', ['Material In', 'Material Out', 'Return'])
            /* ->where(function ($query) {
                $query->where('formlist', '!=', 'Material Out')
                    ->orWhereExists(function ($dispatches) {
                        $dispatches->select(DB::raw(1))
                            ->from('dispatch_material')
                            ->whereColumn('dispatch_material.gate_id', 'gate_and_gaadi.id')
                            ->where('dispatch_material.status', 'sent_for_weight');
                    });
            }) */
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
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

        // Material In
        $vehiclesInYard = $materialEntries->getCollection()->where('formlist', 'Material In')->values();

        // Material Out
        $vehiclesOutsideYard = $materialEntries->getCollection()->where('formlist', 'Material Out')->values();

        // Return
        $scrapVehicles = $materialEntries->getCollection()->where('formlist', 'Return')->values();

        return view('weight.index', compact('materialEntries', 'vehiclesInYard', 'vehiclesOutsideYard', 'scrapVehicles'));
    }

    // 18/11/26
    /* public function add(Request $request)
    {
        
        $this->ensureWeightDepartment();
        //  POST: Save First Weight 

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

            if (! $vehicle) {
                return back()
                    ->withInput()
                    ->withErrors(['id' => 'Selected vehicle ID does not exist.']);
            }
            if ((int) $vehicle->cust_id !== (int) $request->cust_id
                || $vehicle->formlist !== $request->formlist
                || strtoupper((string) $vehicle->truckno) !== strtoupper($request->truckno)) {
                return back()
                    ->withInput()
                    ->withErrors(['vehicle_id' => 'The vehicle, truck number, company, and entry type do not match.']);
            }

            if ($vehicle->formlist === 'Material Out' && ! DispatchMaterial::query()
                    ->where('gate_id', $vehicle->id)
                    ->where('status', 'sent_for_weight')
                    ->exists()) {
                abort(403, 'This vehicle has no items currently assigned to the Weight Department.');
            }

            $data = [
                // GateAndGaadi ID is saved in weights.vehicle_id
                'vehicle_id' => $vehicle->id,

                'truckno' => strtoupper($request->truckno),
                'cust_id' => $request->cust_id,
                'formlist' => $request->formlist,
                'rstno' => $request->rstno,

                'loadedweight' => $request->formlist === 'Material In' ? $request->loadedweight : null,

                'tareweight' => in_array($request->formlist,['Material Out', 'Return'])
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

            DB::transaction(function () use ($data, $vehicle) {
                GateAndGaadi::query()->whereKey($vehicle->id)->lockForUpdate()->firstOrFail();
                $existingWeight = Weight::query()
                    ->select('id')
                    ->where('vehicle_id', $vehicle->id)
                    ->where('cust_id', $vehicle->cust_id)
                    ->where('formlist', $vehicle->formlist)
                    ->lockForUpdate()
                    ->exists();
                if ($existingWeight) {
                    throw ValidationException::withMessages([
                        'vehicle_id' => 'A first-weight record already exists for this vehicle.',
                    ]);
                }

                $weight = Weight::create($data);

                if ($vehicle->formlist === 'Material Out') {
                    DispatchMaterial::query()
                        ->where('gate_id', $vehicle->id)
                        ->where('status', 'sent_for_weight')
                        ->whereNull('weight_id')
                        ->update(['weight_id' => $weight->id]);
                }
            });

            return redirect()
                ->route('material_weight', [
                    'id' => $vehicle->id,
                    'cust_id' => $request->cust_id,
                    'formlist' => $request->formlist,
                ])
                ->with('success', 'Vehicle first weight saved successfully.');
        }

        // GET: Load Vehicle Weight Form

        $user = Auth::user();
        $parties = Customer::query()->select(['id', 'company_name'])->orderBy('company_name')->get();

        // Accept id or vehicle_id in query string
        $id = $request->query('id') ?: $request->query('vehicle_id');

        $custId = $request->query('cust_id');
        $formlist = $request->query('formlist', 'Material In');

        $materialIn = $id ? GateAndGaadi::find($id) : null;
        
        //  if ($formlist === 'Material Out') {
        //     abort_unless($materialIn
        //         && (int) $materialIn->cust_id === (int) $custId
        //         && $materialIn->formlist === 'Material Out'
        //         && DispatchMaterial::query()
        //             ->where('gate_id', $materialIn->id)
        //             ->where('status', 'sent_for_weight')
        //             ->exists(), 404);
        // }

        $weight = null;
        $po_receiving = collect();
        $hasSelectedDispatchItems = $formlist !== 'Material Out';

        // | Get Existing Weight

        if ($id) {

            $weight = Weight::where('vehicle_id', $id)
                ->select([
                    'id',
                    'vehicle_id',
                    'cust_id',
                    'formlist',
                    'loadedweight',
                    'tareweight',
                    'weighmentslip',
                    'vehicleentryslip',
                    'rstno',
                    'material_weight',
                    'unload_tare_weight',
                    'unload_loaded_weight',
                    'first_weight',
                    'second_weight',
                ])
                ->when($custId, function ($query) use ($custId) {
                    $query->where('cust_id', $custId);
                })
                ->where('formlist', $formlist)
                ->first();

            if ($formlist !== 'Material Out') {
                $po_receiving = PurchaseOrderitemReceiving::query()
                    ->leftJoin('purchase_order_items as poi', 'poitem_receiving.purchase_order_item_id', '=', 'poi.id')
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
        }

        // Material Out Items
        
        if ($formlist === 'Material Out') {
            $po_receiving = DB::table('dispatch_material as dm')
                ->join('sale_order_items as soi', 'soi.id', '=', 'dm.sale_order_item_id')
                ->leftJoin('products as p', 'soi.product_id', '=', 'p.id')
                ->leftJoin('grades as g', 'soi.grade_id', '=', 'g.id')
                ->leftJoin('sizes as s', 'soi.size_id', '=', 's.id')
                ->leftJoin('brands as b', 'soi.brand_id', '=', 'b.id')
                ->where('dm.gate_id', $id)
                ->whereColumn('dm.sale_order_id', 'soi.sale_order_id')
                ->where('dm.status', 'sent_for_weight')
                ->when($weight, fn ($query) => $query->where('dm.weight_id', $weight->id))
                ->select(
                    'dm.id as dispatch_material_id',
                    'dm.sale_order_id',
                    'dm.sale_order_item_id',
                    'dm.actual_qty',
                    'soi.size_id',
                    's.size_name as size_name',
                    'soi.product_id',
                    'p.product_name as product_name',
                    'soi.grade_id',
                    'g.grade_name as grade_name',
                    'soi.brand_id',
                    'b.brand_name as brand_name',
                    'soi.item_qty as received_qty',
                    'soi.qty_type',
                    'soi.sale_item_prices',
                    'soi.dispatched_qty',
                    'soi.pending_qty'
                )
                ->get();
            $hasSelectedDispatchItems = $po_receiving->isNotEmpty();
        }

        // Attach Weight Details

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
            'weigh',
            'hasSelectedDispatchItems',
        ));
    } */

    
    public function add(Request $request)
    {
        $this->ensureWeightDepartment();

        // ==========================================
        // POST: SAVE FIRST WEIGHT
        // ==========================================

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

            // Get Vehicle ID
            $vehicleId = $request->input('vehicle_id') ?: $request->input('id');

            // Find Vehicle
            $vehicle = GateAndGaadi::find($vehicleId);

            if (!$vehicle) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'id' => 'Selected vehicle ID does not exist.'
                    ]);
            }

            // Verify Vehicle Details
            if (
                (int) $vehicle->cust_id !== (int) $request->cust_id ||
                $vehicle->formlist !== $request->formlist ||
                strtoupper((string) $vehicle->truckno) !== strtoupper($request->truckno)
            ) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'vehicle_id' => 'The vehicle, truck number, company, and entry type do not match.'
                    ]);
            }

            // ==========================================
            // COMMON WEIGHT DATA
            // ==========================================

            $data = [
                'vehicle_id' => $vehicle->id,
                'truckno' => strtoupper($request->truckno),
                'cust_id' => $request->cust_id,
                'formlist' => $request->formlist,
                'rstno' => $request->rstno,

                'loadedweight' => null,
                'tareweight' => null,

                'first_weight' => 'done',
                'second_weight' => 'pending',

                'created_by' => Auth::id(),
            ];

            // ==========================================
            // WEIGHT TYPE WISE DATA
            // ==========================================

            switch ($vehicle->formlist) {

                case 'Material In':

                    $data['loadedweight'] = $request->loadedweight;
                    $data['tareweight'] = null;

                    break;

                case 'Material Out':

                    // First Weight: Only Tare Weight
                    // No DispatchMaterial validation at this stage

                    $data['tareweight'] = $request->tareweight;
                    $data['loadedweight'] = null;

                    break;

                case 'Return':

                    $data['tareweight'] = $request->tareweight;
                    $data['loadedweight'] = null;

                    break;

                default:

                    abort(422, 'Invalid material entry type.');
            }

            // ==========================================
            // VEHICLE ENTRY SLIP
            // ==========================================

            if ($request->hasFile('vehicleentryslip')) {

                $data['vehicleentryslip'] = $request
                    ->file('vehicleentryslip')
                    ->store('vehicle-entry-slips', 'public');

            } elseif ($request->filled('existing_vehicleentryslip')) {

                $data['vehicleentryslip'] = $request->existing_vehicleentryslip;
            }

            // ==========================================
            // WEIGHMENT SLIP
            // ==========================================

            if ($request->hasFile('weighmentslip')) {

                $data['weighmentslip'] = $request
                    ->file('weighmentslip')
                    ->store('weighment-slips', 'public');
            }

            // ==========================================
            // SAVE FIRST WEIGHT TRANSACTION
            // ==========================================

            DB::transaction(function () use ($data, $vehicle) {

                // Lock Vehicle
                GateAndGaadi::query()
                    ->whereKey($vehicle->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                // Check Existing First Weight
                $existingWeight = Weight::query()
                    ->where('vehicle_id', $vehicle->id)
                    ->where('cust_id', $vehicle->cust_id)
                    ->where('formlist', $vehicle->formlist)
                    ->lockForUpdate()
                    ->exists();

                if ($existingWeight) {

                    throw ValidationException::withMessages([
                        'vehicle_id' => 'A first-weight record already exists for this vehicle.'
                    ]);
                }

                // Save Weight
                Weight::create($data);

                // IMPORTANT:
                // Material Out DispatchMaterial validation and
                // weight_id assignment will happen at SECOND WEIGHT stage.
            });

            return redirect()
                ->route('material_weight', [
                    'id' => $vehicle->id,
                    'cust_id' => $request->cust_id,
                    'formlist' => $request->formlist,
                ])
                ->with('success', 'Vehicle first weight saved successfully.');
        }


        // ==========================================
        // GET: LOAD VEHICLE WEIGHT FORM
        // ==========================================

        $user = Auth::user();

        $parties = Customer::query()
            ->select(['id', 'company_name'])
            ->orderBy('company_name')
            ->get();

        // Accept id or vehicle_id
        $id = $request->query('id') ?: $request->query('vehicle_id');

        $custId = $request->query('cust_id');

        $formlist = $request->query('formlist', 'Material In');

        // Get Vehicle
        $materialIn = $id ? GateAndGaadi::find($id) : null;

        $weight = null;
        $po_receiving = collect();

        $hasSelectedDispatchItems = $formlist !== 'Material Out';


        // ==========================================
        // GET EXISTING WEIGHT
        // ==========================================

        if ($id) {

            $weight = Weight::query()
                ->where('vehicle_id', $id)
                ->where('cust_id', $custId)
                ->where('formlist', $formlist)
                ->select([
                    'id',
                    'vehicle_id',
                    'cust_id',
                    'formlist',
                    'loadedweight',
                    'tareweight',
                    'weighmentslip',
                    'vehicleentryslip',
                    'rstno',
                    'material_weight',
                    'unload_tare_weight',
                    'unload_loaded_weight',
                    'first_weight',
                    'second_weight',
                ])
                ->first();
        }


        // ==========================================
        // FORM TYPE WISE DATA
        // ==========================================

        switch ($formlist) {

            // ======================================
            // MATERIAL IN
            // ======================================

            case 'Material In':

            // ======================================
            // RETURN
            // ======================================

            case 'Return':

                if ($id) {

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
                            's.size_name as size_name',
                            'poitem_receiving.purchase_order_item_id',
                            'poi.product_id',
                            'p.product_name as product_name',
                            'poitem_receiving.grade_id',
                            'g.grade_name as grade_name',
                            'poitem_receiving.po_item_qty',
                            'poitem_receiving.received_qty'
                        )

                        ->get();
                }

                $hasSelectedDispatchItems = true;

                break;


            // ======================================
            // MATERIAL OUT
            // ======================================

            case 'Material Out':

                if ($id) {

                    $po_receiving = DB::table('dispatch_material as dm')

                        ->join(
                            'sale_order_items as soi',
                            'soi.id',
                            '=',
                            'dm.sale_order_item_id'
                        )

                        ->leftJoin('products as p', 'soi.product_id', '=', 'p.id')

                        ->leftJoin('grades as g', 'soi.grade_id', '=', 'g.id')

                        ->leftJoin('sizes as s', 'soi.size_id', '=', 's.id')

                        ->leftJoin('brands as b', 'soi.brand_id', '=', 'b.id')

                        ->where('dm.gate_id', $id)

                        ->whereColumn('dm.sale_order_id', 'soi.sale_order_id')

                        ->where('dm.status', 'sent_for_weight')

                        ->when($weight, function ($query) use ($weight) {
                            $query->where('dm.weight_id', $weight->id);
                        })

                        ->select(
                            'dm.id as dispatch_material_id',
                            'dm.sale_order_id',
                            'dm.sale_order_item_id',
                            'dm.actual_qty',
                            'soi.size_id',
                            's.size_name as size_name',
                            'soi.product_id',
                            'p.product_name as product_name',
                            'soi.grade_id',
                            'g.grade_name as grade_name',
                            'soi.brand_id',
                            'b.brand_name as brand_name',
                            'soi.item_qty as received_qty',
                            'soi.qty_type',
                            'soi.sale_item_prices',
                            'soi.dispatched_qty',
                            'soi.pending_qty'
                        )

                        ->get();
                }

                $hasSelectedDispatchItems = $po_receiving->isNotEmpty();

                break;


            default:

                abort(422, 'Invalid material entry type.');
        }


        // ==========================================
        // ATTACH WEIGHT DETAILS TO VEHICLE
        // ==========================================

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

            // Weight table primary key
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
            'weigh',
            'hasSelectedDispatchItems'
        ));
    }


    public function history(Request $request)
    {
        $this->ensureWeightDepartment();
        $wt = Weight::query()
            ->select([
                'id',
                'cust_id',
                'formlist',
                'truckno',
                'loadedweight',
                'tareweight',
                'material_weight',
                'unload_tare_weight',
                'unload_loaded_weight',
            ])
            ->with('customer:id,company_name')
            ->where('first_weight', 'done')
            ->where('second_weight', 'done')
            ->get()
            ->each(fn (Weight $weight) => $weight->setAttribute(
                'customer_name',
                $weight->customer?->company_name ?? 'N/A'
            ));

        return view('weight.history', compact('wt'));
    }

    public function unload_weight(Request $request)
    {
        $this->ensureWeightDepartment();
        if ($request->input('formlist') === 'Material Out') {
            return $this->saveDispatchSecondWeight($request);
        }

        $validated = $request->validate([
            'id' => 'required|integer|exists:weights,id',
            'vehicle_id' => 'required|integer',
            'cust_id' => 'required|integer',
            'formlist' => 'required|in:Material In,Return',

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
            if (! array_key_exists($index, $actualQty)) {
                return back()
                    ->withInput()
                    ->withErrors([
                        "actual_qty.$index" => 'Actual quantity is required for every material entry.',
                    ]);
            }
        }

        DB::transaction(function () use ($validated, $entries, $actualQty) {
            $weight = Weight::query()
                ->select(['id', 'second_weight'])
                ->whereKey($validated['id'])
                ->where('vehicle_id', $validated['vehicle_id'])
                ->where('cust_id', $validated['cust_id'])
                ->where('formlist', $validated['formlist'])
                ->lockForUpdate()
                ->firstOrFail();
            if ($weight->second_weight !== 'pending') {
                throw ValidationException::withMessages([
                    'id' => 'This vehicle weight has already been completed.',
                ]);
            }

            $weight->update([
                'second_weight' => 'done',
                'material_weight' => $validated['material_weight'],
                'unload_tare_weight' => $validated['unload_tare_weight'] ?? 0,
                'unload_loaded_weight' => $validated['unload_loaded_weight'] ?? 0,
            ]);

            foreach ($entries as $index => $entry) {

                UnloadWeight::create([
                    'weight_id' => $validated['id'],
                    'vehicle_id' => $validated['vehicle_id'],
                    'cust_id' => $validated['cust_id'],
                    'formlist' => $validated['formlist'],
                    'tareweight' => $validated['tareweight'],
                    'loadedweight' => $validated['loadedweight'],
                    'unload_tare_weight' => $validated['unload_tare_weight'] ?? 0,
                    'unload_loaded_weight' => $validated['unload_loaded_weight'] ?? 0,
                    'material_weight' => $validated['material_weight'],
                    'size_id' => $entry['size_id'] ?? null,
                    'purchase_order_item_id' => $entry['purchase_order_item_id'] ?? null,
                    'product_id' => $entry['product_id'] ?? null,
                    'grade_id' => $entry['grade_id'] ?? null,
                    'received_qty' => $entry['received_qty'],
                    'actual_qty' => $actualQty[$index],
                ]);
            }
        });

        return back()->with('success', 'Unload weight records saved successfully.');
    }

    private function saveDispatchSecondWeight(Request $request)
    {
        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:weights,id'],
            'vehicle_id' => ['required', 'integer', 'exists:gate_and_gaadi,id'],
            'cust_id' => ['required', 'integer', 'exists:customers,id'],
            'formlist' => ['required', 'in:Material Out'],
            'unload_loaded_weight' => ['required', 'numeric', 'min:0'],
            'actual_qty' => ['required', 'array', 'min:1'],
            'actual_qty.*' => ['required', 'numeric', 'min:0', 'decimal:0,3'],
            'selected_item_ids' => ['required', 'array', 'min:1'],
            'selected_item_ids.*' => ['required', 'integer', 'distinct'],
        ]);

        $vehicleId = (int) $validated['vehicle_id'];
        $weightId = (int) $validated['id'];
        $itemIds = array_map('intval', $validated['selected_item_ids']);
        $actualQuantities = $validated['actual_qty'];

        if (array_diff($itemIds, array_map('intval', array_keys($actualQuantities))) !== []
            || array_diff(array_map('intval', array_keys($actualQuantities)), $itemIds) !== []) {
            throw ValidationException::withMessages([
                'actual_qty' => 'Enter a final actual quantity for every selected item.',
            ]);
        }

        DB::transaction(function () use ($validated, $vehicleId, $weightId, $itemIds, $actualQuantities) {
            $vehicle = GateAndGaadi::query()
                ->whereKey($vehicleId)
                ->where('cust_id', $validated['cust_id'])
                ->where('formlist', 'Material Out')
                ->lockForUpdate()
                ->firstOrFail();

            $weight = Weight::query()
                ->select(['id', 'second_weight', 'tareweight'])
                ->whereKey($weightId)
                ->where('vehicle_id', $vehicle->id)
                ->where('cust_id', $vehicle->cust_id)
                ->where('formlist', 'Material Out')
                ->lockForUpdate()
                ->firstOrFail();

            if ($weight->second_weight !== 'pending') {
                throw ValidationException::withMessages([
                    'id' => 'Final vehicle weight has already been completed.',
                ]);
            }

            $dispatchRows = DispatchMaterial::query()
                ->where('gate_id', $vehicle->id)
                ->where('weight_id', $weight->id)
                ->where('status', 'sent_for_weight')
                ->whereIn('sale_order_item_id', $itemIds)
                ->orderBy('sale_order_item_id')
                ->lockForUpdate()
                ->select([
                    'id',
                    'gate_id',
                    'sale_order_id',
                    'sale_order_item_id',
                    'weight_id',
                    'actual_qty',
                    'status',
                ])
                ->get();

            if ($dispatchRows->count() !== count($itemIds)
                || $dispatchRows->unique('sale_order_item_id')->count() !== count($itemIds)) {
                throw ValidationException::withMessages([
                    'selected_item_ids' => 'The selected items changed or are no longer pending final weight.',
                ]);
            }
            $dispatchItems = $dispatchRows->keyBy('sale_order_item_id');

            $netMaterialWeight = round(
                (float) $validated['unload_loaded_weight'] - (float) $weight->tareweight,
                2
            );
            $actualTotal = round(array_sum(array_map('floatval', $actualQuantities)), 3);
            if ($netMaterialWeight < 0 || $actualTotal <= 0 || $actualTotal > $netMaterialWeight + 0.01) {
                throw ValidationException::withMessages([
                    'actual_qty' => 'Selected item quantities must total more than zero and cannot exceed the final net vehicle weight.',
                ]);
            }

            $itemDetails = DB::table('sale_order_items as soi')
                ->whereIn('soi.id', $itemIds)
                ->orderBy('soi.id')
                ->lockForUpdate()
                ->select([
                    'soi.id',
                    'soi.sale_order_id',
                    'soi.product_id',
                    'soi.grade_id',
                    'soi.size_id',
                    'soi.item_qty',
                    'soi.qty_type',
                ])
                ->get()
                ->keyBy('id');
            if ($itemDetails->count() !== count($itemIds)) {
                throw ValidationException::withMessages([
                    'selected_item_ids' => 'A selected sale-order item no longer exists.',
                ]);
            }

            $completedWorkOrders = WorkOrderProcess::query()
                ->select([
                    'id',
                    'sale_order_item_id',
                    'fs_bundle_weight',
                    'fs_bundle_unit',
                    'total_full_bundle_count',
                    'lose_bundle_weight',
                    'lose_bundle_count',
                    'per_pcs_weight',
                    'finished_pcs',
                    'no_of_boxes',
                    'loss_box_pcs',
                    'loss_box_count',
                ])
                ->whereIn('sale_order_item_id', $itemIds)
                ->where('status', 'completed')
                ->orderByDesc('id')
                ->get()
                ->unique('sale_order_item_id')
                ->keyBy('sale_order_item_id');

            foreach ($dispatchItems as $itemId => $dispatchItem) {
                $actualQty = round((float) $actualQuantities[$itemId], 3);
                $item = $itemDetails->get($itemId);
                if ((int) $item->sale_order_id !== (int) $dispatchItem->sale_order_id) {
                    throw ValidationException::withMessages([
                        'selected_item_ids' => 'A selected item does not match its dispatch sale order.',
                    ]);
                }
                $workOrder = $completedWorkOrders->get($itemId);
                if (! $workOrder) {
                    throw ValidationException::withMessages([
                        'selected_item_ids' => "Sale order item #{$itemId} has no completed production record.",
                    ]);
                }

                $dispatchItem->actual_qty = $actualQty;
                $dispatchItem->applyProductionSnapshot((int) $item->qty_type, $workOrder);
                $dispatchItem->status = 'weight_completed';
                $dispatchItem->save();

                UnloadWeight::create([
                    'weight_id' => $weight->id,
                    'vehicle_id' => $vehicle->id,
                    'cust_id' => $vehicle->cust_id,
                    'formlist' => 'Material Out',
                    'tareweight' => $weight->tareweight,
                    'unload_loaded_weight' => $validated['unload_loaded_weight'],
                    'material_weight' => $netMaterialWeight,
                    'purchase_order_item_id' => $item?->id,
                    'size_id' => $item?->size_id,
                    'product_id' => $item?->product_id,
                    'grade_id' => $item?->grade_id,
                    'received_qty' => $item?->item_qty ?? 0,
                    'actual_qty' => $actualQty,
                ]);
            }

            $weight->update([
                'second_weight' => 'done',
                'material_weight' => $netMaterialWeight,
                'unload_loaded_weight' => $validated['unload_loaded_weight'],
            ]);
        });

        return redirect()->route('weight.index')
            ->with('success', 'Weight Department process completed. Dispatch quantities are now available.');
    }

    private function ensureWeightDepartment(): void
    {
        $user = auth()->user();
        abort_unless($user && $user->role()->value('short_name') === 'weight', 403);
    }
}
