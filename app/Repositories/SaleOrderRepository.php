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
        $query = SaleOrder::query()->with('customer')->withCount('items');
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
