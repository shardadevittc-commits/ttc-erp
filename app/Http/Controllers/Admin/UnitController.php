<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Unit;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UnitsController extends Controller
{
    public function index(Request $request)
    {
        $query = Unit::query()->with('createdBy');

        /* Sorting */
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');

        $allowedSortColumns = [
            'id',
            'unit_name',
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
                'admin.units.listingLoop',
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
        return view('admin.units.index',[
                'listing' => $listing,
            ]
        );
    }

	public function add(Request $request)
    {
        if($request->isMethod('post')) {
			
            $validated = $request->validate([
                'unit_name' => ['required', 'string', 'max:100'],
            ]);

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('users', 'public');
            }

            Unit::create([
                'unit_name' 	=> $validated['unit_name'],
                'status' 		=> (int) $request->status ?? 1,
                'created_by' 	=> auth()->id(),
            ]);

            return redirect()->route('units')->with('success', 'Brand added successfully.');
        }

        return view('admin.units.add');
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
        $brand = Unit::findOrFail($id);
        $permissions = $brand->permissions;
        if($brand){
            if($request->isMethod('post')) {
                $validated = $request->validate([
                    'unit_name' => [
                        'required',
                        'string',
                        'max:100',
                        // 'alpha_dash',
                        Rule::unique('units', 'unit_name')->ignore($brand->id),
                    ],
                ]);

                $data = [
                    'unit_name' => $validated['unit_name'],
                ];

                if ($request->has('status')) {
                    $data['status'] = (int) $request->status;
                }
    
                $brand->update($data);

                return redirect()->route('brands')->with('success', 'Brand updated successfully.');

            }

            return view('admin.units.edit', compact('brand'));
        }else{
            abort(404);
        }
    }

    /**
     * Delete brand.
     */
    public function destroy(int $id)
    {
        $brand = Unit::findOrFail($id);
        $brand->delete();

        return redirect()->route('brands')->with('success', 'Brand deleted successfully.');
    }
}
