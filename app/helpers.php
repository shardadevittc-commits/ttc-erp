<?php

use Illuminate\Support\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\WorkOrderProcess;
use App\Models\WorkOrderLoss;

function _dt($datetime)
{
	$dateTime = Carbon::parse($datetime)->format('d-m-Y h:i A');
    return $dateTime;
}

function _d($date)
{

    $date = Carbon::parse($date)->format('d-m-Y');
    return $date;
}

function _dtx($date)
{

    $date = Carbon::parse($date)->format('d M, Y');
    return $date;
}

function _time($time)
{
    $date = Carbon::parse($time)->format('h:i A');
	return $date;
}

function sanitizePhone($phone)
{
    $number = explode('-', $phone, 2);
    $number[0] = str_replace(['-'], '', $number[0]);
    //$phonenumber = str_replace('+'.$number[0], '', $number[1]);

    $phone = str_replace([$number[0], '-'], '', count($number) > 1 ? $number[1] : $phone);
    $phone = str_replace("/[0-9]/g", '', $phone);
    $phone = trim($phone);

    if($phone && substr($phone, 0, 1) == '1')
    {
        $phone = substr($phone, 1, strlen($phone));
    }
    else if($phone)
    {
        $phone = (substr($phone, 0, 1) == 0 ? substr($phone, 1, strlen($phone)) : $phone);
    }
    return $number[0] . '-' . $phone;
}

function _rz($value)
{
    $coorestNumFome = rtrim(rtrim(number_format((float) $value, 3, '.', ''), '0'), '.');
    return $coorestNumFome ?? '---';
}

function calculateProductionScrap(Request $request, WorkOrderProcess $workOrder): bool
{
    // pr($request->toArray());
    // pr($request->filled('acid_pickling'));
    // pr($workOrder); 
    // die;
    $activeProcess = (string) $request->input('processtype', '');
    $activeProcess2 = (string) $request->input('processtype2', '');
    $activeStatus = (string) $request->input('workStatus', '');
    $basicWeight = (float) $workOrder->material_send_for_work * 1000;
    $stages = [];
 
    $acidSelection = $request->input('acid_pickling');
    if ($acidSelection === null && $activeProcess === 'shotblastingAcidPickling') {
        $acidSelection = match ($request->input('surface_process')) {
            'shot_blasting' => '1',
            'acid_pickling' => '1',
            default => null,
        };
    }

    if($request->filled('acid_pickling')){
        $stages[] = ['stage_name' => 'acid_pickling', 'loss_percentage' => 0.5, 'scrap_type' => 2, 'basic_weight' => $basicWeight];
    }
    // pr($stages); die;
    if ($activeProcess === 'acid_pickling_phosphating') {
        if ($request->input('acidPrcklingoptionacid') === 'optionacid' || $acidSelection === 'optionacid') {
            $stages[] = ['stage_name' => 'acid_pickling', 'loss_percentage' => 0.5, 'scrap_type' => 2, 'basic_weight' => $basicWeight];
        }
    } elseif ($activeProcess === 'shotblastingAcidPickling') {
        if ($acidSelection === 'optionacid') {
            $stages[] = ['stage_name' => 'short_blasting', 'loss_percentage' => 0.5, 'scrap_type' => 2, 'basic_weight' => $basicWeight];
        } elseif ($acidSelection === 'optionphos') {
            $stages[] = ['stage_name' => 'acid_pickling', 'loss_percentage' => 0.5, 'scrap_type' => 2, 'basic_weight' => $basicWeight];
        }
    } elseif ($activeProcess === 'pointing' && ($request->input('pointyN') === 'pointingOpY' || (string) $request->input('pointing') === '1')) {
        $stages[] = ['stage_name' => 'pointing', 'loss_percentage' => 0.75, 'scrap_type' => 1, 'basic_weight' => $basicWeight];
    } elseif ($activeProcess === 'pointing_discard' && ($request->input('pointdyN') === 'pointdy' || (string) $request->input('poin_discard') === '1')) {
        $stages[] = ['stage_name' => 'point_discard', 'loss_percentage' => 1.75, 'scrap_type' => 1, 'basic_weight' => $basicWeight];
    }

    if ($activeProcess === 'bendsawcut' || $activeProcess2 === 'bendsawcut') {
        $stages[] = ['stage_name' => 'bedsawcut', 'loss_percentage' => 0.75, 'scrap_type' => 1, 'basic_weight' => $basicWeight];
    }

    if ($activeProcess === 'misc' || $activeProcess2 === 'misc') {
        $productType = $request->input('product_type');
        $misc = (float) $request->input('misc', 0);
        $finishQty = (float) $request->input('finish_qty', 0);

        if ($productType === 'brightbar') {
            $basicWeight = $misc + $finishQty;
            $lossWeight = $misc;
        } elseif ($productType === 'annealed_bright_bar') {
            $lossWeight = 0;
        } elseif ($productType === 'bold_rods') {
            if ($request->input('workStatus') === 'operation4') {
                $basicWeight = $misc + $finishQty;
                $lossWeight = $misc;
            } else {
                $rejectPieces = (int) $request->input('autoBlackNR', 0);
                $weightPerPiece = (int) $request->input('wtPpc', 0);
                $rejectPiecesInKg = $rejectPieces * $weightPerPiece;
                $lossWeight = $request->input('tscrapWt')
                    ? $rejectPiecesInKg + (float) $request->input('tscrapWt')
                    : $rejectPiecesInKg;
            }
        } else {
            $lossWeight = $misc;
        }

        $lossPercentage = ($lossWeight / $basicWeight) * 100;
        $stages[] = [
            'stage_name' => 'misc',
            'loss_percentage' => $lossPercentage,
            'scrap_type' => 1,
            'basic_weight' => $basicWeight,
        ];
    }

    if ($stages === []) {
        Log::info('NO SCRAP PROCESS MATCHED', ['work_order_id' => $workOrder->id]);
        return false;
    }

    $inserted = false;

    try {
        foreach ($stages as $stage) {
            $stageName = $stage['stage_name'];
            $uniqueStageName = $stageName . '_' . $activeStatus;
            $percentage = (float) $stage['loss_percentage'];
            $lossAmount = $percentage / 100;
            $stageBasicWeight = (float) $stage['basic_weight'];
            $lossWeight = round($lossAmount * $stageBasicWeight, 2);
            $weightAfterLoss = round($stageBasicWeight - $lossWeight, 1);

            $existingLoss = WorkOrderLoss::withTrashed()
                ->where('work_order_id', $workOrder->id)
                ->where('unique_stage_name', $uniqueStageName)
                ->exists();

            if ($existingLoss) {
                Log::info('DUPLICATE ENTRY SKIPPED', [
                    'work_order_id' => $workOrder->id,
                    'stage' => $uniqueStageName,
                ]);
                continue;
            }

            WorkOrderLoss::create([
                'work_order_id' => $workOrder->id,
                'vehicle_id' => $workOrder->vehicle_id,
                'sale_order_item_id' => $workOrder->sale_order_item_id,
                'stage_name' => $stageName,
                'unique_stage_name' => $uniqueStageName,
                'initial_weight' => $stageBasicWeight,
                'scrap_type' => $stage['scrap_type'],
                'loss_percentage' => $percentage,
                'loss_amount' => $lossAmount,
                'loss_weight' => $lossWeight,
                'weight_after_loss' => $weightAfterLoss,
                'created_by' => $workOrder->created_by,
            ]);

            if (!in_array($stageName, ['acid_pickling', 'short_blasting', 'pointing', 'point_discard', 'bedsawcut', 'misc'], true)) {
                throw new \InvalidArgumentException("Unsupported scrap total stage: {$stageName}");
            }

            $now = now();
            $scrapTotalsId = DB::table('scrap_totals')->orderBy('id')->value('id');
            if ($scrapTotalsId === null) {
                DB::table('scrap_totals')->insertOrIgnore([
                    'id' => 1,
                    'acid_pickling' => 0,
                    'short_blasting' => 0,
                    'pointing' => 0,
                    'point_discard' => 0,
                    'bedsawcut' => 0,
                    'misc' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $scrapTotalsId = DB::table('scrap_totals')->orderBy('id')->value('id');
            }

            DB::table('scrap_totals')
                ->where('id', $scrapTotalsId)
                ->increment($stageName, $lossWeight, ['updated_at' => $now]);

            Log::info('LOSS INSERTED', [
                'work_order_id' => $workOrder->id,
                'stage' => $stageName,
                'loss' => $lossWeight . ' KG',
            ]);
            $inserted = true;
        }
    } catch (QueryException $exception) {
        Log::error('SCRAP DATABASE ERROR', [
            'work_order_id' => $workOrder->id,
            'message' => $exception->getMessage(),
        ]);
        throw $exception;
    }

    return $inserted;
}


/* function calculateProductionScrap(Request $request, WorkOrderProcess $workOrder): bool
{
    pr($request->toArray());
    pr($workOrder); die;
    $activeProcess = (string) $request->input('processtype', '');
    $activeProcess2 = (string) $request->input('processtype2', '');
    $activeStatus = (string) $request->input('workStatus', '');
    $basicWeight = (float) $workOrder->material_send_for_work * 1000;
    $stages = [];

    $acidSelection = $request->input('acid_pickling');
    if ($acidSelection === null && $activeProcess === 'shotblastingAcidPickling') {
        $acidSelection = match ($request->input('surface_process')) {
            'shot_blasting' => '1',
            'acid_pickling' => '1',
            default => null,
        };
    }

    if ($activeProcess === 'acid_pickling_phosphating') {
        if ($request->input('acidPrcklingoptionacid') === 'optionacid' || $acidSelection === 'optionacid') {
            $stages[] = ['stage_name' => 'acid_pickling', 'loss_percentage' => 0.5, 'scrap_type' => 2, 'basic_weight' => $basicWeight];
        }
    } elseif ($activeProcess === 'shotblastingAcidPickling') {
        if ($acidSelection === 'optionacid') {
            $stages[] = ['stage_name' => 'short_blasting', 'loss_percentage' => 0.5, 'scrap_type' => 2, 'basic_weight' => $basicWeight];
        } elseif ($acidSelection === 'optionphos') {
            $stages[] = ['stage_name' => 'acid_pickling', 'loss_percentage' => 0.5, 'scrap_type' => 2, 'basic_weight' => $basicWeight];
        }
    } elseif ($activeProcess === 'pointing' && ($request->input('pointyN') === 'pointingOpY' || (string) $request->input('pointing') === '1')) {
        $stages[] = ['stage_name' => 'pointing', 'loss_percentage' => 0.75, 'scrap_type' => 1, 'basic_weight' => $basicWeight];
    } elseif ($activeProcess === 'pointing_discard' && ($request->input('pointdyN') === 'pointdy' || (string) $request->input('poin_discard') === '1')) {
        $stages[] = ['stage_name' => 'point_discard', 'loss_percentage' => 1.75, 'scrap_type' => 1, 'basic_weight' => $basicWeight];
    }

    if ($activeProcess === 'bendsawcut' || $activeProcess2 === 'bendsawcut') {
        $stages[] = ['stage_name' => 'bedsawcut', 'loss_percentage' => 0.75, 'scrap_type' => 1, 'basic_weight' => $basicWeight];
    }

    if ($activeProcess === 'misc' || $activeProcess2 === 'misc') {
        $productType = $request->input('product_type');
        $misc = (float) $request->input('misc', 0);
        $finishQty = (float) $request->input('finish_qty', 0);

        if ($productType === 'brightbar') {
            $basicWeight = $misc + $finishQty;
            $lossWeight = $misc;
        } elseif ($productType === 'annealed_bright_bar') {
            $lossWeight = 0;
        } elseif ($productType === 'bold_rods') {
            if ($request->input('workStatus') === 'operation4') {
                $basicWeight = $misc + $finishQty;
                $lossWeight = $misc;
            } else {
                $rejectPieces = (int) $request->input('autoBlackNR', 0);
                $weightPerPiece = (int) $request->input('wtPpc', 0);
                $rejectPiecesInKg = $rejectPieces * $weightPerPiece;
                $lossWeight = $request->input('tscrapWt')
                    ? $rejectPiecesInKg + (float) $request->input('tscrapWt')
                    : $rejectPiecesInKg;
            }
        } else {
            $lossWeight = $misc;
        }

        $lossPercentage = ($lossWeight / $basicWeight) * 100;
        $stages[] = [
            'stage_name' => 'misc',
            'loss_percentage' => $lossPercentage,
            'scrap_type' => 1,
            'basic_weight' => $basicWeight,
        ];
    }

    if ($stages === []) {
        Log::info('NO SCRAP PROCESS MATCHED', ['work_order_id' => $workOrder->id]);
        return false;
    }

    $inserted = false;

    try {
        foreach ($stages as $stage) {
            $stageName = $stage['stage_name'];
            $uniqueStageName = $stageName . '_' . $activeStatus;
            $percentage = (float) $stage['loss_percentage'];
            $lossAmount = $percentage / 100;
            $stageBasicWeight = (float) $stage['basic_weight'];
            $lossWeight = round($lossAmount * $stageBasicWeight, 2);
            $weightAfterLoss = round($stageBasicWeight - $lossWeight, 1);

            $existingLoss = WorkOrderLoss::withTrashed()
                ->where('work_order_id', $workOrder->id)
                ->where('unique_stage_name', $uniqueStageName)
                ->exists();

            if ($existingLoss) {
                Log::info('DUPLICATE ENTRY SKIPPED', [
                    'work_order_id' => $workOrder->id,
                    'stage' => $uniqueStageName,
                ]);
                continue;
            }

            WorkOrderLoss::create([
                'work_order_id' => $workOrder->id,
                'vehicle_id' => $workOrder->vehicle_id,
                'sale_order_item_id' => $workOrder->sale_order_item_id,
                'stage_name' => $stageName,
                'unique_stage_name' => $uniqueStageName,
                'initial_weight' => $stageBasicWeight,
                'scrap_type' => $stage['scrap_type'],
                'loss_percentage' => $percentage,
                'loss_amount' => $lossAmount,
                'loss_weight' => $lossWeight,
                'weight_after_loss' => $weightAfterLoss,
                'created_by' => $workOrder->created_by,
            ]);

            if (!in_array($stageName, ['acid_pickling', 'short_blasting', 'pointing', 'point_discard', 'bedsawcut', 'misc'], true)) {
                throw new \InvalidArgumentException("Unsupported scrap total stage: {$stageName}");
            }

            $now = now();
            $scrapTotalsId = DB::table('scrap_totals')->orderBy('id')->value('id');
            if ($scrapTotalsId === null) {
                DB::table('scrap_totals')->insertOrIgnore([
                    'id' => 1,
                    'acid_pickling' => 0,
                    'short_blasting' => 0,
                    'pointing' => 0,
                    'point_discard' => 0,
                    'bedsawcut' => 0,
                    'misc' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $scrapTotalsId = DB::table('scrap_totals')->orderBy('id')->value('id');
            }

            DB::table('scrap_totals')
                ->where('id', $scrapTotalsId)
                ->increment($stageName, $lossWeight, ['updated_at' => $now]);

            Log::info('LOSS INSERTED', [
                'work_order_id' => $workOrder->id,
                'stage' => $stageName,
                'loss' => $lossWeight . ' KG',
            ]);
            $inserted = true;
        }
    } catch (QueryException $exception) {
        Log::error('SCRAP DATABASE ERROR', [
            'work_order_id' => $workOrder->id,
            'message' => $exception->getMessage(),
        ]);
        throw $exception;
    }

    return $inserted;
} */


