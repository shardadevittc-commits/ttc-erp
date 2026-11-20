<?php

namespace App\Http\Controllers;

use App\Models\DispatchMaterial;
use App\Models\GateAndGaadi;
use App\Models\SaleOrder;
use App\Models\SaleOrderItem;
use App\Models\Weight;
use App\Models\WorkOrderProcess;
use App\Models\WorkOrderLoss;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DispatchController extends Controller
{
    private const ITEM_COLUMNS = [
        'sale_order_items.id',
        'sale_order_items.sale_order_id',
        'sale_order_items.product_id',
        'sale_order_items.grade_id',
        'sale_order_items.brand_id',
        'sale_order_items.size_id',
        'sale_order_items.size_unit',
        'sale_order_items.sale_item_prices',
        'sale_order_items.size_extra',
        'sale_order_items.item_qty',
        'sale_order_items.qty_type',
        'sale_order_items.dispatch_qty',
        'sale_order_items.dispatched_qty',
        'sale_order_items.pending_qty',
        'sale_order_items.s_marked_completed',
        'sale_order_items.item_remarks',
        'sale_order_items.straightening',
        'sale_order_items.point_discard',
        'sale_order_items.phosphating',
        'sale_order_items.hardness',
        'sale_order_items.hardness_unit',
        'sale_order_items.pcs_length_unit',
        'sale_order_items.coating',
        'sale_order_items.product_type',
        'sale_order_items.fs_bundle_weight',
        'sale_order_items.fs_bundle_unit',
    ];

    public function index()
    {
        $this->ensureDepartment('dispatch');

        $gate = GateAndGaadi::query()
            ->select([
                'id',
                'cust_id',
                'formlist',
                'truckno',
                'vehicleentryslip',
                'transportname',
                'noofpersons',
                'drivername',
                'contactno',
                'created_at',
            ])
            ->with('customer:id,company_name')
            ->where('formlist', 'Material Out')
            ->latest('id')
            ->paginate(20);

        $dispatchStatusesByGate = DB::table('dispatch_material')
            ->select(['gate_id', 'status'])
            ->whereIn('gate_id', $gate->getCollection()->pluck('id'))
            ->get()
            ->groupBy('gate_id');
        $pencilActionByGate = $dispatchStatusesByGate->map(fn (Collection $statuses) => match (true) {
            $statuses->contains('dispatched') => 'dispatched',
            $statuses->contains('weight_completed') => 'weight_completed',
            default => null,
        });

        return view('dispatch.index', compact('gate', 'pencilActionByGate', 'dispatchStatusesByGate'));
    }

    public function pendingOrders(GateAndGaadi $gate): JsonResponse
    {
        $this->ensureDepartment('dispatch');
        abort_unless($gate->formlist === 'Material Out', 404);

        $orders = $this->loadDispatchOrders($gate->cust_id, null, $gate->id);
        $itemIds = $orders->flatMap(fn (SaleOrder $order) => $order->items->pluck('id'))->all();
        $dispatchStatuses = DispatchMaterial::query()
            ->where('gate_id', $gate->id)
            ->whereIn('sale_order_item_id', $itemIds)
            ->pluck('status', 'sale_order_item_id');

        return response()->json([
            'success' => true,
            'orders' => $orders->map(fn (SaleOrder $order) => [
                'id' => $order->id,
                'basic_rate' => $order->basic_rate,
                'customer_po_no' => $order->customer_po_no,
                'items' => $order->items->map(fn (SaleOrderItem $item) => [
                    'id' => $item->id,
                    'product_name' => $item->product?->product_name,
                    'product_category' => $item->product?->product_category,
                    'grade_name' => $item->grade?->grade_name,
                    'brand_name' => $item->brand?->brand_name,
                    'size_name' => $item->size?->size_name ?? $item->size_unit,
                    'sale_item_prices' => $item->sale_item_prices,
                    'item_qty' => $item->item_qty,
                    'qty_type_name' => $this->quantityUnit($item),
                    'fs_bundle_weight' => $item->fs_bundle_weight,
                    'fs_bundle_unit_name' => $this->bundleUnit($item->fs_bundle_unit),
                    'purchase_entry_count' => (int) $item->purchase_entry_count,
                    'production_entry_count' => (int) $item->production_entry_count,
                    'completed_production_qty' => $item->completed_production_qty,
                    'production_remaining_qty' => $item->production_remaining_qty,
                    'production_status' => $item->production_status,
                    'truck_dispatched_qty' => $item->truck_dispatched_qty,
                    'dispatchable_qty' => $item->dispatchable_qty,
                    'dispatch_status' => $dispatchStatuses->get($item->id),
                ])->values(),
            ])->values(),
        ]);
    }

    public function truckOrders(GateAndGaadi $gate)
    {
        $this->ensureDepartment('dispatch');
        abort_unless($gate->formlist === 'Material Out', 404);
        $gate->load('customer:id,company_name');
        $orders = $this->loadDispatchOrders($gate->cust_id, null, $gate->id, true);
        $dispatchStatuses = DispatchMaterial::query()
            ->where('gate_id', $gate->id)
            ->pluck('status', 'sale_order_item_id');
        $statusValues = $dispatchStatuses->values();
        $vehicleStatus = match (true) {
            $statusValues->contains('dispatched') => 'Final Dispatched',
            $statusValues->contains('weight_completed') => 'Weight Department Process Completed',
            $statusValues->contains('sent_for_weight') => 'Vehicle Sent for Weight',
            default => 'Pending',
        };

        $itemIds = collect($orders)
            ->flatMap(fn($order) => collect($order['items'])->pluck('id'))
            ->toArray();

        $workOrderLosses = WorkOrderLoss::whereIn('sale_order_item_id', $itemIds)
            ->get();
         
        
        return view('dispatch.truck-orders', compact('gate', 'orders', 'dispatchStatuses', 'vehicleStatus', 'workOrderLosses'));
    }

    public function sendForWeight(Request $request)
    {
        $this->ensureDepartment('dispatch');
        $validated = $request->validate([
            'gate_id' => ['required', 'integer', 'exists:gate_and_gaadi,id'],
            'item_ids' => ['required', 'array', 'min:1'],
            'item_ids.*' => ['required', 'integer', 'distinct'],
        ]);

        $gate = GateAndGaadi::query()
            ->select(['id', 'cust_id', 'formlist', 'truckno'])
            ->where('formlist', 'Material Out')
            ->findOrFail($validated['gate_id']);
        $itemIds = array_map('intval', $validated['item_ids']);

        DB::transaction(function () use ($gate, $itemIds, $request) {
            GateAndGaadi::query()->whereKey($gate->id)->lockForUpdate()->firstOrFail();
            SaleOrderItem::query()->whereIn('id', $itemIds)->orderBy('id')->lockForUpdate()->get(['id']);

            $orders = $this->loadDispatchOrders($gate->cust_id, null, $gate->id, false, $itemIds);
            $selectedItems = $orders->flatMap(fn (SaleOrder $order) => $order->items);
            if ($selectedItems->count() !== count($itemIds)) {
                throw ValidationException::withMessages([
                    'item_ids' => 'Only items with a valid production entry for this company can be sent for weight.',
                ]);
            }
            if ($selectedItems->contains(fn (SaleOrderItem $item) => (float) $item->dispatchable_qty <= 0)) {
                throw ValidationException::withMessages([
                    'item_ids' => 'Selected items must have remaining production and order quantity available for dispatch.',
                ]);
            }

            $weight = Weight::query()
                ->select(['id', 'second_weight'])
                ->where('vehicle_id', $gate->id)
                ->where('cust_id', $gate->cust_id)
                ->where('formlist', 'Material Out')
                ->lockForUpdate()
                ->first();

            if ($weight?->second_weight === 'done') {
                throw ValidationException::withMessages([
                    'gate_id' => 'Weighting has already been completed for this vehicle.',
                ]);
            }

            $existingRows = DispatchMaterial::query()
                ->where('gate_id', $gate->id)
                ->whereIn('sale_order_item_id', $itemIds)
                ->lockForUpdate()
                ->select(['id', 'sale_order_id', 'sale_order_item_id', 'weight_id', 'status'])
                ->get();
            if ($existingRows->count() !== $existingRows->unique('sale_order_item_id')->count()) {
                throw ValidationException::withMessages([
                    'item_ids' => 'Duplicate dispatch records exist for a selected item. Resolve the duplicate before continuing.',
                ]);
            }
            $existingRecords = $existingRows->keyBy('sale_order_item_id');

            foreach ($selectedItems as $item) {
                $dispatch = $existingRecords->get($item->id);
                if ($dispatch?->status === 'dispatched') {
                    throw ValidationException::withMessages([
                        'item_ids' => "Sale order item #{$item->id} was already dispatched on this vehicle.",
                    ]);
                }

                if ($dispatch) {
                    if ($dispatch->status === 'weight_completed') {
                        throw ValidationException::withMessages([
                            'item_ids' => "Sale order item #{$item->id} has already completed vehicle weighing.",
                        ]);
                    }
                    if ($dispatch->status === 'sent_for_weight') {
                        throw ValidationException::withMessages([
                            'item_ids' => "Sale order item #{$item->id} has already been sent for weight.",
                        ]);
                    }
                    $dispatch->status = 'sent_for_weight';
                    $dispatch->weight_id = $weight?->id;
                    $dispatch->save();

                    continue;
                }

                DispatchMaterial::query()->create([
                    'gate_id' => $gate->id,
                    'sale_order_id' => $item->sale_order_id,
                    'sale_order_item_id' => $item->id,
                    'weight_id' => $weight?->id,
                    'dispatch_qty' => 0,
                    'status' => 'sent_for_weight',
                    'created_by' => $request->user()?->id,
                ]);
            }
        });

        return redirect()->route('dispatch.truck-orders', ['gate' => $gate->id])
            ->with('success', 'Vehicle Sent for Weight. The Weight Department will complete the weighing process.');
    }

    public function selectedOrders(Request $request)
    {
        $this->ensureDepartment('dispatch');
        $gateId = $request->validate([
            'gate_id' => ['required', 'integer', 'exists:gate_and_gaadi,id'],
        ])['gate_id'];
        $gate = GateAndGaadi::query()
            ->select(['id', 'cust_id', 'formlist', 'truckno'])
            ->with('customer:id,company_name')
            ->where('formlist', 'Material Out')
            ->findOrFail($gateId);

        $dispatchRows = DispatchMaterial::query()
            ->select(['id', 'sale_order_id', 'sale_order_item_id', 'weight_id', 'actual_qty', 'dispatch_qty', 'status'])
            ->where('gate_id', $gate->id)
            ->whereIn('status', ['sent_for_weight', 'weight_completed', 'dispatched'])
            ->get();
        if ($dispatchRows->count() !== $dispatchRows->unique('sale_order_item_id')->count()) {
            abort(422, 'Duplicate dispatch records exist for this vehicle.');
        }
        $dispatchRecords = $dispatchRows->keyBy('sale_order_item_id');

        if ($dispatchRecords->isEmpty()) {
            return redirect()->route('dispatch.index')
                ->with('error', 'No items previously sent for weight are available for this vehicle.');
        }

        $itemIds = $dispatchRecords->keys()->map(fn ($id) => (int) $id)->all();
        $orderIds = $dispatchRecords->pluck('sale_order_id')->unique()->map(fn ($id) => (int) $id)->all();
        $weightIds = $dispatchRecords->pluck('weight_id')->filter()->unique()->values();
        abort_if($weightIds->count() > 1, 422, 'Selected items refer to different vehicle weight records.');
        $weight = Weight::query()
            ->select([
                'id',
                'vehicle_id',
                'cust_id',
                'formlist',
                'created_by',
                'created_at',
                'tareweight',
                'second_weight',
                'unload_loaded_weight',
                'material_weight',
            ])
            ->where('vehicle_id', $gate->id)
            ->where('cust_id', $gate->cust_id)
            ->where('formlist', 'Material Out')
            ->when($weightIds->isNotEmpty(), fn ($query) => $query->whereKey($weightIds->first()))
            ->first();

        $orders = $this->loadDispatchOrders($gate->cust_id, $orderIds, $gate->id, false, $itemIds);
        if ($orders->flatMap(fn (SaleOrder $order) => $order->items)->count() !== count($itemIds)) {
            abort(422, 'A selected item is no longer eligible for dispatch.');
        }

        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $dispatchRecord = $dispatchRecords->get($item->id);
                $item->setAttribute('dispatch_material_id', $dispatchRecord->id);
                $item->setAttribute('dispatch_status', $dispatchRecord->status);
                $item->setAttribute('weighed_qty_kg', (float) ($dispatchRecord->actual_qty ?? 0));
                $unitDivisor = (int) $item->qty_type === SaleOrderItem::QTY_KGS ? 1 : 1000;
                $item->setAttribute(
                    'dispatch_available_qty',
                    round(min(
                        (float) $item->dispatchable_qty,
                        (float) ($dispatchRecord->actual_qty ?? 0) / $unitDivisor
                    ), 3)
                );
                $item->setAttribute(
                    'can_dispatch',
                    $dispatchRecord->status === 'weight_completed'
                        && $weight?->second_weight === 'done'
                        && $item->dispatch_available_qty > 0
                );
            }
        }
        // pr($dispatchRows->toArray()); die;
        return view('dispatch.selected-orders', compact('gate', 'orders', 'weight', 'dispatchRows'));
    }

    public function store(Request $request)
    {
        $this->ensureDepartment('dispatch');
        $validated = $request->validate([
            'gate_id' => ['required', 'integer', 'exists:gate_and_gaadi,id'],
            'order_ids' => ['required', 'array', 'min:1'],
            'order_ids.*' => ['required', 'integer', 'distinct'],
            'items' => ['required', 'array', 'min:1'],
            'items.*' => ['required', 'array'],
            'items.*.*.dispatch_qty' => ['required', 'numeric', 'min:0', 'decimal:0,3'],
        ]);

        $gateId = (int) $validated['gate_id'];
        $orderIds = array_map('intval', $validated['order_ids']);

        DB::transaction(function () use ($validated, $gateId, $orderIds) {
            $gate = GateAndGaadi::query()
                ->where('formlist', 'Material Out')
                ->lockForUpdate()
                ->findOrFail($gateId);
            $weight = Weight::query()
                ->select(['id', 'second_weight'])
                ->where('vehicle_id', $gate->id)
                ->where('cust_id', $gate->cust_id)
                ->where('formlist', 'Material Out')
                ->lockForUpdate()
                ->first();
            if (! $weight || $weight->second_weight !== 'done') {
                throw ValidationException::withMessages([
                    'gate_id' => 'Dispatch quantities are locked until the Weight Department completes weighing.',
                ]);
            }

            $postedQuantities = [];
            foreach ($validated['items'] as $orderId => $items) {
                if (! in_array((int) $orderId, $orderIds, true)) {
                    throw ValidationException::withMessages([
                        'items' => 'Dispatch quantities contain an order that was not selected.',
                    ]);
                }
                foreach ($items as $itemId => $fields) {
                    $postedQuantities[(int) $itemId] = [
                        'order_id' => (int) $orderId,
                        'quantity' => (float) $fields['dispatch_qty'],
                    ];
                }
            }

            $itemIds = array_keys($postedQuantities);
            $dispatchRows = DispatchMaterial::query()
                ->where('gate_id', $gate->id)
                ->where('weight_id', $weight->id)
                ->whereIn('sale_order_item_id', $itemIds)
                ->where('status', 'weight_completed')
                ->lockForUpdate()
                ->get([
                    'id',
                    'sale_order_id',
                    'sale_order_item_id',
                    'weight_id',
                    'actual_qty',
                    'status',
                ]);

            if ($dispatchRows->count() !== count($itemIds)
                || $dispatchRows->unique('sale_order_item_id')->count() !== count($itemIds)) {
                throw ValidationException::withMessages([
                    'items' => 'Only items selected and completed by the Weight Department can be dispatched.',
                ]);
            }
            $dispatchRecords = $dispatchRows->keyBy('sale_order_item_id');

            $orderIdsForItems = $dispatchRecords->pluck('sale_order_id')->unique()->map(fn ($id) => (int) $id)->all();
            if (array_diff($orderIdsForItems, $orderIds) !== []) {
                throw ValidationException::withMessages([
                    'order_ids' => 'The selected items do not belong to the submitted sale orders.',
                ]);
            }

            SaleOrderItem::query()->whereIn('id', $itemIds)->orderBy('id')->lockForUpdate()->get(['id']);
            $orders = $this->loadDispatchOrders($gate->cust_id, $orderIds, $gate->id, false, $itemIds);
            $items = $orders->flatMap(fn (SaleOrder $order) => $order->items)->keyBy('id');
            if ($items->count() !== count($itemIds)) {
                throw ValidationException::withMessages([
                    'items' => 'A selected item is no longer production-eligible for dispatch.',
                ]);
            }

            $hasDispatch = false;
            foreach ($postedQuantities as $itemId => $posted) {
                $item = $items->get($itemId);
                $dispatchRecord = $dispatchRecords->get($itemId);
                if ((int) $dispatchRecord->sale_order_id !== $posted['order_id']) {
                    throw ValidationException::withMessages([
                        'items' => 'A selected item does not match its sale order.',
                    ]);
                }

                $quantity = $posted['quantity'];
                $unitDivisor = (int) $item->qty_type === SaleOrderItem::QTY_KGS ? 1 : 1000;
                $weighedAvailable = (float) $dispatchRecord->actual_qty / $unitDivisor;
                $available = min((float) $item->dispatchable_qty, $weighedAvailable);
                $quantityMilliunits = (int) round($quantity * 1000);
                $availableMilliunits = (int) round($available * 1000);

                if ($quantityMilliunits > $availableMilliunits) {
                    throw ValidationException::withMessages([
                        "items.{$posted['order_id']}.{$itemId}.dispatch_qty" => 'Dispatch quantity cannot exceed completed production, order balance, or the Weight Department actual quantity.',
                    ]);
                }

                if ($quantityMilliunits === 0) {
                    continue;
                }

                $hasDispatch = true;
                $newDispatchedQty = round((float) $item->already_dispatched_qty + ($quantityMilliunits / 1000), 3);
                $pendingQty = round(max(0, (float) $item->item_qty - $newDispatchedQty), 3);
                $dispatchRecord->dispatch_qty = $quantityMilliunits / 1000;
                $dispatchRecord->status = 'dispatched';
                $dispatchRecord->save();

                SaleOrderItem::query()->whereKey($itemId)->update([
                    'dispatch_qty' => $quantityMilliunits / 1000,
                    'dispatched_qty' => $newDispatchedQty,
                    'pending_qty' => $pendingQty,
                ]);
            }

            if (! $hasDispatch) {
                throw ValidationException::withMessages([
                    'items' => 'Enter a positive dispatch quantity for at least one production-eligible item.',
                ]);
            }
        });

        return redirect()->route('dispatch.selected-orders.form', ['gate_id' => $gateId])
            ->with('success', 'Dispatch quantities were saved.');
    }

    public function history()
    {
        $this->ensureDepartment('dispatch');

        $production = DB::table('work_orders_and_process as wop')
            ->select('wop.sale_order_item_id')
            ->selectRaw(<<<'SQL'
                SUM(CASE WHEN wop.status = 'completed' THEN
                    CASE
                        WHEN COALESCE(wop.finished_pcs_weight, 0) > 0 THEN wop.finished_pcs_weight
                        WHEN wop.total_full_bundle_count IS NOT NULL OR wop.lose_bundle_weight IS NOT NULL THEN
                            (COALESCE(wop.total_full_bundle_count, 0) * COALESCE(wop.fs_bundle_weight, 0)
                                + COALESCE(wop.lose_bundle_weight, 0))
                            * CASE WHEN LOWER(COALESCE(wop.fs_bundle_unit, '')) IN ('2', 'ton', 'tons') THEN 1000 ELSE 1 END
                        ELSE COALESCE(wop.material_send_for_work, 0) * 1000
                    END
                ELSE 0 END) AS completed_production_kg
                SQL)
            ->whereNull('wop.delete_at')
            ->whereIn(
                'wop.sale_order_item_id',
                DB::table('dispatch_material')
                    ->select('sale_order_item_id')
                    ->where('status', 'dispatched')
            )
            ->groupBy('wop.sale_order_item_id');

        $dispatches = DB::table('dispatch_material as dm')
            ->join('gate_and_gaadi as gate', 'gate.id', '=', 'dm.gate_id')
            ->join('customers as customer', 'customer.id', '=', 'gate.cust_id')
            ->join('sale_orders as so', 'so.id', '=', 'dm.sale_order_id')
            ->join('sale_order_items as soi', 'soi.id', '=', 'dm.sale_order_item_id')
            ->leftJoin('products as product', 'product.id', '=', 'soi.product_id')
            ->leftJoin('grades as grade', 'grade.id', '=', 'soi.grade_id')
            ->leftJoin('brands as brand', 'brand.id', '=', 'soi.brand_id')
            ->leftJoin('sizes as size', 'size.id', '=', 'soi.size_id')
            ->leftJoin('weights as weight', 'weight.id', '=', 'dm.weight_id')
            ->leftJoin('users as weight_user', 'weight_user.id', '=', 'weight.created_by')
            ->leftJoin('users as dispatch_user', 'dispatch_user.id', '=', 'dm.created_by')
            ->leftJoinSub($production, 'production_summary', 'production_summary.sale_order_item_id', '=', 'soi.id')
            ->where('dm.status', 'dispatched')
            ->select([
                'dm.id',
                'dm.updated_at as dispatched_at',
                'dm.created_by as dispatch_created_by',
                'dm.dispatch_qty',
                'dm.actual_qty',
                'gate.truckno',
                'customer.company_name',
                'so.id as sale_order_id',
                'so.customer_po_no',
                'soi.id as sale_order_item_id',
                'soi.item_qty as ordered_qty',
                'soi.qty_type',
                'product.product_name',
                'product.product_category',
                'grade.grade_name',
                'brand.brand_name',
                'size.size_name',
                'soi.size_unit',
                'weight.tareweight',
                'weight.unload_loaded_weight',
                'weight.material_weight',
                'weight.second_weight',
                'weight.created_by as weight_created_by',
                'weight.created_at as weight_created_at',
                'weight_user.first_name as weight_user_first_name',
                'weight_user.last_name as weight_user_last_name',
                'dispatch_user.first_name as dispatch_user_first_name',
                'dispatch_user.last_name as dispatch_user_last_name',
                'production_summary.completed_production_kg',
            ])
            ->latest('dm.updated_at')
            ->paginate(25)
            ->withQueryString();

        return view('dispatch.history', compact('dispatches'));
    }

    private function ensureDepartment(string $shortName): void
    {
        $user = auth()->user();
        abort_unless($user && $user->role()->value('short_name') === $shortName, 403);
    }

    private function loadDispatchOrders(
        int $customerId,
        ?array $orderIds = null,
        ?int $gateId = null,
        bool $includeProductionHistory = false,
        ?array $itemIds = null
    ): Collection {
        $orders = SaleOrder::query()
            ->select([
                'id',
                'customer_id',
                'payment_term',
                'customer_po_no',
                'order_qty',
                'basic_rate',
                'dispatch_date',
                'remarks',
                'freight_basis',
                'created_at',
            ])
            ->where('customer_id', $customerId)
            ->when($orderIds !== null, fn ($query) => $query->whereIn('id', $orderIds))
            ->with(['items' => function ($items) use ($customerId, $orderIds, $gateId, $itemIds) {
                $items->select(self::ITEM_COLUMNS);
                $items->when($itemIds !== null, fn ($query) => $query->whereIn('sale_order_items.id', $itemIds));
                $this->addDispatchItemMetrics($items, $customerId, $orderIds, $gateId);
                $items->with([
                    'product:id,product_name,product_category',
                    'grade:id,grade_name',
                    'brand:id,brand_name',
                    'size:id,size_name',
                ]);
            }])
            ->when($orderIds === null, fn ($query) => $query->orderByDesc('id'))
            ->get()
            ->keyBy('id');

        if ($orderIds !== null) {
            if ($orders->count() !== count($orderIds)) {
                return collect();
            }

            $orders = collect($orderIds)->map(fn ($id) => $orders->get($id));
        }

        $orders = $orders->filter(fn (SaleOrder $order) => $order->items->isNotEmpty())->values();

        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $productionKg = (float) ($item->completed_production_kg ?? 0);
                $unitDivisor = (int) $item->qty_type === SaleOrderItem::QTY_KGS ? 1 : 1000;
                $completedQuantity = round($productionKg / $unitDivisor, 3);
                $orderedQuantity = max(0, (float) ($item->item_qty ?? 0));
                $alreadyDispatched = max(
                    (float) ($item->dispatched_qty ?? 0),
                    (float) ($item->dispatch_records_qty ?? 0)
                );

                $item->setAttribute('completed_production_qty', $completedQuantity);
                $item->setAttribute('production_remaining_qty', round(max(0, $orderedQuantity - $completedQuantity), 3));
                $item->setAttribute('production_status', $orderedQuantity > 0 && $completedQuantity >= $orderedQuantity
                    ? 'Completed'
                    : ($completedQuantity > 0 ? 'Partially produced' : 'Production started'));
                $item->setAttribute('already_dispatched_qty', $alreadyDispatched);
                $item->setAttribute('dispatchable_qty', round(max(0, min(
                    $orderedQuantity - $alreadyDispatched,
                    $completedQuantity - $alreadyDispatched
                )), 3));
                if ((int) ($item->active_dispatch_count ?? 0) > 0) {
                    $item->setAttribute('dispatchable_qty', 0);
                }
                $item->setAttribute('truck_dispatched_qty', (float) ($item->truck_dispatched_qty ?? 0));
            }
        }

        if ($includeProductionHistory) {
            $itemIds = $orders->flatMap(fn (SaleOrder $order) => $order->items->pluck('id'))->all();
            $productionHistory = WorkOrderProcess::query()
                ->select([
                    'id',
                    'sale_order_item_id',
                    'status',
                    'material_send_for_work',
                    'finished_pcs_weight',
                    'fs_bundle_weight',
                    'fs_bundle_unit',
                    'total_full_bundle_count',
                    'lose_bundle_weight',
                    'created_at',
                ])
                ->whereIn('sale_order_item_id', $itemIds)
                ->orderByDesc('id')
                ->get()
                ->groupBy('sale_order_item_id');

            foreach ($orders as $order) {
                foreach ($order->items as $item) {
                    $entries = $productionHistory->get($item->id, collect());
                    foreach ($entries as $entry) {
                        $outputKg = $this->workOrderOutputKg($entry);
                        $divisor = (int) $item->qty_type === SaleOrderItem::QTY_KGS ? 1 : 1000;
                        $entry->setAttribute(
                            'completed_output_qty',
                            $entry->status === 'completed' ? round($outputKg / $divisor, 3) : 0
                        );
                    }

                    $item->setRelation('productionHistory', $entries);
                }
            }
        }

        return $orders;
    }

    private function workOrderOutputKg(WorkOrderProcess $workOrder): float
    {
        if ((float) $workOrder->finished_pcs_weight > 0) {
            return (float) $workOrder->finished_pcs_weight;
        }

        if ($workOrder->total_full_bundle_count !== null || $workOrder->lose_bundle_weight !== null) {
            $bundleQty = ((float) ($workOrder->total_full_bundle_count ?? 0)
                    * (float) ($workOrder->fs_bundle_weight ?? 0))
                + (float) ($workOrder->lose_bundle_weight ?? 0);

            return in_array(strtolower((string) $workOrder->fs_bundle_unit), ['2', 'ton', 'tons'], true)
                ? $bundleQty * 1000
                : $bundleQty;
        }

        return (float) ($workOrder->material_send_for_work ?? 0) * 1000;
    }

    private function addDispatchItemMetrics($query, int $customerId, ?array $orderIds, ?int $gateId): void
    {
        $selectedOrderIds = fn () => DB::table('sale_orders')
            ->select('id')
            ->where('customer_id', $customerId)
            ->whereNull('deleted_at')
            ->when($orderIds !== null, fn ($orders) => $orders->whereIn('id', $orderIds));
        $selectedItemIds = fn () => DB::table('sale_order_items')
            ->select('id')
            ->whereNull('deleted_at')
            ->whereIn('sale_order_id', $selectedOrderIds());

        $productionOutputKg = <<<'SQL'
        CASE
            WHEN COALESCE(wop.finished_pcs_weight, 0) > 0
                THEN wop.finished_pcs_weight
            WHEN wop.total_full_bundle_count IS NOT NULL OR wop.lose_bundle_weight IS NOT NULL
                THEN (
                    (COALESCE(wop.total_full_bundle_count, 0) * COALESCE(wop.fs_bundle_weight, 0)
                        + COALESCE(wop.lose_bundle_weight, 0))
                    * CASE
                        WHEN LOWER(COALESCE(wop.fs_bundle_unit, '')) IN ('2', 'ton', 'tons') THEN 1000
                        ELSE 1
                    END
                )
            ELSE COALESCE(wop.material_send_for_work, 0) * 1000
        END
        SQL;

        $production = DB::table('work_orders_and_process as wop')
            ->select('wop.sale_order_item_id')
            ->selectRaw('COUNT(*) AS production_entry_count')
            ->selectRaw(
                "SUM(CASE WHEN wop.status = 'completed' THEN {$productionOutputKg} ELSE 0 END) AS completed_production_kg"
            )
            ->whereNull('wop.delete_at')
            ->whereIn('wop.sale_order_item_id', $selectedItemIds())
            ->groupBy('wop.sale_order_item_id');

        $purchases = DB::table('purchase_order_items')
            ->select('ref_saleorder_id', 'grade_id')
            ->selectRaw('COUNT(*) AS purchase_entry_count')
            ->whereIn('ref_saleorder_id', $selectedOrderIds())
            ->groupBy('ref_saleorder_id', 'grade_id');

        $dispatches = DB::table('dispatch_material')
            ->select('sale_order_item_id')
            ->selectRaw('COALESCE(SUM(dispatch_qty), 0) AS dispatch_records_qty')
            ->whereIn('sale_order_item_id', $selectedItemIds())
            ->groupBy('sale_order_item_id');
        $activeDispatches = DB::table('dispatch_material')
            ->select('sale_order_item_id')
            ->selectRaw('COUNT(*) AS active_dispatch_count')
            ->whereIn('sale_order_item_id', $selectedItemIds())
            ->whereIn('status', ['sent_for_weight', 'weight_completed'])
            ->when($gateId !== null, fn ($query) => $query->where('gate_id', '!=', $gateId))
            ->groupBy('sale_order_item_id');

        $query->leftJoinSub(
            $production,
            'production_summary',
            'production_summary.sale_order_item_id',
            '=',
            'sale_order_items.id'
        )
            ->leftJoinSub(
                $purchases,
                'purchase_summary',
                function ($join) {
                    $join->on('purchase_summary.ref_saleorder_id', '=', 'sale_order_items.sale_order_id')
                        ->on('purchase_summary.grade_id', '=', 'sale_order_items.grade_id');
                }
            )
            ->leftJoinSub(
                $dispatches,
                'dispatch_summary',
                'dispatch_summary.sale_order_item_id',
                '=',
                'sale_order_items.id'
            )
            ->leftJoinSub(
                $activeDispatches,
                'active_dispatch_summary',
                'active_dispatch_summary.sale_order_item_id',
                '=',
                'sale_order_items.id'
            )
            ->whereNotNull('production_summary.sale_order_item_id')
            ->addSelect([
                'production_summary.production_entry_count',
                'production_summary.completed_production_kg',
                'purchase_summary.purchase_entry_count',
                'dispatch_summary.dispatch_records_qty',
                'active_dispatch_summary.active_dispatch_count',
            ]);

        if ($gateId !== null) {
            $truckDispatches = DB::table('dispatch_material')
                ->select('sale_order_item_id')
                ->selectRaw('COALESCE(SUM(dispatch_qty), 0) AS truck_dispatched_qty')
                ->where('gate_id', $gateId)
                ->whereIn('sale_order_item_id', $selectedItemIds())
                ->groupBy('sale_order_item_id');

            $query->leftJoinSub(
                $truckDispatches,
                'truck_dispatch_summary',
                'truck_dispatch_summary.sale_order_item_id',
                '=',
                'sale_order_items.id'
            )
                ->addSelect('truck_dispatch_summary.truck_dispatched_qty');
        }
    }

    private function quantityUnit(SaleOrderItem $item): string
    {
        return (int) $item->qty_type === SaleOrderItem::QTY_KGS ? 'KG' : 'Tons';
    }

    private function bundleUnit($unit): string
    {
        return match ((string) $unit) {
            '1' => 'KG',
            '2' => 'Tons',
            default => '-',
        };
    }
}
