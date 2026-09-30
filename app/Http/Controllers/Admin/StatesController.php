<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StatesController extends Controller
{
    private array $columns = [
        'id' => ['label' => 'ID'],
        'name' => ['label' => 'State'],
        'code' => ['label' => 'Code'],
        'country.name' => ['label' => 'Country'],
        'status' => ['label' => 'Status'],
        'created_at' => ['label' => 'Created'],
    ];

    private array $fields = [
        ['name' => 'country_id', 'label' => 'Country', 'type' => 'select', 'required' => true],
        ['name' => 'name', 'label' => 'State Name', 'required' => true, 'max' => 100],
        ['name' => 'code', 'label' => 'State Code', 'max' => 20],
        ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'required' => true, 'default' => 1, 'options' => [1 => 'Active', 0 => 'Inactive']],
    ];

    public function index(Request $request)
    {
        $query = State::query()->with('country');
        $search = trim((string) $request->query('search', ''));

        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhereHas('country', fn ($country) => $country->where('name', 'like', "%{$search}%"));
            });
        }

        $sortBy = in_array($request->query('sort_by'), ['id', 'name', 'code', 'status', 'created_at'], true)
            ? $request->query('sort_by') : 'id';
        $sortOrder = $request->query('sort_order') === 'asc' ? 'asc' : 'desc';
        $perPage = (int) $request->query('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 10;

        return view('admin.shared.catalog.index', $this->viewData([
            'listing' => $query->orderBy($sortBy, $sortOrder)->paginate($perPage)->withQueryString(),
            'search' => $search,
            'perPage' => $perPage,
        ]));
    }

    public function add(Request $request)
    {
        if ($request->isMethod('post')) {
            State::create($request->validate($this->validationRules($request)));

            return redirect()->route('admin.states')->with('success', 'State added successfully.');
        }

        return view('admin.shared.catalog.form', $this->formViewData());
    }

    public function view(int $id)
    {
        return view('admin.shared.catalog.show', $this->viewData([
            'item' => State::with('country')->findOrFail($id),
        ]));
    }

    public function edit(Request $request, int $id)
    {
        $state = State::findOrFail($id);

        if ($request->isMethod('post')) {
            $state->update($request->validate($this->validationRules($request, $state)));

            return redirect()->route('admin.states')->with('success', 'State updated successfully.');
        }

        return view('admin.shared.catalog.form', $this->formViewData($state));
    }

    public function destroy(int $id)
    {
        $state = State::findOrFail($id);
        if ($state->cities()->exists()) {
            return redirect()->route('admin.states')->with('error', 'This state cannot be deleted while it has cities assigned.');
        }

        $state->delete();

        return redirect()->route('admin.states')->with('success', 'State deleted successfully.');
    }

    private function validationRules(Request $request, ?State $state = null): array
    {
        $uniqueName = Rule::unique('states', 'name')->where('country_id', $request->input('country_id'));
        if ($state !== null) {
            $uniqueName->ignore($state->id);
        }

        return [
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'name' => ['required', 'string', 'max:100', $uniqueName],
            'code' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'boolean'],
        ];
    }

    private function formViewData(?State $state = null): array
    {
        return $this->viewData([
            'item' => $state,
            'formAction' => $state ? route('admin.states.edit', ['id' => $state->id]) : route('admin.states.add'),
            'formTitle' => $state ? 'Update State' : 'Create New State',
            'options' => [
                'country_id' => Country::where('status', true)->orderBy('name')->pluck('name', 'id')->all(),
            ],
        ]);
    }

    private function viewData(array $data = []): array
    {
        return array_merge([
            'title' => 'State',
            'pluralTitle' => 'States',
            'routePrefix' => 'admin.states',
            'columns' => $this->columns,
            'fields' => $this->fields,
        ], $data);
    }
}