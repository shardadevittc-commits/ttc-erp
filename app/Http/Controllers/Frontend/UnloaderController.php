<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VehicleWeighRecord;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseOrderitemReceiving;
use App\Models\Grade;
use Illuminate\Support\Facades\DB;

class UnloaderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $listing = VehicleWeighRecord::with('customer')->get();
        return view('unloader.index', compact('listing'));
    }

    public function unloadMaterials(Request $request, $vehicleId = null)
    {
        if($vehicleId){
            $vehicalDetails = VehicleWeighRecord::with('customer')->findOrFail($vehicleId);

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
