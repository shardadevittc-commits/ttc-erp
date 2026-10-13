<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Customer;
use App\Models\Grade;
use App\Models\Product;
use App\Models\SaleOrder;
use App\Models\Size;
use App\Models\State;
use App\Repositories\SaleOrderRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SaleOrderController extends Controller
{
    public function __construct(private SaleOrderRepository $saleOrderRepository)
    {
    }

    public function index(Request $request)
    {
        $listing = $this->saleOrderRepository->listing($request);

        if ($request->ajax()) {
            return response()->json([
                'status' => true,
                'html' => view('admin.saleOrders.listingLoop', compact('listing'))->render(),
                'page' => $listing->currentPage(),
                'counter' => $listing->perPage(),
                'count' => $listing->total(),
                'lastPage' => $listing->lastPage(),
                'pagination' => $listing->links('pagination::bootstrap-5')->toHtml(),
            ]);
        }

        return view('admin.saleOrders.index', compact('listing'));
    }

    public function add(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $this->validateSaleOrder($request);
            // pr($validated); die;
            $this->saleOrderRepository->save(
                $this->saleOrderData($validated),
                $validated['items']
            );

            return redirect()->route('saleOrders')->with('success', 'Sale Order added successfully.');
        }

        return view('admin.saleOrders.add', $this->formData());
    }

    public function view(int $id)
    {
        $saleOrder = $this->saleOrderRepository->find($id);

        return view('admin.saleOrders.view', compact('saleOrder'));
    }

    public function edit(Request $request, int $id)
    {
        $saleOrder = $this->saleOrderRepository->find($id);

        if ($request->isMethod('post')) {
            $validated = $this->validateSaleOrder($request);
            $this->saleOrderRepository->save(
                $this->saleOrderData($validated),
                $validated['items'],
                $saleOrder
            );

            return redirect()->route('saleOrders')->with('success', 'Sale Order updated successfully.');
        }

        return view('admin.saleOrders.edit', array_merge(
            $this->formData(),
            compact('saleOrder')
        ));
    }

    public function destroy(int $id)
    {
        $this->saleOrderRepository->find($id)->delete();

        return redirect()->route('saleOrders')->with('success', 'Sale Order deleted successfully.');
    }

    private function validateSaleOrder(Request $request): array
    {
        $productType = $request->input('product_type');
        $typeFields = [
            'brightbar' => ['straightening', 'point_discard'],
            'hb_wire' => ['phosphating'],
            'hhb_wire' => ['straightening', 'hardness', 'hardness_unit'],
            'annealed_bright_bar' => ['hardness', 'hardness_unit'],
            'bold_rods' => ['pcs_length_unit', 'coating'],
        ];
        $requiredFields = $typeFields[$productType] ?? [];

        $validated = $request->validate([
            'product_type' => ['required', 'string', Rule::in(array_keys($typeFields))],
            'customer_id' => ['required', 'integer', Rule::exists('customers', 'id')->whereNull('deleted_at')],
            'payment_term' => ['nullable', 'string'],
            'customer_po_no' => ['nullable', 'string'],
            'order_qty' => ['nullable', 'numeric', 'min:0'],
            'basic_rate' => ['nullable', 'numeric', 'min:0'],
            'dispatch_date' => ['nullable', 'string'],
            'remarks' => ['nullable', 'string'],
            'freight_basis' => ['required', 'integer', Rule::in([SaleOrder::FREIGHT_EX, SaleOrder::FREIGHT_FOR])],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['nullable', 'integer'],
            'items.*.product_id' => ['required', 'integer', Rule::exists('products', 'id')->whereNull('deleted_at')],
            'items.*.size_unit' => ['nullable', 'string', 'required_without:items.*.size_id'],
            'items.*.grade_id' => ['required', 'integer', Rule::exists('grades', 'id')->whereNull('deleted_at')],
            'items.*.brand_id' => ['nullable', 'integer', Rule::exists('brands', 'id')->whereNull('deleted_at')],
            'items.*.size_id' => ['nullable', 'integer', 'required_without:items.*.size_unit', Rule::exists('sizes', 'id')->whereNull('deleted_at')],
            'items.*.sale_item_prices' => ['required', 'numeric', 'min:0'],
            'items.*.size_extra' => ['nullable', 'numeric', 'min:0'],
            'items.*.item_qty' => ['required', 'numeric', 'min:0'],
            'items.*.qty_type' => ['required', 'integer', Rule::in([1, 2])],
            'items.*.dispatch_qty' => ['nullable', 'numeric', 'min:0'],
            'items.*.dispatched_qty' => ['nullable', 'numeric', 'min:0'],
            'items.*.pending_qty' => ['nullable', 'numeric', 'min:0'],
            'items.*.s_marked_completed' => ['nullable', 'integer', Rule::in([1, 2])],
            'items.*.item_remarks' => ['required', 'string'],
            'items.*.straightening' => [Rule::requiredIf(in_array('straightening', $requiredFields, true)), 'nullable', 'integer', Rule::in([1, 2])],
            'items.*.point_discard' => [Rule::requiredIf(in_array('point_discard', $requiredFields, true)), 'nullable', 'integer', Rule::in([1, 2])],
            'items.*.phosphating' => [Rule::requiredIf(in_array('phosphating', $requiredFields, true)), 'nullable', 'integer', Rule::in([1, 2])],
            'items.*.hardness' => [Rule::requiredIf(in_array('hardness', $requiredFields, true)), 'nullable', 'string'],
            'items.*.hardness_unit' => [Rule::requiredIf(in_array('hardness_unit', $requiredFields, true)), 'nullable', 'string', 'max:20'],
            'items.*.pcs_length_unit' => [Rule::requiredIf(in_array('pcs_length_unit', $requiredFields, true)), 'nullable', 'string', 'max:20'],
            'items.*.coating' => [Rule::requiredIf(in_array('coating', $requiredFields, true)), 'nullable', 'string', Rule::in(['autoblack', 'galvanized'])],
            'items.*.fs_bundle_weight' => ['required', 'numeric', 'min:0'],
            'items.*.fs_bundle_unit' => ['required', 'integer', Rule::in([1, 2])],
        ]);

        $productTypeIds = [
            'brightbar' => 1,
            'hb_wire' => 2,
            'hhb_wire' => 3,
            'annealed_bright_bar' => 4,
            'bold_rods' => 5,
        ];

        foreach ($validated['items'] as $index => $item) {
            $validated['items'][$index]['product_type'] = $productTypeIds[$validated['product_type']];
        }

        return $validated;
    }

    private function saleOrderData(array $validated): array
    {
        return [
            'customer_id' => $validated['customer_id'],
            'payment_term' => $validated['payment_term'] ?? null,
            'customer_po_no' => $validated['customer_po_no'] ?? null,
            'order_qty' => $validated['order_qty'] ?? null,
            'basic_rate' => $validated['basic_rate'] ?? null,
            'dispatch_date' => $validated['dispatch_date'] ?? null,
            'remarks' => $validated['remarks'] ?? null,
            'freight_basis' => $validated['freight_basis'],
            'created_by' => auth()->id(),
        ];
    }

    private function formData(): array
    {
        return [
            'customers' => Customer::query()->orderBy('company_name')->get(),
            'products' => Product::query()->with(['grade', 'size'])->orderBy('product_name')->get(),
            'grades' => Grade::query()->orderBy('grade_name')->get(),
            'brands' => Brand::query()->orderBy('brand_name')->get(),
            'sizes' => Size::query()->orderBy('size_name')->get(),
            'states' => State::query()->orderBy('name')->get(),
        ];
    }
}
