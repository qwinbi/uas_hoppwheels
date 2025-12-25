<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController as ApiProductController;
use App\Http\Controllers\Api\CartController as ApiCartController;
use App\Http\Controllers\Api\OrderController as ApiOrderController;
use App\Http\Controllers\Api\PaymentController as ApiPaymentController;
use App\Http\Controllers\Api\RatingController as ApiRatingController;
use App\Http\Controllers\Api\ProfileController as ApiProfileController;
use App\Http\Controllers\Api\AboutController as ApiAboutController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    Route::apiResource('products', ApiProductController::class);
    Route::get('/products/available', [ApiProductController::class, 'available']);
    
    Route::apiResource('carts', ApiCartController::class);
    
    Route::apiResource('orders', ApiOrderController::class);
    Route::post('/orders/checkout', [ApiOrderController::class, 'store']);
    
    Route::apiResource('payments', ApiPaymentController::class);
    
    Route::apiResource('ratings', ApiRatingController::class);
    
    Route::prefix('profile')->group(function () {
        Route::get('/', [ApiProfileController::class, 'index']);
        Route::put('/', [ApiProfileController::class, 'update']);
    });
    
    Route::get('/about', [ApiAboutController::class, 'index']);
});