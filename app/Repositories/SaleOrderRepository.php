<?php

namespace App\Repositories;

use App\Models\SaleOrder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleOrderRepository
{
    public function listing(Request $request): LengthAwarePaginator
    {
        $query = SaleOrder::query()
            ->with([
                'customer',
                'items' => function ($itemsQuery) {
                    $itemsQuery
                        ->with(['product', 'size', 'grade'])
                        ->withSum([
                            'dispatchMaterials as dispatched_qty_total' => function ($dispatchQuery) {
                                $dispatchQuery->where('status', 'dispatched');
                            },
                        ], 'dispatch_qty')
                        ->addSelect([
                            'has_purchase' => DB::table('purchase_order_items as purchase_items')
                                ->selectRaw('1')
                                ->whereColumn('purchase_items.ref_saleorder_id', 'sale_order_items.sale_order_id')
                                ->whereColumn('purchase_items.grade_id', 'sale_order_items.grade_id')
                                ->limit(1),
                            'has_receiving' => DB::table('poitem_receiving as receiving')
                                ->selectRaw('1')
                                ->join('purchase_order_items as purchase_items', 'purchase_items.id', '=', 'receiving.purchase_order_item_id')
                                ->whereColumn('purchase_items.ref_saleorder_id', 'sale_order_items.sale_order_id')
                                ->whereColumn('purchase_items.grade_id', 'sale_order_items.grade_id')
                                ->whereNull('receiving.deleted_at')
                                ->limit(1),
                            'has_lab_testing' => DB::table('poitem_receiving as receiving')
                                ->selectRaw('1')
                                ->join('purchase_order_items as purchase_items', 'purchase_items.id', '=', 'receiving.purchase_order_item_id')
                                ->whereColumn('purchase_items.ref_saleorder_id', 'sale_order_items.sale_order_id')
                                ->whereColumn('purchase_items.grade_id', 'sale_order_items.grade_id')
                                ->whereNull('receiving.deleted_at')
                                ->where(function ($labQuery) {
                                    $labQuery->where('receiving.lab_test', '1')
                                        ->orWhereExists(function ($resultQuery) {
                                            $resultQuery->selectRaw('1')
                                                ->from('lab_results')
                                                ->whereColumn('lab_results.purchase_order_item_id', 'receiving.id');
                                        });
                                })
                                ->limit(1),
                            'has_production' => DB::table('work_orders_and_process as work_orders')
                                ->selectRaw('1')
                                ->whereColumn('work_orders.sale_order_item_id', 'sale_order_items.id')
                                ->whereNull('work_orders.delete_at')
                                ->limit(1),
                        ]);
                },
            ])
            ->withCount('items');
        // pr($query->toSql()); die;
        $search = trim((string) $request->query('search', ''));

        if ($search !== '') {
            $query->where(function (Builder $query) use ($search) {
                $query->where('id', 'like', "%{$search}%")
                    ->orWhere('customer_po_no', 'like', "%{$search}%")
                    ->orWhere('payment_term', 'like', "%{$search}%")
                    ->orWhereHas('customer', function (Builder $customerQuery) use ($search) {
                        $customerQuery->where('company_name', 'like', "%{$search}%")
                            ->orWhere('cust_code', 'like', "%{$search}%");
                    });
            });
        }

        $sortBy = in_array($request->query('sort_by'), ['id', 'customer_po_no', 'created_at'], true)
            ? $request->query('sort_by')
            : 'id';
        $sortOrder = $request->query('sort_order') === 'asc' ? 'asc' : 'desc';
        $perPage = (int) $request->query('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 10;

        return $query->orderBy($sortBy, $sortOrder)->paginate($perPage)->withQueryString();
    }

    public function find(int $id): SaleOrder
    {
        return SaleOrder::query()
            ->with(['customer', 'items.product', 'items.grade', 'items.brand', 'items.size'])
            ->findOrFail($id);
    }

    public function save(array $saleOrderData, array $itemRows, ?SaleOrder $saleOrder = null): SaleOrder
    {
        return DB::transaction(function () use ($saleOrderData, $itemRows, $saleOrder) {
            if ($saleOrder === null) {
                $saleOrder = SaleOrder::create($saleOrderData);
            } else {
                unset($saleOrderData['created_by']);
                $saleOrder->update($saleOrderData);
            }

            $existingItems = $saleOrder->items()->get()->keyBy('id');
            $submittedIds = [];

            foreach ($itemRows as $itemRow) {
                $itemId = $itemRow['id'] ?? null;
                unset($itemRow['id']);
                unset($itemRow['category']);

                if ($itemId !== null) {
                    $item = $existingItems->get($itemId);
                    if ($item === null) {
                        throw ValidationException::withMessages([
                            'items' => 'One or more sale order items do not belong to this sale order.',
                        ]);
                    }

                    $item->update($itemRow);
                    $submittedIds[] = $item->id;

                    continue;
                }

                $itemRow['created_by'] = auth()->id();
                $saleOrder->items()->create($itemRow);
            }

            $existingItems
                ->reject(fn ($item) => in_array($item->id, $submittedIds, true))
                ->each
                ->delete();

            return $saleOrder->load(['customer', 'items.product', 'items.grade', 'items.brand', 'items.size']);
        });
    }
}
