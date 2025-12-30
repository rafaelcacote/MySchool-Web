<?php

use App\Http\Controllers\PlansController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware(['auth', 'admin.geral'])->group(function () {
    Route::get('plans', [PlansController::class, 'index'])->name('plans.index');
    Route::get('plans/create', [PlansController::class, 'create'])->name('plans.create');
    Route::post('plans', [PlansController::class, 'store'])->name('plans.store');
    Route::get('plans/{plan}/edit', [PlansController::class, 'edit'])->name('plans.edit');
    Route::patch('plans/{plan}', [PlansController::class, 'update'])->name('plans.update');
});

