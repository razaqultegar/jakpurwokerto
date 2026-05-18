<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MerchandiseController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/merchandise/{slug}', [MerchandiseController::class, 'show'])->name('merchandise.show');
