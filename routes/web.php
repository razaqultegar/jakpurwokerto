<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MerchandiseController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/pembayaran/{orderId}', [CheckoutController::class, 'payment'])->name('checkout.payment');
Route::post('/pembayaran/{orderId}/bukti', [CheckoutController::class, 'uploadProof'])->name('checkout.proof');
Route::get('/terimakasih/{orderId}', [CheckoutController::class, 'success'])->name('checkout.success');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders/data', [AdminController::class, 'ordersData'])->name('orders.data');
    Route::get('/orders/export', [AdminController::class, 'exportOrders'])->name('orders.export');
    Route::get('/orders/{order}', [AdminController::class, 'showOrder'])->name('orders.show');
    Route::post('/orders/{order}/status', [AdminController::class, 'updateStatus'])->name('orders.status');
    Route::post('/orders/{order}/shipping', [AdminController::class, 'updateShipping'])->name('orders.shipping');
    Route::post('/orders/{order}/dp-proof', [AdminController::class, 'uploadDpProof'])->name('orders.dp-proof');
    Route::delete('/orders/{order}', [AdminController::class, 'destroyOrder'])->name('orders.destroy');
});

Route::get('/{slug}', [MerchandiseController::class, 'show'])->name('merchandise.show');
