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
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseController extends Controller
{
    public function index(Request $request)
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

            $saleOrders = SaleOrder::with('customer')->get();

            $payType = '';
            $payTypeUnit = '';

            if ($purchaseOrder && $purchaseOrder->term) {
                if (preg_match('/^([\d.]+)\s+(Days|Week|Month)$/i',trim($purchaseOrder->term),$matches)) {
                    $payType = $matches[1];
                    $payTypeUnit = ucfirst(strtolower($matches[2]));
                }
            }

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
                'html' => view('admin.rawMaterials.listingLoop', compact('listing'))->render(),
                'page' => $listing->currentPage(),
                'counter' => $listing->perPage(),
                'count' => $listing->total(),
                'lastPage' => $listing->lastPage(),
                'pagination' => $listing->links('pagination::bootstrap-5')->toHtml(),
            ]);
        }

        $products = Product::query()->where('status', 1)->select(['id', 'product_name', 'status'])->get();
        $sizes    = Size::query()->where('status', 1)->select(['id', 'size_name', 'created_at'])->get();
        $brands   = Brand::query()->where('status', 1)->select(['id', 'brand_name','created_at'])->get();
        $grades   = Grade::query()->where('status', 1)->select(['id', 'grade_name', 'created_at'])->get();
        
        return view('admin.rawMaterials.index', compact('listing','products','sizes','brands', 'grades'));
    }

    public function indexVH(Request $request)
    {
        $query = PurchaseOrderItem::with([
                'customer',
                'purchaseOrder',
                'product',
                'size',
                'grade',
                'sizeUnit',
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
                'html' => view('admin.rawMaterials.listingLoop', compact('listing'))->render(),
                'page' => $listing->currentPage(),
                'counter' => $listing->perPage(),
                'count' => $listing->total(),
                'lastPage' => $listing->lastPage(),
                'pagination' => $listing->links('pagination::bootstrap-5')->toHtml(),
            ]);
        }

        $products = Product::query()->where('status', 1)->select(['id', 'product_name', 'status'])->get();
        $sizes    = Size::query()->where('status', 1)->select(['id', 'size_name', 'created_at'])->get();
        $brands   = Brand::query()->where('status', 1)->select(['id', 'brand_name','created_at'])->get();
        $grades   = Grade::query()->where('status', 1)->select(['id', 'grade_name', 'created_at'])->get();
        
        return view('admin.vehicleHistory.index', compact('listing','products','sizes','brands', 'grades'));
    }
}
