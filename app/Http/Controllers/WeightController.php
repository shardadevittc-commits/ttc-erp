<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Customer;
use App\Models\GateAndGaadi;
use App\Models\Weight;
use App\Models\PurchaseOrderitemReceiving;
use App\Models\UnloadWeight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class WeightController extends Controller
{
    public function index(Request $request)
    {
        $parties = Customer::orderBy('id')->get();

        $materialEntries = GateAndGaadi::with('customer')->whereIn('formlist', ['Material In', 'Material Out', 'Return',])->latest('id')->paginate(10)->withQueryString();

        $materialEntries->getCollection()->each(function ($entry) {
            $weighRecord = Weight::where('vehicle_id', $entry->id)
                ->where('cust_id', $entry->cust_id)
                ->first();

            $entry->setAttribute(
                'weigh',
                $weighRecord ? (array) $weighRecord : null
            );
        });

        // Pagination collection ko form type ke according separate karein.
        $vehiclesInYard = $materialEntries->getCollection()->where('formlist', 'Material In')->values();

        $vehiclesOutsideYard = $materialEntries->getCollection()->where('formlist', 'Material Out')->values();

        $scrapVehicles = $materialEntries->getCollection()->where('formlist', 'Return')->values();
        //echo"<pre>";print_r($materialEntries->toArray());die;
        return view('weight.index', compact('parties', 'materialEntries', 'vehiclesInYard', 'vehiclesOutsideYard', 'scrapVehicles'));
    }

    public function add(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'vehicle_id' => 'nullable|integer',
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

            $data = [
                'vehicle_id' => $request->input('vehicle_id'),
                'truckno' => $request->truckno,
                'cust_id' => $request->cust_id,
                'formlist' => $request->formlist,
                'rstno' => $request->rstno,
                'loadedweight' => $request->formlist === 'Material In' ? $request->loadedweight : null,
                'tareweight' => in_array($request->formlist, ['Material Out', 'Return']) ? $request->tareweight : null,
                'first_weight' => 'done',
                'second_weight' => 'pending',
                // Logged-in user ID
                'created_by' => Auth::id(),
            ];

            // Vehicle Entry Slip
            if ($request->hasFile('vehicleentryslip')) {
                $data['vehicleentryslip'] = $request->file('vehicleentryslip')->store('vehicle-entry-slips', 'public');
            }

            // Weighment Slip
            if ($request->hasFile('weighmentslip')) {
                $data['weighmentslip'] = $request->file('weighmentslip')->store('weighment-slips', 'public');
            }

            // Save record
            Weight::create($data);

            return redirect()->route('weight.index') ->with('success', 'Vehicle weight record saved successfully.');
        }

        $user = Auth::user();

        $parties = Customer::all();

        // URL parameters
        $id = $request->query('id');
        $custId = $request->query('cust_id');
        $formlist = $request->query('formlist', 'Material In');

        // Material record
        $materialIn = $id ? GateAndGaadi::find($id) : null;

        $weight = null;
        $po_receiving = collect();
        if ($id) {
            $weight = Weight::where('id', $id)
                ->when($custId, function ($query) use ($custId) {
                    $query->where('cust_id', $custId);
                })
                ->where('formlist', $formlist)
                ->first();

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
                )->get()->toArray();
        }

        if ($materialIn) {
            $materialIn->loadedweight            = $weight->loadedweight ?? $materialIn->loadedweight ?? null;
            $materialIn->tareweight              = $weight->tareweight ?? $materialIn->tareweight ?? null;
            $materialIn->weighmentslip           = $weight->weighmentslip ?? $materialIn->weighmentslip ?? null;
            $materialIn->rstno                   = $weight->rstno ?? $materialIn->rstno ?? null;
            $materialIn->vehicle_weighId         = $weight->id ?? $materialIn->vehicle_weighId ?? null;
            $materialIn->material_weight         = $weight->material_weight ?? $materialIn->material_weight ?? null;
            $materialIn->unload_tare_weight      = $weight->unload_tare_weight ?? $materialIn->unload_tare_weight ?? null;
            $materialIn->unload_loaded_weight    = $weight->unload_loaded_weight ?? $materialIn->unload_loaded_weight ?? null;
            $materialIn->first_weight            = $weight->first_weight ?? $materialIn->first_weight ?? null;
            $materialIn->second_weight           = $weight->second_weight ?? $materialIn->second_weight ?? null;
            $materialIn->po_receiving           = $po_receiving;
        }
        //echo"<pre>";print_r($materialIn->toArray());die;
        return view('weight.material_weight', compact('user', 'parties', 'materialIn', 'id', 'custId', 'formlist'));
    }

    public function history(Request $request)
    {
        $parties = Customer::orderBy('id')->get();

        return view('weight.history', compact('parties'));
    }

    public function unload_weight(Request $request)
    {

        $validated = $request->validate([
            'id' => 'required|integer|exists:weigh,id',
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
//echo"<pre>";print_r($request->all());die;
        $entries = $validated['po_receiving'];
        $actualQty = $validated['actual_qty'];

        // Har material entry ke liye actual quantity required hai.
        foreach ($entries as $index => $entry) {
            if (!array_key_exists($index, $actualQty)) {
                return back()
                    ->withInput()
                    ->withErrors([
                        "actual_qty.$index" =>
                            'Actual quantity is required for every material entry.',
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
