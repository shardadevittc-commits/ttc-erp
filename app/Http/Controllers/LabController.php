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
    /*public function index(Request $request)
    {
        $unloadWeights = UnloadWeight::all();
        //echo"<pre>";print_r($unloadWeights->toArray());die;
        $unloaderData = [];

        foreach ($unloadWeights as $unloadWeight) {

            $vehicleId = $unloadWeight->vehicle_id;
            $customerId = $unloadWeight->cust_id;
            $poItemId = $unloadWeight->purchase_order_item_id;

            $weighRecord = Weight::where('vehicle_id', $vehicleId)->where('cust_id', $customerId)->first();

            $labTest = PurchaseOrderitemReceiving::where('vehical_id',$vehicleId)->where('customer_id', $customerId)
                    ->where('purchase_order_item_id', $poItemId)
                    ->where('lab_test', '1')->first();

            if (!$labTest) { continue; }

            $item = PurchaseOrderItem::find($poItemId);

            if (!$item) { continue; }

            $workorder = DB::table('work_orders_and_process')->where('po_item_id', $poItemId)->first();

            $sizeName = Size::where('id', $item->size_id)->value('size_name');

            $grade = Grade::find($item->grade_id);

            $customerName = Customer::where('id', $item->cust_id)->value('company_name');

            $productionId = $workorder->id ?? null;

            $savedResults = LabResult::where('purchase_order_item_id', $item->id)->get()->keyBy('property_id');
            pr($savedResults);die;
            $gradeReactions = GradeReaction::with(['property.gradeCategory','category'])->where('grade_id', $item->grade_id)->get();

            $gradeProperties = $gradeReactions->map(
                function ($reaction) use ($savedResults) {
                    $property = $reaction->property;
                    $saved = $savedResults->get($reaction->property_id);
                    return [
                        'category_id'   => $reaction->grade_category_id ?? $property?->category_id,
                        'category_name' => $reaction->category?->name ?? $property?->gradeCategory?->name ?? 'Other',
                        'property_id'   => $reaction->property_id,'property_name' => $property?->name ?? '-',
                        'unit'          => $property?->unit ?? '',
                        'min_value'     => $reaction->min_value,
                        'max_value'     => $reaction->max_value,
                        'actual_value'  => $saved?->actual_value,
                        'final_value'   => $saved?->final_value,
                    ];
                }
            )->values()->all();

            $unloaderData[] = [
                'unload_weight_id'          => $unloadWeight->id,
                'vehicle_id'                => $vehicleId,
                'weight_id'                 => $weighRecord?->id,
                'customer_id'               => $item->cust_id,
                'cust_id'                   => $item->cust_id,
                'customer_name'             => $customerName ?? '-',
                'purchase_order_item_id'    => $item->id,
                'production_id'             => $productionId,
                'po_item_qty'               => $item->qty ?? 0,
                'received_qty'              => $unloadWeight->received_qty ?? 0,
                'production_used_qty'       => $workorder->material_send_for_work ?? 'null',
                'order_price'               => $item->rate ?? 0,
                'received_price'            => $unloadWeight->received_price ?? 0,
                'size_id'                   => $item->size_id,
                'size_name'                 => $sizeName ?? '-',
                'grade_id'                  => $item->grade_id,
                'grade_name'                => $grade?->grade_name ?? '-',
                'furnance_heat_no'          => $labTest?->furnance_heat_no,
                'genrated_heat_no'          => $labTest?->genrated_heat_no,
                'lab_test'                  => $labTest?->lab_test,
                'item_remarks'              => $item->remarks,
                'formlist'                  => $weighRecord?->formlist,
                'grade_reactions'           => $gradeProperties,
                'created_at'                => $unloadWeight->created_at,
                'updated_at'                => $unloadWeight->updated_at,
                'formlist'                  => $unloadWeight->formlist,
            ];
        }
        //pr($unloaderData);die;
        return view('lab.index', compact('unloaderData'));
    }*/


    public function index(Request $request)
    {
        $unloadWeights = UnloadWeight::all();

        $unloaderData = [];

        foreach ($unloadWeights as $unloadWeight) {

            $vehicleId = $unloadWeight->vehicle_id;
            $customerId = $unloadWeight->cust_id;
            $poItemId = $unloadWeight->purchase_order_item_id;

            $weighRecord = Weight::where('vehicle_id', $vehicleId)
                ->where('cust_id', $customerId)
                ->first();

            $labTest = PurchaseOrderitemReceiving::where('vehical_id', $vehicleId)
                ->where('customer_id', $customerId)
                ->where('purchase_order_item_id', $poItemId)
                ->where('lab_test', '1')
                ->first();

            if (!$labTest) {
                continue;
            }

            $item = PurchaseOrderItem::find($poItemId);

            if (!$item) {
                continue;
            }

            $workorder = DB::table('work_orders_and_process')
                ->where('po_item_id', $poItemId)
                ->first();

            $sizeName = Size::where('id', $item->size_id)
                ->value('size_name');

            $grade = Grade::find($item->grade_id);

            $customerName = Customer::where('id', $item->cust_id)
                ->value('company_name');

            $productionId = $workorder->id ?? null;


            /*
            |--------------------------------------------------------------------------
            | Get Saved Lab Results
            |--------------------------------------------------------------------------
            */

            $savedResults = LabResult::where(
                'purchase_order_item_id',
                $item->id
            )->get();

            $propertyValues = [];

            foreach ($savedResults as $saved) {

                $actualValues = $saved->actual_value;

                $minValues = $saved->min_value;

                $maxValues = $saved->max_value;

                // Decode JSON if values are strings.
                if (is_string($actualValues)) {
                    $actualValues = json_decode($actualValues, true);
                }

                if (is_string($minValues)) {
                    $minValues = json_decode($minValues, true);
                }

                if (is_string($maxValues)) {
                    $maxValues = json_decode($maxValues, true);
                }

                $actualValues = is_array($actualValues) ? $actualValues : [];
                $minValues = is_array($minValues) ? $minValues : [];
                $maxValues = is_array($maxValues) ? $maxValues : [];


                // Actual Values.
                foreach ($actualValues as $actual) {

                    if (!isset($actual['property_id'])) {
                        continue;
                    }

                    $propertyId = $actual['property_id'];

                    $propertyValues[$propertyId]['actual_value'] =
                        $actual['value'] ?? null;
                }


                // Min Values.
                foreach ($minValues as $min) {

                    if (!isset($min['property_id'])) {
                        continue;
                    }

                    $propertyId = $min['property_id'];

                    $propertyValues[$propertyId]['min_value'] =
                        $min['min_value'] ?? null;
                }


                // Max Values.
                foreach ($maxValues as $max) {

                    if (!isset($max['property_id'])) {
                        continue;
                    }

                    $propertyId = $max['property_id'];

                    $propertyValues[$propertyId]['max_value'] =
                        $max['max_value'] ?? null;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Grade Reactions
            |--------------------------------------------------------------------------
            */

            $gradeReactions = GradeReaction::with([
                'property.gradeCategory',
                'category'
            ])
            ->where('grade_id', $item->grade_id)
            ->get();


            /*
            |--------------------------------------------------------------------------
            | Prepare Property Wise Data
            |--------------------------------------------------------------------------
            */

            $gradeProperties = $gradeReactions->map(
                function ($reaction) use ($propertyValues) {

                    $property = $reaction->property;

                    $propertyId = $reaction->property_id;

                    $saved = $propertyValues[$propertyId] ?? [];

                    return [

                        'category_id' => $reaction->grade_category_id
                            ?? $property?->category_id,

                        'category_name' => $reaction->category?->name
                            ?? $property?->gradeCategory?->name
                            ?? 'Other',

                        'property_id' => $propertyId,

                        'property_name' => $property?->name ?? '-',

                        'unit' => $property?->unit ?? '',

                        'min_value' => $saved['min_value']
                            ?? $reaction->min_value
                            ?? null,

                        'max_value' => $saved['max_value']
                            ?? $reaction->max_value
                            ?? null,

                        'actual_value' => $saved['actual_value'] ?? null,

                    ];

                }
            )->values()->all();


            /*
            |--------------------------------------------------------------------------
            | Prepare Unloader Data
            |--------------------------------------------------------------------------
            */

            $unloaderData[] = [

                'unload_weight_id' => $unloadWeight->id,

                'vehicle_id' => $vehicleId,

                'weight_id' => $weighRecord?->id,

                'customer_id' => $item->cust_id,

                'cust_id' => $item->cust_id,

                'customer_name' => $customerName ?? '-',

                'purchase_order_item_id' => $item->id,

                'production_id' => $productionId,

                'po_item_qty' => $item->qty ?? 0,

                'received_qty' => $unloadWeight->received_qty ?? 0,

                'production_used_qty' => $workorder->material_send_for_work ?? null,

                'order_price' => $item->rate ?? 0,

                'received_price' => $unloadWeight->received_price ?? 0,

                'size_id' => $item->size_id,

                'size_name' => $sizeName ?? '-',

                'grade_id' => $item->grade_id,

                'grade_name' => $grade?->grade_name ?? '-',

                'furnance_heat_no' => $labTest?->furnance_heat_no,

                'genrated_heat_no' => $labTest?->genrated_heat_no,

                'lab_test' => $labTest?->lab_test,

                'item_remarks' => $item->remarks,

                'formlist' => $unloadWeight->formlist,

                'grade_reactions' => $gradeProperties,

                'created_at' => $unloadWeight->created_at,

                'updated_at' => $unloadWeight->updated_at,

            ];
        }
        //pr($unloaderData);die;
        return view('lab.index', compact('unloaderData'));
    }


    /*
    |--------------------------------------------------------------------------
    | Save Actual Values and Final Values
    |--------------------------------------------------------------------------
    */

    /*public function store(Request $request)
    {
        echo"<pre>";print_r($request->all());die;
        $validated = $request->validate([
            'stage' => ['required','in:actual,final'],
            'purchase_order_item_id' => ['required','integer','min:1','exists:purchase_order_items,id'],
            'values' => ['required','array','min:1'],
            'values.*.property_id' => ['required','integer','min:1'],
            'values.*.value' => ['required','numeric','min:0'],
        ]);

        DB::transaction(function () use ($validated) {

            $purchaseOrderItem = PurchaseOrderItem::where('id', $validated['purchase_order_item_id'])->lockForUpdate()->firstOrFail();

            if ($validated['stage'] === 'final') {

                $workOrderId = DB::table('work_orders_and_process')->where('po_item_id', $validated['purchase_order_item_id'])->first();

                if (!$workOrderId || empty($workOrderId->id)) {

                    throw ValidationException::withMessages([
                        'production_id' => 'Production ID not found.'
                    ]);
                }
            }

            $gradeProperties = GradeReaction::where('grade_id',$purchaseOrderItem->grade_id)->pluck('property_id')
                ->map(fn ($id) => (int) $id)->unique()->values()->all();

            if (empty($gradeProperties)) {
                throw ValidationException::withMessages([
                    'values' => 'No grade properties found.'
                ]);
            }
            $submittedIds = collect($validated['values'])->pluck('property_id')->map(fn ($id) => (int) $id);

            if ($submittedIds->unique()->count() !== $submittedIds->count()) {
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

            $savedResults = LabResult::where('purchase_order_item_id',$purchaseOrderItem->id)
                ->lockForUpdate()->get()->keyBy('property_id');

            $assertCompleteSubmission = function ($expectedIds) use ($submittedIds) {
                $expectedIds = collect($expectedIds);
                $missingIds = $expectedIds->diff($submittedIds);
                $unexpectedIds = $submittedIds->diff($expectedIds);

                if ($missingIds->isNotEmpty()) {
                    throw ValidationException::withMessages([
                        'values' => 'Submit all unsaved property values.'
                    ]);
                }
                if ($unexpectedIds->isNotEmpty()) {
                    throw ValidationException::withMessages([
                        'values' => 'Only unsaved property values can be submitted.'
                    ]);
                }
            };

            if ($validated['stage'] === 'actual') {

                $pendingActualIds = collect($gradeProperties)
                    ->filter(function ($propertyId) use ($savedResults) {
                        $saved = $savedResults->get($propertyId);
                        return !$saved || $saved->actual_value === null || $saved->actual_value === '';
                    })
                    ->values();

                $assertCompleteSubmission($pendingActualIds);

                foreach ($validated['values'] as $row) {

                    $propertyId = (int) $row['property_id'];

                    $existing = $savedResults->get($propertyId);
                    if ($existing && $existing->actual_value !== null && $existing->actual_value !== '') {
                        throw ValidationException::withMessages([
                            'values' =>
                                'Actual value already saved for property ID: '
                                . $propertyId
                        ]);
                    }

                    $labResult = $existing ?: new LabResult();
                    $labResult->purchase_order_item_id = $purchaseOrderItem->id;
                    $labResult->property_id = $propertyId;
                    $labResult->actual_value = $row['value'];
                    $labResult->save();
                }
                return;
            }
            if ($validated['stage'] === 'final') {

                foreach ($gradeProperties as $propertyId) {

                    $saved = $savedResults->get($propertyId);

                    if (!$saved ||$saved->actual_value === null ||$saved->actual_value === '') {
                        throw ValidationException::withMessages([
                            'values' =>
                                'First save all Actual Values.'
                        ]);
                    }
                }

                $pendingFinalIds = collect($gradeProperties)
                    ->filter(function ($propertyId) use ($savedResults) {
                        $saved = $savedResults->get($propertyId);

                        return !$saved ||$saved->final_value === null ||$saved->final_value === '';
                    })->values();

                $assertCompleteSubmission($pendingFinalIds);

                foreach ($validated['values'] as $row) {

                    $propertyId = (int) $row['property_id'];
                    $labResult = $savedResults->get($propertyId);
                    if (!$labResult) {
                        throw ValidationException::withMessages([
                            'values' => 'Actual value not found.'
                        ]);
                    }
                    if ($labResult->final_value !== null && $labResult->final_value !== '') {
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
            'message' => $validated['stage'] === 'actual' ? 'Actual values saved successfully.' : 'Final values saved successfully.',
        ]);
    }*/

    public function store(Request $request)
    {
        $validated = $request->validate([
            'stage' => ['required', 'in:actual,final'],
            'formlist' => ['required'],
            'purchase_order_item_id' => ['required', 'integer', 'min:1', 'exists:purchase_order_items,id'],
            'values' => ['required', 'array', 'min:1'],
            'values.*.property_id' => ['required', 'integer', 'min:1'],
            'values.*.value' => ['required', 'numeric', 'min:0'],
        ]);

        try {
            DB::beginTransaction();

            if ($validated['stage'] == 'actual') {

                if ($validated['formlist'] != 'Material In') {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Invalid Formlist.'], 422);
                }

                $receiving = DB::table('poitem_receiving')->where('purchase_order_item_id', $validated['purchase_order_item_id'])->first();

                if (!$receiving) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'PO Receiving record not found.'], 404);
                }

                $propertyIds = [];
                $gradeCategoryIds = [];
                $actualValues = [];
                $minValues = [];
                $maxValues = [];

                foreach ($validated['values'] as $value) {

                    $propertyId = $value['property_id'];

                    $reactions = DB::table('grade_reactions')
                        ->where('grade_id', $receiving->grade_id)
                        ->where('property_id', $propertyId)
                        ->get();

                    if ($reactions->isEmpty()) {
                        DB::rollBack();
                        return response()->json(['success' => false, 'message' => "Grade reaction not found for property ID: {$propertyId}"], 422);
                    }

                    $propertyIds[] = $propertyId;

                    $actualValues[] = [
                        'property_id' => $propertyId,
                        'value' => $value['value']
                    ];

                    foreach ($reactions as $reaction) {

                        $gradeCategoryIds[] = $reaction->grade_category_id;

                        $minValues[] = [
                            'property_id' => $propertyId,
                            'min_value' => $reaction->min_value
                        ];

                        $maxValues[] = [
                            'property_id' => $propertyId,
                            'max_value' => $reaction->max_value
                        ];
                    }
                }

                $propertyIds = array_values(array_unique($propertyIds));
                $gradeCategoryIds = array_values(array_unique($gradeCategoryIds));

                DB::table('lab_results')->insert([
                    'cust_id' => $receiving->customer_id,
                    'purchase_order_item_id' => $validated['purchase_order_item_id'],
                    'size_id' => $receiving->size_id,
                    'grade_id' => $receiving->grade_id,
                    'grade_category_id' => json_encode($gradeCategoryIds),
                    'property_id' => json_encode($propertyIds),
                    'actual_value' => json_encode($actualValues),
                    'min_value' => json_encode($minValues),
                    'max_value' => json_encode($maxValues),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            } else {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Final stage logic is not implemented yet.'], 422);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Actual values saved successfully.'
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
