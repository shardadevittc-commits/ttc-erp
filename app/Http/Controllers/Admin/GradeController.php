<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        $query = Grade::query();
        $search = trim((string) $request->query('search', ''));
        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $query->where('grade_name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('rack_no', 'like', "%{$search}%")
                    ->orWhere('warehouse', 'like', "%{$search}%");
            });
        }

        $sortBy = in_array($request->query('sort_by'), ['id', 'grade_name', 'status', 'created_at'], true)
            ? $request->query('sort_by') : 'id';
        $sortOrder = $request->query('sort_order') === 'asc' ? 'asc' : 'desc';
        $perPage = (int) $request->query('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 10;

        $listing = $query->orderBy($sortBy, $sortOrder)->paginate($perPage)->withQueryString();

        /* AJAX Request */
        if ($request->ajax()) {

            $html = view(
                'admin.grades.listingLoop',
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
        return view('admin.grades.index',[
                'listing' => $listing,
            ]
        );
    }

    public function add(Request $request)
    {
        if($request->isMethod('post')) 
        {			
            $validated = $request->validate([
                'grade_name' => ['required', 'string', 'max:50', Rule::unique('grades', 'grade_name')->whereNull('deleted_at')],
            ]);

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('users', 'public');
            }

            Grade::create([
                'grade_name' => $validated['grade_name'],
                'status' => (int) $request->status ?? 1,
                'created_by' => auth()->id(),
            ]);

            return redirect()->route('grades')->with('success', 'Grade added successfully.');
        }

        return view('admin.grades.add');
    }

    public function view(int $id)
    {
        $grades = Grade::with(['createdBy'])->findOrFail($id);
        return view('admin.grades.view',[
            'grade' => $grades
        ]);
    }

    public function edit(Request $request, $id)
    {
        $grade = Grade::findOrFail($id);
        $permissions = $grade->permissions;
        if($grade){
            if($request->isMethod('post')) {
                $validated = $request->validate([
                    'grade_name' => [
                        'required',
                        'string',
                        'max:100',
                        Rule::unique('grades', 'grade_name')->ignore($grade->id),
                    ],
                ]);

                $data = [
                    'grade_name' => $validated['grade_name'],
                ];

                if ($request->has('status')) {
                    $data['status'] = (int) $request->status;
                }
    
                $grade->update($data);

                return redirect()->route('grades')->with('success', 'Grade updated successfully.');

            }

            return view('admin.grades.edit', compact('grade'));
        }
        else
        {
            abort(404);
        }
    }

    public function destroy(int $id)
    {
        Grade::findOrFail($id)->delete();

        return redirect()->route('grades')->with('success', 'Grade deleted successfully.');
    }
}