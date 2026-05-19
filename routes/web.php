<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MerchandiseController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/bayar', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/bayar', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/terimakasih/{orderId}', [CheckoutController::class, 'success'])->name('checkout.success');
Route::post('/terimakasih/{orderId}/bukti', [CheckoutController::class, 'uploadProof'])->name('checkout.proof');
Route::get('/{slug}', [MerchandiseController::class, 'show'])->name('merchandise.show');
