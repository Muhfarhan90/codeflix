<?php

use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::post('/midtrans/callback', [TransactionController::class, 'callback'])->name('payment.callback');
