<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseOrderitemReceiving;
use App\Models\SaleOrderItem;
use App\Models\WorkOrderLoss;
use App\Models\WorkOrderProcess;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProductionController extends Controller
{
    private const PRODUCT_TYPE_OPERATIONS = [
        1 => 'operation6', /* Bright Bar */
        2 => 'operation5', /* HB Wire */
        3 => 'operation5', /* HHB Wire */
        4 => 'operation8', /* Annealing Bar */
        5 => 'operation9', /* Bolt Road */
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $productionHistory = $request->routeIs('production-history');

        if ($productionHistory) {
            $query = WorkOrderProcess::query()
                ->with([
                    'saleOrder.customer',
                    'saleOrderItem.saleOrder.customer',
                    'saleOrderItem.product',
                    'saleOrderItem.grade',
                    'saleOrderItem.size',
                ])
                ->where(function (Builder $statusQuery) {
                    $statusQuery->where('status', 'completed')
                        ->orWhere(function (Builder $activeQuery) {
                            $this->applyInProgressWorkOrderFilter($activeQuery);
                        });
                })
                ->whereHas('saleOrderItem.saleOrder');
        } else {
            // Load production items with the related data needed by the listing.
            $query = SaleOrderItem::query()
                ->with(['saleOrder.customer', 'product', 'grade', 'size'])
                ->whereHas('saleOrder')
                ->whereExists(function ($receivingQuery) {
                    $receivingQuery
                        ->selectRaw('1')
                        ->from('poitem_receiving as receiving')
                        ->join(
                            'purchase_order_items as purchase_items',
                            'purchase_items.id',
                            '=',
                            'receiving.purchase_order_item_id'
                        )
                        ->whereColumn('purchase_items.ref_saleorder_id', 'sale_order_items.sale_order_id')
                        ->whereColumn('purchase_items.grade_id', 'sale_order_items.grade_id')
                        ->whereNull('receiving.deleted_at')
                        ->where(function ($labEligibilityQuery) {
                            $labEligibilityQuery
                                ->where('receiving.lab_test', '2')
                                ->orWhere(function ($requiredLabQuery) {
                                    $requiredLabQuery
                                        ->where('receiving.lab_test', '1')
                                        ->whereExists(function ($labResultQuery) {
                                            $labResultQuery
                                                ->selectRaw('1')
                                                ->from('lab_results')
                                                ->whereColumn(
                                                    'lab_results.purchase_order_item_id',
                                                    'receiving.id'
                                                );
                                        });
                                });
                        });
                })
                ->whereNotExists(function ($completedProductionQuery) {
                    $completedProductionQuery
                        ->selectRaw('1')
                        ->from('work_orders_and_process as completed_production')
                        ->whereColumn(
                            'completed_production.sale_order_item_id',
                            'sale_order_items.id'
                        )
                        ->where('completed_production.status', 'completed')
                        ->whereNull('completed_production.delete_at');
                });
        }

        // Validate listing inputs before adding them to the query.
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'created_on' => ['sometimes', 'array'],
            'created_on.0' => ['nullable', 'date_format:Y-m-d'],
            'created_on.1' => ['nullable', 'date_format:Y-m-d'],
            'status' => ['nullable', Rule::in($productionHistory
                ? ['under_process', 'completed']
                : ['1', '2'])],
        ]);

        // Apply search across the sale item and its related order, customer, and product data.
        $search = trim($filters['search'] ?? '');
        if ($search !== '') {
            $query->where(function ($query) use ($search, $productionHistory) {
                $itemQuery = $productionHistory ? 'saleOrderItem' : null;
                $itemIdColumn = $productionHistory ? 'sale_order_item_id' : 'sale_order_items.id';
                $saleOrderIdColumn = $productionHistory ? 'sale_order_id' : 'sale_order_items.sale_order_id';
                $remarksColumn = $productionHistory ? 'j_messages' : 'sale_order_items.item_remarks';

                $query->where($itemIdColumn, 'like', "%{$search}%")
                    ->orWhere($saleOrderIdColumn, 'like', "%{$search}%")
                    ->orWhere($remarksColumn, 'like', "%{$search}%")
                    ->orWhereHas('saleOrder', function ($saleOrderQuery) use ($search) {
                        $saleOrderQuery->where('customer_po_no', 'like', "%{$search}%")
                            ->orWhereHas('customer', function ($customerQuery) use ($search) {
                                $customerQuery->where('company_name', 'like', "%{$search}%")
                                    ->orWhere('cust_code', 'like', "%{$search}%");
                            });
                    })
                    ->orWhereHas($itemQuery ? 'saleOrderItem.product' : 'product', function ($productQuery) use ($search) {
                        $productQuery->where('product_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas($itemQuery ? 'saleOrderItem.grade' : 'grade', function ($gradeQuery) use ($search) {
                        $gradeQuery->where('grade_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas($itemQuery ? 'saleOrderItem.size' : 'size', function ($sizeQuery) use ($search) {
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
            if ($productionHistory) {
                if ($filters['status'] === 'under_process') {
                    $this->applyInProgressWorkOrderFilter($query);
                } else {
                    $query->where('status', 'completed');
                }
            } else {
                $query->where('s_marked_completed', (int) $filters['status']);
            }
        }

        /* Sorting */
        // Whitelist sortable columns to keep ordering predictable.
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');

        $allowedSortColumns = $productionHistory
            ? ['id', 'created_at']
            : ['id', 'created_at', 'item_qty', 'dispatch_qty', 'dispatched_qty', 'pending_qty', 's_marked_completed'];

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
            ->orderBy(
                ($productionHistory ? 'work_orders_and_process.' : 'sale_order_items.') . $sortBy,
                $sortOrder
            )
            ->paginate($perPage)
            ->withQueryString();

        if (!$productionHistory) {
            // Add receipt totals and first available lot metadata for the modal.
            $this->addReceivingTotals($listing->getCollection());
        }

        /* AJAX Request */
        // Return refreshed table rows for AJAX-driven listing updates.
        if ($request->ajax()) {
            $html = view('productions.listingLoop', compact('listing', 'productionHistory'))->render();

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
        return view('productions.index', compact('listing', 'productionHistory'));
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
                ->with('saleOrder.items')
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
            $purchaseOrderItems = $this->purchaseOrderItemsFor($lockedSaleOrderItem, $lockedSaleOrderItem->saleorder->id)
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
                fn ($receiving) => ((float) $receiving->received_qty * 100)
            );
            $workQuantityThousandths = ((float) $validated['send_work_qty'] * 1000);
            if ($workQuantityThousandths > $receivedCents * 10) {
                throw ValidationException::withMessages([
                    'send_work_qty' => 'The quantity for work cannot exceed the purchase received quantity.',
                ]);
            }

            // Calculate the remaining usable quantity from each received lot.
            $requestedCents = ((float) $validated['send_rm_qty'] * 100);
            $availableCents = $receivings->sum(function ($receiving) {
                $receivedCents = ((float) $receiving->received_qty * 100);
                $usedCents = ((float) ($receiving->production_used_qty ?? 0) * 100);

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

                $receivedCents = (int) ((float) $receiving->received_qty * 100);
                $usedCents = (int) ((float) ($receiving->production_used_qty ?? 0) * 100);
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
                'status' => 'under_process',
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
            'saleOrderItem.losses',
        ]);
        abort_unless($workOrder->saleOrderItem, 404);

        if($workOrder->saleOrderItem->product_type === 1){
            $view = 'productions.workOrderProcess.brightBar';
        }elseif($workOrder->saleOrderItem->product_type === 2){
            $view = 'productions.workOrderProcess.hbWire';
        }elseif($workOrder->saleOrderItem->product_type === 3){
            $view = 'productions.workOrderProcess.hhbWire';
        }
        $totalNonRecScrap = $workOrder->saleOrderItem->losses
                ->where('scrap_type', 2)
                ->sum('loss_weight');
        $totalRecScrap = $workOrder->saleOrderItem->losses
                ->where('scrap_type', 1)
                ->sum('loss_weight');
        $adjScrap = $workOrder->saleOrderItem->losses
                ->sum('adjustable_material');
        return view($view, [
            'workOrder'         => $workOrder,
            'saleOrderItem'     => $workOrder->saleOrderItem,
            'currentOperation'  => (int) ($workOrder->current_operation ?: 1),
            'totalNonRecScrap'  => @$totalNonRecScrap ?? 0,
            'totalRecScrap'     => @$totalRecScrap ?? 0,
            'adjScrap'          => @$adjScrap ?? 0,
        ]);
    }

    public function completeOperation(Request $request, WorkOrderProcess $workOrder, int $operation)
    {
        $workOrder->loadMissing(['saleOrderItem.product', 'saleOrderItem.size']);
        abort_unless($workOrder->saleOrderItem, 404);
    
        // Product type ke hisaab se uska apna function chalega.
        match ((int) $workOrder->saleOrderItem->product_type) {
            1 => $this->brightBarProduction($request, $workOrder, $operation),
            2 => $this->hbWireProduction($request, $workOrder, $operation),
            3 => $this->hhbWireProduction($request, $workOrder, $operation),
            default => abort(404),
        };
    
        return redirect()
            ->route('productions.workflow', ['workOrder' => $workOrder->id])
            ->with('success', "Operation {$operation} completed successfully.");
    }

    /**
     * Bright Bar (product_type = 1): operations 1 to 6.
     * Validation, save, bundle calculation aur status sab yahin hai.
     */
    private function brightBarProduction(Request $request, WorkOrderProcess $workOrder, int $operation): void
    {
        $finalOperation = 6;
        abort_unless($operation >= 1 && $operation <= $finalOperation, 404);
    
        // Validation
        $validated = match ($operation) {
            1 => $request->validate([
                'surface_process' => ['required', Rule::in(['shot_blasting', 'acid_pickling'])],
            ]),
            2 => $request->validate([
                'pointing' => ['required', 'integer', Rule::in([1, 2])],
            ]),
            3 => $request->validate([
                'fs_observed_size' => ['required', 'string', 'max:20'],
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
    
        DB::transaction(function () use ($request, $workOrder, $operation, $validated, $finalOperation) {
            $lockedWorkOrder = $this->lockWorkOrderAtOperation($workOrder, $operation);
    
            // Operation pehle hi complete ho chuka hai (double submit / refresh).
            if ($lockedWorkOrder === null) {
                return;
            }
    
            // Save
            switch ($operation) {
                case 1:
                    $lockedWorkOrder->shot_blasting = $validated['surface_process'] === 'shot_blasting' ? 1 : 2;
                    $lockedWorkOrder->acid_pickling = $validated['surface_process'] === 'acid_pickling' ? 1 : 2;
                    break;
                case 2:
                    $lockedWorkOrder->pointing = (int) $validated['pointing'];
                    break;
                case 3:
                    $lockedWorkOrder->fs_observed_size = $validated['fs_observed_size'];
                    $lockedWorkOrder->fs_observed_unit = $validated['fs_observed_unit'] ?? null;
                    break;
                case 4:
                    $lockedWorkOrder->poin_discard = (int) $validated['poin_discard'];
                    break;
                case 5:
                    $lockedWorkOrder->straightening = (int) $validated['straightening'];
                    break;
                case 6:
                    $this->calculateBundles($lockedWorkOrder);
                    break;
            }
    
            calculateProductionScrap($request, $lockedWorkOrder);
    
            $lockedWorkOrder->status = $operation === $finalOperation ? 'completed' : 'under_process';
            $lockedWorkOrder->current_operation = $operation + 1;
            $lockedWorkOrder->save();
        });
    }
    
    /**
     * HB Wire (product_type = 2): operations 1 to 2.
     */
    private function hbWireProduction(Request $request, WorkOrderProcess $workOrder, int $operation): void
    {
        $finalOperation = 2;
        abort_unless($operation >= 1 && $operation <= $finalOperation, 404);
    
        // Validation
        $validated = match ($operation) {
            1 => $request->validate([
                'acid_pickling' => ['nullable', Rule::in(['1'])],
                'phosphating' => ['nullable', Rule::in(['1'])],
                'acid_remarks' => ['nullable', 'string', 'max:255'],
            ]),
            2 => $request->validate([
                'pcsfsbndle' => ['required', 'integer', 'min:0'],
                'misc' => ['nullable'],
                'fs_size' => 'required',
                'fs_size_unit' => ['required', Rule::in(['MM', 'THOU'])],
                'fs_observed_size' => 'required',
                'fs_observed_unit' => ['required', Rule::in(['MM', 'THOU'])],
                
                'rm_observed_size' => 'required',
                'rm_observed_unit' => ['required', Rule::in(['MM', 'THOU'])],
                'rm_size' => 'required',
                'rm_size_unit' => ['required', Rule::in(['MM', 'THOU'])],
            ]),
        };
    
        // Acid pickling aur phosphating mein se exactly ek select hona chahiye.
        if ($operation === 1 && !isset($validated['acid_pickling']) && !isset($validated['phosphating'])) 
        {
            throw ValidationException::withMessages([
                'acid_pickling' => 'Select acid pickling or phosphating before completing this operation.',
            ]);
        }

        DB::transaction(function () use ($request, $workOrder, $operation, $validated, $finalOperation) {
            $lockedWorkOrder = $this->lockWorkOrderAtOperation($workOrder, $operation);
    
            if ($lockedWorkOrder === null) {
                return;
            }
    
            // Save
            switch ($operation) {
                case 1:
                    // 1 = acid pickling, 2 = phosphating
                    case 1:
                        // Checkbox select ho to 1, warna 2
                        $lockedWorkOrder->acid_pickling = isset($validated['acid_pickling']) ? 1 : 2;
                        $lockedWorkOrder->phosphating   = isset($validated['phosphating']) ? 1 : 2;
                    break;
                case 2:
                        $lockedWorkOrder->fs_observed_size  = $validated['fs_observed_size'];
                        $lockedWorkOrder->fs_observed_unit  = $validated['fs_observed_unit'];
                        $lockedWorkOrder->fs_size           = $validated['fs_size'];
                        $lockedWorkOrder->fs_size_unit      = $validated['fs_size_unit'];

                        $lockedWorkOrder->rm_observed_size  = $validated['rm_observed_size'];
                        $lockedWorkOrder->rm_observed_unit  = $validated['rm_observed_unit'];
                        $lockedWorkOrder->rm_size           = $validated['rm_size'];
                        $lockedWorkOrder->rm_size_unit      = $validated['rm_size_unit'];

                        $bundleUnit = $this->bundleUnitLabel($lockedWorkOrder->fs_bundle_unit);
                        $bundleWeight = (float) $lockedWorkOrder->fs_bundle_weight;
                        if ($bundleWeight <= 0 || $bundleUnit === null) {
                            throw ValidationException::withMessages([
                                'fs_bundle_weight' => 'A positive bundle weight and a valid bundle unit are required.',
                            ]);
                        }

                        $bundleWeightKg = $this->truncateToThreeDecimals($bundleWeight * ($bundleUnit === 'KG' ? 1 : 1000));
                        $initialWeightKg = $this->truncateToThreeDecimals((float) $lockedWorkOrder->material_send_for_work * 1000);
                        
                        $nonRecoverableScrapKg = $this->truncateToThreeDecimals(
                            (float) WorkOrderLoss::query()
                                ->where('work_order_id', $lockedWorkOrder->id)
                                // ->where('scrap_type', 2)
                                ->sum('loss_weight')
                        );
                        $availableWeightKg = $this->truncateToThreeDecimals($initialWeightKg - $nonRecoverableScrapKg);

                        if ($bundleWeightKg <= 0 || $availableWeightKg <= 0) {
                            throw ValidationException::withMessages([
                                'fs_bundle_weight' => 'A positive quantity of raw material must be available for bundling.',
                            ]);
                        }

                        $maxFullBundleCount = (int) floor(($availableWeightKg + 0.000000001) / $bundleWeightKg);
                        $initialLooseWeightKg = $this->truncateToThreeDecimals($availableWeightKg - ($maxFullBundleCount * $bundleWeightKg));
                        $requestedFullBundleCount = (int) $validated['pcsfsbndle'];
                        $miscWeightKg = $this->truncateToThreeDecimals((float) ($validated['misc'] ?? 0));
                        $fullBundleCount = min($requestedFullBundleCount, $maxFullBundleCount);

                        if ($requestedFullBundleCount < $maxFullBundleCount) {
                            $selectedBundlesWeightKg = $this->truncateToThreeDecimals($fullBundleCount * $bundleWeightKg);
                            if ($miscWeightKg > $selectedBundlesWeightKg) {
                                throw ValidationException::withMessages([
                                    'misc' => 'Miscellaneous scrap cannot exceed the selected bundle weight.',
                                ]);
                            }

                            $finishedWeightKg = $this->truncateToThreeDecimals($selectedBundlesWeightKg - $miscWeightKg);
                            $looseWeightKg = 0;
                        } else {
                            if ($miscWeightKg > $initialLooseWeightKg) {
                                throw ValidationException::withMessages([
                                    'misc' => 'Miscellaneous scrap cannot exceed the available loose material.',
                                ]);
                            }

                            $looseWeightKg = $this->truncateToThreeDecimals($initialLooseWeightKg - $miscWeightKg);
                            $finishedWeightKg = $this->truncateToThreeDecimals(($fullBundleCount * $bundleWeightKg) + $looseWeightKg);
                        }

                        $miscScrapKg = $this->truncateToThreeDecimals($availableWeightKg - $finishedWeightKg);

                        $lockedWorkOrder->fs_bundle_unit = $bundleUnit;
                        $lockedWorkOrder->total_full_bundle_count = $fullBundleCount;
                        $lockedWorkOrder->lose_bundle_count = $looseWeightKg > 0 ? 1 : 0;
                        $lockedWorkOrder->lose_bundle_weight = $bundleUnit === 'KG'
                            ? $looseWeightKg
                            : $this->truncateToThreeDecimals($looseWeightKg / 1000);

                        if ($miscScrapKg > 0) {
                            $request->merge([
                                'processtype' => 'misc',
                                'workStatus' => 'operation2',
                                'product_type' => 'hbwire',
                                'misc' => $miscScrapKg,
                                'finish_qty' => $finishedWeightKg,
                            ]);
                            calculateProductionScrap($request, $lockedWorkOrder);
                        }
                    break;
            }

            if ($operation === 1) {
                calculateProductionScrap($request, $lockedWorkOrder);
            }

            $lockedWorkOrder->status = $operation === $finalOperation ? 'completed' : 'under_process';
            $lockedWorkOrder->current_operation = $operation + 1;
            $lockedWorkOrder->save();
        });
    }

    /**
     * HHB Wire (product_type = 3): operations 1 to 5.
     *  1 Acid Pickling | Phosphating
     *  2 Wire Drawing
     *  3 Heat Treatment
     *  4 Acid Pickling | Phosphating (2nd)
     *  5 Wire Drawing (Final Pass)
     */
    private function hhbWireProduction(Request $request, WorkOrderProcess $workOrder, int $operation): void
    {
        $finalOperation = 5;
        abort_unless($operation >= 1 && $operation <= $finalOperation, 404);
    
        $sizeUnits = Rule::in(['MM', 'THOU']);
        $hardnessUnits = Rule::in(['BHN', 'HRC']);
    
        // Validation (readonly fields jaise SO size/bundle weight request se nahi liye jaate)
        $validated = match ($operation) {
            1 => $request->validate([
                'acid_pickling' => ['nullable', Rule::in(['1'])],
                'phosphating' => ['nullable', Rule::in(['1'])],
                'acid_remarks' => ['nullable', 'string', 'max:255'],
            ]),
            2 => $request->validate([
                'final_bundle_count' => ['required', 'integer', 'min:1'],

                'fs_size' => 'required',
                'fs_size_unit' => ['required', $sizeUnits],
                'fs_observed_size' => 'required',
                'fs_observed_unit' => ['required', $sizeUnits],
                
                'rm_observed_size' => 'required',
                'rm_observed_unit' => ['required', $sizeUnits],
                'rm_size' => 'required',
                'rm_size_unit' => ['required', $sizeUnits],
            ]),
            3 => $request->validate([
                'batch_no' => ['required', 'string', 'max:50'],
                'hardness' => 'nullable',
                'hardness_unit' => ['nullable', 'required_with:hardness', $hardnessUnits],
                'hardness_observed' => 'required',
                'hardness_observed_unit' => ['required', $hardnessUnits],
            ]),
            4 => $request->validate([
                'second_acid_pickling' => ['nullable', Rule::in(['1'])],
                'second_phosphating' => ['nullable', Rule::in(['1'])],
                // 'batch_no_old' => ['nullable', 'string', 'max:50'],
                'second_acid_remarks' => ['nullable', 'string', 'max:255'],
            ]),
            5 => $request->validate([
                'misc_bundle_weight' => ['nullable', 'numeric', 'min:0', 'decimal:0,3'],
                'manage_misc_scrap' => ['nullable', 'numeric', 'min:0', 'decimal:0,3'],
                /* 'first_draw_observed_size' => ['required', 'numeric', 'gt:0', 'decimal:0,3'],
                'first_draw_observed_unit' => ['required', $sizeUnits], */
                'final_observed_size' => 'required',
                'final_observed_unit' => ['required', $sizeUnits],
            ]),
        };
    
        // Operation 1 aur 4: acid pickling aur phosphating mein se exactly ek select hona chahiye.
        /* if (in_array($operation, [1, 4], true) && !isset($validated['acid_pickling']) && !isset($validated['phosphating']) || !isset($validated['second_acid_pickling']) && !isset($validated['second_phosphating'])) {
            throw ValidationException::withMessages([
                'acid_pickling' => 'Select either Acid Pickling or Phosphating before completing this operation.',
            ]);
        } */

        /* if ( in_array($operation, [1, 4], true) && (
                (!isset($validated['acid_pickling']) && !isset($validated['phosphating'])) ||
                (!isset($validated['second_acid_pickling']) && !isset($validated['second_phosphating']))
            )) {
            throw ValidationException::withMessages([
                'acid_pickling' => 'Select either acid pickling or phosphating before completing this operation.',
            ]);
        } */
    
        DB::transaction(function () use ($request, $workOrder, $operation, $validated, $finalOperation) {
            $lockedWorkOrder = $this->lockWorkOrderAtOperation($workOrder, $operation);
    
            // Operation pehle hi complete ho chuka hai (double submit / refresh).
            if ($lockedWorkOrder === null) {
                return;
            }
    
            // Save (checkbox: selected = 1, otherwise 2)
            switch ($operation) {
                case 1:
                    $lockedWorkOrder->acid_pickling = isset($validated['acid_pickling']) ? 1 : 2;
                    $lockedWorkOrder->phosphating = isset($validated['phosphating']) ? 1 : 2;
                    $lockedWorkOrder->acid_remarks = $validated['acid_remarks'] ?? null;
                    break;
    
                case 2:
                    $lockedWorkOrder->fs_observed_size  = $validated['fs_observed_size'];
                    $lockedWorkOrder->fs_observed_unit  = $validated['fs_observed_unit'];
                    $lockedWorkOrder->fs_size           = $validated['fs_size'];
                    $lockedWorkOrder->fs_size_unit      = $validated['fs_size_unit'];

                    $lockedWorkOrder->rm_observed_size  = $validated['rm_observed_size'];
                    $lockedWorkOrder->rm_observed_unit  = $validated['rm_observed_unit'];
                    $lockedWorkOrder->rm_size           = $validated['rm_size'];
                    $lockedWorkOrder->rm_size_unit      = $validated['rm_size_unit'];

                    $bundleUnit = $this->bundleUnitLabel($lockedWorkOrder->fs_bundle_unit);
                    $bundleWeight = (float) $lockedWorkOrder->fs_bundle_weight;
                    if ($bundleWeight <= 0 || $bundleUnit === null) {
                        throw ValidationException::withMessages([
                            'fs_bundle_weight' => 'A positive bundle weight and a valid bundle unit are required.',
                        ]);
                    }
                    $bundleWeightKg = $this->truncateToThreeDecimals($bundleWeight * ($bundleUnit === 'KG' ? 1 : 1000));
                    $initialWeightKg = $this->truncateToThreeDecimals((float) $lockedWorkOrder->material_send_for_work * 1000);
                    $previousScrapKg = $this->truncateToThreeDecimals(
                        (float) WorkOrderLoss::query()
                            ->where('work_order_id', $lockedWorkOrder->id)
                            ->sum('loss_weight')
                    );
                    $availableWeightKg = $this->truncateToThreeDecimals($initialWeightKg - $previousScrapKg);

                    if ($bundleWeightKg <= 0 || $availableWeightKg <= 0) {
                        throw ValidationException::withMessages([
                            'fs_bundle_weight' => 'A positive quantity of raw material must be available for bundling.',
                        ]);
                    }

                    $maxFullBundleCount = (int) floor(($availableWeightKg + 0.000000001) / $bundleWeightKg);
                    $requestedFullBundleCount = (int) $validated['final_bundle_count'];
                    $requestedMiscKg = max(0, $this->truncateToThreeDecimals((float) ($request->input('misc') ?? 0)));
                    $outputWeightKg = $requestedFullBundleCount < $maxFullBundleCount
                        ? min($requestedFullBundleCount * $bundleWeightKg, $availableWeightKg)
                        : $availableWeightKg;
                    $finishedWeightKg = max(0, $this->truncateToThreeDecimals($outputWeightKg - $requestedMiscKg));
                    $fullBundleCount = (int) floor(($finishedWeightKg + 0.000000001) / $bundleWeightKg);
                    $looseWeightKg = $this->truncateToThreeDecimals($finishedWeightKg - ($fullBundleCount * $bundleWeightKg));
                    $miscScrapKg = $this->truncateToThreeDecimals($availableWeightKg - $finishedWeightKg);

                    $lockedWorkOrder->fs_bundle_unit = $bundleUnit;
                    $lockedWorkOrder->total_full_bundle_count = $fullBundleCount;
                    $lockedWorkOrder->lose_bundle_count = $looseWeightKg > 0 ? 1 : 0;
                    $lockedWorkOrder->lose_bundle_weight = $bundleUnit === 'KG'
                        ? $looseWeightKg
                        : $this->truncateToThreeDecimals($looseWeightKg / 1000);

                    if ($miscScrapKg > 0) {
                        $request->merge([
                            'processtype' => 'misc',
                            'workStatus' => 'operation2',
                            'product_type' => 'hbwire',
                            'misc' => $miscScrapKg,
                            'finish_qty' => $finishedWeightKg,
                        ]);
                        calculateProductionScrap($request, $lockedWorkOrder);
                    }
                    break;
    
                case 3:
                    $lockedWorkOrder->batch_no = $validated['batch_no'];
                    $lockedWorkOrder->hardness = $validated['hardness'] ?? $saleOrderItem->hardness;
                    $lockedWorkOrder->hardness_unit = $validated['hardness_unit'] ?? $saleOrderItem->hardness_unit;
                    $lockedWorkOrder->hardness_observed = $validated['hardness_observed'];
                    $lockedWorkOrder->hardness_observed_unit = $validated['hardness_observed_unit'];
                    break;
    
                case 4:
                    $lockedWorkOrder->second_acid_pickling = isset($validated['second_acid_pickling']) ? 1 : 2;
                    $lockedWorkOrder->second_phosphating = isset($validated['second_phosphating']) ? 1 : 2;
                    // $lockedWorkOrder->second_batch_no = $validated['batch_no_old'] ?? null;
                    $lockedWorkOrder->second_acid_remarks = $validated['second_acid_remarks'] ?? null;
                    break;
    
                case 5:
                    $lockedWorkOrder->final_fs_observed_size = $validated['final_observed_size'];
                    $lockedWorkOrder->final_fs_observed_unit = $validated['final_observed_unit'];
                    $this->calculateBundles($lockedWorkOrder);
                    
                    $bundleUnit          = $this->bundleUnitLabel($lockedWorkOrder->fs_bundle_unit);
                    $bundleWeight        = (float) $lockedWorkOrder->fs_bundle_weight;
                    $bundleWeightKg      = $this->truncateToThreeDecimals($bundleWeight * ($bundleUnit === 'KG' ? 1 : 1000));
                    
                    $bundleTotalWeightKg = $this->truncateToThreeDecimals(
                        ((int) $lockedWorkOrder->total_full_bundle_count * $bundleWeightKg)
                        + ((float) $lockedWorkOrder->lose_bundle_weight * ($bundleUnit === 'KG' ? 1 : 1000))
                    );
                        
                    $scrapAdjustmentKg = 0.0;
                    if (isset($validated['manage_misc_scrap'])) {
                        $recoverableLosses = WorkOrderLoss::query()
                            ->where('sale_order_item_id', $lockedWorkOrder->sale_order_item_id)
                            ->where('scrap_type', 1)
                            ->orderBy('id')
                            ->lockForUpdate()
                            ->get();
                        $recordedScrapKg = $this->truncateToThreeDecimals($recoverableLosses->sum(
                            fn (WorkOrderLoss $loss) => max(
                                0,(float) $loss->loss_weight - (float) ($loss->adjustable_material ?? 0)
                            )
                        ));
                        
                        $requestedScrapKg = $this->truncateToThreeDecimals((float) $validated['manage_misc_scrap']);
                        $remainingAdjustmentKg = max(0, $this->truncateToThreeDecimals($recordedScrapKg - $requestedScrapKg));

                        foreach ($recoverableLosses as $recoverableLoss) {
                            if ($remainingAdjustmentKg <= 0) {
                                break;
                            }

                            $previousAdjustmentKg = $this->truncateToThreeDecimals((float) ($recoverableLoss->adjustable_material ?? 0));
                            $adjustableLossKg = max(0, $this->truncateToThreeDecimals((float) $recoverableLoss->loss_weight - $previousAdjustmentKg));
                            $adjustmentKg = min($remainingAdjustmentKg, $adjustableLossKg);

                            if ($adjustmentKg <= 0) {
                                continue;
                            }

                            $recoverableLoss->adjustable_material = $this->truncateToThreeDecimals($previousAdjustmentKg + $adjustmentKg);
                            $recoverableLoss->weight_after_adjustable_material = $this->truncateToThreeDecimals(
                                (float) ($recoverableLoss->weight_after_adjustable_material ?? $recoverableLoss->weight_after_loss ?? 0) + $adjustmentKg
                            );
                            $recoverableLoss->save();

                            $scrapAdjustmentKg = $this->truncateToThreeDecimals($scrapAdjustmentKg + $adjustmentKg);
                            $remainingAdjustmentKg = $this->truncateToThreeDecimals($remainingAdjustmentKg - $adjustmentKg);
                        }
                    }

                    pr($bundleTotalWeightKg); 
                    pr($request->toArray()); die;

                    $miscBundleWeightKg = max(0, $this->truncateToThreeDecimals((float) ($validated['misc_bundle_weight'] ?? 0)));
                    $updatedBundleWeightKg = max(
                        0,
                        $this->truncateToThreeDecimals($bundleTotalWeightKg + $scrapAdjustmentKg - $miscBundleWeightKg)
                    );
                    $fullBundleCount = (int) floor(($updatedBundleWeightKg + 0.000000001) / $bundleWeightKg);
                    $looseBundleWeightKg = $this->truncateToThreeDecimals(
                        $updatedBundleWeightKg - ($fullBundleCount * $bundleWeightKg)
                    );

                    $lockedWorkOrder->total_full_bundle_count = $fullBundleCount;
                    $lockedWorkOrder->lose_bundle_count = $looseBundleWeightKg > 0 ? 1 : 0;
                    $lockedWorkOrder->lose_bundle_weight = $bundleUnit === 'KG'
                        ? $looseBundleWeightKg
                        : $this->truncateToThreeDecimals($looseBundleWeightKg / 1000);

                    if ($miscBundleWeightKg > 0) {
                        $request->merge([
                            'processtype' => 'misc',
                            'workStatus' => 'operation5',
                            'product_type' => 'hhbwire',
                            'misc' => $miscBundleWeightKg,
                        ]);
                    }
                    break;
            }
    
            calculateProductionScrap($request, $lockedWorkOrder);
    
            $lockedWorkOrder->status = $operation === $finalOperation ? 'completed' : 'under_process';
            $lockedWorkOrder->current_operation = $operation + 1;
            $lockedWorkOrder->save();
        });
    }
    
    /**
     * Transaction ke andar work order lock karta hai.
     * - Operation pehle ho chuka ho -> null return karta hai.
     * - Pichla operation baaki ho -> ValidationException.
     */
    private function lockWorkOrderAtOperation(WorkOrderProcess $workOrder, int $operation): ?WorkOrderProcess
    {
        $lockedWorkOrder = WorkOrderProcess::query()
            ->whereKey($workOrder->id)
            ->lockForUpdate()
            ->firstOrFail();
    
        // Loaded relations calculateProductionScrap() jaise helpers ke liye available rahein.
        $lockedWorkOrder->setRelations($workOrder->getRelations());
    
        $currentOperation = (int) ($lockedWorkOrder->current_operation ?: 1);
    
        if ($currentOperation > $operation) {
            return null;
        }
    
        if ($currentOperation < $operation) {
            throw ValidationException::withMessages([
                'operation' => 'Complete the previous operation before continuing.',
            ]);
        }
    
        return $lockedWorkOrder;
    }
    
    /**
     * Full aur loose bundles sale-order unit mein calculate karta hai (full bundle round-up nahi hota).
     */
    private function calculateBundles(WorkOrderProcess $workOrder): void
    {
        $bundleWeight = (float) $workOrder->fs_bundle_weight;
        if ($bundleWeight <= 0) {
            throw ValidationException::withMessages([
                'fs_bundle_weight' => 'The sale order bundle weight must be greater than zero.',
            ]);
        }
    
        $unit = $this->bundleUnitLabel($workOrder->fs_bundle_unit);
        if ($unit === null) {
            throw ValidationException::withMessages([
                'fs_bundle_weight' => 'The sale order bundle unit must be KG or Tons.',
            ]);
        }
    
        $bundleWeightKg = $this->truncateToThreeDecimals($bundleWeight * ($unit === 'KG' ? 1 : 1000));
        $materialWeightKg = $this->truncateToThreeDecimals((float) $workOrder->material_send_for_work * 1000);
    
        if ($bundleWeightKg <= 0 || $materialWeightKg <= 0) {
            throw ValidationException::withMessages([
                'fs_bundle_weight' => 'A positive material quantity and bundle weight are required.',
            ]);
        }
    
        $fullBundleCount = (int) floor(($materialWeightKg + 0.000000001) / $bundleWeightKg);
        $remainingWeightKg = $this->truncateToThreeDecimals($materialWeightKg - ($fullBundleCount * $bundleWeightKg));
    
        $workOrder->fs_bundle_unit = $unit;
        $workOrder->total_full_bundle_count = $fullBundleCount;
        $workOrder->lose_bundle_weight = $unit === 'KG'
            ? $remainingWeightKg
            : $this->truncateToThreeDecimals($remainingWeightKg / 1000);
        $workOrder->lose_bundle_count = $remainingWeightKg > 0 ? 1 : 0;
    }

    private function truncateToThreeDecimals(float $value): float
    {
        $number = strtolower((string) $value);
        $negative = str_starts_with($number, '-');
        $number = ltrim($number, '+-');
        [$significand, $exponent] = array_pad(explode('e', $number, 2), 2, '0');
        [$integer, $fraction] = array_pad(explode('.', $significand, 2), 2, '');

        $digits = $integer . $fraction;
        $decimalPosition = strlen($integer) + (int) $exponent;

        if ($decimalPosition <= 0) {
            $integer = '0';
            $fraction = str_repeat('0', -$decimalPosition) . $digits;
        } elseif ($decimalPosition >= strlen($digits)) {
            $integer = $digits . str_repeat('0', $decimalPosition - strlen($digits));
            $fraction = '';
        } else {
            $integer = substr($digits, 0, $decimalPosition);
            $fraction = substr($digits, $decimalPosition);
        }

        $truncated = $integer . ($fraction === '' ? '' : '.' . substr($fraction, 0, 3));

        return (float) ($negative ? '-' . $truncated : $truncated);
    }

    private function bundleUnitLabel($unit): ?string
    {
        return match (mb_strtolower((string) $unit)) {
            (string) SaleOrderItem::BUNDLE_KG, 'kg' => 'KG',
            (string) SaleOrderItem::BUNDLE_TONS, 'ton', 'tons' => 'Tons',
            default => null,
        };
    }

    private function applyInProgressWorkOrderFilter(Builder $query): void
    {
        $query->where(function (Builder $activeQuery) {
            $activeQuery->whereBetween('current_operation', [1, 6])
                ->where(function (Builder $activeStatusQuery) {
                    $activeStatusQuery
                        ->whereNull('status')
                        ->orWhere('status', '!=', 'completed');
                });
        });
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
        return PurchaseOrderItem::query()
            ->where(function ($query) use ($saleOrderItem, $saleOrderId) {
                if ($saleOrderItem->sale_order_id) {
                    $query->where('ref_saleorder_id', $saleOrderId ?? $saleOrderItem->sale_order_id);
                }
            });
    }
}
