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

Route::middleware('auth')->group(function () {
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
});

Route::get('/{slug}', [MerchandiseController::class, 'show'])->name('merchandise.show');
