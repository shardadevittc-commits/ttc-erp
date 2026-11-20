<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DispatchMaterial;
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

        $listing = DB::table('dispatch_material as dm')
            ->join('gate_and_gaadi as gate', 'gate.id', '=', 'dm.gate_id')
            ->leftJoin('customers as customer', 'customer.id', '=', 'gate.cust_id')
            ->join('sale_orders as so', 'so.id', '=', 'dm.sale_order_id')
            ->join('sale_order_items as soi', 'soi.id', '=', 'dm.sale_order_item_id')
            ->leftJoin('products as product', 'product.id', '=', 'soi.product_id')
            ->leftJoin('grades as grade', 'grade.id', '=', 'soi.grade_id')
            ->leftJoin('brands as brand', 'brand.id', '=', 'soi.brand_id')
            ->leftJoin('sizes as size', 'size.id', '=', 'soi.size_id')
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
            )
            ->select([
                'dm.id',
                'dm.gate_id',
                'dm.sale_order_id',
                'dm.sale_order_item_id',
                'dm.actual_qty',
                'dm.dispatch_qty',
                'dm.status',
                'dm.created_at',
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
            ->latest('dm.created_at')
            ->paginate((int) ($validated['per_page'] ?? 25))
            ->withQueryString();

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
                    'saleOrderItem:id,sale_order_id,product_id,grade_id,brand_id,size_id,size_unit,pcs_length_unit,item_qty,qty_type,sale_item_prices,size_extra,fs_bundle_weight',
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
        $tareWeight = $gate->weights->max('tareweight') ?? $gate->dispatchMaterials->first()?->weight?->tareweight ?? 0;
        $loadedWeight = $gate->weights->max('loadedweight') ?? $gate->weights->max('unload_loaded_weight') ?? $gate->weights->max('material_weight') ?? $gate->dispatchMaterials->first()?->weight?->loadedweight ?? 0;
        $subtotal = $gate->dispatchMaterials->sum(function (DispatchMaterial $dispatch) {
            $actual = max(0, (float) ($dispatch->actual_qty ?? $dispatch->dispatch_qty ?? 0));
            $returned = max(0, (float) ($dispatch->return_qty ?? 0));
            $netRate = (float) ($dispatch->saleOrderItem?->sale_item_prices ?? 0)
                + (float) ($dispatch->saleOrderItem?->size_extra ?? 0);

            return max(0, $actual - $returned) * $netRate;
        });

        return view('accounts.addInVoice', compact(
            'gate',
            'invoice',
            'partyName',
            'tareWeight',
            'loadedWeight',
            'subtotal'
        ));
    }

    public function storeInvoice(Request $request)
    {
        $validated = $request->validate([
            'gate_id' => ['required', 'integer', 'exists:gate_and_gaadi,id'],
            'billed_to' => ['nullable', 'string', 'max:255'],
            'invoice_no' => ['required', 'string', 'max:100', 'unique:invoices,invoice_no'],
            'invoice_date' => ['required', 'date'],
            'cash_discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'labor_charges' => ['nullable', 'numeric', 'min:0'],
            'other_charges' => ['nullable', 'numeric', 'min:0'],
            'tds_amount' => ['nullable', 'numeric', 'min:0'],
            'insurance_amount' => ['nullable', 'numeric', 'min:0'],
            'tcs_amount' => ['nullable', 'numeric', 'min:0'],
            'due_days' => ['nullable', 'integer', 'min:0'],
            'advance_payment' => ['nullable', 'numeric', 'min:0'],
            'sgst_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'cgst_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'igst_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $sgstPercent = round((float) ($validated['sgst_percent'] ?? 0), 3);
        $cgstPercent = round((float) ($validated['cgst_percent'] ?? 0), 3);
        $igstPercent = round((float) ($validated['igst_percent'] ?? 0), 3);

        if ($igstPercent > 0 && ($sgstPercent > 0 || $cgstPercent > 0)) {
            throw ValidationException::withMessages([
                'igst_percent' => 'I-GST cannot be combined with S-GST or C-GST.',
            ]);
        }

        $invoice = DB::transaction(function () use ($validated, $sgstPercent, $cgstPercent, $igstPercent) {
            $gate = GateAndGaadi::query()->lockForUpdate()->findOrFail($validated['gate_id']);

            if (Invoice::where('gate_id', $gate->id)->exists()) {
                throw ValidationException::withMessages([
                    'gate_id' => 'An invoice already exists for this gate.',
                ]);
            }

            $materials = DispatchMaterial::query()
                ->with('saleOrderItem:id,sale_item_prices,size_extra')
                ->where('gate_id', $gate->id)
                ->get();

            if ($materials->isEmpty()) {
                throw ValidationException::withMessages([
                    'gate_id' => 'No dispatch material records exist for this gate.',
                ]);
            }

            $subtotal = $materials->sum(function (DispatchMaterial $dispatch) {
                $actual = max(0, (float) ($dispatch->actual_qty ?? $dispatch->dispatch_qty ?? 0));
                $returned = max(0, (float) ($dispatch->return_qty ?? 0));
                $item = $dispatch->saleOrderItem;
                $netRate = (float) ($item?->sale_item_prices ?? 0) + (float) ($item?->size_extra ?? 0);
                return max(0, $actual - $returned) * $netRate;
            });

            $discountPercent = round((float) ($validated['cash_discount_percent'] ?? 0), 3);
            $discountAmount = round($subtotal * $discountPercent / 100, 3);
            $labor = round((float) ($validated['labor_charges'] ?? 0), 3);
            $other = round((float) ($validated['other_charges'] ?? 0), 3);
            $taxable = round(max(0, $subtotal - $discountAmount + $labor + $other), 3);

            $gstType = null;
            if ($sgstPercent > 0 || $cgstPercent > 0) {
                $gstType = 'SGST_CGST';
            } elseif ($igstPercent > 0) {
                $gstType = 'IGST';
            }

            $sgstAmount = $gstType === 'SGST_CGST'
                ? round($taxable * $sgstPercent / 100, 3)
                : 0;
            $cgstAmount = $gstType === 'SGST_CGST'
                ? round($taxable * $cgstPercent / 100, 3)
                : 0;
            $igstAmount = $gstType === 'IGST'
                ? round($taxable * $igstPercent / 100, 3)
                : 0;
            $gstAmount = round($sgstAmount + $cgstAmount + $igstAmount, 3);
            $tcs = round((float) ($validated['tcs_amount'] ?? 0), 3);
            $total = round($taxable + $gstAmount + $tcs, 3);

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
                'taxable_amount' => $taxable,
                'sgst_percent' => $gstType === 'SGST_CGST' ? $sgstPercent : 0,
                'sgst_amount' => $sgstAmount,
                'cgst_percent' => $gstType === 'SGST_CGST' ? $cgstPercent : 0,
                'cgst_amount' => $cgstAmount,
                'igst_percent' => $gstType === 'IGST' ? $igstPercent : 0,
                'igst_amount' => $igstAmount,
                'gst_amount' => $gstAmount,
                'tds_amount' => round((float) ($validated['tds_amount'] ?? 0), 3),
                'insurance_amount' => round((float) ($validated['insurance_amount'] ?? 0), 3),
                'tcs_amount' => $tcs,
                'due_days' => (int) ($validated['due_days'] ?? 0),
                'advance_payment' => round((float) ($validated['advance_payment'] ?? 0), 3),
                'total_amount' => $total,
                'created_by' => auth()->id(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Invoice created successfully.',
            'invoice_id' => $invoice->id,
        ]);
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
