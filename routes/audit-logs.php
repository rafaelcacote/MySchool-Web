<?php

use App\Http\Controllers\AuditLogsController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware(['auth', 'admin.geral'])->group(function () {
    Route::get('audit-logs', [AuditLogsController::class, 'index'])->name('audit-logs.index');
});

