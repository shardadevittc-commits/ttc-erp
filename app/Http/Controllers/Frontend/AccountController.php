<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DispatchMaterial;
use App\Models\SaleOrderItem;
use Illuminate\Support\Facades\DB;
use App\Models\GateAndGaadi;
use App\Models\Invoice;
use Illuminate\Validation\ValidationException;

class AccountController extends Controller
{
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

        if ($gateIds->isNotEmpty()) {
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

        $listing->setCollection($listing->getCollection()->map(function ($gateRow) use ($materialsByGate) {
            $materials = $materialsByGate->get($gateRow->gate_id, collect());
            $vehicle = $materials->first();
            if (! $vehicle) {
                return null;
            }

            $vehicle->materials = $materials;

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

        $gate->load([
            'customer',
            'dispatchMaterials.saleOrder',
            'dispatchMaterials.saleOrderItem.product',
            'dispatchMaterials.saleOrderItem.size',
            'dispatchMaterials.saleOrderItem.grade',
            'dispatchMaterials.saleOrderItem.brand',
        ]);

        return view('accounts.invoiceBill', [
            'invoice' => $invoice,
            'gate' => $gate,
            'materials' => $gate->dispatchMaterials,
            'customer' => $gate->customer,
        ]);
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
            'due_days' => ['nullable', 'text'],
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
}
