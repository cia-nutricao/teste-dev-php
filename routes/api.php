<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SupplierController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rotas da API de Fornecedores
Route::prefix('suppliers')->group(function () {
    Route::get('/', [SupplierController::class, 'index']);
    Route::post('/', [SupplierController::class, 'store']);
    Route::get('/stats', [SupplierController::class, 'getStats']);
    Route::get('/{supplier}', [SupplierController::class, 'show']);
    Route::put('/{supplier}', [SupplierController::class, 'update']);
    Route::delete('/{supplier}', [SupplierController::class, 'destroy']);
    
    // Rotas auxiliares
    Route::post('/find-by-document', [SupplierController::class, 'findByDocument']);
    Route::post('/cnpj-data', [SupplierController::class, 'getCnpjData']);
    
    // Rotas de cache e performance
    Route::post('/cache/clear', [SupplierController::class, 'clearCache']);
    Route::get('/cache/info', [SupplierController::class, 'getCacheInfo']);
    Route::post('/cache/warm-up', [SupplierController::class, 'warmUpCache']);
});
