<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\ProductionController;

Route::get('/productions', [ProductionController::class, 'index'])
    ->name('productions');

Route::get('/production-history', [ProductionController::class, 'index'])
    ->name('production-history');

Route::get('/productions/unlinked-listings', [ProductionController::class, 'unlinkedListings'])
    ->name('productions.unlinked-listings');

Route::post('/productions/{sale_order_item_id}/send-for-work/{sale_order_id?}', [ProductionController::class, 'sendForWorkMaterial'])
    ->name('productions.send-for-work');

Route::get('/productions/work-orders/{workOrder}/workflow', [ProductionController::class, 'workflow'])
    ->whereNumber('workOrder')
    ->name('productions.workflow');

Route::get('/productions/work-orders/{workOrder}/stickers', [ProductionController::class, 'bundleStickers'])
    ->whereNumber('workOrder')
    ->name('productions.bundle-stickers');

Route::post('/productions/work-orders/{workOrder}/operations/{operation}', [ProductionController::class, 'completeOperation'])
    ->whereNumber('workOrder')
    ->whereNumber('operation')
    ->name('productions.operation');
