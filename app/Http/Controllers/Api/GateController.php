<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GateAndGaadi;
use Illuminate\Support\Facades\Auth;
use App\Models\PurchaseOrder;
use App\Models\Customer;

class GateController extends Controller
{
    // Add / Update Vehicle Entry API
    public function add(Request $request)
    {
        $id = $request->input('id');

        $materialIn = $id
            ? GateAndGaadi::find($id)
            : null;

        if ($id && !$materialIn) {
            return response()->json([
                'status' => false,
                'message' => 'Vehicle entry not found.'
            ], 404);
        }

        $validated = $request->validate([
            'cust_id' => 'required|exists:customers,id',
            'formlist' => 'nullable|string|max:100',
            'truckno' => [
                'required',
                'string',
                'regex:/^[A-Z]{2}[0-9]{2}[A-Z]{2}[0-9]{4}$/',
            ],
            'vehicleentryslip' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'transportname' => 'nullable|string|max:255',
            'noofpersons' => 'nullable|integer|min:0',
            'drivername' => 'nullable|string|max:255',
            'contactno' => 'nullable|digits_between:1,15',
        ]);

        if ($request->hasFile('vehicleentryslip')) {
            $validated['vehicleentryslip'] =
                $request->file('vehicleentryslip')
                    ->store('vehicle-entry-slips', 'public');
        }

        if ($materialIn) {
            $materialIn->update($validated);

            return response()->json([
                'status' => true,
                'message' => 'Vehicle entry updated successfully.',
                'data' => $materialIn->fresh()
            ]);
        }

        $validated['created_by'] = Auth::id();

        $materialIn = GateAndGaadi::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Vehicle entry added successfully.',
            'data' => $materialIn
        ], 201);
    }


    // Delete Vehicle Entry API
    public function delete($id)
    {
        $materialIn = GateAndGaadi::find($id);

        if (!$materialIn) {
            return response()->json([
                'status' => false,
                'message' => 'Vehicle entry not found.'
            ], 404);
        }

        $materialIn->delete();

        return response()->json([
            'status' => true,
            'message' => 'Vehicle entry deleted successfully.',
            'data' => [
                'id' => $id
            ]
        ]);
    }
}
