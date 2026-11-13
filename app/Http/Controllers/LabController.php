<?php

namespace App\Http\Controllers;

use App\Models\Size;
use App\Models\Grade;
use App\Models\GradeReaction;
use App\Models\GradeProperty;
use App\Models\GradeCategory;
use App\Models\Role;
use App\Models\User;
use App\Models\Customer;
use App\Models\Weight;
use App\Models\PurchaseOrderItem;
use App\Models\UnloadWeight;
use App\Models\PurchaseOrderitemReceiving;
use App\Models\LabResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class LabController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Lab Management Listing
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $unloadWeights = UnloadWeight::all();

        $unloaderData = [];

        foreach ($unloadWeights as $unloadWeight) {

            $vehicleId = $unloadWeight->vehicle_id;
            $customerId = $unloadWeight->cust_id;

            $weighRecord = Weight::where('vehicle_id', $vehicleId)
                ->where('cust_id', $customerId)
                ->first();

            $labTest = PurchaseOrderitemReceiving::where(
                'vehical_id',
                $vehicleId
            )
                ->where('customer_id', $customerId)
                ->where('lab_test', '1')
                ->first();

            $formlist = $weighRecord?->formlist;

            $purchaseOrderItems = PurchaseOrderItem::where(
                'id',
                $unloadWeight->purchase_order_item_id
            )->get();

            foreach ($purchaseOrderItems as $item) {

                $sizeName = Size::where('id', $item->size_id)
                    ->value('size_name');

                $grade = Grade::find($item->grade_id);

                $customerName = Customer::where('id', $item->cust_id)
                    ->value('company_name');

                /*
                 * Fetch previously saved Actual and Final values.
                 */

                $savedResults = LabResult::where(
                    'purchase_order_item_id',
                    $item->id
                )
                    ->get()
                    ->keyBy('property_id');

                /*
                 * Fetch Grade Properties.
                 */

                $gradeReactions = GradeReaction::with([
                    'property.gradeCategory',
                    'category',
                ])
                    ->where('grade_id', $item->grade_id)
                    ->get();

                /*
                 * Attach Actual and Final values.
                 */

                $gradeProperties = $gradeReactions->map(
                    function ($reaction) use ($savedResults) {

                        $property = $reaction->property;

                        $saved = $savedResults->get(
                            $reaction->property_id
                        );

                        return [

                            'category_id' =>
                                $reaction->grade_category_id
                                ?? $property?->category_id,

                            'category_name' =>
                                $reaction->category?->name
                                ?? $property?->gradeCategory?->name
                                ?? 'Other',

                            'property_id' =>
                                $reaction->property_id,

                            'property_name' =>
                                $property?->name ?? '-',

                            'unit' =>
                                $property?->unit ?? '',

                            'min_value' =>
                                $reaction->min_value,

                            'max_value' =>
                                $reaction->max_value,

                            // Actual Value - Read Only
                            'actual_value' =>
                                $saved?->actual_value,

                            // Final Value - Editable
                            'final_value' =>
                                $saved?->final_value,
                        ];
                    }
                )->values()->all();

                $unloaderData[] = [

                    'unload_weight_id' =>
                        $unloadWeight->id,

                    'vehicle_id' =>
                        $vehicleId,

                    'weight_id' =>
                        $weighRecord?->id,

                    'customer_id' =>
                        $item->cust_id,

                    'cust_id' =>
                        $item->cust_id,

                    'customer_name' =>
                        $customerName ?? '-',

                    'purchase_order_item_id' =>
                        $item->id,

                    'po_item_qty' =>
                        $item->qty ?? 0,

                    'received_qty' =>
                        $unloadWeight->received_qty ?? 0,

                    'production_used_qty' => 0,

                    'order_price' =>
                        $item->rate ?? 0,

                    'received_price' =>
                        $unloadWeight->received_price ?? 0,

                    'size_id' =>
                        $item->size_id,

                    'size_name' =>
                        $sizeName ?? '-',

                    'grade_id' =>
                        $item->grade_id,

                    'grade_name' =>
                        $grade?->grade_name ?? '-',

                    'furnance_heat_no' =>
                        $labTest?->furnance_heat_no,

                    'genrated_heat_no' =>
                        $labTest?->genrated_heat_no,

                    'lab_test' =>
                        $labTest?->lab_test,

                    'item_remarks' =>
                        $item->remarks,

                    'formlist' =>
                        $formlist,

                    'grade_reactions' =>
                        $gradeProperties,

                    'production_id' => 1,

                    'created_at' =>
                        $unloadWeight->created_at,

                    'updated_at' =>
                        $unloadWeight->updated_at,
                ];
            }
        }

        return view('lab.index', compact('unloaderData'));
    }


    /*
    |--------------------------------------------------------------------------
    | Update Final Lab Results
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([

            'purchase_order_item_id' => [
                'required',
                'integer',
                'min:1',
                'exists:purchase_order_items,id'
            ],

            'final_values' => [
                'required',
                'array',
                'min:1'
            ],

            'final_values.*.property_id' => [
                'required',
                'integer',
                'min:1'
            ],

            'final_values.*.final_value' => [
                'required',
                'numeric',
                'min:0'
            ],

        ]);

        DB::transaction(function () use ($validated) {

            foreach ($validated['final_values'] as $item) {

                /*
                 * Find existing Actual Result.
                 * Update only Final Value.
                 */

                $labResult = LabResult::where(
                    'purchase_order_item_id',
                    $validated['purchase_order_item_id']
                )
                    ->where('property_id', $item['property_id'])
                    ->lockForUpdate()
                    ->first();

                if (!$labResult) {

                    throw ValidationException::withMessages([
                        'final_values' =>
                            'Actual value not found for property ID: '
                            . $item['property_id']
                    ]);
                }

                /*
                 * Actual Value remains unchanged.
                 */

                $labResult->final_value = $item['final_value'];

                $labResult->save();
            }
        });

        return response()->json([

            'success' => true,

            'message' =>
                'Final values updated successfully.',

        ]);
    }
}
