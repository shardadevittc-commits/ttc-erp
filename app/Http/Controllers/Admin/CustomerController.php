<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Customer;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
  
    public function index(Request $request)
    {
        $query = Customer::query()->with(['country', 'state']);
        $search = trim((string) $request->query('search', ''));
        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $query->where('company_name', 'like', "%{$search}%")
                    ->orWhere('cust_code', 'like', "%{$search}%")
                    ->orWhere('gst_no', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $sortBy = in_array($request->query('sort_by'), ['id', 'company_name', 'status', 'created_at'], true)
            ? $request->query('sort_by') : 'id';
        $sortOrder = $request->query('sort_order') === 'asc' ? 'asc' : 'desc';
        $perPage = (int) $request->query('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 10;

        $listing = $query->orderBy($sortBy, $sortOrder)->paginate($perPage)->withQueryString();

        /* AJAX Request */
        if ($request->ajax()) {

            $html = view(
                'admin.customers.listingLoop',
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
        return view('admin.customers.index',[
                'listing' => $listing,
            ]
        );
    }

    public function add(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->merge(['gst_no' => strtoupper(trim((string) $request->input('gst_no')))]);
            $validated = $request->validate($this->validationRules($request));
            $validated['gst_details'] = $this->decodeGstDetails($validated['gst_details'] ?? null);
            $validated['created_by'] = auth()->id();
            Customer::create($validated);

            return redirect()->route('customers')->with('success', 'Customer added successfully.');
        }

        $countries = Country::where('status', 1)->orderBy('name')->get();
        $states = State::where('status', 1)->where('country_id', old('country_id'))->orderBy('name')->get();
        $cities = City::where('status', 1)->where('state_id', old('state_id'))->orderBy('name')->get();

        return view('admin.customers.add', [
            'customer' => null,
            'countries' => $countries,
            'states' => $states,
            'cities' => $cities,
        ]);
    }

    public function view(int $id)
    {
        $customer = Customer::with(['country', 'state', 'city', 'createdBy'])->findOrFail($id);

        return view('admin.customers.show', compact('customer'));
    }

    public function edit(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        if ($request->isMethod('post')) {
            $request->merge(['gst_no' => strtoupper(trim((string) $request->input('gst_no')))]);
            $validated = $request->validate($this->validationRules($request, $customer));
            $gstDetails = $this->decodeGstDetails($validated['gst_details'] ?? null);
            $validated['gst_details'] = $gstDetails
                ?? ($validated['gst_no'] === $customer->gst_no ? $customer->gst_details : null);
            $customer->update($validated);

            return redirect()->route('customers')->with('success', 'Customer updated successfully.');
        }

        $countries = Country::where('status', 1)->orderBy('name')->get();
        $states = State::where('status', 1)->where('country_id', old('country_id', $customer->country_id))->orderBy('name')->get();
        $cities = City::where('status', 1)->where('state_id', old('state_id', $customer->state_id))->orderBy('name')->get();

        return view('admin.customers.form', compact('customer', 'countries', 'states', 'cities'));
    }

    public function statesByCountry(Request $request)
    {
        $states = State::where('status', 1)
            ->where('country_id', $request->integer('country_id'))
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json(['options' => $states]);
    }

    public function citiesByState(Request $request)
    {
        $cities = City::where('status', 1)
            ->where('state_id', $request->integer('state_id'))
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json(['options' => $cities]);
    }

    public function verifyGst(string $gstin)
    {
        $gstin = strtoupper(trim($gstin));
        if (! preg_match('/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/', $gstin)) {
            return response()->json(['message' => 'Enter a valid 15-character GSTIN.'], 422);
        }

        $lookupUrl = (string) config('services.gst.lookup_url');
        if ($lookupUrl === '') {
            return response()->json(['message' => 'GST verification is not configured. Set GST_LOOKUP_URL to your GST lookup provider endpoint.'], 503);
        }

        $url = str_contains($lookupUrl, '{gstin}')
            ? str_replace('{gstin}', rawurlencode($gstin), $lookupUrl)
            : $lookupUrl;
        $client = Http::acceptJson()->timeout(15);
        $token = (string) config('services.gst.api_token');
        if ($token !== '') {
            $client = $client->withToken($token);
        }

        try {
            $response = $client->get($url, str_contains($lookupUrl, '{gstin}') ? [] : ['gstin' => $gstin]);
        } catch (ConnectionException) {
            return response()->json(['message' => 'The GST verification provider could not be reached.'], 502);
        }

        $payload = $response->json();
        if (! $response->successful() || ! is_array($payload)) {
            return response()->json(['message' => 'The GST verification provider returned an error.'], 502);
        }

        $details = $payload['data'] ?? $payload['result'] ?? $payload;
        if (! is_array($details) || (isset($payload['success']) && $payload['success'] === false)) {
            return response()->json(['message' => $payload['message'] ?? 'GST details were not found.'], 422);
        }

        $countryName = $this->gstValue($details, ['country_name', 'countryName', 'country']);
        $country = $countryName
            ? Country::where('status', 1)->where('name', $countryName)->first()
            : Country::where('status', 1)->where('name', 'India')->first();
        $stateQuery = State::where('status', 1);
        if ($country) {
            $stateQuery->where('country_id', $country->id);
        }
        $stateName = $this->gstValue($details, ['state_name', 'stateName', 'state', 'stcd', 'pradr.addr.st']);
        $stateCode = $this->gstValue($details, ['state_code', 'stateCode', 'state_code_jurisdiction', 'pradr.addr.stcd']);
        $state = $stateName ? (clone $stateQuery)->where('name', $stateName)->first() : null;
        if (! $state && $stateCode) {
            $state = (clone $stateQuery)->where('code', $stateCode)->first();
        }
        $cityName = $this->gstValue($details, ['city', 'city_name', 'cityName', 'district', 'locality', 'pradr.addr.city', 'pradr.addr.dst']);
        $city = $cityName && $state
            ? City::where('status', 1)->where('state_id', $state->id)->where('name', $cityName)->first()
            : null;

        return response()->json([
            'details' => $payload,
            'fields' => [
                'company_name' => $this->gstValue($details, ['legal_name', 'legalName', 'trade_name', 'tradeName', 'company_name', 'companyName', 'taxpayer_name', 'name', 'lgnm', 'tradeNam']),
                'email' => $this->gstValue($details, ['email', 'email_id', 'emailAddress']),
                'mobile' => $this->gstValue($details, ['mobile', 'phone', 'contact_number', 'contactNumber']),
                'address' => $this->gstValue($details, ['address', 'principal_address', 'principalAddress', 'full_address', 'fullAddress', 'address_line'])
                    ?? $this->formatGstAddress(data_get($details, 'pradr.addr')),
                'pincode' => $this->gstValue($details, ['pincode', 'pin_code', 'postal_code', 'postalCode', 'pradr.addr.pncd']),
                'country_id' => $country?->id,
                'state_id' => $state?->id,
                'city_id' => $city?->id,
            ],
        ]);
    }

    public function destroy(int $id)
    {
        Customer::findOrFail($id)->delete();

        return redirect()->route('customers')->with('success', 'Customer deleted successfully.');
    }

    private function validationRules(Request $request, ?Customer $customer = null): array
    {
        $gstUnique = Rule::unique('customers', 'gst_no')->whereNull('deleted_at');
        $codeUnique = Rule::unique('customers', 'cust_code')->whereNull('deleted_at');
        if ($customer) {
            $gstUnique->ignore($customer->id);
            $codeUnique->ignore($customer->id);
        }

        return [
            'company_name' => ['required', 'string', 'max:255'],
            'cust_code' => ['required', 'string', 'max:100', $codeUnique],
            'gst_no' => ['required', 'string', 'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/', $gstUnique],
            'gst_details' => ['nullable', 'json', 'max:50000'],
            'email' => ['required', 'email', 'max:255'],
            'mobile' => ['required', 'string', 'max:30'],
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'state_id' => ['required', 'integer', Rule::exists('states', 'id')->where('country_id', $request->input('country_id'))],
            'city_id' => ['nullable', 'integer', Rule::exists('cities', 'id')->where('state_id', $request->input('state_id'))],
            'pincode' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:5000'],
            'buyer' => ['required', Rule::in([Customer::YES, Customer::NO])],
            'supplier' => ['required', Rule::in([Customer::YES, Customer::NO])],
            'status' => ['required', Rule::in([Customer::STATUS_ACTIVE, Customer::STATUS_DEACTIVE])],
        ];
    }

    private function decodeGstDetails(?string $details): ?array
    {
        if ($details === null || $details === '') {
            return null;
        }

        $decoded = json_decode($details, true);

        return is_array($decoded) ? $decoded : null;
    }

    private function gstValue(array $details, array $keys): ?string
    {
        foreach ($keys as $key) {
            $value = data_get($details, $key);
            if (is_scalar($value) && trim((string) $value) !== '') {
                return trim((string) $value);
            }
        }

        return null;
    }

    private function formatGstAddress(mixed $address): ?string
    {
        if (is_string($address) && trim($address) !== '') {
            return trim($address);
        }
        if (! is_array($address)) {
            return null;
        }

        $parts = array_filter(array_map(
            static fn ($key) => isset($address[$key]) && is_scalar($address[$key]) ? trim((string) $address[$key]) : null,
            ['flno', 'floor', 'bno', 'building', 'bnm', 'building_name', 'st', 'street', 'loc', 'locality', 'city', 'dst', 'district']
        ));

        return $parts ? implode(', ', array_unique($parts)) : null;
    }
}