<?php

use App\Http\Controllers\Admin\PermissionsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin.geral'])->prefix('admin')->group(function () {
    Route::get('permissions', [PermissionsController::class, 'index'])->name('permissions.index');
    Route::get('permissions/create', [PermissionsController::class, 'create'])->name('permissions.create');
    Route::post('permissions', [PermissionsController::class, 'store'])->name('permissions.store');
    Route::get('permissions/{permission}/edit', [PermissionsController::class, 'edit'])->name('permissions.edit');
    Route::patch('permissions/{permission}', [PermissionsController::class, 'update'])->name('permissions.update');
    Route::delete('permissions/{permission}', [PermissionsController::class, 'destroy'])->name('permissions.destroy');
});


