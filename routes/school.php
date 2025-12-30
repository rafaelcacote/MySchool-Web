<?php

use App\Http\Controllers\School\ParentsController;
use App\Http\Controllers\School\SchoolProfileController;
use App\Http\Controllers\School\StudentsController;
use App\Http\Controllers\School\TeachersController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('school')->name('school.')->group(function () {
    // Perfil da escola
    Route::get('profile', [SchoolProfileController::class, 'show'])
        ->middleware('permission:escola.perfil.visualizar')
        ->name('profile');

    // Alunos
    Route::get('students', [StudentsController::class, 'index'])
        ->middleware('permission:escola.alunos.visualizar')
        ->name('students.index');
    Route::get('students/create', [StudentsController::class, 'create'])
        ->middleware('permission:escola.alunos.criar')
        ->name('students.create');
    Route::post('students', [StudentsController::class, 'store'])
        ->middleware('permission:escola.alunos.criar')
        ->name('students.store');
    Route::get('students/{student}', [StudentsController::class, 'show'])
        ->middleware('permission:escola.alunos.visualizar')
        ->name('students.show');
    Route::get('students/{student}/edit', [StudentsController::class, 'edit'])
        ->middleware('permission:escola.alunos.editar')
        ->name('students.edit');
    Route::patch('students/{student}', [StudentsController::class, 'update'])
        ->middleware('permission:escola.alunos.editar')
        ->name('students.update');
    Route::delete('students/{student}', [StudentsController::class, 'destroy'])
        ->middleware('permission:escola.alunos.excluir')
        ->name('students.destroy');

    // Responsáveis
    Route::get('parents', [ParentsController::class, 'index'])
        ->middleware('permission:escola.responsaveis.visualizar')
        ->name('parents.index');
    Route::get('parents/create', [ParentsController::class, 'create'])
        ->middleware('permission:escola.responsaveis.criar')
        ->name('parents.create');
    Route::post('parents', [ParentsController::class, 'store'])
        ->middleware('permission:escola.responsaveis.criar')
        ->name('parents.store');

    // Professores
    Route::get('teachers', [TeachersController::class, 'index'])
        ->middleware('permission:escola.professores.visualizar')
        ->name('teachers.index');
    Route::get('teachers/create', [TeachersController::class, 'create'])
        ->middleware('permission:escola.professores.criar')
        ->name('teachers.create');
    Route::post('teachers', [TeachersController::class, 'store'])
        ->middleware('permission:escola.professores.criar')
        ->name('teachers.store');
    Route::get('teachers/{teacher}', [TeachersController::class, 'show'])
        ->middleware('permission:escola.professores.visualizar')
        ->name('teachers.show');
});

