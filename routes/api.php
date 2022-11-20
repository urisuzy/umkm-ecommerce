<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Foto\FotoProdukController;
use App\Http\Controllers\Holding\OwnerHoldingController;
use App\Http\Controllers\PaymentGateway\IpaymuController;
use App\Http\Controllers\Produk\SellerProdukController;
use App\Http\Controllers\Umkm\UmkmController;
use App\Http\Middleware\EnsureOwnThisProduk;
use App\Http\Middleware\EnsureOwnThisUmkm;
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
    Route::post('resend-verification', [AuthController::class, 'resendVerification']);

    Route::post('logout', [AuthController::class, 'logout'])->middleware(['auth:sanctum']);
});

// Seller Routes
Route::prefix('umkm')->middleware(['auth:sanctum'])->group(function () {

    Route::post('/', [UmkmController::class, 'store']);
    Route::get('/', [UmkmController::class, 'list']);

    Route::prefix('{umkmId}')->middleware(EnsureOwnThisUmkm::class)->group(function () {

        Route::get('/', [UmkmController::class, 'get']);
        Route::put('/', [UmkmController::class, 'update']);

        // Foto Routes
        Route::prefix('foto')->group(function () {
            Route::post('produk', [FotoProdukController::class, 'upload']);
        });

        // Produk for Seller
        Route::prefix('produk')->group(function () {
            Route::post('/', [SellerProdukController::class, 'store']);
            Route::get('/', [SellerProdukController::class, 'list']);
            Route::get('{id}', [SellerProdukController::class, 'get'])->middleware(EnsureOwnThisProduk::class);
            Route::put('{id}', [SellerProdukController::class, 'update'])->middleware(EnsureOwnThisProduk::class);
            Route::delete('{id}', [SellerProdukController::class, 'delete'])->middleware(EnsureOwnThisProduk::class);
        });
    });
});

// Holding
Route::prefix('holding')->middleware(['auth:sanctum'])->group(function () {
    Route::post('/', [OwnerHoldingController::class, 'store']);
    Route::get('/', [OwnerHoldingController::class, 'list']);
    Route::post('{id}/add-umkm', [OwnerHoldingController::class, 'addUmkmToHolding']);
    Route::post('{id}/remove-umkm', [OwnerHoldingController::class, 'removeUmkmFromHolding']);
    Route::get('{id}', [OwnerHoldingController::class, 'get']);
    Route::post('{id}/update', [OwnerHoldingController::class, 'update']);
});

Route::prefix('payment-gateway')->group(function () {
    Route::post('ipaymu', [IpaymuController::class, 'webhook']);
});
// require __DIR__ . '/auth.php';
