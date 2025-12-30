<?php

use App\Http\Controllers\SubscriptionsController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware(['auth', 'admin.geral'])->group(function () {
    Route::get('subscriptions', [SubscriptionsController::class, 'index'])->name('subscriptions.index');
});

