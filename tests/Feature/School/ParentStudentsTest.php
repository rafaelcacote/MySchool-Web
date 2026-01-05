<?php

use App\Http\Middleware\HandleInertiaRequests;
use App\Models\Responsavel;
use App\Models\Student;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

it('creates and links a student from the parent screen', function () {
    $this->withoutMiddleware([
        HandleInertiaRequests::class,
        PermissionMiddleware::class,
        RoleMiddleware::class,
        RoleOrPermissionMiddleware::class,
    ]);

    $tenant = Tenant::factory()->create();
    $authUser = User::factory()->create();
    $authUser->tenants()->attach($tenant->id);

    $parentUser = User::factory()->create([
        'cpf' => '33322211100',
        'email' => 'parent@example.com',
    ]);
    $parentUser->tenants()->attach($tenant->id);

    $parent = Responsavel::create([
        'tenant_id' => $tenant->id,
        'usuario_id' => $parentUser->id,
        'cpf' => $parentUser->cpf,
        'parentesco' => 'Pai',
    ]);

    $payload = [
        'nome_completo' => 'Aluno Filho',
        'cpf' => '12345678909',
        'email' => 'aluno@example.com',
        'telefone' => '11999999999',
        'matricula' => '2025001',
        'serie' => '5º ano',
        'turma' => 'A',
        'ativo' => '1',
    ];

    $response = $this->actingAs($authUser)->post("/school/parents/{$parent->id}/students", $payload);

    $response->assertRedirect(route('school.parents.show', $parent, absolute: false));

    $student = Student::query()->where('matricula', '2025001')->first();
    expect($student)->not()->toBeNull();

    $this->assertDatabaseHas('alunos', [
        'tenant_id' => $tenant->id,
        'matricula' => '2025001',
        'serie' => '5º ano',
        'turma' => 'A',
    ], 'shared');

    $this->assertDatabaseHas('aluno_responsavel', [
        'tenant_id' => $tenant->id,
        'aluno_id' => $student?->id,
        'responsavel_id' => $parent->id,
    ], 'shared');
});

it('detaches a student from the parent', function () {
    $this->withoutMiddleware([
        HandleInertiaRequests::class,
        PermissionMiddleware::class,
        RoleMiddleware::class,
        RoleOrPermissionMiddleware::class,
    ]);

    $tenant = Tenant::factory()->create();
    $authUser = User::factory()->create();
    $authUser->tenants()->attach($tenant->id);

    $parentUser = User::factory()->create(['cpf' => '55544433322']);
    $parentUser->tenants()->attach($tenant->id);

    $parent = Responsavel::create([
        'tenant_id' => $tenant->id,
        'usuario_id' => $parentUser->id,
        'cpf' => $parentUser->cpf,
        'parentesco' => 'Mãe',
    ]);

    $studentUser = User::factory()->create(['cpf' => '99988877766']);
    $studentUser->tenants()->attach($tenant->id);

    $student = Student::create([
        'tenant_id' => $tenant->id,
        'usuario_id' => $studentUser->id,
        'matricula' => '2025002',
        'serie' => '6º ano',
        'ativo' => true,
    ]);

    DB::connection('shared')->table('aluno_responsavel')->insert([
        'id' => Str::uuid(),
        'tenant_id' => $tenant->id,
        'aluno_id' => $student->id,
        'responsavel_id' => $parent->id,
        'principal' => false,
        'created_at' => now(),
    ]);

    $response = $this->actingAs($authUser)->delete("/school/parents/{$parent->id}/students/{$student->id}");

    $response->assertRedirect(route('school.parents.show', $parent, absolute: false));

    $this->assertDatabaseMissing('aluno_responsavel', [
        'tenant_id' => $tenant->id,
        'aluno_id' => $student->id,
        'responsavel_id' => $parent->id,
    ], 'shared');
});
