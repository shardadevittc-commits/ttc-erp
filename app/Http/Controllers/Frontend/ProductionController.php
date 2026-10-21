<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseOrderitemReceiving;
use App\Models\SaleOrderItem;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProductionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SaleOrderItem::query()
            ->with(['saleOrder.customer', 'product', 'grade', 'size'])
            ->whereHas('saleOrder');

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'created_on' => ['sometimes', 'array'],
            'created_on.0' => ['nullable', 'date_format:Y-m-d'],
            'created_on.1' => ['nullable', 'date_format:Y-m-d'],
            'status' => ['nullable', 'in:1,2'],
        ]);

        $search = trim($filters['search'] ?? '');
        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $query->where('sale_order_items.id', 'like', "%{$search}%")
                    ->orWhere('sale_order_items.sale_order_id', 'like', "%{$search}%")
                    ->orWhere('sale_order_items.item_remarks', 'like', "%{$search}%")
                    ->orWhereHas('saleOrder', function ($saleOrderQuery) use ($search) {
                        $saleOrderQuery->where('customer_po_no', 'like', "%{$search}%")
                            ->orWhereHas('customer', function ($customerQuery) use ($search) {
                                $customerQuery->where('company_name', 'like', "%{$search}%")
                                    ->orWhere('cust_code', 'like', "%{$search}%");
                            });
                    })
                    ->orWhereHas('product', function ($productQuery) use ($search) {
                        $productQuery->where('product_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('grade', function ($gradeQuery) use ($search) {
                        $gradeQuery->where('grade_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('size', function ($sizeQuery) use ($search) {
                        $sizeQuery->where('size_name', 'like', "%{$search}%");
                    });
            });
        }

        $createdOn = $filters['created_on'] ?? [];
        $startDate = $createdOn[0] ?? null;
        $endDate = $createdOn[1] ?? null;

        if ($startDate && $endDate && $endDate < $startDate) {
            throw ValidationException::withMessages([
                'created_on.1' => 'The end date must be on or after the start date.',
            ]);
        }

        if ($startDate) {
            $query->whereHas('saleOrder', function ($saleOrderQuery) use ($startDate) {
                $saleOrderQuery->whereDate('created_at', '>=', $startDate);
            });
        }

        if ($endDate) {
            $query->whereHas('saleOrder', function ($saleOrderQuery) use ($endDate) {
                $saleOrderQuery->whereDate('created_at', '<=', $endDate);
            });
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('s_marked_completed', (int) $filters['status']);
        }

        /* Sorting */
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');

        $allowedSortColumns = [
            'id',
            'created_at',
            'item_qty',
            'dispatch_qty',
            'dispatched_qty',
            'pending_qty',
            's_marked_completed',
        ];

        if (!in_array($sortBy, $allowedSortColumns, true)) {
            $sortBy = 'id';
        }

        $sortOrder = $sortOrder === 'asc' ? 'asc' : 'desc';

        /* Pagination */
        $perPage = (int) $request->get('per_page', 10);

        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $listing = $query
            ->orderBy('sale_order_items.' . $sortBy, $sortOrder)
            ->paginate($perPage)
            ->withQueryString();

        $this->addReceivingTotals($listing->getCollection());

        /* AJAX Request */
        if ($request->ajax()) {
            $html = view('productions.listingLoop', compact('listing'))->render();

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
        return view('productions.index', [
            'listing' => $listing,
        ]);
    }

    public function sendForWorkMaterial(Request $request, int $saleOrderItemId, $saleOrderId = null)
    {
        $saleOrderItem = SaleOrderItem::query()
            ->with('saleOrder')
            ->whereHas('saleOrder')
            ->findOrFail($saleOrderItemId);

        if ($saleOrderId !== null && (int) $saleOrderId !== (int) $saleOrderItem->sale_order_id) {
            abort(404);
        }

        $validated = $request->validate([
            'send_work_qty' => ['required', 'numeric', 'decimal:0,3', 'min:0'],
            'send_rm_qty' => ['required', 'numeric', 'decimal:0,2', 'min:0.01'],
            'remarks' => ['nullable', 'string'],
        ]);

        $workOrder = DB::transaction(function () use ($validated, $saleOrderItem, $request) {
            $purchaseOrderItems = $this->purchaseOrderItemsFor($saleOrderItem)
                ->where('grade_id', $saleOrderItem->grade_id)
                ->orderBy('id')
                ->get();

            if ($purchaseOrderItems->isEmpty()) {
                throw ValidationException::withMessages([
                    'send_rm_qty' => 'No purchase order items match this sale order item grade.',
                ]);
            }

            $receivings = PurchaseOrderitemReceiving::query()
                ->whereNull('deleted_at')
                ->whereIn('purchase_order_item_id', $purchaseOrderItems->modelKeys())
                ->where('grade_id', $saleOrderItem->grade_id)
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $receivedCents = $receivings->sum(
                fn ($receiving) => (int) round((float) $receiving->received_qty * 100)
            );
            $workQuantityThousandths = (int) round((float) $validated['send_work_qty'] * 1000);
            if ($workQuantityThousandths > $receivedCents * 10) {
                throw ValidationException::withMessages([
                    'send_work_qty' => 'The quantity for work cannot exceed the purchase received quantity.',
                ]);
            }

            $requestedCents = (int) round((float) $validated['send_rm_qty'] * 100);
            $availableCents = $receivings->sum(function ($receiving) {
                $receivedCents = (int) round((float) $receiving->received_qty * 100);
                $usedCents = (int) round((float) ($receiving->production_used_qty ?? 0) * 100);

                return max(0, $receivedCents - $usedCents);
            });

            if ($requestedCents > $availableCents) {
                throw ValidationException::withMessages([
                    'send_rm_qty' => 'The requested quantity exceeds the available received quantity for this purchase order item and grade.',
                ]);
            }

            $remainingCents = $requestedCents;
            foreach ($receivings as $receiving) {
                if ($remainingCents === 0) {
                    break;
                }

                $receivedCents = (int) round((float) $receiving->received_qty * 100);
                $usedCents = (int) round((float) ($receiving->production_used_qty ?? 0) * 100);
                $rowAvailableCents = max(0, $receivedCents - $usedCents);
                $allocatedCents = min($remainingCents, $rowAvailableCents);

                if ($allocatedCents > 0) {
                    $receiving->production_used_qty = ($usedCents + $allocatedCents) / 100;
                    $receiving->save();
                    $remainingCents -= $allocatedCents;
                }
            }

            return WorkOrder::create([
                'sale_order_id' => $saleOrderItem->sale_order_id,
                'sale_order_item_id' => $saleOrderItem->id,
                'purchase_order_id' => $purchaseOrderItems->first()->purchase_order_id,
                'po_item_id' => $purchaseOrderItems->first()->id,
                'grade_id' => $saleOrderItem->grade_id,
                'size_id' => $saleOrderItem->size_id,
                'material_send_for_work' => $validated['send_rm_qty'],
                'pending_raw_material' => ($availableCents - $requestedCents) / 100,
                'j_messages' => $validated['remarks'] ?? null,
                'status' => 'pending',
                'created_by' => $request->user()->id,
            ]);
        });

        return response()->json([
            'status' => true,
            'message' => 'Work order created successfully.',
            'work_order_id' => $workOrder->id,
            'available_qty' => $workOrder->pending_raw_material,
        ], 201);
    }

    private function addReceivingTotals($saleOrderItems): void
    {
        if ($saleOrderItems->isEmpty()) {
            return;
        }

        $totals = PurchaseOrderitemReceiving::query()
            ->join('purchase_order_items as poi', 'poi.id', '=', 'poitem_receiving.purchase_order_item_id')
            ->whereNull('poitem_receiving.deleted_at')
            ->whereIn('poi.ref_saleorder_id', $saleOrderItems->pluck('sale_order_id')->unique())
            ->whereIn('poi.grade_id', $saleOrderItems->pluck('grade_id')->filter()->unique())
            ->whereColumn('poi.grade_id', 'poitem_receiving.grade_id')
            ->select('poi.ref_saleorder_id', 'poi.grade_id')
            ->selectRaw('COUNT(CASE WHEN poitem_receiving.received_qty > 0 THEN 1 END) as received_item_count')
            ->selectRaw('COALESCE(SUM(poitem_receiving.received_qty), 0) as received_qty_total')
            ->selectRaw('COALESCE(SUM(CASE WHEN poitem_receiving.received_qty > COALESCE(poitem_receiving.production_used_qty, 0) THEN poitem_receiving.received_qty - COALESCE(poitem_receiving.production_used_qty, 0) ELSE 0 END), 0) as available_qty_total')
            ->groupBy('poi.ref_saleorder_id', 'poi.grade_id')
            ->get()
            ->keyBy(fn ($row) => $row->ref_saleorder_id . ':' . $row->grade_id);

        foreach ($saleOrderItems as $saleOrderItem) {
            $receipt = $totals->get($saleOrderItem->sale_order_id . ':' . $saleOrderItem->grade_id);
            $saleOrderItem->setAttribute('received_item_count', (int) ($receipt?->received_item_count ?? 0));
            $saleOrderItem->setAttribute('received_qty_total', (float) ($receipt?->received_qty_total ?? 0));
            $saleOrderItem->setAttribute('available_qty_total', (float) ($receipt?->available_qty_total ?? 0));
        }
    }

    private function purchaseOrderItemsFor(SaleOrderItem $saleOrderItem, $saleOrderId = null): Builder
    {
        if (!$saleOrderItem->sale_order_id) {
            return PurchaseOrderItem::query()->whereRaw('1 = 0');
        }

        return PurchaseOrderItem::query()
            ->where(function ($query) use ($saleOrderItem, $saleOrderId) {
                if ($saleOrderItem->saleOrder?->customer_id) {
                    $query->where('cust_id', $saleOrderItem->saleOrder->customer_id);
                }
                $query->where('ref_saleorder_id', $saleOrderId ?? $saleOrderItem->sale_order_id);
            });
    }
}
