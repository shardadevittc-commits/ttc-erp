<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\ProductionController;

Route::get('/productions', [ProductionController::class, 'index'])
    ->name('productions');

/* Route::match(['get', 'post'],'/productions/{saleOrderItem}/send-for-work', [ProductionController::class, 'workOrderDetails'])
    ->whereNumber('saleOrderItem')
    ->name('productions.work-order.create'); */

Route::post('/productions/{sale_order_item_id}/send-for-work/{sale_order_id?}', [ProductionController::class, 'sendForWorkMaterial'])
    ->name('productions.send-for-work');

Route::get('/productions/work-orders/{workOrder}/workflow', [ProductionController::class, 'workflow'])
    ->whereNumber('workOrder')
    ->name('productions.workflow');

Route::post('/productions/work-orders/{workOrder}/operations/{operation}', [ProductionController::class, 'completeOperation'])
    ->whereNumber('workOrder')
    ->whereNumber('operation')
    ->name('productions.operation');

/* Route::get('/productions/{vid}/details', [ProductionController::class, 'returnDetails'])
    ->name('productions.return-details');

Route::post('/productions-return', [ProductionController::class, 'returnMaterial'])
    ->name('productions.return');

Route::match(['get', 'post'],'/unload-materials/{vid?}', [ProductionController::class, 'unloadMaterials'])
    ->name('productions.unload-materials'); */
