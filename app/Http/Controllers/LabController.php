<?php

namespace App\Http\Controllers;

use App\Models\Size;
use App\Models\Grade;
use App\Models\GradeReaction;
use App\Models\Customer;
use App\Models\Weight;
use App\Models\PurchaseOrderItem;
use App\Models\UnloadWeight;
use App\Models\PurchaseOrderitemReceiving;
use App\Models\LabResult;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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

            $item = PurchaseOrderItem::find(
                $unloadWeight->purchase_order_item_id
            );

            if (!$item) {
                continue;
            }

            $sizeName = Size::where('id', $item->size_id)
                ->value('size_name');

            $grade = Grade::find($item->grade_id);

            $customerName = Customer::where('id', $item->cust_id)
                ->value('company_name');

            /*
             * Production ID
             */

            $productionId = $item->production_id;

            /*
             * Fetch Saved Results
             */

            $savedResults = LabResult::where(
                'purchase_order_item_id',
                $item->id
            )
                ->get()
                ->keyBy('property_id');

            /*
             * Fetch Grade Properties
             */

            $gradeReactions = GradeReaction::with([
                'property.gradeCategory',
                'category',
            ])
                ->where('grade_id', $item->grade_id)
                ->get();

            /*
             * Attach Actual and Final Values
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

                        'actual_value' =>
                            $saved?->actual_value,

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

                'production_id' =>
                    $productionId,

                'po_item_qty' =>
                    $item->qty ?? 0,

                'received_qty' =>
                    $unloadWeight->received_qty ?? 0,

                'production_used_qty' =>
                    0,

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
                    $weighRecord?->formlist,

                'grade_reactions' =>
                    $gradeProperties,

                'created_at' =>
                    $unloadWeight->created_at,

                'updated_at' =>
                    $unloadWeight->updated_at,

            ];
        }

        return view('lab.index', compact('unloaderData'));
    }


    /*
    |--------------------------------------------------------------------------
    | Save Actual Values and Final Values
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([

            'stage' => [
                'required',
                'in:actual,final'
            ],

            'purchase_order_item_id' => [
                'required',
                'integer',
                'min:1',
                'exists:purchase_order_items,id'
            ],

            'values' => [
                'required',
                'array',
                'min:1'
            ],

            'values.*.property_id' => [
                'required',
                'integer',
                'min:1'
            ],

            'values.*.value' => [
                'required',
                'numeric',
                'min:0'
            ],

        ]);

        DB::transaction(function () use ($validated) {

            $purchaseOrderItem = PurchaseOrderItem::where(
                'id',
                $validated['purchase_order_item_id']
            )
                ->lockForUpdate()
                ->firstOrFail();

            /*
             * Check Production ID
             */

            if (
                $validated['stage'] === 'final' &&
                empty($purchaseOrderItem->production_id)
            ) {

                throw ValidationException::withMessages([
                    'production_id' =>
                        'Production ID not found.'
                ]);
            }

            /*
             * Get Grade Properties
             */

            $gradeProperties = GradeReaction::where(
                'grade_id',
                $purchaseOrderItem->grade_id
            )
                ->pluck('property_id')
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values()
                ->all();

            if (empty($gradeProperties)) {

                throw ValidationException::withMessages([
                    'values' => 'No grade properties found.'
                ]);
            }

            /*
             * Validate Property IDs
             */

            $submittedIds = collect($validated['values'])
                ->pluck('property_id')
                ->map(fn ($id) => (int) $id);

            if (
                $submittedIds->unique()->count()
                !== $submittedIds->count()
            ) {

                throw ValidationException::withMessages([
                    'values' => 'Duplicate properties are not allowed.'
                ]);
            }

            foreach ($submittedIds as $propertyId) {

                if (!in_array($propertyId, $gradeProperties, true)) {

                    throw ValidationException::withMessages([
                        'values' => 'Invalid property ID: ' . $propertyId
                    ]);
                }
            }

            /*
             * Fetch Existing Results
             */

            $savedResults = LabResult::where(
                'purchase_order_item_id',
                $purchaseOrderItem->id
            )
                ->lockForUpdate()
                ->get()
                ->keyBy('property_id');

            /*
             * ACTUAL VALUE SAVE
             */

            if ($validated['stage'] === 'actual') {

                foreach ($validated['values'] as $row) {

                    $propertyId = (int) $row['property_id'];

                    $existing = $savedResults->get($propertyId);

                    /*
                     * Do not overwrite existing Actual Value
                     */

                    if (
                        $existing &&
                        $existing->actual_value !== null &&
                        $existing->actual_value !== ''
                    ) {

                        throw ValidationException::withMessages([
                            'values' =>
                                'Actual value already saved for property ID: '
                                . $propertyId
                        ]);
                    }

                    $labResult = $existing ?: new LabResult();

                    $labResult->purchase_order_item_id =
                        $purchaseOrderItem->id;

                    $labResult->property_id =
                        $propertyId;

                    $labResult->actual_value =
                        $row['value'];

                    $labResult->save();
                }

                return;
            }

            /*
             * FINAL VALUE SAVE
             */

            if ($validated['stage'] === 'final') {

                /*
                 * Every Actual Value must be saved
                 */

                foreach ($gradeProperties as $propertyId) {

                    $saved = $savedResults->get($propertyId);

                    if (
                        !$saved ||
                        $saved->actual_value === null ||
                        $saved->actual_value === ''
                    ) {

                        throw ValidationException::withMessages([
                            'values' =>
                                'First save all Actual Values.'
                        ]);
                    }
                }

                /*
                 * Save Final Values
                 */

                foreach ($validated['values'] as $row) {

                    $propertyId = (int) $row['property_id'];

                    $labResult = $savedResults->get($propertyId);

                    if (!$labResult) {

                        throw ValidationException::withMessages([
                            'values' => 'Actual value not found.'
                        ]);
                    }

                    /*
                     * Do not overwrite existing Final Value
                     */

                    if (
                        $labResult->final_value !== null &&
                        $labResult->final_value !== ''
                    ) {

                        throw ValidationException::withMessages([
                            'values' =>
                                'Final value already saved for property ID: '
                                . $propertyId
                        ]);
                    }

                    $labResult->final_value = $row['value'];

                    $labResult->save();
                }
            }

        });

        return response()->json([

            'success' => true,

            'message' => $validated['stage'] === 'actual'
                ? 'Actual values saved successfully.'
                : 'Final values saved successfully.',

        ]);
    }
}
