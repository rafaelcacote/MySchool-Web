<?php

use App\Http\Middleware\HandleInertiaRequests;
use App\Models\Teacher;
use App\Models\Tenant;
use App\Models\User;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Spatie\Permission\Models\Role;

it('creates usuario and professor on store', function () {
    $this->withoutMiddleware([
        HandleInertiaRequests::class,
        PermissionMiddleware::class,
        RoleMiddleware::class,
        RoleOrPermissionMiddleware::class,
    ]);

    $tenant = Tenant::factory()->create();
    $authUser = User::factory()->create();
    $authUser->tenants()->attach($tenant->id);

    $payload = [
        'nome_completo' => 'Maria Silva',
        'cpf' => '12345678909',
        'email' => 'maria@example.com',
        'telefone' => '11999999999',
        'matricula' => 'PROF2024001',
        'disciplinas' => ['Matemática', 'Física'],
        'especializacao' => 'Educação Especial',
        'ativo' => '1',
    ];

    $response = $this->actingAs($authUser)->post('/school/teachers', $payload);

    $response->assertRedirect(route('school.teachers.index', absolute: false));

    $this->assertDatabaseHas('usuarios', [
        'cpf' => '12345678909',
        'nome_completo' => 'Maria Silva',
        'email' => 'maria@example.com',
    ], 'shared');

    $user = User::query()->where('cpf', '12345678909')->firstOrFail();

    // Ensure the "Professor" role exists
    Role::findOrCreate('Professor', 'web');

    // Verify the user has the "Professor" role
    expect($user->hasRole('Professor'))->toBeTrue();

    // Verify the user is linked to the tenant
    $this->assertDatabaseHas('usuario_tenants', [
        'usuario_id' => $user->id,
        'tenant_id' => $tenant->id,
    ], 'shared');

    $this->assertDatabaseHas('professores', [
        'tenant_id' => $tenant->id,
        'usuario_id' => $user->id,
        'matricula' => 'PROF2024001',
        'especializacao' => 'Educação Especial',
    ], 'shared');

    $teacher = Teacher::query()->where('matricula', 'PROF2024001')->firstOrFail();
    expect($teacher->disciplinas)->toBe(['Matemática', 'Física']);
});

it('validates matricula uniqueness within the tenant', function () {
    $this->withoutMiddleware([
        HandleInertiaRequests::class,
        PermissionMiddleware::class,
        RoleMiddleware::class,
        RoleOrPermissionMiddleware::class,
    ]);

    $tenant = Tenant::factory()->create();
    $authUser = User::factory()->create();
    $authUser->tenants()->attach($tenant->id);

    $usuario = User::factory()->create();
    Teacher::factory()->create([
        'tenant_id' => $tenant->id,
        'usuario_id' => $usuario->id,
        'matricula' => 'PROF2024001',
    ]);

    $payload = [
        'nome_completo' => 'João Santos',
        'matricula' => 'PROF2024001',
        'ativo' => '1',
    ];

    $response = $this->actingAs($authUser)->post('/school/teachers', $payload);

    $response->assertSessionHasErrors(['matricula']);
});

it('can update teacher data', function () {
    $this->withoutMiddleware([
        HandleInertiaRequests::class,
        PermissionMiddleware::class,
        RoleMiddleware::class,
        RoleOrPermissionMiddleware::class,
    ]);

    $tenant = Tenant::factory()->create();
    $authUser = User::factory()->create();
    $authUser->tenants()->attach($tenant->id);

    $usuario = User::factory()->create([
        'nome_completo' => 'Pedro Alves',
        'cpf' => '11122233344',
        'email' => 'pedro@example.com',
    ]);

    $teacher = Teacher::factory()->create([
        'tenant_id' => $tenant->id,
        'usuario_id' => $usuario->id,
        'matricula' => 'PROF2024002',
        'disciplinas' => ['História'],
        'especializacao' => 'História Antiga',
    ]);

    $payload = [
        'nome_completo' => 'Pedro Alves da Silva',
        'cpf' => '11122233344',
        'email' => 'pedro.silva@example.com',
        'matricula' => 'PROF2024002',
        'disciplinas' => ['História', 'Geografia'],
        'especializacao' => 'História Medieval',
        'ativo' => '1',
    ];

    $response = $this->actingAs($authUser)->patch("/school/teachers/{$teacher->id}", $payload);

    $response->assertRedirect(route('school.teachers.edit', $teacher, absolute: false));

    $this->assertDatabaseHas('usuarios', [
        'id' => $usuario->id,
        'nome_completo' => 'Pedro Alves da Silva',
        'email' => 'pedro.silva@example.com',
    ], 'shared');

    $this->assertDatabaseHas('professores', [
        'id' => $teacher->id,
        'especializacao' => 'História Medieval',
    ], 'shared');

    $teacher->refresh();
    expect($teacher->disciplinas)->toBe(['História', 'Geografia']);
});
