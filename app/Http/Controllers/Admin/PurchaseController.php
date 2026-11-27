<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Grade;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\SaleOrder;
use App\Models\Size;
use App\Models\Unit;
use App\Models\GateAndGaadi;
use App\Models\WorkOrderProcess;
use App\Models\Brand;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseController extends Controller
{

    private const RELATIONS = [
        'saleOrderItem.saleOrder.customer',
        'saleOrderItem.product',
        'saleOrderItem.grade',
        'saleOrderItem.size',
        'lab'
    ];

    /* public function index(Request $request)
    {
        $query = PurchaseOrder::with([
            'customer',
            'items.product',
            'items.size',
            'items.grade',
            'items.sizeUnit',
        ]);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('basis', 'like', "%{$search}%")
                    ->orWhere('term', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('company_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('items.product', function ($productQuery) use ($search) {
                        $productQuery->where('product_name', 'like', "%{$search}%");
                    });
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');

        $allowedSorts = ['id', 'basis', 'created_at'];

        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'id';
        }

        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        $purchaseOrders = $query->orderBy($sortBy, $sortOrder)->paginate(10)->withQueryString();
        //echo "<pre>"; print_r($purchaseOrders->toArray()); echo "</pre>"; die;
        return view('admin.purchases.index', compact('purchaseOrders'));
    } */

    public function index(Request $request)
    {
        $query = PurchaseOrder::with([
            'customer',
            'saleOrder.customer',
            'items.product',
            'items.size',
            'items.grade',
            'items.sizeUnit',
        ]);

        // Search functionality
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('basis', 'like', "%{$search}%")
                    ->orWhere('term', 'like', "%{$search}%")

                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('company_name', 'like', "%{$search}%");
                    })

                    ->orWhereHas('saleOrder.customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('company_name', 'like', "%{$search}%");
                    })

                    ->orWhereHas('items.product', function ($productQuery) use ($search) {
                        $productQuery->where('product_name', 'like', "%{$search}%");
                    });
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');

        $allowedSorts = ['id', 'basis', 'created_at'];

        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'id';
        }

        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        $purchaseOrders = $query
            ->orderBy($sortBy, $sortOrder)
            ->paginate(10)
            ->withQueryString();

        return view('admin.purchases.index', compact('purchaseOrders'));
    }

    /**
     * Add / Store Purchase Order
     */
    public function add(Request $request)
    {
        // =========================================
        // GET: ADD / EDIT PURCHASE ORDER
        // =========================================
        if ($request->isMethod('get')) {

            $purchaseOrder = null;

            if ($request->filled('id')) {
                $purchaseOrder = PurchaseOrder::with('items')->findOrFail($request->query('id'));
            }

            $customers  = Customer::where(['supplier'=>1,'status'=>'1'])->orderBy('company_name')->get();
            $sizes      = Size::where(['status'=>'1'])->orderBy('size_name')->get();
            $units      = Unit::all();
            $grades     = Grade::where(['status'=>'1'])->orderBy('grade_name')->get();
            $products   = Product::where(['product_category'=>'raw_material', 'status'=>'1'])->orderBy('product_name')->get();

            $saleOrders = SaleOrder::with('customer','items:id,sale_order_id,item_qty,qty_type')
                ->withSum('items as total_qty', 'item_qty')
                ->whereExists(function ($query) {
                    $orderedQuantities = DB::table('sale_order_items as sale_items')
                        ->select('sale_order_id', 'grade_id')
                        ->selectRaw('SUM(COALESCE(item_qty, 0)) as ordered_qty')
                        ->whereNull('deleted_at')
                        ->groupBy('sale_order_id', 'grade_id');

                    $purchasedQuantities = DB::table('purchase_order_items as purchase_items')
                        ->select('ref_saleorder_id', 'grade_id')
                        ->selectRaw('SUM(COALESCE(qty, 0)) as purchased_qty')
                        ->groupBy('ref_saleorder_id', 'grade_id');

                    $query->selectRaw('1')
                        ->fromSub($orderedQuantities, 'ordered_quantities')
                        ->leftJoinSub(
                            $purchasedQuantities,
                            'purchased_quantities',
                            function ($join) {
                                $join->on(
                                    'purchased_quantities.ref_saleorder_id',
                                    '=',
                                    'ordered_quantities.sale_order_id'
                                )->where(function ($gradeJoin) {
                                    $gradeJoin->on(
                                        'purchased_quantities.grade_id',
                                        '=',
                                        'ordered_quantities.grade_id'
                                    )->orOn(function ($nullGradeJoin) {
                                        $nullGradeJoin->whereNull('purchased_quantities.grade_id')
                                            ->whereNull('ordered_quantities.grade_id');
                                    });
                                });
                            }
                        )
                        ->whereColumn('ordered_quantities.sale_order_id', 'sale_orders.id')
                        ->whereRaw(
                            'ordered_quantities.ordered_qty > COALESCE(purchased_quantities.purchased_qty, 0)'
                        );
                })
                ->get();
            $payType = '';
            $payTypeUnit = '';

            if ($purchaseOrder && $purchaseOrder->term) {
                if (preg_match('/^([\d.]+)\s+(Days|Week|Month)$/i',trim($purchaseOrder->term),$matches)) {
                    $payType = $matches[1];
                    $payTypeUnit = ucfirst(strtolower($matches[2]));
                }
            }
            //echo"<pre>";print_r($products);die;
            return view('admin.purchases.add', compact('purchaseOrder', 'customers', 'sizes', 'units', 'grades', 'products', 'saleOrders', 'payType', 'payTypeUnit'));
        }

        // =========================================
        // POST: VALIDATION
        // =========================================
        $validated = $request->validate([
            'purchase_order_id'     => ['nullable','integer', 'exists:purchase_orders,id'],
            'customer'              => ['required','integer','exists:customers,id'],
            'basicRate'             => ['nullable', 'numeric', 'min:0'],
            'cust_po'               => ['nullable', 'integer'],
            'frtBasis'              => ['nullable', 'in:FOR,EX'],
            'payType'               => ['nullable', 'numeric', 'min:0'],
            'payTypeUnit'           => ['nullable', 'in:Days,Week,Month'],
            'remarks'               => ['nullable', 'string'],
            'items'                 => ['required', 'array', 'min:1'],
            'items.*.item_id'       => ['required','integer','exists:products,id'],
            'items.*.size'          => ['nullable','integer','exists:sizes,id'],
            'items.*.size_unit'     => ['nullable','integer','exists:units,id'],
            'items.*.grade'         => ['nullable','integer','exists:grades,id'],
            'items.*.length'        => ['nullable', 'string', 'max:255'],
            'items.*.rate'          => ['nullable', 'numeric', 'min:0'],
            'items.*.qty'           => ['nullable', 'numeric', 'min:0'],
            'items.*.qty_unit'      => ['nullable', 'string', 'max:50'],
            'items.*.qty_variation' => ['nullable', 'string', 'max:50'],
            'items.*.remarks'       => ['nullable', 'string'],
        ]);

        DB::beginTransaction();

        try {

            // =========================================
            // PAYMENT TERM
            // =========================================
            $term = null;

            if (
                $request->filled('payType') &&
                $request->filled('payTypeUnit')
            ) {
                $term = trim($validated['payType']) . ' ' .
                    $validated['payTypeUnit'];
            }

            // =========================================
            // PURCHASE ORDER DATA
            // =========================================
            $orderData = [
                'cust_id'          => $validated['customer'],
                'b_rate'           => $validated['basicRate'] ?? null,
                'ref_saleorder_id' => $validated['cust_po'] ?? null,
                'basis'            => $validated['frtBasis'] ?? 'FOR',
                'term'             => $term,
                'remarks'          => $validated['remarks'] ?? null,
            ];

            // =========================================
            // UPDATE EXISTING OR CREATE NEW
            // =========================================
            if (!empty($validated['purchase_order_id'])) {

                // SAME ID UPDATE HOGA
                $purchaseOrder = PurchaseOrder::findOrFail(
                    $validated['purchase_order_id']
                );

                $purchaseOrder->update($orderData);

                // Existing items replace honge.
                // Parent Purchase Order delete nahi hoga.
                $purchaseOrder->items()->delete();

            } else {

                // Sirf ADD MODE mein new Purchase Order
                $orderData['created_by'] = auth()->id();

                $purchaseOrder = PurchaseOrder::create($orderData);
            }

            // =========================================
            // SAVE PURCHASE ORDER ITEMS
            // =========================================
            foreach ($validated['items'] as $item) {

                $purchaseOrder->items()->create([
                    'cust_id'          => $validated['customer'],
                    'b_rate'           => $validated['basicRate'] ?? null,
                    'ref_saleorder_id' => $validated['cust_po'] ?? null,
                    'basis'            => $validated['frtBasis'] ?? 'FOR',

                    'product_id'       => $item['item_id'],
                    'grade_id'         => $item['grade'] ?? null,
                    'size_id'          => $item['size'] ?? null,
                    'size_unit_id'     => $item['size_unit'] ?? null,
                    'length'           => $item['length'] ?? null,
                    'rate'             => $item['rate'] ?? null,
                    'qty'              => $item['qty'] ?? null,
                    'qty_unit'         => $item['qty_unit'] ?? null,
                    'qty_varaiate'     => $item['qty_variation'] ?? null,
                    'remarks'          => $item['remarks'] ?? null,
                    'created_by'       => auth()->id(),
                ]);
            }

            DB::commit();

            return redirect()
                ->route('purchase')
                ->with(
                    'success',
                    !empty($validated['purchase_order_id'])
                        ? 'Purchase Order updated successfully.'
                        : 'Purchase Order added successfully.'
                );

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Purchase Order Save Error', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Purchase Order save nahi ho saka. Please try again.'
                );
        }
    }


    public function delete($id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);

        $purchaseOrder->items()->delete();
        $purchaseOrder->delete();

        return redirect()->route('purchase')->with('success', 'Purchase Order deleted successfully.');
    }


    public function indexRM(Request $request)
    {
        $query = PurchaseOrderItem::with([
                'customer',
                'purchaseOrder',
                'product',
                'size',
                'grade',
                'sizeUnit',
                'poitemReceiving',
                'lab'
            ]);

            // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('basis', 'like', "%{$search}%")
                    ->orWhere('term', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('company_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('items.product', function ($productQuery) use ($search) {
                        $productQuery->where('product_name', 'like', "%{$search}%");
                    });
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');

        $allowedSorts = ['id', 'basis', 'created_at'];

        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'id';
        }

        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        $listing = $query->orderBy($sortBy, $sortOrder)->paginate(10)->withQueryString();
        // pr($listing); die;

        if ($request->ajax()) {
            return response()->json([
                'status' => true,
                'html'       => view('admin.rawMaterials.listingLoop', compact('listing'))->render(),
                'page'       => $listing->currentPage(),
                'counter'    => $listing->perPage(),
                'count'      => $listing->total(),
                'lastPage'   => $listing->lastPage(),
                'pagination' => $listing->links('pagination::bootstrap-5')->toHtml(),
            ]);
        }

        $products = Product::query()->where('status', 1)->select(['id', 'product_name', 'status'])->get();
        $sizes    = Size::query()->where('status', 1)->select(['id', 'size_name', 'created_at'])->get();
        $brands   = Brand::query()->where('status', 1)->select(['id', 'brand_name','created_at'])->get();
        $grades   = Grade::query()->where('status', 1)->select(['id', 'grade_name', 'created_at'])->get();
        //echo"<pre>";print_r($listing->toArray());die;
        return view('admin.rawMaterials.index', compact('listing','products','sizes','brands', 'grades'));
    }

    /*public function indexFM(Request $request)
    {
        $data = DB::table('work_orders_and_process as wop')
            ->join('sale_orders as so', 'wop.sale_order_id', '=', 'so.id')
            ->join('sale_order_items as soi', 'so.id', '=', 'soi.sale_order_id')

            ->leftJoin('grades as g', 'soi.grade_id', '=', 'g.id')
            ->leftJoin('products as p', 'soi.product_id', '=', 'p.id')
            ->leftJoin('sizes as s', 'soi.size_id', '=', 's.id')
            ->leftJoin('customers as c', 'so.customer_id', '=', 'c.id')

            ->select(
                'wop.material_send_for_work',
                'wop.fs_bundle_weight',
                'wop.fs_bundle_unit',
                'wop.total_full_bundle_count',
                'wop.lose_bundle_weight',
                'wop.lose_bundle_count',

                'so.customer_id',
                'so.order_qty',
                'so.basic_rate',

                'soi.grade_id',
                'soi.size_id',
                'soi.product_id',

                'g.grade_name',
                'p.product_name',
                's.size_name',

                'c.company_name'
            )
            ->get();

        return view('admin.finishMaterials.index', compact('data'));
    }*/

    public function indexVH(Request $request)
    {
        $query = GateAndGaadi::with('customer')->whereNotNull('check_out');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                ->orWhereHas('customer', function ($customerQuery) use ($search) {
                    $customerQuery->where('company_name', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('check_out', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('check_out', '<=', $request->to_date);
        }

        $materialIns = $query->latest('id')->paginate(10)->withQueryString();
        return view('admin.vehicleHistory.index', compact('materialIns'));
    }

    public function view(Request $request, $id)
    {
        $purchaseOrders = PurchaseOrder::with([
            'items.product',
            'items.size',
            'items.grade',
            'items.sizeUnit',
        ])->findOrFail($id);

        // Sale Order se customer ID fetch karo
        $saleOrder = SaleOrder::find($purchaseOrders->ref_saleorder_id);

        // Sale Order ke cust_id se customer details fetch karo
        $customer = null;

        if ($saleOrder) {
            $customer = Customer::find($saleOrder->customer_id);
        }

        return view('admin.purchases.view', compact(
            'purchaseOrders',
            'customer',
            'saleOrder'
        ));
    }

    public function printRM(Request $request, $id)
    {
        // Unload weight ki single entry
        $unloadWeight = DB::table('unload_weights as uw')
            ->leftJoin('grades as g', 'g.id', '=', 'uw.grade_id')
            ->leftJoin('sizes as s', 's.id', '=', 'uw.size_id')
            ->leftJoin('customers as c', 'c.id', '=', 'uw.cust_id')
            ->leftJoin('products as p', 'p.id', '=', 'uw.product_id')
            ->leftJoin('gate_and_gaadi as v', 'v.id', '=', 'uw.vehicle_id')
            ->select(
                'uw.id',
                'uw.purchase_order_item_id',
                'uw.received_qty',
                'uw.actual_qty',
                'uw.formlist',
                'g.grade_name',
                's.size_name',
                'c.company_name',
                'p.product_name',
                'v.truckno'
            )
            ->where('uw.purchase_order_item_id', $id)
            ->first();

        if (!$unloadWeight) {
            abort(404, 'Unload Weight Not Found');
        }

        // Matching Lab Results - Multiple Records
        $labResults = DB::table('lab_results as lr')
            ->leftJoin('grade_properties as gp', 'gp.id', '=', 'lr.property_id')
            ->select(
                'lr.property_id',
                'gp.name as property_name',
                'gp.unit',
                'lr.actual_value'
            )
            ->where(
                'lr.purchase_order_item_id',
                $unloadWeight->purchase_order_item_id
            )
            ->get();
        echo"<pre>";print_r
        return view('admin.rawMaterials.print', compact(
            'unloadWeight',
            'labResults'
        ));
    }


    public function printFM(Request $request)
    {
        $data = WorkOrderProcess::query()
            ->with(self::RELATIONS)
            ->where('status', 'completed')
            ->whereHas('saleOrderItem.saleOrder');
        //echo"<pre>";print_r($data);die;
        return view('admin.rawMaterials.fmprint', compact(
            'data',
        ));
    }

    public function indexDM(Request $request)
    {
        $dispatches = DB::table('dispatch_material as dm')
            ->leftJoin('sale_orders as so', 'dm.sale_order_id', '=', 'so.id')
            ->leftJoin('customers as c', 'so.customer_id', '=', 'c.id')
            ->leftJoin('gate_and_gaadi as gg', 'dm.gate_id', '=', 'gg.id')
            ->leftJoin('sale_order_items as soi', 'dm.sale_order_id', '=', 'soi.sale_order_id')
            ->leftJoin('products as p', 'soi.product_id', '=', 'p.id')
            ->leftJoin('sizes as sz', 'soi.size_id', '=', 'sz.id')
            ->leftJoin('grades as gr', 'soi.grade_id', '=', 'gr.id')
            ->select(
                // Dispatch Material
                'dm.id',
                'dm.gate_id',
                'dm.sale_order_id',
                'dm.sale_order_item_id',
                'dm.dispatch_qty',
                'dm.weight_id',
                'dm.actual_qty',
                'dm.status',

                // Customer Details
                'c.company_name',
                'c.email',
                'c.mobile',
                'c.gst_no',
                'c.cust_code',

                // Gate and Vehicle
                'gg.cust_id',
                'gg.truckno',

                // Sale Order Item
                'soi.product_id',
                'soi.size_id',
                'soi.grade_id',
                'soi.item_qty',

                // Product Details
                'p.product_name',

                // Size Details
                'sz.size_name',

                // Grade Details
                'gr.grade_name'
            )
            ->orderBy('dm.id', 'desc')
            ->get();
        $branch = Branch::first();
        return view('dispatch.dispatch_report', compact('dispatches', 'branch'));
    }
}
