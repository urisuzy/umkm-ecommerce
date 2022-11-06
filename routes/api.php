<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Foto\FotoProdukController;
use App\Http\Controllers\Produk\SellerProdukController;
use App\Http\Controllers\Umkm\UmkmController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::post('logout', [AuthController::class, 'logout'])->middleware(['auth:sanctum']);
});

// Seller Routes
Route::prefix('umkm')->middleware(['auth:sanctum'])->group(function () {

    Route::post('/', [UmkmController::class, 'store']);

    Route::middleware(['ability:umkm'])->group(function () {
        // Foto Routes
        Route::prefix('foto')->group(function () {
            Route::post('produk', [FotoProdukController::class, 'upload']);
        });

        // Produk for Seller
        Route::prefix('produk')->group(function () {
            Route::post('/', [SellerProdukController::class, 'store']);
        });
    });
});

// require __DIR__ . '/auth.php';
