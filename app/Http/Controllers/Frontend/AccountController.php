<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DispatchMaterial;
use App\Models\SaleOrderItem;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use App\Models\GateAndGaadi;
use App\Models\Invoice;
use App\Models\ScrapDispatch;
use App\Models\ScrapTotal;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class AccountController extends Controller
{
    private const SCRAP_CATEGORIES = [
        'acid_pickling' => [
            'label' => 'Acid Pickling',
            'stock' => 'acid_pickling',
            'recoverability' => 'Non-recoverable',
        ],
        'short_blasting' => [
            'label' => 'Short Blasting',
            'stock' => 'short_blasting',
            'recoverability' => 'Non-recoverable',
        ],
        'pointing' => [
            'label' => 'Pointing Scrap',
            'stock' => 'pointing',
            'recoverability' => 'Recoverable',
        ],
        'point_discard' => [
            'label' => 'Point Discard',
            'stock' => 'point_discard',
            'recoverability' => 'Recoverable',
        ],
        'bedsawcut' => [
            'label' => 'Bend Saw Cut',
            'stock' => 'bedsawcut',
            'recoverability' => 'Recoverable',
        ],
        'misc' => [
            'label' => 'Misc Scrap',
            'stock' => 'misc',
            'recoverability' => 'Recoverable',
        ],
    ];

    public function index(Request $request)
    {
        $validated = $request->validate([
            'party_name' => ['nullable', 'string', 'max:255'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'token_no' => ['nullable', 'string', 'max:100'],
            'vehicle_no' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string', 'max:30'],
            'per_page' => ['nullable', 'integer', 'in:10,25,50,100'],
        ]);

        $matchingGates = DB::table('dispatch_material as dm')
            ->join('gate_and_gaadi as gate', 'gate.id', '=', 'dm.gate_id')
            ->leftJoin('customers as customer', 'customer.id', '=', 'gate.cust_id')
            ->when($request->filled('party_name'), fn ($query) =>
                $query->where('customer.company_name', 'like', '%' . $validated['party_name'] . '%')
            )
            ->when($request->filled('date_from'), fn ($query) =>
                $query->whereDate('dm.created_at', '>=', $validated['date_from'])
            )
            ->when($request->filled('date_to'), fn ($query) =>
                $query->whereDate('dm.created_at', '<=', $validated['date_to'])
            )
            ->when($request->filled('token_no'), fn ($query) =>
                $query->where('gate.tokenno', 'like', '%' . $validated['token_no'] . '%')
            )
            ->when($request->filled('vehicle_no'), fn ($query) =>
                $query->where('gate.truckno', 'like', '%' . $validated['vehicle_no'] . '%')
            )
            ->when($request->filled('status'), fn ($query) =>
                $query->where('dm.status', $validated['status'])
            );

        $listing = (clone $matchingGates)
            ->select('dm.gate_id')
            ->groupBy('dm.gate_id')
            ->orderByRaw('MAX(dm.created_at) DESC')
            ->orderByDesc('dm.gate_id')
            ->paginate((int) ($validated['per_page'] ?? 25))
            ->withQueryString();

        $gateIds = $listing->getCollection()->pluck('gate_id');
        $materialsByGate = collect();
        $invoicedGateIds = collect();

        if ($gateIds->isNotEmpty()) {
            $invoicedGateIds = Invoice::query()
                ->whereIn('gate_id', $gateIds)
                ->pluck('gate_id')
                ->flip();

            $materialsByGate = DB::table('dispatch_material as dm')
                ->join('gate_and_gaadi as gate', 'gate.id', '=', 'dm.gate_id')
                ->leftJoin('customers as customer', 'customer.id', '=', 'gate.cust_id')
                ->join('sale_orders as so', 'so.id', '=', 'dm.sale_order_id')
                ->join('sale_order_items as soi', 'soi.id', '=', 'dm.sale_order_item_id')
                ->leftJoin('products as product', 'product.id', '=', 'soi.product_id')
                ->leftJoin('grades as grade', 'grade.id', '=', 'soi.grade_id')
                ->leftJoin('brands as brand', 'brand.id', '=', 'soi.brand_id')
                ->leftJoin('sizes as size', 'size.id', '=', 'soi.size_id')
                ->whereIn('dm.gate_id', $gateIds)
                ->select([
                    'dm.id',
                    'dm.gate_id',
                    'dm.sale_order_id',
                    'dm.sale_order_item_id',
                    'dm.actual_qty',
                    'dm.dispatch_qty',
                    'dm.status',
                    'dm.created_at',
                    'gate.cust_id',
                    'gate.tokenno',
                    'gate.truckno',
                    'customer.company_name',
                    'so.customer_po_no',
                    'product.product_name',
                    'grade.grade_name',
                    'brand.brand_name',
                    'size.size_name',
                    'soi.size_unit',
                    'soi.qty_type',
                ])
                ->orderByDesc('dm.created_at')
                ->get()
                ->groupBy('gate_id');
        }

        $listing->setCollection($listing->getCollection()->map(function ($gateRow) use ($materialsByGate, $invoicedGateIds) {
            $materials = $materialsByGate->get($gateRow->gate_id, collect());
            $vehicle = $materials->first();
            if (! $vehicle) {
                return null;
            }

            $vehicle->materials = $materials;
            $vehicle->invoice_submitted = $invoicedGateIds->has($gateRow->gate_id);

            return $vehicle;
        })->filter()->values());

        $statuses = DispatchMaterial::select('status')
            ->distinct()
            ->whereNotNull('status')
            ->orderBy('status')
            ->pluck('status');

        return view('accounts.index', compact('listing', 'statuses'));
    }

    public function show(GateAndGaadi $gate)
    {
        return $this->addInvoice($gate);
    }

    public function invoiceBill(GateAndGaadi $gate)
    {
        $invoice = Invoice::query()
            ->where('gate_id', $gate->id)
            ->firstOrFail();

        return $this->renderAccountInvoice($invoice);
    }

    public function publicAccountInvoice(string $token)
    {
        $invoice = Invoice::query()
            ->where('public_token', $token)
            ->firstOrFail();

        return $this->renderAccountInvoice($invoice);
    }

    private function renderAccountInvoice(Invoice $invoice)
    {
        $gate = GateAndGaadi::withTrashed()->findOrFail($invoice->gate_id);
        $gate->load([
            'customer',
            'dispatchMaterials.saleOrder',
            'dispatchMaterials.saleOrderItem.product',
            'dispatchMaterials.saleOrderItem.size',
            'dispatchMaterials.saleOrderItem.grade',
            'dispatchMaterials.saleOrderItem.brand',
        ]);

        return response()->view('accounts.invoiceBill', [
            'invoice' => $invoice,
            'gate' => $gate,
            'materials' => $gate->dispatchMaterials,
            'customer' => $gate->customer,
        ])->header('Cache-Control', 'private, no-store')
            ->header('Referrer-Policy', 'no-referrer')
            ->header('X-Robots-Tag', 'noindex, nofollow');
    }

    public function addInvoice(GateAndGaadi $gate)
    {
        $gate->load([
            'customer:id,company_name',
            'entrySecurity:id,first_name,last_name',
            'exitSecurity:id,first_name,last_name',
            'weights:id,vehicle_id,tareweight,loadedweight,unload_loaded_weight,material_weight',
            'dispatchMaterials' => function ($query) {
                $query->select([
                    'id', 'gate_id', 'sale_order_id', 'sale_order_item_id', 'weight_id',
                    'actual_qty', 'dispatch_qty', 'return_qty', 'return_remarks', 'status',
                ])->with([
                    'weight:id,tareweight,loadedweight,unload_loaded_weight,material_weight',
                    'saleOrderItem:id,sale_order_id,product_id,grade_id,brand_id,size_id,size_unit,pcs_length_unit,item_qty,qty_type,sale_item_prices,size_extra,fs_bundle_weight,fs_bundle_unit',
                    'saleOrderItem.product:id,product_name',
                    'saleOrderItem.grade:id,grade_name',
                    'saleOrderItem.brand:id,brand_name',
                    'saleOrderItem.size:id,size_name',
                ]);
            },
        ]);
        abort_if($gate->dispatchMaterials->isEmpty(), 404);

        $invoice = Invoice::where('gate_id', $gate->id)->first();
        $partyName = $gate->customer?->company_name ?: '-';
        $suggestedInvoiceNo = $invoice ? null : $this->nextInvoiceNumber();
        $tareWeight = $gate->weights->max('tareweight') ?? $gate->dispatchMaterials->first()?->weight?->tareweight ?? 0;
        $loadedWeight = $gate->weights->max('loadedweight') ?? $gate->weights->max('unload_loaded_weight') ?? $gate->weights->max('material_weight') ?? $gate->dispatchMaterials->first()?->weight?->loadedweight ?? 0;
        $totalDispatchTons = $gate->dispatchMaterials->sum(function (DispatchMaterial $dispatch) {
            $dispatchQty = max(0, (float) ($dispatch->dispatch_qty ?? 0));

            return (int) $dispatch->saleOrderItem?->qty_type === SaleOrderItem::QTY_KGS
                ? $dispatchQty / 1000
                : $dispatchQty;
        });
        $subtotal = $gate->dispatchMaterials->sum(function (DispatchMaterial $dispatch) {
            $dispatchQty = max(0, (float) ($dispatch->dispatch_qty ?? 0));
            $dispatchTons = (int) $dispatch->saleOrderItem?->qty_type === SaleOrderItem::QTY_KGS
                ? $dispatchQty / 1000
                : $dispatchQty;
            $netRate = (float) ($dispatch->saleOrderItem?->sale_item_prices ?? 0)
                + (float) ($dispatch->saleOrderItem?->size_extra ?? 0);

            return $dispatchTons * $netRate;
        });

        return view('accounts.addInVoice', compact(
            'gate',
            'invoice',
            'partyName',
            'suggestedInvoiceNo',
            'totalDispatchTons',
            'tareWeight',
            'loadedWeight',
            'subtotal'
        ));
    }

    private function nextInvoiceNumber(): string
    {
        $year = now()->format('Y');
        $prefix = 'INV-'.$year.'-';
        $sequence = Invoice::query()
            ->where('invoice_no', 'like', $prefix.'%')
            ->pluck('invoice_no')
            ->reduce(function (int $highest, string $invoiceNo) use ($year) {
                if (preg_match('/^INV-'.preg_quote($year, '/').'-(\d+)$/', $invoiceNo, $matches)) {
                    return max($highest, (int) $matches[1]);
                }

                return $highest;
            }, 0) + 1;

        do {
            $invoiceNo = $prefix.str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
            $sequence++;
        } while (Invoice::where('invoice_no', $invoiceNo)->exists());

        return $invoiceNo;
    }

    public function storeInvoice(Request $request)
    {
        $validated = $request->validate([
            'gate_id' => ['required', 'integer', 'exists:gate_and_gaadi,id'],
            'billed_to' => ['nullable', 'string', 'max:255'],
            'invoice_no' => ['required', 'string', 'max:100', 'unique:invoices,invoice_no'],
            'invoice_date' => ['required', 'date'],
            'cash_discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'labor_rate_per_ton' => ['nullable', 'numeric', 'min:0'],
            'other_charges' => ['nullable', 'numeric', 'min:0'],
            'freight_type' => ['required', 'integer', 'in:1,2'],
            'freight_amount' => ['nullable', 'required_if:freight_type,2', 'numeric', 'min:0'],
            'tds_amount' => ['nullable', 'numeric', 'min:0'],
            'insurance_amount' => ['nullable', 'numeric', 'min:0'],
            'tcs_amount' => ['nullable', 'numeric', 'min:0'],
            'due_days' => ['nullable'],
            'advance_payment' => ['nullable', 'numeric', 'min:0'],
            'sgst_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'cgst_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'igst_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $sgstPercent = (float) ($validated['sgst_percent'] ?? 0);
        $cgstPercent = (float) ($validated['cgst_percent'] ?? 0);
        $igstPercent = (float) ($validated['igst_percent'] ?? 0);

        if ($igstPercent > 0 && ($sgstPercent > 0 || $cgstPercent > 0)) {
            throw ValidationException::withMessages([
                'igst_percent' => 'I-GST cannot be combined with S-GST or C-GST.',
            ]);
        }

        try {
            $invoice = DB::transaction(function () use ($validated, $sgstPercent, $cgstPercent, $igstPercent) {
            $gate = GateAndGaadi::query()->lockForUpdate()->findOrFail($validated['gate_id']);

            if (Invoice::where('gate_id', $gate->id)->exists()) {
                throw ValidationException::withMessages([
                    'gate_id' => 'An invoice already exists for this gate.',
                ]);
            }

            $materials = DispatchMaterial::query()
                ->with('saleOrderItem:id,sale_item_prices,size_extra,qty_type')
                ->where('gate_id', $gate->id)
                ->get();

            if ($materials->isEmpty()) {
                throw ValidationException::withMessages([
                    'gate_id' => 'No dispatch material records exist for this gate.',
                ]);
            }

            $totalDispatchTons = $materials->sum(function (DispatchMaterial $dispatch) {
                $dispatchQty = max(0, (float) ($dispatch->dispatch_qty ?? 0));

                return (int) $dispatch->saleOrderItem?->qty_type === SaleOrderItem::QTY_KGS
                    ? $dispatchQty / 1000
                    : $dispatchQty;
            });
            $subtotal = $materials->sum(function (DispatchMaterial $dispatch) {
                $dispatchQty = max(0, (float) ($dispatch->dispatch_qty ?? 0));
                $dispatchTons = (int) $dispatch->saleOrderItem?->qty_type === SaleOrderItem::QTY_KGS
                    ? $dispatchQty / 1000
                    : $dispatchQty;
                $item = $dispatch->saleOrderItem;
                $netRate = (float) ($item?->sale_item_prices ?? 0) + (float) ($item?->size_extra ?? 0);

                return $dispatchTons * $netRate;
            });

            $discountPercent = (float) ($validated['cash_discount_percent'] ?? 0);
            $discountAmount = $subtotal * $discountPercent / 100;
            $labor = $totalDispatchTons * (float) ($validated['labor_rate_per_ton'] ?? 0);
            $other = (float) ($validated['other_charges'] ?? 0);
            $freightType = (int) ($validated['freight_type'] ?? 1);
            $freight = $freightType === 2
                ? (float) ($validated['freight_amount'] ?? 0)
                : 0;
            $taxable = max(0, $subtotal - $discountAmount + $labor + $freight + $other);

            $gstType = null;
            if ($sgstPercent > 0 || $cgstPercent > 0) {
                $gstType = 'SGST_CGST';
            } elseif ($igstPercent > 0) {
                $gstType = 'IGST';
            }

            $sgstAmount = $gstType === 'SGST_CGST'
                ? $taxable * $sgstPercent / 100
                : 0;
            $cgstAmount = $gstType === 'SGST_CGST'
                ? $taxable * $cgstPercent / 100
                : 0;
            $igstAmount = $gstType === 'IGST'
                ? $taxable * $igstPercent / 100
                : 0;
            $gstAmount = $sgstAmount + $cgstAmount + $igstAmount;
            $tcs = (float) ($validated['tcs_amount'] ?? 0);
            $total = $taxable + $gstAmount + $tcs;

            return Invoice::create([
                'gate_id' => $gate->id,
                'billed_to' => $validated['billed_to'] ?? $gate->customer?->company_name,
                'invoice_no' => $validated['invoice_no'],
                'invoice_date' => $validated['invoice_date'],
                'gst_type' => $gstType,
                'cash_discount_percent' => $discountPercent,
                'cash_discount_amount' => $discountAmount,
                'labor_charges' => $labor,
                'other_charges' => $other,
                'freight_type' => $freightType,
                'freight_amount' => $freight,
                'taxable_amount' => $taxable,
                'sgst_percent' => $gstType === 'SGST_CGST' ? $sgstPercent : 0,
                'sgst_amount' => $sgstAmount,
                'cgst_percent' => $gstType === 'SGST_CGST' ? $cgstPercent : 0,
                'cgst_amount' => $cgstAmount,
                'igst_percent' => $gstType === 'IGST' ? $igstPercent : 0,
                'igst_amount' => $igstAmount,
                'gst_amount' => $gstAmount,
                'tds_amount' => (float) ($validated['tds_amount'] ?? 0),
                'insurance_amount' => (float) ($validated['insurance_amount'] ?? 0),
                'tcs_amount' => $tcs,
                'due_days' => (int) ($validated['due_days'] ?? 0),
                'advance_payment' => (float) ($validated['advance_payment'] ?? 0),
                'total_amount' => $total,
                'created_by' => auth()->id(),
            ]);
            });
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Unable to create the invoice. Please review the entered values and try again.');
        }

        return redirect()
            ->route('accounts.addInvoice', ['gate' => $validated['gate_id']])
            ->with('success', 'Invoice created successfully.');
    }

    public function storeReturn(Request $request)
    {
        $validated = $request->validate([
            'dispatch_material_id' => ['required', 'integer', 'exists:dispatch_material,id'],
            'return_qty' => ['required', 'numeric', 'min:0'],
            'return_remarks' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($validated) {
            $dispatch = DispatchMaterial::query()
                ->lockForUpdate()
                ->findOrFail($validated['dispatch_material_id']);
            $available = (float) ($dispatch->actual_qty ?? $dispatch->dispatch_qty ?? 0);
            $returnQty = round((float) $validated['return_qty'], 3);

            if ($returnQty > $available) {
                throw ValidationException::withMessages([
                    'return_qty' => 'Return quantity cannot exceed dispatched quantity.',
                ]);
            }

            $dispatch->update([
                'return_qty' => $returnQty,
                'return_remarks' => $validated['return_remarks'] ?? null,
            ]);
        });

        return redirect()->back()->with('success', 'Return quantity saved successfully.');
    }

    // Scrap dispatch related methods
    public function scrapDispatch()
    {
        $this->authorizeScrapDispatch();

        $stock = ScrapTotal::query()->orderBy('id')->first() ?? new ScrapTotal();
        $usedVehicleIds = ScrapDispatch::query()
                    ->pluck('vehicle_id')
                    ->toArray();

        $vehicles = GateAndGaadi::query()
            ->select(['id', 'cust_id', 'truckno', 'formlist'])
            ->with('customer:id,company_name')
            ->whereNotNull('truckno')
            ->where('formlist', 'Scrap Vehicle')
            ->whereNotIn('id', $usedVehicleIds)
            ->orderByDesc('id')
            ->limit(500)
            ->get();

        return view('accounts.scrapDispatch', [
            'stock' => $stock,
            'categories' => self::SCRAP_CATEGORIES,
            'vehicles' => $vehicles,
            'submissionToken' => (string) Str::uuid(),
        ]);
    }

    public function storeScrapDispatch(Request $request)
    {
        $this->authorizeScrapDispatch();

        $rules = [
            'submission_token' => ['required', 'uuid'],
            'vehicle_id' => ['required', 'integer', 'exists:gate_and_gaadi,id'],
            'first_weight' => ['required', 'numeric', 'min:0'],
            'last_weight' => ['required', 'numeric', 'min:0'],
            'weight_slip' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'invoice_no' => ['required', 'string', 'max:10'],
            'invoice_date' => ['required', 'date'],
            'invoice_amt' => ['required', 'numeric', 'min:0'],
            'invoice_gst' => ['nullable', 'numeric', 'min:0'],
            'misc_amt' => ['nullable', 'numeric', 'min:0'],
            'mode_of_payment' => ['required', 'string', 'in:Cash,Bank Transfer,Cheque,UPI,Credit'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ];

        foreach (array_keys(self::SCRAP_CATEGORIES) as $category) {
            $rules[$category . '_dispatch'] = ['nullable', 'numeric', 'min:0'];
            $rules[$category . '_rate'] = ['nullable', 'numeric', 'min:0'];
        }

        $validated = $request->validate($rules);
        $requestedQuantities = [];
        $projectedAmount = 0.0;
        $rateErrors = [];

        foreach (self::SCRAP_CATEGORIES as $category => $definition) {
            $quantity = (float) ($validated[$category . '_dispatch'] ?? 0);
            $rate = (float) ($validated[$category . '_rate'] ?? 0);
            $requestedQuantities[$category] = $quantity;
            $projectedAmount += $quantity * $rate;

            if ($quantity > 0 && ! isset($validated[$category . '_rate'])) {
                $rateErrors[$category . '_rate'] = 'Enter a rate for ' . $definition['label'] . '.';
            }
        }

        if ($rateErrors) {
            throw ValidationException::withMessages($rateErrors);
        }

        if (array_sum($requestedQuantities) <= 0) {
            throw ValidationException::withMessages([
                'scrap' => 'Enter a dispatch quantity for at least one scrap category.',
            ]);
        }

        $invoiceAmount = (float) $validated['invoice_amt'];
        $gstAmount = (float) ($validated['invoice_gst'] ?? 0);
        $miscAmount = (float) ($validated['misc_amt'] ?? 0);
        $finalAmount = $invoiceAmount + $gstAmount + $miscAmount;
        $storedSlip = null;

        try {
            if ($request->hasFile('weight_slip')) {
                $storedSlip = $request->file('weight_slip')->store('scrap-dispatch/weight-slips', 'public');
            }

            DB::transaction(function () use (
                $validated,
                $requestedQuantities,
                $storedSlip,
                $invoiceAmount,
                $gstAmount,
                $miscAmount,
                $finalAmount
            ) {
                $usedVehicleIds = ScrapDispatch::query()
                    ->pluck('vehicle_id')
                    ->toArray();

                $vehicles = GateAndGaadi::query()
                    ->where('formlist', 'Scrap Vehicle')
                    ->whereNotIn('id', $usedVehicleIds)
                    ->get();

                $customer = Customer::query()
                    ->findOrFail((int) $vehicle->cust_id);

                $stock = ScrapTotal::query()
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->first();

                if (! $stock) {
                    DB::table('scrap_totals')->insertOrIgnore([
                        'id' => 1,
                        'acid_pickling' => 0,
                        'short_blasting' => 0,
                        'pointing' => 0,
                        'point_discard' => 0,
                        'bedsawcut' => 0,
                        'misc' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $stock = ScrapTotal::query()
                        ->orderBy('id')
                        ->lockForUpdate()
                        ->firstOrFail();
                }

                if (ScrapDispatch::query()->where('submission_token', $validated['submission_token'])->exists()) {
                    throw ValidationException::withMessages([
                        'submission_token' => 'This scrap dispatch has already been submitted.',
                    ]);
                }

                $dispatchData = [
                    'submission_token' => $validated['submission_token'],
                    'vehicle_id' => $vehicle->id,
                    'customer_id' => $customer->id,
                    'vehicle_type' => $vehicle->formlist,
                    'first_weight' => (float) $validated['first_weight'],
                    'last_weight' => (float) $validated['last_weight'],
                    'actual_weight' => abs((float) $validated['first_weight'] - (float) $validated['last_weight']),
                    'weight_slip' => $storedSlip,
                    'invoice_no' => $validated['invoice_no'],
                    'invoice_date' => $validated['invoice_date'],
                    'invoice_amt' => $invoiceAmount,
                    'invoice_gst' => $gstAmount,
                    'misc_amt' => $miscAmount,
                    'final_amt' => $finalAmount,
                    'mode_of_payment' => $validated['mode_of_payment'],
                    'remarks' => $validated['remarks'] ?? null,
                    'created_by' => auth()->id(),
                ];

                foreach (self::SCRAP_CATEGORIES as $category => $definition) {
                    $quantity = $requestedQuantities[$category];
                    $rate = (float) ($validated[$category . '_rate'] ?? 0);
                    $available = (float) ($stock->{$definition['stock']} ?? 0);

                    if ($quantity > $available) {
                        throw ValidationException::withMessages([
                            $category . '_dispatch' => $definition['label'] . ' dispatch quantity exceeds the available stock of ' . $available . ' kg.',
                        ]);
                    }

                    $dispatchData[$category . '_dispatch'] = $quantity;
                    $dispatchData[$category . '_type'] = $definition['recoverability'];
                    $dispatchData[$category . '_rate'] = $rate;
                    $dispatchData[$category . '_total'] = $quantity * $rate;

                    if ($quantity > 0) {
                        $stock->{$definition['stock']} = $available - $quantity;
                    }
                }

                $stock->save();
                ScrapDispatch::query()->create($dispatchData);
            });
        } catch (ValidationException $exception) {
            if ($storedSlip) {
                Storage::disk('public')->delete($storedSlip);
            }

            throw $exception;
        } catch (Throwable $exception) {
            if ($storedSlip) {
                Storage::disk('public')->delete($storedSlip);
            }

            report($exception);

            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    $exception instanceof QueryException
                        ? 'A database error prevented saving the scrap dispatch. No stock was deducted. Please contact the administrator to verify the Scrap Dispatch database setup.'
                        : 'Unable to save the scrap dispatch. No stock was deducted. Please try again or contact the administrator if the problem continues.'
                );
        }

        return redirect()
            ->route('scrapDispatch.index')
            ->with('success', 'Scrap dispatch saved successfully. Projected scrap value: ' . number_format($projectedAmount, 2, '.', '') . '.');
    }

    public function scrapDispatchHistory(Request $request)
    {
        $this->authorizeScrapDispatch();

        $validated = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'buyer' => ['nullable', 'string', 'max:255'],
            'vehicle_no' => ['nullable', 'string', 'max:50'],
            'per_page' => ['nullable', 'integer', 'in:10,25,50,100'],
        ]);

        $listing = ScrapDispatch::query()
            ->leftJoin('customers', 'customers.id', '=', 'scrap_dispatch.customer_id')
            ->leftJoin('gate_and_gaadi', 'gate_and_gaadi.id', '=', 'scrap_dispatch.vehicle_id')
            ->select([
                'scrap_dispatch.*',
                'customers.company_name as buyer_name',
                'gate_and_gaadi.truckno as vehicle_no',
            ])
            ->when($request->filled('date_from'), fn ($query) =>
                $query->whereDate('scrap_dispatch.created_at', '>=', $validated['date_from'])
            )
            ->when($request->filled('date_to'), fn ($query) =>
                $query->whereDate('scrap_dispatch.created_at', '<=', $validated['date_to'])
            )
            ->when($request->filled('buyer'), fn ($query) =>
                $query->where('customers.company_name', 'like', '%' . $validated['buyer'] . '%')
            )
            ->when($request->filled('vehicle_no'), fn ($query) =>
                $query->where('gate_and_gaadi.truckno', 'like', '%' . $validated['vehicle_no'] . '%')
            )
            ->latest('scrap_dispatch.created_at')
            ->paginate((int) ($validated['per_page'] ?? 25))
            ->withQueryString();

        return view('accounts.scrapDispatchHistory', [
            'listing' => $listing,
            'categories' => self::SCRAP_CATEGORIES,
        ]);
    }

    public function scrapDispatchInvoice(ScrapDispatch $scrapDispatch)
    {
        $this->authorizeScrapDispatch();

        abort_if(blank($scrapDispatch->invoice_no), 404);

        return $this->renderScrapDispatchInvoice($scrapDispatch);
    }

    public function publicScrapDispatchInvoice(string $token)
    {
        $scrapDispatch = ScrapDispatch::query()
            ->where('public_token', $token)
            ->whereNotNull('invoice_no')
            ->firstOrFail();

        return $this->renderScrapDispatchInvoice($scrapDispatch);
    }

    private function renderScrapDispatchInvoice(ScrapDispatch $scrapDispatch)
    {
        return response()->view('accounts.scrapDispatchInvoice', [
            'dispatch' => $scrapDispatch,
            'customer' => Customer::withTrashed()->find($scrapDispatch->customer_id),
            'vehicle' => GateAndGaadi::withTrashed()->find($scrapDispatch->vehicle_id),
            'categories' => self::SCRAP_CATEGORIES,
        ])->header('Cache-Control', 'private, no-store')
            ->header('Referrer-Policy', 'no-referrer')
            ->header('X-Robots-Tag', 'noindex, nofollow');
    }

    private function authorizeScrapDispatch(): void
    {
        abort_unless(auth()->user()?->role?->short_name === 'account', 403);
    }
}
