<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\{CategoryController, ProductController, CartController, OrderController, PaymentController};

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // Categories
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::middleware('role:admin')->group(function () {
        Route::post('/categories',            [CategoryController::class, 'store']);
        Route::put('/categories/{id}',        [CategoryController::class, 'update']);
        Route::delete('/categories/{id}',     [CategoryController::class, 'destroy']);
    });

    // Products
    Route::get('/products',     [ProductController::class, 'index']);
    Route::get('/products/{id}',[ProductController::class, 'show']);
    Route::middleware('role:admin')->group(function () {
        Route::post('/products',           [ProductController::class, 'store']);
        Route::put('/products/{id}',       [ProductController::class, 'update']);
        Route::delete('/products/{id}',    [ProductController::class, 'destroy']);
    });

    // Cart (customer)
    Route::middleware('role:customer')->group(function () {
        Route::get('/cart',            [CartController::class, 'index']);
        Route::post('/cart',           [CartController::class, 'store']);
        Route::put('/cart/{id}',       [CartController::class, 'update']);
        Route::delete('/cart/{id}',    [CartController::class, 'destroy']);
    });

    // Orders
    Route::post('/orders',                 [OrderController::class, 'store'])->middleware('stock.check');
    Route::get('/orders',                  [OrderController::class, 'index']);
    Route::put('/orders/{id}/status',      [OrderController::class, 'updateStatus'])->middleware('role:admin');

    // Payments
    Route::post('/orders/{id}/payment', [PaymentController::class, 'pay']);
    Route::get('/payments/{id}',        [PaymentController::class, 'show']);
});
