<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseOrderitemReceiving;
use App\Models\SaleOrderItem;
use App\Models\WorkOrderProcess;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProductionController extends Controller
{
    private const PRODUCT_TYPE_OPERATIONS = [
        1 => 'operation6',
        2 => 'operation5',
        3 => 'operation2',
        4 => 'operation8',
        5 => 'operation9',
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Load production items with the related data needed by the listing.
        $query = SaleOrderItem::query()
            ->with(['saleOrder.customer', 'product', 'grade', 'size'])
            ->whereHas('saleOrder');

        // Validate listing inputs before adding them to the query.
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'created_on' => ['sometimes', 'array'],
            'created_on.0' => ['nullable', 'date_format:Y-m-d'],
            'created_on.1' => ['nullable', 'date_format:Y-m-d'],
            'status' => ['nullable', 'in:1,2'],
        ]);

        // Apply search across the sale item and its related order, customer, and product data.
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

        // Reject an invalid date range rather than silently returning an empty result.
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

        // Apply the requested completion filter.
        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('s_marked_completed', (int) $filters['status']);
        }

        /* Sorting */
        // Whitelist sortable columns to keep ordering predictable.
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
        // Limit pagination to supported page sizes.
        $perPage = (int) $request->get('per_page', 10);

        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $listing = $query
            ->orderBy('sale_order_items.' . $sortBy, $sortOrder)
            ->paginate($perPage)
            ->withQueryString();

        // Add receipt totals and first available lot metadata for the modal.
        $this->addReceivingTotals($listing->getCollection());

        /* AJAX Request */
        // Return refreshed table rows for AJAX-driven listing updates.
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
        // Render the full listing for normal page requests.
        return view('productions.index', [
            'listing' => $listing,
        ]);
    }

    public function sendForWorkMaterial(Request $request, int $saleOrderItemId, $saleOrderId = null)
    {
        // Resolve the requested item and verify the optional order ID.
        $saleOrderItem = SaleOrderItem::query()
            ->with('saleOrder')
            ->whereHas('saleOrder')
            ->findOrFail($saleOrderItemId);

        if ($saleOrderId !== null && (int) $saleOrderId !== (int) $saleOrderItem->sale_order_id) {
            abort(404);
        }

        // Validate quantities and remarks submitted by the modal.
        $validated = $request->validate([
            'send_work_qty' => ['required', 'numeric', 'decimal:0,3', 'min:0'],
            'send_rm_qty' => ['required', 'numeric', 'decimal:0,3', 'min:0.01'],
            'remarks' => ['nullable', 'string'],
            'submission_token' => ['required', 'uuid'],
        ]);

        // Lock and allocate receipt rows in the same transaction as work-order creation.
        $workOrder = DB::transaction(function () use ($validated, $saleOrderItem, $request) {
            // Serialize submissions for this sale item before checking the idempotency token.
            $lockedSaleOrderItem = SaleOrderItem::query()
                ->with('saleOrder')
                ->whereKey($saleOrderItem->id)
                ->lockForUpdate()
                ->firstOrFail();

            $existingWorkOrder = WorkOrderProcess::query()
                ->where('submission_token', $validated['submission_token'])
                ->first();

            if ($existingWorkOrder) {
                if ((int) $existingWorkOrder->sale_order_item_id !== (int) $lockedSaleOrderItem->id) {
                    throw ValidationException::withMessages([
                        'submission_token' => 'This submission token has already been used.',
                    ]);
                }

                return $existingWorkOrder;
            }

            // Find purchase items linked to this sale order and the item's grade.
            $purchaseOrderItems = $this->purchaseOrderItemsFor($lockedSaleOrderItem)
                ->where('grade_id', $lockedSaleOrderItem->grade_id)
                ->orderBy('id')
                ->get();

            if ($purchaseOrderItems->isEmpty()) {
                throw ValidationException::withMessages([
                    'send_rm_qty' => 'No purchase order items match this sale order item grade.',
                ]);
            }

            // Lock matching received lots to prevent concurrent submissions from overusing stock.
            $receivings = PurchaseOrderitemReceiving::query()
                ->whereNull('deleted_at')
                ->whereIn('purchase_order_item_id', $purchaseOrderItems->modelKeys())
                ->where('grade_id', $lockedSaleOrderItem->grade_id)
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            // Ensure the work quantity does not exceed the matching grade's received quantity.
            $receivedCents = $receivings->sum(
                fn ($receiving) => round((float) $receiving->received_qty * 100)
            );
            $workQuantityThousandths = round((float) $validated['send_work_qty'] * 1000);
            if ($workQuantityThousandths > $receivedCents * 10) {
                throw ValidationException::withMessages([
                    'send_work_qty' => 'The quantity for work cannot exceed the purchase received quantity.',
                ]);
            }

            // Calculate the remaining usable quantity from each received lot.
            $requestedCents = round((float) $validated['send_rm_qty'] * 100);
            $availableCents = $receivings->sum(function ($receiving) {
                $receivedCents = round((float) $receiving->received_qty * 100);
                $usedCents = round((float) ($receiving->production_used_qty ?? 0) * 100);

                return max(0, $receivedCents - $usedCents);
            });

            if ($requestedCents > $availableCents) {
                throw ValidationException::withMessages([
                    'send_rm_qty' => 'The requested quantity exceeds the available received quantity for this purchase order item and grade.',
                ]);
            }

            // Allocate raw material oldest-first and retain the first lot's heat/vehicle data.
            $remainingCents = $requestedCents;
            $firstAllocatedReceiving = null;
            foreach ($receivings as $receiving) {
                if ($remainingCents === 0) {
                    break;
                }

                $receivedCents = (int) round((float) $receiving->received_qty * 100);
                $usedCents = (int) round((float) ($receiving->production_used_qty ?? 0) * 100);
                $rowAvailableCents = max(0, $receivedCents - $usedCents);
                $allocatedCents = min($remainingCents, $rowAvailableCents);

                if ($allocatedCents > 0) {
                    $firstAllocatedReceiving ??= $receiving;
                    $receiving->production_used_qty = ($usedCents + $allocatedCents) / 100;
                    $receiving->save();
                    $remainingCents -= $allocatedCents;
                }
            }

            $allocatedPurchaseOrderItem = $purchaseOrderItems->firstWhere(
                'id',
                $firstAllocatedReceiving->purchase_order_item_id
            );

            // Create one work-order row; each later operation updates this same record.
            return WorkOrderProcess::create([
                'sale_order_id' => $lockedSaleOrderItem->sale_order_id,
                'sale_order_item_id' => $lockedSaleOrderItem->id,
                'purchase_order_id' => $allocatedPurchaseOrderItem->purchase_order_id,
                'po_item_id' => $allocatedPurchaseOrderItem->id,
                'grade_id' => $lockedSaleOrderItem->grade_id,
                'size_id' => $lockedSaleOrderItem->size_id,
                'vehicle_id' => $firstAllocatedReceiving?->vehical_id,
                'material_send_for_work' => $validated['send_rm_qty'],
                'pending_raw_material' => ($availableCents - $requestedCents) / 100,
                'heat_no' => $firstAllocatedReceiving?->genrated_heat_no,
                'j_messages' => $validated['remarks'] ?? null,
                'total_operation_perfome' => self::PRODUCT_TYPE_OPERATIONS[$lockedSaleOrderItem->product_type] ?? null,
                'status' => 'pending',
                'current_operation' => 1,
                'created_by' => $request->user()->id,
                'submission_token' => $validated['submission_token'],
                'fs_size' => $lockedSaleOrderItem->size?->size_name ?? $lockedSaleOrderItem->size_unit,
                'fs_size_unit' => $lockedSaleOrderItem->size_unit,
                'fs_bundle_weight' => $lockedSaleOrderItem->fs_bundle_weight,
                'fs_bundle_unit' => $this->bundleUnitLabel($lockedSaleOrderItem->fs_bundle_unit),
            ]);
        });

        $saleOrderItem->loadMissing('product');
        $workflowUrl = route('productions.workflow', ['workOrder' => $workOrder->id]);

        return response()->json([
            'status' => true,
            'message' => 'Work order created successfully.',
            'work_order_id' => $workOrder->id,
            'available_qty' => $workOrder->pending_raw_material,
            'heat_no' => $workOrder->heat_no,
            'vehicle_id' => $workOrder->vehicle_id,
            'total_operation_perfome' => $workOrder->total_operation_perfome,
            'workflow_url' => $workflowUrl,
        ], 201);
    }

    public function workflow(WorkOrderProcess $workOrder)
    {
        $workOrder->load([
            'saleOrderItem.product',
            'saleOrderItem.grade',
            'saleOrderItem.size',
            'saleOrderItem.saleOrder.customer',
        ]);
        abort_unless($workOrder->saleOrderItem, 404);

        return view('productions.workOrderProcess.brightBar', [
            'workOrder' => $workOrder,
            'saleOrderItem' => $workOrder->saleOrderItem,
            'currentOperation' => (int) ($workOrder->current_operation ?: 1),
        ]);
    }

    public function completeOperation(Request $request, WorkOrderProcess $workOrder, int $operation)
    {
        abort_unless($operation >= 1 && $operation <= 6, 404);
        $workOrder->loadMissing(['saleOrderItem.product', 'saleOrderItem.size']);
        abort_unless($workOrder->saleOrderItem, 404);

        $validated = match ($operation) {
            1 => $request->validate([
                'surface_process' => ['required', Rule::in(['shot_blasting', 'acid_pickling'])],
            ]),
            2 => $request->validate([
                'pointing' => ['required', 'integer', Rule::in([1, 2])],
            ]),
            3 => $request->validate([
                'fs_observed_size' => ['required', 'numeric', 'gt:0', 'decimal:0,3'],
                'fs_observed_unit' => ['nullable', 'string', 'max:20'],
            ]),
            4 => $request->validate([
                'poin_discard' => ['required', 'integer', Rule::in([1, 2])],
            ]),
            5 => $request->validate([
                'straightening' => ['required', 'integer', Rule::in([1, 2])],
            ]),
            6 => [],
        };

        DB::transaction(function () use ($workOrder, $operation, $validated) {
            // Serialize stage completion and reject attempts to skip a required operation.
            $lockedWorkOrder = WorkOrderProcess::query()
                ->whereKey($workOrder->id)
                ->lockForUpdate()
                ->firstOrFail();

            $currentOperation = (int) ($lockedWorkOrder->current_operation ?: 1);

            if ($currentOperation > $operation) {
                return;
            }

            if ($currentOperation < $operation) {
                throw ValidationException::withMessages([
                    'operation' => 'Complete the previous operation before continuing.',
                ]);
            }

            if ($operation === 1) {
                $lockedWorkOrder->shot_blasting = $validated['surface_process'] === 'shot_blasting' ? 1 : 2;
                $lockedWorkOrder->acid_pickling = $validated['surface_process'] === 'acid_pickling' ? 1 : 2;
            } elseif ($operation === 2) {
                $lockedWorkOrder->pointing = (int) $validated['pointing'];
            } elseif ($operation === 3) {
                $lockedWorkOrder->fs_observed_size = $validated['fs_observed_size'];
                $lockedWorkOrder->fs_observed_unit = $validated['fs_observed_unit'] ?? null;
            } elseif ($operation === 4) {
                $lockedWorkOrder->poin_discard = (int) $validated['poin_discard'];
            } elseif ($operation === 5) {
                $lockedWorkOrder->straightening = (int) $validated['straightening'];
            } else {
                // Calculate bundle totals in the sale-order unit without rounding up full bundles.
                $bundleWeight = (float) $lockedWorkOrder->fs_bundle_weight;
                if ($bundleWeight <= 0) {
                    throw ValidationException::withMessages([
                        'fs_bundle_weight' => 'The sale order bundle weight must be greater than zero.',
                    ]);
                }

                $unit = $this->bundleUnitLabel($lockedWorkOrder->fs_bundle_unit);
                if ($unit === null) {
                    throw ValidationException::withMessages([
                        'fs_bundle_weight' => 'The sale order bundle unit must be KG or Tons.',
                    ]);
                }
                $bundleWeightMilli = (int) round($bundleWeight * 1000);
                $materialMilli = (int) round((float) $lockedWorkOrder->material_send_for_work * 1000);

                if ($bundleWeightMilli <= 0 || $materialMilli <= 0) {
                    throw ValidationException::withMessages([
                        'fs_bundle_weight' => 'A positive material quantity and bundle weight are required.',
                    ]);
                }

                if ($unit === 'KG') {
                    $materialMilli *= 1000;
                }

                $fullBundles = intdiv($materialMilli, $bundleWeightMilli);
                $remainingWeight = ($materialMilli % $bundleWeightMilli) / 1000;

                $lockedWorkOrder->fs_bundle_unit = $unit;
                $lockedWorkOrder->total_full_bundle_count = $fullBundles;
                $lockedWorkOrder->lose_bundle_weight = $remainingWeight;
                $lockedWorkOrder->lose_bundle_count = $remainingWeight > 0 ? 1 : 0;
                $lockedWorkOrder->status = 'completed';
            }

            $lockedWorkOrder->current_operation = $operation + 1;
            $lockedWorkOrder->save();
        });

        return redirect()
            ->route('productions.workflow', ['workOrder' => $workOrder->id])
            ->with('success', "Operation {$operation} completed successfully.");
    }

    private function bundleUnitLabel($unit): ?string
    {
        return match (mb_strtolower((string) $unit)) {
            (string) SaleOrderItem::BUNDLE_KG, 'kg' => 'KG',
            (string) SaleOrderItem::BUNDLE_TONS, 'ton', 'tons' => 'Tons',
            default => null,
        };
    }

    private function addReceivingTotals($saleOrderItems): void
    {
        // No query is needed for an empty listing page.
        if ($saleOrderItems->isEmpty()) {
            return;
        }

        // Aggregate matching received rows by sale order and grade for the listing.
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
            ->selectRaw('MIN(CASE WHEN poitem_receiving.received_qty > COALESCE(poitem_receiving.production_used_qty, 0) THEN poitem_receiving.id END) as first_available_receiving_id')
            ->groupBy('poi.ref_saleorder_id', 'poi.grade_id')
            ->get()
            ->keyBy(fn ($row) => $row->ref_saleorder_id . ':' . $row->grade_id);

        // Load the first available receiving row to prefill its generated heat and vehicle.
        $firstReceivingIds = $totals->pluck('first_available_receiving_id')->filter()->unique();
        $firstReceivings = PurchaseOrderitemReceiving::query()
            ->whereIn('id', $firstReceivingIds)
            ->get(['id', 'genrated_heat_no', 'vehical_id'])
            ->keyBy('id');

        // Attach receipt totals and product operation values to each sale-order item.
        foreach ($saleOrderItems as $saleOrderItem) {
            $receipt = $totals->get($saleOrderItem->sale_order_id . ':' . $saleOrderItem->grade_id);
            $firstReceiving = $firstReceivings->get($receipt?->first_available_receiving_id);
            $saleOrderItem->setAttribute('received_item_count', (int) ($receipt?->received_item_count ?? 0));
            $saleOrderItem->setAttribute('received_qty_total', (float) ($receipt?->received_qty_total ?? 0));
            $saleOrderItem->setAttribute('available_qty_total', (float) ($receipt?->available_qty_total ?? 0));
            $saleOrderItem->setAttribute('rm_heat_no', $firstReceiving?->genrated_heat_no);
            $saleOrderItem->setAttribute('vehicle_id', $firstReceiving?->vehical_id);
            $saleOrderItem->setAttribute(
                'work_operation',
                self::PRODUCT_TYPE_OPERATIONS[$saleOrderItem->product_type] ?? null
            );
        }
    }

    private function purchaseOrderItemsFor(SaleOrderItem $saleOrderItem, $saleOrderId = null): Builder
    {
        // Return an empty query if the sale item has no persisted order relationship.
        if (!$saleOrderItem->sale_order_id) {
            return PurchaseOrderItem::query()->whereRaw('1 = 0');
        }

        // Restrict purchase-order items to the sale order/customer used by production.
        return PurchaseOrderItem::query()
            ->where(function ($query) use ($saleOrderItem, $saleOrderId) {
                if ($saleOrderItem->saleOrder?->customer_id) {
                    $query->where('cust_id', $saleOrderItem->saleOrder->customer_id);
                }
                $query->where('ref_saleorder_id', $saleOrderId ?? $saleOrderItem->sale_order_id);
            });
    }
}
