<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CitiesController extends Controller
{
    private array $columns = [
        'id' => ['label' => 'ID'],
        'name' => ['label' => 'City'],
        'state.name' => ['label' => 'State'],
        'state.country.name' => ['label' => 'Country'],
        'status' => ['label' => 'Status'],
        'created_at' => ['label' => 'Created'],
    ];

    private array $fields = [
        ['name' => 'state_id', 'label' => 'State', 'type' => 'select', 'required' => true],
        ['name' => 'name', 'label' => 'City Name', 'required' => true, 'max' => 150],
        ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'required' => true, 'default' => 1, 'options' => [1 => 'Active', 0 => 'Inactive']],
    ];

    public function index(Request $request)
    {
        $query = City::query()->with('state.country');
        $search = trim((string) $request->query('search', ''));

        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhereHas('state', fn ($state) => $state->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('state.country', fn ($country) => $country->where('name', 'like', "%{$search}%"));
            });
        }

        $sortBy = in_array($request->query('sort_by'), ['id', 'name', 'status', 'created_at'], true)
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
            City::create($request->validate($this->validationRules($request)));

            return redirect()->route('admin.cities')->with('success', 'City added successfully.');
        }

        return view('admin.shared.catalog.form', $this->formViewData());
    }

    public function view(int $id)
    {
        return view('admin.shared.catalog.show', $this->viewData([
            'item' => City::with('state.country')->findOrFail($id),
        ]));
    }

    public function edit(Request $request, int $id)
    {
        $city = City::findOrFail($id);

        if ($request->isMethod('post')) {
            $city->update($request->validate($this->validationRules($request, $city)));

            return redirect()->route('admin.cities')->with('success', 'City updated successfully.');
        }

        return view('admin.shared.catalog.form', $this->formViewData($city));
    }

    public function destroy(int $id)
    {
        City::findOrFail($id)->delete();

        return redirect()->route('admin.cities')->with('success', 'City deleted successfully.');
    }

    private function validationRules(Request $request, ?City $city = null): array
    {
        $uniqueName = Rule::unique('cities', 'name')->where('state_id', $request->input('state_id'));
        if ($city !== null) {
            $uniqueName->ignore($city->id);
        }

        return [
            'state_id' => ['required', 'integer', 'exists:states,id'],
            'name' => ['required', 'string', 'max:150', $uniqueName],
            'status' => ['required', 'boolean'],
        ];
    }

    private function formViewData(?City $city = null): array
    {
        return $this->viewData([
            'item' => $city,
            'formAction' => $city ? route('admin.cities.edit', ['id' => $city->id]) : route('admin.cities.add'),
            'formTitle' => $city ? 'Update City' : 'Create New City',
            'options' => [
                'state_id' => State::where('status', true)->orderBy('name')->pluck('name', 'id')->all(),
            ],
        ]);
    }

    private function viewData(array $data = []): array
    {
        return array_merge([
            'title' => 'City',
            'pluralTitle' => 'Cities',
            'routePrefix' => 'admin.cities',
            'columns' => $this->columns,
            'fields' => $this->fields,
        ], $data);
    }
}