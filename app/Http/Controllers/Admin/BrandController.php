<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Brand;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $query = Brand::query()->with('createdBy');
		// dd($query->toSql(), $query->getBindings());

        /* Sorting */
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');

        $allowedSortColumns = [
            'id',
            'brand_name',
            'created_at',
        ];

        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'id';
        }

        $sortOrder = $sortOrder === 'asc' ? 'asc' : 'desc';

        /*Pagination*/

        $perPage = (int) $request->get('per_page', 10);

        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $listing = $query->orderBy($sortBy, $sortOrder)->paginate($perPage)->withQueryString();

        /* AJAX Request */
        if ($request->ajax()) {

            $html = view(
                'admin.brands.listingLoop',
                [
                    'listing' => $listing
                ]
            )->render();

            return response()->json([
                'status' => 'success',
                'html' => $html,
                'page' => $listing->currentPage(),
                'counter' => $listing->perPage(),
                'count' => $listing->total(),
                'pagination_counter' =>$listing->currentPage() * $listing->perPage(),
            ], 200);
        }

        /* Normal Request */
        return view('admin.brands.index',[
                'listing' => $listing,
            ]
        );
    }

	public function add(Request $request)
    {
        if($request->isMethod('post')) {
			
            $validated = $request->validate([
                'brand_name' => ['required', 'string', 'max:100'],
            ]);

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('users', 'public');
            }

            Brand::create([
                'brand_name' 	=> $validated['brand_name'],
                'status' 		=> (int) $request->status ?? 1,
                'created_by' 	=> auth()->id(),
            ]);

            return redirect()->route('brands')->with('success', 'Brand added successfully.');
        }

        return view('admin.brands.add');
    }

    
    /**
     * Show brand.
     */
    public function show(Brand $brand): View
    {
        return view('brands.show', compact('brand'));
    }

    /**
     * Edit page.
     */
    public function edit(Request $request, $id)
    {
        $brand = Brand::findOrFail($id);
        $permissions = $brand->permissions;
        if($brand){
            if($request->isMethod('post')) {
                $validated = $request->validate([
                    'brand_name' => [
                        'required',
                        'string',
                        'max:100',
                        // 'alpha_dash',
                        Rule::unique('brands', 'brand_name')->ignore($brand->id),
                    ],
                ]);

                $data = [
                    'brand_name' => $validated['brand_name'],
                ];

                if ($request->has('status')) {
                    $data['status'] = (int) $request->status;
                }
    
                $brand->update($data);

                return redirect()->route('brands')->with('success', 'Brand updated successfully.');

            }

            return view('admin.brands.edit', compact('brand'));
        }else{
            abort(404);
        }
    }

    /**
     * Delete brand.
     */
    public function destroy(int $id)
    {
        $brand = Brand::findOrFail($id);
        $brand->delete();

        return redirect()->route('brands')->with('success', 'Brand deleted successfully.');
    }
}
