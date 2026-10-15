<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\GradeCategory;
use App\Models\GradeProperty;
use App\Models\GradeReaction;
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

            $grade = Grade::create([
                'grade_name' => $validated['grade_name'],
                'status' => (int) $request->status ?? 1,
                'created_by' => auth()->id(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'id' => $grade->id,
                    'text' => $grade->grade_name,
                ], 201);
            }

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

    public function composition(Request $request)
    {
        $composition = GradeCategory::all();
        return view('admin.grades.comIndex', compact('composition'));
    }

    public function grade_composition_add(Request $request)
    {
        $id = $request->query('id');

        // Fetch record for edit
        $gradeCategory = $id
            ? GradeCategory::whereNull('deleted_at')->findOrFail($id)
            : null;

        if ($request->isMethod('post')) {

            $validated = $request->validate([
                'c_name' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('grade_categories', 'name')
                        ->whereNull('deleted_at')
                        ->ignore($id),
                ],
            ]);

            if ($gradeCategory) {
                // Update existing record
                $gradeCategory->update([
                    'name' => $validated['c_name'],
                    'updated_at' => now(),
                ]);
            } else {
                // Create new record
                $gradeCategory = GradeCategory::create([
                    'name' => $validated['c_name'],
                    'created_by' => auth()->id(),
                ]);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'id' => $gradeCategory->id,
                    'text' => $gradeCategory->name,
                ], $id ? 200 : 201);
            }

            return redirect()
                ->route('grades.composition')
                ->with(
                    'success',
                    $id
                        ? 'Grade Composition updated successfully.'
                        : 'Grade Composition added successfully.'
                );
        }

        return view('admin.grades.comAdd', compact('gradeCategory'));
    }

    public function properties(Request $request)
    {
        $properties = GradeProperty::with('gradeCategory')->get();
        return view('admin.grades.properties', compact('properties'));
    }

    public function grade_properties_add(Request $request)
    {
        $id = $request->query('id');

        // Fetch record for edit
        $gradeProperty = $id
            ? GradeProperty::whereNull('deleted_at')->findOrFail($id)
            : null;

        if ($request->isMethod('post')) {

            $validated = $request->validate([
                'category_id' => ['required', 'exists:grade_categories,id'],
                'name' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('grade_properties', 'name')
                        ->whereNull('deleted_at')
                        ->ignore($id),
                ],
                'unit' => ['nullable', 'string', 'max:20'],
            ]);

            if ($gradeProperty) {
                // Update existing record
                $gradeProperty->update([
                    'category_id' => $validated['category_id'],
                    'name' => $validated['name'],
                    'unit' => $validated['unit'] ?? null,
                    'updated_at' => now(),
                ]);
            } else {
                // Create new record
                $gradeProperty = GradeProperty::create([
                    'category_id' => $validated['category_id'],
                    'name' => $validated['name'],
                    'unit' => $validated['unit'] ?? null,
                ]);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'id' => $gradeProperty->id,
                    'text' => $gradeProperty->name,
                ], $id ? 200 : 201);
            }

            return redirect()
                ->route('grade_properties')
                ->with(
                    'success',
                    $id
                        ? 'Grade Composition Property updated successfully.'
                        : 'Grade Composition Property added successfully.'
                );
        }

        // Fetch all grade categories for the dropdown
        $categories = GradeCategory::all();

        return view('admin.grades.properties_add', compact('gradeProperty', 'categories'));
    }

    public function grade_min_max(Request $request)
    {
        $gradeReactions = GradeReaction::with(['grade', 'property', 'category'])->get();
        return view('admin.grades.min_max_index', compact('gradeReactions'));
    }

    public function grade_add(Request $request)
    {
        $id = $request->query('id');

        // Fetch record for edit
        $gradeReaction = $id
            ? GradeReaction::whereNull('deleted_at')->findOrFail($id)
            : null;

        if ($request->isMethod('post')) {

            $validated = $request->validate([
                'grade_id' => ['required', 'exists:grades,id'],
                'property_id' => ['required', 'exists:grade_properties,id'],
                'grade_category_id' => ['required', 'exists:grade_categories,id'],
                'min_value' => ['required', 'numeric'],
                'max_value' => ['required', 'numeric'],
            ]);

            if ($gradeReaction) {
                // Update existing record
                $gradeReaction->update([
                    'grade_id' => $validated['grade_id'],
                    'property_id' => $validated['property_id'],
                    'grade_category_id' => $validated['grade_category_id'],
                    'min_value' => $validated['min_value'],
                    'max_value' => $validated['max_value'],
                    'updated_at' => now(),
                ]);
            } else {
                // Create new record
                $gradeReaction = GradeReaction::create([
                    'grade_id' => $validated['grade_id'],
                    'property_id' => $validated['property_id'],
                    'grade_category_id' => $validated['grade_category_id'],
                    'min_value' => $validated['min_value'],
                    'max_value' => $validated['max_value'],
                ]);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'id' => $gradeReaction->id,
                    'text' => "Grade Reaction for Grade ID: {$gradeReaction->grade_id}",
                ], $id ? 200 : 201);
            }

            return redirect()
                ->route('grade_min_max')
                ->with(
                    'success',
                    $id
                        ? 'Grade Reaction updated successfully.'
                        : 'Grade Reaction added successfully.'
                );
        }

        // Fetch all grades, properties, and categories for the dropdowns
        $grades = Grade::all();
        $properties = GradeProperty::all();
        $categories = GradeCategory::all();

        return view('admin.grades.min_max_add', compact('gradeReaction', 'grades', 'properties', 'categories'));
    }
}
