<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Customer;
use App\Models\MaterialIn;
use App\Models\PurchaseOrder;
use App\Models\VehicleWeighRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class GateController extends Controller
{
    public function index(Request $request)
    {
        $parties = Customer::orderBy('id')->get();
        $materialIns = MaterialIn::with('customer')->latest('id')->paginate(10)->withQueryString();
        $weighedIds = VehicleWeighRecord::whereNotNull('material_in_id')->pluck('material_in_id')->toArray();
        return view('gate.index', compact('parties', 'materialIns', 'weighedIds'));
    }

    public function add(Request $request)
    {
        $id = $request->input('id') ?? $request->query('id');

        $materialIn = $id
            ? MaterialIn::findOrFail($id)
            : null;

        if ($request->isMethod('post')) {

            $validated = $request->validate([
                'cust_id'          => 'required|exists:customers,id',
                'formlist'         => 'nullable|string|max:100',
                'truckno'          => [
                    'required',
                    'string',
                    'regex:/^[A-Z]{2}[0-9]{2}[A-Z]{2}[0-9]{4}$/',
                ],
                'vehicleentryslip' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
                'transportname'    => 'nullable|string|max:255',
                'noofpersons'      => 'nullable|integer|min:0',
                'drivername'       => 'nullable|string|max:255',
                'contactno'        => 'nullable|digits_between:1,15',
            ]);

            if ($request->hasFile('vehicleentryslip')) {
                $validated['vehicleentryslip'] =
                    $request->file('vehicleentryslip')
                        ->store('vehicle-entry-slips', 'public');
            }

            if ($materialIn) {
                // Same record update hoga
                $materialIn->update($validated);

                return redirect()->route('gate.index')
                    ->with('success', 'Vehicle entry updated successfully.');
            }

            // Sirf new entry ke case mein record create hoga
            $validated['created_by'] = Auth::id();

            MaterialIn::create($validated);

            return redirect()->route('gate.index')
                ->with('success', 'Vehicle entry added successfully.');
        }

        $user = Auth::user();
        $custIds = PurchaseOrder::distinct()->pluck('cust_id');
        $parties = Customer::select([''])->where('status', '1')->whereIn('id', $custIds)->get();

        return view('gate.material_in', compact(
            'user',
            'parties',
            'materialIn'
        ));
    }


    public function delete($id)
    {
        $materialIn = MaterialIn::findOrFail($id);

        $materialIn->delete();

        return redirect()->route('gate.index')
            ->with('success', 'Vehicle entry deleted successfully.');
    }






}
