<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Weight;
use App\Models\GateAndGaadi;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseOrderitemReceiving;
use App\Models\Grade;
use App\Models\ReturnMaterial;
use Illuminate\Support\Facades\DB;

class UnloaderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $listing = Weight::with('customer')->get();
        $listing = Weight::with('customer:id,cust_code,company_name,status')
            ->select('weights.*')
            ->selectRaw("CASE WHEN EXISTS (SELECT 1 FROM poitem_receiving WHERE poitem_receiving.vehical_id = weights.vehicle_id)
                THEN 1 ELSE 0 END AS return_material_status")
            ->whereNotIn('id', function ($query) {
                $query->select('weight_id')
                    ->from('return_materials')
                    ->whereNotNull('weight_id');
            })
            ->get();

        return view('unloader.index', compact('listing'));
    }

    public function returnDetails($vehicleId)
    {
        $vehicle = Weight::with('customer')->findOrFail($vehicleId);
        $receivedItems = $this->receivedItemsForVehicle($vehicle)->get();

        return response()->json([
            'vehicle' => [
                'id' => $vehicle->id,
                'truckno' => $vehicle->truckno,
                'customer_name' => $vehicle->customer->company_name ?? '---',
            ],
            'items' => $receivedItems->map(function ($receiving) {
                $purchaseOrderItem = $receiving->purchaseOrderItem;

                return [
                    'product' => $purchaseOrderItem->product->product_name ?? '---',
                    'grade' => $receiving->grade->grade_name ?? '---',
                    'size' => $receiving->size->size_name ?? '---',
                    'received_qty' => $receiving->received_qty,
                    'received_price' => $receiving->received_price,
                    'generated_heat_no' => $receiving->genrated_heat_no,
                    'furnace_heat_no' => $receiving->furnance_heat_no,
                ];
            })->values(),
        ]);
    }

    public function returnMaterial(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => ['required', 'integer', 'exists:weights,id'],
            'return_reason' => ['required', 'string', 'max:2000'],
        ]);

        $vehicle = Weight::findOrFail($validated['vehicle_id']);
       /*  $receivedItems = $this->receivedItemsForVehicle($vehicle)->get();

        if ($receivedItems->isEmpty()) {
            return response()->json([
                'message' => 'No received materials were found for this vehicle and customer.',
            ], 422);
        } */

        /* $purchaseOrderDetails = $receivedItems->map(function ($receiving) {
            $purchaseOrderItem = $receiving->purchaseOrderItem;

            return [
                'purchase_order_item_id' => $receiving->purchase_order_item_id,
                'product_id' => $purchaseOrderItem->product_id ?? null,
                'product_name' => $purchaseOrderItem->product->product_name ?? null,
                'grade_id' => $receiving->grade_id,
                'grade_name' => $receiving->grade->grade_name ?? null,
                'size_id' => $receiving->size_id,
                'size_name' => $receiving->size->size_name ?? null,
                'po_item_qty' => $receiving->po_item_qty,
                'received_qty' => $receiving->received_qty,
                'order_price' => $receiving->order_price,
                'received_price' => $receiving->received_price,
                'furnance_heat_no' => $receiving->furnance_heat_no,
                'genrated_heat_no' => $receiving->genrated_heat_no,
                'lab_test' => $receiving->lab_test,
                'item_remarks' => $receiving->item_remarks,
            ];
        })->values()->all(); */
        /* , $purchaseOrderDetails */
        $receivedItems = null;
        $return = DB::transaction(function () use ($validated, $vehicle, $receivedItems) {
            return ReturnMaterial::create([
                'vehicle_id' => $vehicle->vehicle_id,
                'customer_id' => $vehicle->cust_id,
                'weight_id' => $vehicle->id,
                // 'weight_id' => $receivedItems->first()->weight_id,
                // 'purchase_orders_details' => $purchaseOrderDetails,
                'return_reason' => $validated['return_reason'],
                'vehicle_no' => $vehicle->truckno,
                'created_by' => auth()->id(),
            ]);
        });

        if($return->id){
            Weight::where('id', $vehicle->id)->update(['return_material_id' => $return->id]);
            GateAndGaadi::where('id', $vehicle->vehicle_id)->update(['return_material_id' => $return->id]);
        }

        return response()->json([
            'message' => 'Materials returned successfully.',
            'return_id' => $return->id,
        ]);
    }

    private function receivedItemsForVehicle(Weight $vehicle)
    {
        return PurchaseOrderitemReceiving::with([
            'grade',
            'size',
            'purchaseOrderItem.product',
        ])
            ->where('customer_id', $vehicle->cust_id)
            ->where(function ($query) use ($vehicle) {
                $query->where('weight_id', $vehicle->id)
                    ->orWhere('vehical_id', $vehicle->id);

                if ($vehicle->vehicle_id) {
                    $query->orWhere('vehical_id', $vehicle->vehicle_id);
                }
            })
            ->whereHas('purchaseOrderItem', function ($query) use ($vehicle) {
                $query->where('cust_id', $vehicle->cust_id);
            })
            ->orderBy('id');
    }

    public function unloadMaterials(Request $request, $vehicleId = null)
    {
        if($vehicleId){
            $vehicalDetails = Weight::with('customer')->findOrFail($vehicleId);

            // Pehle receiving quantities ka total calculate karein
            $receivedTotals = PurchaseOrderitemReceiving::select('purchase_order_item_id')
                ->selectRaw('SUM(received_qty) as total_received')
                ->where('vehical_id', $vehicleId)
                ->groupBy('purchase_order_item_id');

            // Pending purchase order items
            $purchaseOrderItems = PurchaseOrderItem::query()
                ->with(['grade', 'size', 'product'])
                ->leftJoinSub($receivedTotals, 'recTotal', function ($join) {
                    $join->on('purchase_order_items.id','=','recTotal.purchase_order_item_id');
                })
                ->where('purchase_order_items.cust_id', $vehicalDetails->cust_id)
                ->whereRaw('purchase_order_items.qty > COALESCE(recTotal.total_received, 0)')
                ->select('purchase_order_items.*')
                ->get();

            // Is vehicle ka receiving data
            $poItemsReceived = PurchaseOrderitemReceiving::with(['customer','grade','size','purchaseOrderItem.product'])->where('vehical_id', $vehicleId)->get();
        }

        if ($request->isMethod('post')) {
            // pr($request->toArray()); die;
            $validated = $request->validate([
                'weight_id'    => 'required|array',
                'vehical_id'   => 'required|array',
                'customer_id'  => 'required|array',
                'item_name'    => 'required|array',
                'grade'        => 'required|array',
                'po_rate'      => 'required|array',
                'recd_rate'    => 'required|array',
                'pending_qty'  => 'required|array',
                'qty'          => 'required|array',
                'furnace_heat' => 'required|array',
                'item_name.*'  => 'nullable|integer',
                'qty.*'        => 'nullable|numeric|min:0',
                'po_rate.*'    => 'nullable|numeric|min:0',
                'recd_rate.*'  => 'nullable|numeric|min:0',
            ]);

            $data = $request->all();
            $rows = [];
            $now = now();

            foreach ($data['item_name'] as $key => $itemName) {

                if (empty($itemName)) {
                    continue;
                }

                $rows[] = [
                    'vehical_id'             => $data['vehical_id'][$key] ?? null,
                    'weight_id'              => $data['weight_id'][$key] ?? null,
                    'customer_id'             => $data['customer_id'][$key] ?? null,
                    'purchase_order_item_id'  => $itemName,
                    'grade_id'                => $data['grade'][$key] ?? null,
                    'size_id'                 => $data['size_id'][$key] ?? null,
                    'order_price'             => $data['po_rate'][$key] ?? 0,
                    'received_price'          => $data['recd_rate'][$key] ?? 0,
                    'po_item_qty'              => $data['pending_qty'][$key] ?? 0,
                    'received_qty'             => $data['qty'][$key] ?? 0,
                    'furnance_heat_no'         => $data['furnace_heat'][$key] ?? null,
                    'genrated_heat_no'         => $this->generatedHeatNo($data['parent_heat'][$key]),
                    'item_remarks'             => $data['item_remarks'][$key] ?? null,
                    'lab_test'                 => $data['lab_test'][$key] ?? null,
                    'created_at'               => $now,
                    'updated_at'               => $now,
                ];
            }

            if (!empty($rows)) {
                DB::transaction(function () use ($rows) {
                    foreach (array_chunk($rows, 500) as $chunk) {
                        PurchaseOrderitemReceiving::insert($chunk);
                    }
                });
            }

            return redirect()->back() ->with('success', 'Materials received successfully!');
        }

        $grades = Grade::get();
        
        return view('unloader.unloadMaterials', 
            compact(
                'purchaseOrderItems',
                'vehicalDetails',
                'grades',
                'poItemsReceived'
            )
        );
    }

    function generatedHeatNo($parent = null)
    {
        $prefix = 'TTC-1';
        $letter = 'A';

        do {
            $heatNo = $prefix . $letter;

            $exists = PurchaseOrderitemReceiving::where('genrated_heat_no', $heatNo)
                ->exists();

            if ($exists) {
                $letter++;
            }

        } while ($exists);

        return $heatNo;
    }
}
