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
use App\Models\LabResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class LabController extends Controller
{


/*$unloaderData = [
                'vehical_id' => 1,
                'weight_id' => 1,
                'customer_id' => 1,
                'purchase_order_item_id' => 1,
                'po_item_qty' => 1000.00,
                'received_qty' => 800.00,
                'production_used_qty' => 250.00,
                'order_price' => 50.00,
                'received_price' => 40000.00,
                'size_id' => 1,
                'grade_id' => 1,
                'furnance_heat_no' => 'FH-2026-001',
                'genrated_heat_no' => 'GH-2026-001',
                'lab_test' => '1',
                'item_remarks' => 'Material received and lab tested.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'vehical_id' => 2,
                'weight_id' => 2,
                'customer_id' => 2,
                'purchase_order_item_id' => 2,
                'po_item_qty' => 1500.00,
                'received_qty' => 1200.00,
                'production_used_qty' => 500.00,
                'order_price' => 45.00,
                'received_price' => 54000.00,
                'size_id' => 2,
                'grade_id' => 2,
                'furnance_heat_no' => 'FH-2026-002',
                'genrated_heat_no' => 'GH-2026-002',
                'lab_test' => '1',
                'item_remarks' => 'Material partially used in production.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'vehical_id' => 3,
                'weight_id' => 3,
                'customer_id' => 1,
                'purchase_order_item_id' => 3,
                'po_item_qty' => 2000.00,
                'received_qty' => 1000.00,
                'production_used_qty' => 0.00,
                'order_price' => 60.00,
                'received_price' => 60000.00,
                'size_id' => 3,
                'grade_id' => 1,
                'furnance_heat_no' => 'FH-2026-003',
                'genrated_heat_no' => 'GH-2026-003',
                'lab_test' => '2',
                'item_remarks' => 'Lab test pending.',
                'created_at' => now(),
                'updated_at' => now(),
            ],*/
    public function index(Request $request)
    {
        $vehicleId = 1;
        $materialInId = 1;
        $customerId = 1;

        $weighRecord = Weight::where('id', $vehicleId)
            ->where('vehicle_id', $materialInId)
            ->where('cust_id', $customerId)
            ->first();

        $formlist = $weighRecord?->formlist;

        $purchaseOrderItems = PurchaseOrderItem::whereIn('id', [1])->get();

        $unloaderData = [];

        foreach ($purchaseOrderItems as $item) {

            $sizeName = Size::where('id', $item->size_id)
                ->value('size_name');

            $grade = Grade::where('id', $item->grade_id)->first();

            $gradeName = $grade?->grade_name;

            $customerName = Customer::where('id', $item->cust_id)
                ->value('company_name');

            /*
            |--------------------------------------------------------------------------
            | Dynamic Grade Properties
            |--------------------------------------------------------------------------
            | Grade -> GradeReaction -> GradeProperty -> GradeCategory
            */

            $gradeReactions = GradeReaction::with([
                    'property.gradeCategory',
                    'category',
                ])
                ->where('grade_id', $item->grade_id)
                ->get();

            $gradeProperties = $gradeReactions->map(function ($reaction) {
                $property = $reaction->property;

                return [
                    'category_id' => $reaction->grade_category_id
                        ?? $property?->category_id,

                    'category_name' => $reaction->category?->name
                        ?? $property?->gradeCategory?->name
                        ?? 'Other',

                    'property_id' => $reaction->property_id,

                    'property_name' => $property?->name ?? '-',

                    'unit' => $property?->unit ?? '',

                    'min_value' => $reaction->min_value,

                    'max_value' => $reaction->max_value,
                ];
            })->values()->all();

                $unloaderData[] = [
                    'vehicle_id' => $vehicleId,

                    'weight_id' => $weighRecord?->id,

                    'customer_id' => $item->cust_id,

                    'customer_name' => $customerName ?? '-',

                    'purchase_order_item_id' => $item->id,

                    'po_item_qty' => $item->qty ?? 0,

                    'received_qty' => 12,

                    'production_used_qty' => 0,

                    'order_price' => $item->rate ?? 0,

                    'received_price' => 45000,

                    'size_id' => $item->size_id,

                    'size_name' => $sizeName ?? '-',

                    'grade_id' => $item->grade_id,

                    'grade_name' => $gradeName ?? '-',

                    'furnance_heat_no' => null,

                    'genrated_heat_no' => null,

                    'lab_test' => '1',

                    'item_remarks' => $item->remarks,

                    'formlist' => $formlist,

                    // Dynamic composition and grade properties
                    'grade_reactions' => $gradeProperties,

                    // static
                    'production_id' => 1,

                    'created_at' => now(),

                    'updated_at' => now(),
                ];
            }
        //echo"<pre>";print_r($unloaderData);die;
        return view('lab.index', compact('unloaderData'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => ['nullable', 'integer'],
            'weight_id' => ['nullable', 'integer'],
            'cust_id' => ['nullable', 'integer'],
            'purchase_order_item_id' => ['required', 'integer', 'min:1'],
            'size_id' => ['nullable', 'integer'],
            'grade_id' => ['nullable', 'integer'],

            // Formlist must be an array
            'formlist' => ['required', 'array'],

            // Actual values must contain at least one item
            'actual_values' => ['required', 'array', 'min:1'],

            'actual_values.*.property_id' => [
                'required',
                'integer',
                'min:1',
            ],

            'actual_values.*.grade_category_id' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'actual_values.*.actual_value' => [
                'required',
                'numeric',
                'min:0',
            ],
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['actual_values'] as $item) {

                LabResult::updateOrCreate(
                    [
                        'purchase_order_item_id' =>
                            $validated['purchase_order_item_id'],

                        'property_id' => $item['property_id'],
                    ],
                    [
                        'vehicle_id' => $validated['vehicle_id'] ?? null,
                        'weight_id' => $validated['weight_id'] ?? null,
                        'cust_id' => $validated['cust_id'] ?? null,
                        'size_id' => $validated['size_id'] ?? null,
                        'grade_id' => $validated['grade_id'] ?? null,

                        'grade_category_id' =>
                            $item['grade_category_id'] ?? null,

                        // Convert array to JSON before saving
                        'formlist' => json_encode(
                            $validated['formlist'],
                            JSON_THROW_ON_ERROR
                        ),

                        'actual_value' => $item['actual_value'],
                    ]
                );
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Lab results saved successfully.',
        ]);
    }


}
