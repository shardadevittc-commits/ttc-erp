<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Grade;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Size;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseController extends Controller
{

    public function index(Request $request)
    {
        return view('admin.purchases.index');
    }

    /**
     * Add / Store Purchase Order
     */
    public function add(Request $request)
    {
        if ($request->isMethod('post')) {

            $validated = $request->validate([
                'customer' => ['required', 'integer', 'exists:customers,id'],
                'basicRate' => ['nullable', 'numeric', 'min:0'],
                'cust_po' => ['nullable', 'integer'],
                'frtBasis' => ['nullable', 'in:FOR,EX'],
                'payType' => ['nullable', 'numeric', 'min:0'],
                'payTypeUnit' => ['nullable', 'in:Days,Week,Month'],
                'remarks' => ['nullable', 'string'],

                'items' => ['required', 'array', 'min:1'],

                'items.*.item_id' => ['required', 'integer', 'exists:products,id'],
                'items.*.size' => ['nullable', 'integer', 'exists:sizes,id'],
                'items.*.size_unit' => ['nullable', 'integer', 'exists:units,id'],
                'items.*.grade' => ['nullable', 'integer', 'exists:grades,id'],
                'items.*.length' => ['nullable', 'string', 'max:255'],
                'items.*.rate' => ['nullable', 'numeric', 'min:0'],
                'items.*.qty' => ['nullable', 'numeric', 'min:0'],
                'items.*.qty_unit' => ['nullable', 'string', 'max:50'],
                'items.*.qty_variation' => ['nullable', 'string', 'max:50'],
                'items.*.remarks' => ['nullable', 'string'],
            ]);

            DB::beginTransaction();

            try {

                $term = null;

                if (
                    !empty($request->payType) &&
                    !empty($request->payTypeUnit)
                ) {

                    $term =
                        trim($request->payType) .
                        ' ' .
                        trim($request->payTypeUnit);
                }

                $purchaseOrder = PurchaseOrder::create([
                    'cust_id'          => $request->customer,
                    'b_rate'           => $request->basicRate,
                    'ref_saleorder_id' => $request->cust_po,
                    'basis'            => $request->frtBasis ?? 'FOR',
                    'term'             => $term,
                    'remarks'          => $request->remarks,
                    'created_by'       => auth()->id(),
                ]);

                foreach ($request->items as $item) {

                    PurchaseOrderItem::create([

                        'purchase_order_id' => $purchaseOrder->id,
                        'cust_id'           => $request->customer,
                        'b_rate'            => $request->basicRate,
                        'ref_saleorder_id'  => $request->cust_po,
                        'basis'             => $request->frtBasis ?? 'FOR',
                        'product_id'        => $item['item_id'] ?? null,
                        'grade_id'          => $item['grade'] ?? null,
                        'size_id'           => $item['size'] ?? null,
                        'size_unit_id'      => $item['size_unit'] ?? null,
                        'length'            => $item['length'] ?? null,
                        'rate'              => $item['rate'] ?? null,
                        'qty'               => $item['qty'] ?? null,
                        'qty_unit'          => $item['qty_unit'] ?? null,
                        'qty_varaiate'      => $item['qty_variation'] ?? null,
                        'remarks'           => $item['remarks'] ?? null,
                        'created_by'        => auth()->id(),

                    ]);
                }

                DB::commit();

                return redirect()->route('purchase')->with('success','Purchase Order added successfully.');

            } catch (\Throwable $e) {
                DB::rollBack();

                Log::error(
                    'Purchase Order Save Error',
                    [
                        'message' => $e->getMessage(),
                        'file'    => $e->getFile(),
                        'line'    => $e->getLine(),
                    ]
                );
                return back()->withInput()->with('error','Purchase Order save nahi ho saka. Please try again.');
            }
        }


        /*
        |--------------------------------------------------------------------------
        | GET - SHOW ADD FORM
        |--------------------------------------------------------------------------
        */

        $customers = Customer::all();
        $sizes     = Size::all();
        $units     = Unit::all();
        $grades    = Grade::all();
        $products  = Product::all();

        return view('admin.purchases.add',compact('customers','sizes','units','grades','products'));
    }
}
