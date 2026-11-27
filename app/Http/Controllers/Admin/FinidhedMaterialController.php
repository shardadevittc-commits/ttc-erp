<?php

namespace App\Http\Controllers\Admin;

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

class FinidhedMaterialController extends Controller
{
    // sale_order_items.product_type (number) => products.product_type (slug) + naam
    private const PRODUCT_TYPES = [
        1 => ['key' => 'bright_bar',   'name' => 'Bright Bar'],
        2 => ['key' => 'hb_wire',      'name' => 'HB Wire'],
        3 => ['key' => 'hhb_wire',     'name' => 'HHB Wire'],
        4 => ['key' => 'annealed_bar', 'name' => 'Annealed Bar'],
        5 => ['key' => 'bolt_rod',     'name' => 'Bolt Rod'],
    ];

    private const RELATIONS = [
        'saleOrderItem.saleOrder.customer',
        'saleOrderItem.product',
        'saleOrderItem.grade',
        'saleOrderItem.size',
        'lab'
    ];

    /**
     * Listing: completed work orders + unka finished summary.
     */
    public function index(Request $request)
    {
        // product_type filter number (1-5) ya slug (bright_bar...) dono accept karta hai
        $productTypeKeys = array_map(fn ($type) => $type['key'], self::PRODUCT_TYPES);

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'product_type' => ['nullable', Rule::in(array_merge(array_keys(self::PRODUCT_TYPES), $productTypeKeys))],
            'created_on' => ['sometimes', 'array'],
            'created_on.0' => ['nullable', 'date_format:Y-m-d'],
            'created_on.1' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $startDate = $filters['created_on'][0] ?? null;
        $endDate = $filters['created_on'][1] ?? null;

        if ($startDate && $endDate && $endDate < $startDate) {
            throw ValidationException::withMessages([
                'created_on.1' => 'The end date must be on or after the start date.',
            ]);
        }

        $query = WorkOrderProcess::query()
            ->with(self::RELATIONS)
            ->where('status', 'completed')
            ->whereHas('saleOrderItem.saleOrder');

        // Search: work order, sale order, heat no, customer, PO no, product, grade, size
        $search = trim($filters['search'] ?? '');
        if ($search !== '') {
            $like = '%' . addcslashes($search, '\\%_') . '%';

            $query->where(function ($q) use ($like) {
                $q->where('id', 'like', $like)
                    ->orWhere('sale_order_id', 'like', $like)
                    ->orWhere('heat_no', 'like', $like)
                    ->orWhereHas('saleOrderItem.saleOrder', function ($saleOrder) use ($like) {
                        $saleOrder->where('customer_po_no', 'like', $like)
                            ->orWhereHas('customer', function ($customer) use ($like) {
                                $customer->where('company_name', 'like', $like)
                                    ->orWhere('cust_code', 'like', $like);
                            });
                    })
                    ->orWhereHas('saleOrderItem.product', fn ($p) => $p->where('product_name', 'like', $like))
                    ->orWhereHas('saleOrderItem.grade', fn ($g) => $g->where('grade_name', 'like', $like))
                    ->orWhereHas('saleOrderItem.size', fn ($s) => $s->where('size_name', 'like', $like));
            });
        }

        // Number ho to seedha, slug ho to number dhundh kar filter
        $typeFilter = $filters['product_type'] ?? null;
        if ($typeFilter !== null && $typeFilter !== '') {
            $typeId = is_numeric($typeFilter)
                ? (int) $typeFilter
                : array_search($typeFilter, $productTypeKeys, true);

            $query->whereHas('saleOrderItem', fn ($item) => $item->where('product_type', $typeId));
        }

        // Work order ki created date par range
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        /* Sorting */
        $sortBy = in_array($request->query('sort_by'), ['id', 'created_at', 'updated_at', 'material_send_for_work'], true)
            ? $request->query('sort_by') : 'id';
        $sortOrder = $request->query('sort_order') === 'asc' ? 'asc' : 'desc';

        /* Pagination */
        $perPage = (int) $request->query('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 10;

        $listing = $query
            ->orderBy($query->getModel()->getTable() . '.' . $sortBy, $sortOrder)
            ->paginate($perPage)
            ->withQueryString();

        // Is page ke saare work orders ka loss ek hi query mein (N+1 nahi)
        $workOrders = $listing->getCollection();
        $losses = WorkOrderLoss::query()
            ->whereIn('work_order_id', $workOrders->pluck('id'))
            ->selectRaw('work_order_id, SUM(loss_weight) as total_loss, SUM(COALESCE(adjustable_material, 0)) as total_adjusted')
            ->groupBy('work_order_id')
            ->get()
            ->keyBy('work_order_id');

        $workOrders->each(function (WorkOrderProcess $workOrder) use ($losses) {
            $loss = $losses->get($workOrder->id);

            $workOrder->setAttribute('finished_summary', $this->finishedSummary(
                $workOrder,
                (float) ($loss->total_loss ?? 0),
                (float) ($loss->total_adjusted ?? 0)
            ));
        });

        $viewData = [
            'listing' => $listing,
            // Filter dropdown ke liye: number => naam
            'productTypes' => array_map(fn ($type) => $type['name'], self::PRODUCT_TYPES),
        ];

        /* AJAX Request */
        if ($request->ajax()) {
            return response()->json([
                'status' => true,
                'html' => view('admin.finidhedMaterial.listingLoop', $viewData)->render(),
                'page' => $listing->currentPage(),
                'counter' => $listing->perPage(),
                'count' => $listing->total(),
                'lastPage' => $listing->lastPage(),
                'pagination' => $listing->links('pagination::bootstrap-5')->toHtml(),
            ], 200);
        }
        //pr($viewData);die;
        return view('admin.finidhedMaterial.index', $viewData);
    }

    /**
     * View: ek work order ka poora detail (header, finished summary, operations, scrap).
     */
    public function view(int $id)
    {
        $workOrder = WorkOrderProcess::query()
            ->with(self::RELATIONS)
            ->where('status', 'completed')
            ->findOrFail($id);

        $saleOrderItem = $workOrder->saleOrderItem;
        abort_unless($saleOrderItem && $saleOrderItem->saleOrder, 404);

        $productType = (int) $saleOrderItem->product_type;

        $losses = WorkOrderLoss::query()
            ->where('work_order_id', $workOrder->id)
            ->orderBy('id')
            ->get();

        $summary = $this->finishedSummary(
            $workOrder,
            (float) $losses->sum('loss_weight'),
            (float) $losses->sum('adjustable_material')
        );

        return view('admin.finidhedMaterial.view', [
            'workOrder' => $workOrder,
            'saleOrderItem' => $saleOrderItem,
            'productName' => self::PRODUCT_TYPES[$productType]['name'] ?? ($saleOrderItem->product?->product_name ?? '-'),
            'productKey' => self::PRODUCT_TYPES[$productType]['key'] ?? null,
            'summary' => $summary,
            'operations' => $this->operationDetails($workOrder, $productType),
            'losses' => $losses,
            'recoverableScrapKg' => round((float) $losses->where('scrap_type', 1)->sum('loss_weight'), 3),
            'nonRecoverableScrapKg' => round((float) $losses->where('scrap_type', 2)->sum('loss_weight'), 3),
            'adjustedScrapKg' => round((float) $losses->sum('adjustable_material'), 3),
        ]);
    }

    /**
     * Finished summary: sent, scrap, finished (Kg) + packing text.
     * Finished = Sent - (Loss - Adjusted). material_send_for_work Tons mein hai.
     */
    private function finishedSummary(WorkOrderProcess $workOrder, float $lossKg, float $adjustedKg): array
    {
        $sentKg = round((float) $workOrder->material_send_for_work * 1000, 3);
        $scrapKg = max(round($lossKg - $adjustedKg, 3), 0);
        $finishedKg = max(round($sentKg - $scrapKg, 3), 0);

        if ((int) $workOrder->saleOrderItem?->product_type === 5) {
            // Bolt Rod: boxes aur pcs
            $packing = (int) $workOrder->no_of_boxes . ' boxes x ' . (int) $workOrder->pcs_per_box . ' pcs';

            if ((int) $workOrder->loss_box_pcs > 0) {
                $packing .= (int) $workOrder->loss_box_count > 0
                    ? ' + 1 loose box of ' . (int) $workOrder->loss_box_pcs . ' pcs'
                    : ' (last box +' . (int) $workOrder->loss_box_pcs . ' pcs)';
            }
        } else {
            // Baaki products: bundles
            $packing = (int) $workOrder->total_full_bundle_count . ' full bundles';

            if ((int) $workOrder->lose_bundle_count > 0) {
                $packing .= ' + ' . (int) $workOrder->lose_bundle_count . ' loose bundle';
            }
        }

        return [
            'sent_kg' => $sentKg,
            'scrap_kg' => $scrapKg,
            'finished_kg' => $finishedKg,
            'packing' => $packing,
        ];
    }

    /**
     * Product ke hisaab se operations ki list: [['title' => ..., 'fields' => [label => value]], ...]
     * View mein loop karke dikhao.
     */
    private function operationDetails(WorkOrderProcess $w, int $type): array
    {
        $yesNo = fn ($value) => match ((int) $value) {
            1 => 'Yes',
            2 => 'No',
            default => '-',
        };
        $size = fn ($value, $unit) => ($value !== null && $value !== '') ? trim($value . ' ' . $unit) : '-';
        $text = fn ($value) => ($value !== null && $value !== '') ? $value : '-';
        $step = fn (string $title, array $fields) => ['title' => $title, 'fields' => $fields];

        $surface = $step('Shot Blasting / Acid Pickling', [
            'Process' => $w->shot_blasting == 1 ? 'Shot Blasting' : ($w->acid_pickling == 1 ? 'Acid Pickling' : '-'),
        ]);
        $pointing = $step('Pointing', ['Pointing' => $yesNo($w->pointing)]);
        $drawing = $step('Drawing', [
            'Required size' => $size($w->fs_size, $w->fs_size_unit),
            'Observed size' => $size($w->fs_observed_size, $w->fs_observed_unit),
        ]);
        $pointDiscard = $step('Point Discard', ['Point discard' => $yesNo($w->poin_discard)]);
        $straightening = $step('Straightening', ['Straightening' => $yesNo($w->straightening)]);
        $bundles = fn (string $title) => $step($title, [
            'Full bundles' => (int) $w->total_full_bundle_count,
            'Loose bundle' => (int) $w->lose_bundle_count,
        ]);
        $acidPhosphating = fn (string $title, $acid, $phos, $remarks) => $step($title, [
            'Acid pickling' => $yesNo($acid),
            'Phosphating' => $yesNo($phos),
            'Remarks' => $text($remarks),
        ]);
        $wireDrawing = fn (string $title) => $step($title, [
            'RM size' => $size($w->rm_size, $w->rm_size_unit),
            'RM observed size' => $size($w->rm_observed_size, $w->rm_observed_unit),
            'FM size' => $size($w->fs_size, $w->fs_size_unit),
            'FM observed size' => $size($w->fs_observed_size, $w->fs_observed_unit),
        ]);

        return match ($type) {
            // Bright Bar
            1 => [$surface, $pointing, $drawing, $pointDiscard, $straightening, $bundles('Bend Saw Cut')],

            // HB Wire
            2 => [
                $acidPhosphating('Acid Pickling / Phosphating', $w->acid_pickling, $w->phosphating, $w->acid_remarks),
                $wireDrawing('Wire Drawing'),
            ],

            // HHB Wire
            3 => [
                $acidPhosphating('Acid Pickling / Phosphating', $w->acid_pickling, $w->phosphating, $w->acid_remarks),
                $wireDrawing('Wire Drawing'),
                $step('Heat Treatment', [
                    'Batch no' => $text($w->batch_no),
                    'Hardness (SO)' => $size($w->hardness, $w->hardness_unit),
                    'Observed hardness' => $size($w->hardness_observed, $w->hardness_observed_unit),
                ]),
                $acidPhosphating('Acid Pickling / Phosphating (2nd)', $w->second_acid_pickling, $w->second_phosphating, $w->second_acid_remarks),
                $step('Wire Drawing (Final Pass)', [
                    'Observed size' => $size($w->final_fs_observed_size, $w->final_fs_observed_unit),
                    'Full bundles' => (int) $w->total_full_bundle_count,
                    'Loose bundle' => (int) $w->lose_bundle_count,
                ]),
            ],

            // Annealed Bar
            4 => [
                $step('Process Annealing', [
                    'Required hardness (SO)' => $size($w->hardness, $w->hardness_unit),
                    'Observed hardness' => $size($w->hardness_observed, $w->hardness_observed_unit),
                ]),
                $surface, $pointing, $drawing, $pointDiscard, $straightening,
                $bundles('Bend Saw Cut'),
            ],

            // Bolt Rod
            5 => [
                $surface,
                $pointing,
                $step('Drawing', [
                    'Required size' => $size($w->fs_size, $w->fs_size_unit),
                    'Observed size' => $size($w->fs_observed_size, $w->fs_observed_unit),
                ]),
                $step('Press Cutting / Bend Saw Cutting', [
                    'Cutting type' => match ($w->cutting_type) {
                        'press_cutting' => 'Press Cutting',
                        'bend_saw_cutting' => 'Bend Saw Cutting',
                        default => '-',
                    },
                    'Per pcs weight (Kg)' => $text($w->per_pcs_weight),
                    'Finished pcs' => $text($w->finished_pcs),
                    'Finished pcs weight (Kg)' => $text($w->finished_pcs_weight),
                ]),
                $step('Marking', [
                    'Finish (pcs)' => $text($w->marking_finish),
                    'Rejected (pcs)' => $text($w->marking_rejected),
                ]),
                $step('Threading', [
                    'Finish (pcs)' => $text($w->threading_finish),
                    'Rejected (pcs)' => $text($w->threading_rejected),
                ]),
                $step('Auto Black', [
                    'Finish (pcs)' => $text($w->auto_black_finish),
                    'Rejected (pcs)' => $text($w->auto_black_rejected),
                    'Weight per pcs (Kg)' => $text($w->weight_per_pcs),
                ]),
                $step('Packing', [
                    'No of boxes' => $text($w->no_of_boxes),
                    'Pcs per box' => $text($w->pcs_per_box),
                    'Loose box count' => $text($w->loss_box_count),
                    'Loose box pcs' => $text($w->loss_box_pcs),
                ]),
            ],

            default => [],
        };
    }
}
