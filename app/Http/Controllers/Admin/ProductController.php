<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->with(['size', 'grade']);
        $search = trim((string) $request->query('search', ''));
        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $query->where('product_name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('rack_no', 'like', "%{$search}%")
                    ->orWhere('warehouse', 'like', "%{$search}%");
            });
        }

        $sortBy = in_array($request->query('sort_by'), ['id', 'product_name', 'status', 'created_at'], true)
            ? $request->query('sort_by') : 'id';
        $sortOrder = $request->query('sort_order') === 'asc' ? 'asc' : 'desc';
        $perPage = (int) $request->query('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 10;

        $listing = $query->orderBy($sortBy, $sortOrder)->paginate($perPage)->withQueryString();

        /* AJAX Request */
        if ($request->ajax()) {

            $html = view(
                'admin.products.listingLoop',
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
        return view('admin.products.index',[
                'listing' => $listing,
            ]
        );
    }

    public function add(Request $request)
    {
        if($request->isMethod('post')) 
        {			
            $validated = $request->validate([
                'product_name' => ['required', 'string', 'max:50', Rule::unique('products', 'product_name')->whereNull('deleted_at')],
            ]);

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('users', 'public');
            }

            Product::create([
                'product_name' => $validated['product_name'],
                'status' => (int) $request->status ?? 1,
                'created_by' => auth()->id(),
            ]);

            return redirect()->route('products')->with('success', 'Product added successfully.');
        }

        return view('admin.products.add');
    }

    public function view(int $id)
    {
        $product = Product::with(['createdBy'])->findOrFail($id);
        
        return view('admin.products.view',[
            'products' => $product
        ]);
    }

    public function edit(Request $request, $id)
    {
        $product = Product::with(['createdBy'])->findOrFail($id);
        $permissions = $product->permissions;
        if($product){
            if($request->isMethod('post')) {
                $validated = $request->validate([
                    'product_name' => [
                        'required',
                        'string',
                        'max:100',
                        Rule::unique('products', 'product_name')->ignore($product->id),
                    ],
                ]);

                $data = [
                    'product_name' => $validated['product_name'],
                ];

                if ($request->has('status')) {
                    $data['status'] = (int) $request->status;
                }
    
                $products->update($data);

                return redirect()->route('products')->with('success', 'Product updated successfully.');

            }

            return view('admin.products.edit', compact('product'));
        }
        else
        {
            abort(404);
        }
    }

    public function destroy(int $id)
    {
        Product::findOrFail($id)->delete();

        return redirect()->route('products')->with('success', 'Product deleted successfully.');
    }
}