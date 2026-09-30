<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Grade;
use App\Models\Size;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SizeController extends Controller
{
  
    public function index(Request $request)
    {
        $query = Size::query()->with(['grade', 'brand', 'unit']);
        $search = trim((string) $request->query('search', ''));
        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $query->where('size_name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('rack_no', 'like', "%{$search}%")
                    ->orWhere('warehouse', 'like', "%{$search}%");
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
                'admin.sizes.listingLoop',
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
        return view('admin.sizes.index',[
                'listing' => $listing,
            ]
        );
    }

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

            Size::create([
                'size_name' => $validated['size_name'],
                'status' => (int) $request->status ?? 1,
                'created_by' => auth()->id(),
            ]);

            return redirect()->route('sizes')->with('success', 'Size added successfully.');
        }

        return view('admin.sizes.add');
    }

    public function view(int $id)
    {
        $sizes = Size::with(['grade', 'brand', 'unit'])->findOrFail($id);
        return view('admin.sizes.view',[
            'sizes' => $sizes
        ]);
    }

    public function edit(Request $request, $id)
    {
        $size = Size::findOrFail($id);
        $permissions = $size->permissions;
        if($size){
            if($request->isMethod('post')) {
                $validated = $request->validate([
                    'size_name' => [
                        'required',
                        'string',
                        'max:100',
                        // 'alpha_dash',
                        Rule::unique('sizes', 'size_name')->ignore($size->id),
                    ],
                ]);

                $data = [
                    'size_name' => $validated['size_name'],
                ];

                if ($request->has('status')) {
                    $data['status'] = (int) $request->status;
                }
    
                $size->update($data);

                return redirect()->route('sizes')->with('success', 'Size updated successfully.');

            }

            return view('admin.sizes.edit', compact('size'));
        }else{
            abort(404);
        }
    }

    public function destroy(int $id)
    {
        Size::findOrFail($id)->delete();

        return redirect()->route('sizes')->with('success', 'Size deleted successfully.');
    }
}