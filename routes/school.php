<?php

use App\Http\Controllers\School\ClassesController;
use App\Http\Controllers\School\ParentsController;
use App\Http\Controllers\School\ParentStudentsController;
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
    Route::post('students/{student}/reenroll', [StudentsController::class, 'reenroll'])
        ->middleware('permission:escola.alunos.editar')
        ->name('students.reenroll');
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
    Route::get('parents/{parent}', [ParentsController::class, 'show'])
        ->middleware('permission:escola.responsaveis.visualizar')
        ->name('parents.show');
    Route::get('parents/{parent}/edit', [ParentsController::class, 'edit'])
        ->middleware('permission:escola.responsaveis.editar')
        ->name('parents.edit');
    Route::patch('parents/{parent}', [ParentsController::class, 'update'])
        ->middleware('permission:escola.responsaveis.editar')
        ->name('parents.update');
    Route::post('parents/{parent}/students', [ParentStudentsController::class, 'store'])
        ->middleware('permission:escola.alunos.criar')
        ->name('parents.students.store');
    Route::delete('parents/{parent}/students/{student}', [ParentStudentsController::class, 'destroy'])
        ->middleware('permission:escola.alunos.editar')
        ->name('parents.students.destroy');

    // Professores
    Route::get('teachers', [TeachersController::class, 'index'])
        ->middleware('permission:escola.professores.visualizar')
        ->name('teachers.index');
    Route::post('teachers/check-cpf', [TeachersController::class, 'checkCpf'])
        ->middleware('permission:escola.professores.criar')
        ->name('teachers.check-cpf');
    Route::get('teachers/create', [TeachersController::class, 'create'])
        ->middleware('permission:escola.professores.criar')
        ->name('teachers.create');
    Route::post('teachers', [TeachersController::class, 'store'])
        ->middleware('permission:escola.professores.criar')
        ->name('teachers.store');
    Route::get('teachers/{teacher}', [TeachersController::class, 'show'])
        ->middleware('permission:escola.professores.visualizar')
        ->name('teachers.show');
    Route::get('teachers/{teacher}/edit', [TeachersController::class, 'edit'])
        ->middleware('permission:escola.professores.editar')
        ->name('teachers.edit');
    Route::patch('teachers/{teacher}', [TeachersController::class, 'update'])
        ->middleware('permission:escola.professores.editar')
        ->name('teachers.update');

    // Turmas
    Route::get('classes', [ClassesController::class, 'index'])
        ->middleware('permission:escola.turmas.visualizar')
        ->name('classes.index');
    Route::get('classes/create', [ClassesController::class, 'create'])
        ->middleware('permission:escola.turmas.criar')
        ->name('classes.create');
    Route::post('classes', [ClassesController::class, 'store'])
        ->middleware('permission:escola.turmas.criar')
        ->name('classes.store');
    Route::get('classes/{class}', [ClassesController::class, 'show'])
        ->middleware('permission:escola.turmas.visualizar')
        ->name('classes.show');
    Route::get('classes/{class}/students', [ClassesController::class, 'students'])
        ->middleware('permission:escola.turmas.visualizar')
        ->name('classes.students');
    Route::get('classes/{class}/edit', [ClassesController::class, 'edit'])
        ->middleware('permission:escola.turmas.editar')
        ->name('classes.edit');
    Route::patch('classes/{class}/toggle-status', [ClassesController::class, 'toggleStatus'])
        ->middleware('permission:escola.turmas.editar')
        ->name('classes.toggle-status');
    Route::patch('classes/{class}', [ClassesController::class, 'update'])
        ->middleware('permission:escola.turmas.editar')
        ->name('classes.update');
    Route::delete('classes/{class}', [ClassesController::class, 'destroy'])
        ->middleware('permission:escola.turmas.excluir')
        ->name('classes.destroy');
});
