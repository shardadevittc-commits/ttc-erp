<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Customer;
use App\Models\GateAndGaadi;
use App\Models\Weight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class WeightController extends Controller
{
    public function index(Request $request)
    {
        $parties = Customer::orderBy('id')->get();

        $materialEntries = GateAndGaadi::with('customer')
            ->whereIn('formlist', [
                'Material In',
                'Material Out',
                'Return',
            ])
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $materialEntries->getCollection()->each(function ($entry) {
            $weighRecord = DB::table('weigh')
                ->where('vehicle_id', $entry->id)
                ->where('cust_id', $entry->cust_id)
                ->first();

            $entry->setAttribute(
                'weigh',
                $weighRecord ? (array) $weighRecord : null
            );
        });

        // Pagination collection ko form type ke according separate karein.
        $vehiclesInYard = $materialEntries->getCollection()
            ->where('formlist', 'Material In')
            ->values();

        $vehiclesOutsideYard = $materialEntries->getCollection()
            ->where('formlist', 'Material Out')
            ->values();

        $scrapVehicles = $materialEntries->getCollection()
            ->where('formlist', 'Return')
            ->values();
        //echo"<pre>";print_r($materialEntries->toArray());die;
        return view('weight.index', compact(
            'parties',
            'materialEntries',
            'vehiclesInYard',
            'vehiclesOutsideYard',
            'scrapVehicles'
        ));
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

                'loadedweight' => $request->formlist === 'Material In'
                    ? $request->loadedweight
                    : null,

                'tareweight' => in_array($request->formlist, ['Material Out', 'Return'])
                    ? $request->tareweight
                    : null,

                // Logged-in user ID
                'created_by' => Auth::id(),
            ];

            // Vehicle Entry Slip
            if ($request->hasFile('vehicleentryslip')) {
                $data['vehicleentryslip'] = $request
                    ->file('vehicleentryslip')
                    ->store('vehicle-entry-slips', 'public');
            }

            // Weighment Slip
            if ($request->hasFile('weighmentslip')) {
                $data['weighmentslip'] = $request
                    ->file('weighmentslip')
                    ->store('weighment-slips', 'public');
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

        if ($id) {
            $weight = Weight::where('vehicle_id', $id)
                ->when($custId, function ($query) use ($custId) {
                    $query->where('cust_id', $custId);
                })
                ->where('formlist', $formlist)
                ->first();
        }

        if ($materialIn) {
            $materialIn->loadedweight       = $weight->loadedweight ?? $materialIn->loadedweight ?? null;
            $materialIn->tareweight         = $weight->tareweight ?? $materialIn->tareweight ?? null;
            $materialIn->weighmentslip      = $weight->weighmentslip ?? $materialIn->weighmentslip ?? null;
            $materialIn->rstno              = $weight->rstno ?? $materialIn->rstno ?? null;
            $materialIn->vehicle_weighId    = $weight->id ?? $materialIn->vehicle_weighId ?? null;
        }
        //echo"<pre>";print_r($materialIn->toArray());die;
        return view('weight.material_weight', compact(
            'user',
            'parties',
            'materialIn',
            'id',
            'custId',
            'formlist'
        ));
    }

    public function history(Request $request)
    {
        $parties = Customer::orderBy('id')->get();

        return view('weight.history', compact(
            'parties',
        ));
    }
}
