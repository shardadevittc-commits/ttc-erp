<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use App\Models\SaleOrder;
use App\Models\Customer;

class SaleOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SaleOrder::query()->with(['grade', 'brand', 'unit', 'saleOrderItems', 'customer']);
        $search = trim((string) $request->query('search', ''));
        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $query->where('customer_id', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%")
                    ->orWhere('payment_term', 'like', "%{$search}%")
                    ->orWhere('order_qty', 'like', "%{$search}%")
                    ->orWhere('basic_rate', 'like', "%{$search}%")
                    ->orWhere('dispatch_date', 'like', "%{$search}%")
                    ->orWhere('remarks', 'like', "%{$search}%")
                    ->orWhere('freight_basis', 'like', "%{$search}%")
                    ->orWhere('created_at', 'like', "%{$search}%")
                    ->orWhere('updated_at', 'like', "%{$search}%")
                    ->orWhere('customer_po_no', 'like', "%{$search}%");
            });
        }

        $sortBy = in_array($request->query('sort_by'), ['id', 'size_name', 'status', 'created_at'], true)
            ? $request->query('sort_by') : 'id';
        $sortOrder = $request->query('sort_order') === 'asc' ? 'asc' : 'desc';
        $perPage = (int) $request->query('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 10;

        $listing = $query->orderBy($sortBy, $sortOrder)->paginate($perPage)->withQueryString();

        /* AJAX Request */
        if ($request->ajax()) {

            $html = view(
                'admin.saleOrders.listingLoop',
                [
                    'listing' => $listing
                ]
            )->render();

            return response()->json([
                'status' => true,
                'html' => $html,
                'page' => $listing->currentPage(),
                'counter' => $listing->perPage(),
                'count' => $listing->total(),
                'lastPage' => $listing->lastPage(),
                'pagination' => $listing->links('pagination::bootstrap-5')->toHtml(),
            ], 200);
        }

        /* Normal Request */
        $customers = Customer::all();
        return view('admin.saleOrders.index',[
                'listing' => $listing,
                'customers' => $customers
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function add(Request $request)
    {
        if($request->isMethod('post')) {
			
            $validated = $request->validate([
                'size_name' => ['required', 'string', 'max:50', Rule::unique('sizes', 'size_name')->whereNull('deleted_at')],
            ]);

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('users', 'public');
            }

            SaleOrder::create([
                'size_name' => $validated['size_name'],
                'status' => (int) $request->status ?? 1,
                'created_by' => auth()->id(),
            ]);

            return redirect()->route('saleOrders')->with('success', 'Sale Order added successfully.');
        }
        $customers = Customer::all();
        return view('admin.saleOrders.add', compact('customers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function view(int $id)
    {
        $sizes = SaleOrder::with(['grade', 'brand', 'unit'])->findOrFail($id);
        return view('admin.saleOrders.view',[
            'sizes' => $sizes
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
        $saleOrder = SaleOrder::findOrFail($id);
        $permissions = $saleOrder->permissions;
        if($saleOrder){
            if($request->isMethod('post')) {
                $validated = $request->validate([
                    'size_name' => [
                        'required',
                        'string',
                        'max:100',
                        // 'alpha_dash',
                        Rule::unique('sizes', 'size_name')->ignore($saleOrder->id),
                    ],
                ]);

                $data = [
                    'size_name' => $validated['size_name'],
                ];

                if ($request->has('status')) {
                    $data['status'] = (int) $request->status;
                }
    
                $size->update($data);

                return redirect()->route('saleOrders')->with('success', 'Sale Order updated successfully.');

            }

            return view('admin.saleOrders.edit', compact('size'));
        }else{
            abort(404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        SaleOrder::findOrFail($id)->delete();

        return redirect()->route('saleOrders')->with('success', 'Sale Order deleted successfully.');
    }
}
