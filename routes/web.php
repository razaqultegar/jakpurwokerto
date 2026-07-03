<?php

use App\Http\Controllers\Admin\CheckinController as AdminCheckinController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MerchandiseController as AdminMerchandiseController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MerchandiseController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/tiket/{slug}', [TicketController::class, 'show'])->name('ticket.show');
Route::get('/merchandise/{slug}', [MerchandiseController::class, 'show'])->name('merchandise.show');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/pembayaran/{orderId}', [CheckoutController::class, 'payment'])->name('checkout.payment');
Route::post('/pembayaran/{orderId}/bukti', [CheckoutController::class, 'uploadProof'])->name('checkout.proof');
Route::get('/terimakasih/{orderId}', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/pelunasan/{orderId}', [CheckoutController::class, 'settlement'])->name('checkout.settlement');
Route::post('/pelunasan/{orderId}/bukti', [CheckoutController::class, 'uploadSettlement'])->name('checkout.settlement.proof');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', AdminDashboardController::class)->name('dashboard');
    Route::get('/ticket', AdminTicketController::class)->name('ticket');
    Route::get('/merchandise', AdminMerchandiseController::class)->name('merchandise');
    Route::get('/orders/data', [AdminOrderController::class, 'data'])->name('orders.data');
    Route::get('/orders/export', [AdminOrderController::class, 'export'])->name('orders.export');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/invoice', [AdminOrderController::class, 'invoice'])->name('orders.invoice');
    Route::post('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
    Route::post('/orders/{order}/shipping', [AdminOrderController::class, 'updateShipping'])->name('orders.shipping');
    Route::post('/orders/{order}/pickup', [AdminOrderController::class, 'updatePickup'])->name('orders.pickup');
    Route::post('/orders/{order}/payment-proof', [AdminOrderController::class, 'uploadPaymentProof'])->name('orders.payment-proof');
    Route::post('/orders/{order}/dp-proof', [AdminOrderController::class, 'uploadDpProof'])->name('orders.dp-proof');
    Route::post('/orders/{order}/settlement-verify', [AdminOrderController::class, 'verifySettlement'])->name('orders.settlement-verify');
    Route::post('/orders/{order}/sync-payment', [AdminOrderController::class, 'syncPayment'])->name('orders.sync-payment');
    Route::delete('/orders/{order}', [AdminOrderController::class, 'destroy'])->name('orders.destroy');

    Route::get('/checkin', [AdminCheckinController::class, 'index'])->name('checkin.index');
    Route::post('/checkin', [AdminCheckinController::class, 'lookup'])->name('checkin.lookup');
    Route::get('/checkin/{code}', [AdminCheckinController::class, 'show'])->name('checkin.show');
    Route::post('/checkin/{code}/confirm', [AdminCheckinController::class, 'confirm'])->name('checkin.confirm');
    Route::post('/checkin/{code}/undo', [AdminCheckinController::class, 'undo'])->name('checkin.undo');
});

