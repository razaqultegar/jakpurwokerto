<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MerchandiseController;
use App\Http\Controllers\CheckoutController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/merchandise/{slug}', [MerchandiseController::class, 'show'])->name('merchandise.show');
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/success/{orderId}', [CheckoutController::class, 'success'])->name('checkout.success');
