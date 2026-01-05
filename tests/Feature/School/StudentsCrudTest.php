<?php

use App\Http\Middleware\HandleInertiaRequests;
use App\Models\Student;
use App\Models\Tenant;
use App\Models\User;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

it('creates usuario and aluno on store', function () {
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
        'nome_completo' => 'João da Silva',
        'cpf' => '12345678909',
        'email' => 'joao@example.com',
        'telefone' => '11999999999',
        'matricula' => '2024001',
        'serie' => '5º ano',
        'turma' => 'A',
        'ativo' => '1',
    ];

    $response = $this->actingAs($authUser)->post('/school/students', $payload);

    $response->assertRedirect(route('school.students.index', absolute: false));

    $this->assertDatabaseHas('usuarios', [
        'cpf' => '12345678909',
        'nome_completo' => 'João da Silva',
        'email' => 'joao@example.com',
    ], 'shared');

    $user = User::query()->where('cpf', '12345678909')->firstOrFail();

    $this->assertDatabaseHas('alunos', [
        'tenant_id' => $tenant->id,
        'usuario_id' => $user->id,
        'matricula' => '2024001',
        'serie' => '5º ano',
        'turma' => 'A',
    ], 'shared');
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

    $existingUser = User::factory()->create();

    Student::create([
        'tenant_id' => $tenant->id,
        'usuario_id' => $existingUser->id,
        'matricula' => '2024001',
        'serie' => '5º ano',
        'ativo' => true,
    ]);

    $response = $this->actingAs($authUser)->post('/school/students', [
        'nome_completo' => 'Novo aluno',
        'cpf' => '98765432100',
        'email' => 'novo@example.com',
        'matricula' => '2024001',
        'serie' => '5º ano',
        'ativo' => '1',
    ]);

    $response->assertSessionHasErrors(['matricula']);
});

it('updates usuario and aluno together', function () {
    $this->withoutMiddleware([
        HandleInertiaRequests::class,
        PermissionMiddleware::class,
        RoleMiddleware::class,
        RoleOrPermissionMiddleware::class,
    ]);

    $tenant = Tenant::factory()->create();
    $authUser = User::factory()->create();
    $authUser->tenants()->attach($tenant->id);

    $studentUser = User::factory()->create([
        'cpf' => '11122233344',
        'email' => 'old@example.com',
        'nome_completo' => 'Nome antigo',
    ]);
    $studentUser->tenants()->attach($tenant->id);

    $student = Student::create([
        'tenant_id' => $tenant->id,
        'usuario_id' => $studentUser->id,
        'ativo' => true,
        'matricula' => '2024001',
        'serie' => '5º ano',
    ]);

    $response = $this->actingAs($authUser)->patch("/school/students/{$student->id}", [
        'nome_completo' => 'Nome novo',
        'cpf' => '11122233344',
        'email' => 'new@example.com',
        'telefone' => '11988887777',
        'matricula' => '2024001',
        'serie' => '6º ano',
        'turma' => 'B',
        'ativo' => '1',
    ]);

    $response->assertRedirect(route('school.students.edit', $student, absolute: false));

    $this->assertDatabaseHas('usuarios', [
        'id' => $studentUser->id,
        'nome_completo' => 'Nome novo',
        'email' => 'new@example.com',
        'telefone' => '11988887777',
    ], 'shared');

    $this->assertDatabaseHas('alunos', [
        'id' => $student->id,
        'usuario_id' => $studentUser->id,
        'matricula' => '2024001',
        'serie' => '6º ano',
        'turma' => 'B',
    ], 'shared');
});

it('reuses existing usuario when cpf already exists', function () {
    $this->withoutMiddleware([
        HandleInertiaRequests::class,
        PermissionMiddleware::class,
        RoleMiddleware::class,
        RoleOrPermissionMiddleware::class,
    ]);

    $tenant = Tenant::factory()->create();
    $authUser = User::factory()->create();
    $authUser->tenants()->attach($tenant->id);

    $existingUser = User::factory()->create([
        'cpf' => '12345678909',
        'email' => null,
        'nome_completo' => 'Nome anterior',
    ]);

    $response = $this->actingAs($authUser)->post('/school/students', [
        'nome_completo' => 'João da Silva',
        'cpf' => '12345678909',
        'email' => 'joao@example.com',
        'telefone' => '11999999999',
        'matricula' => '2024002',
        'serie' => '5º ano',
        'ativo' => '1',
    ]);

    $response->assertRedirect(route('school.students.index', absolute: false));

    expect(User::query()->where('cpf', '12345678909')->count())->toBe(1);

    $this->assertDatabaseHas('usuarios', [
        'id' => $existingUser->id,
        'cpf' => '12345678909',
        'email' => 'joao@example.com',
        'nome_completo' => 'João da Silva',
    ], 'shared');

    $this->assertDatabaseHas('usuario_tenants', [
        'usuario_id' => $existingUser->id,
        'tenant_id' => $tenant->id,
    ], 'shared');

    $this->assertDatabaseHas('alunos', [
        'tenant_id' => $tenant->id,
        'usuario_id' => $existingUser->id,
        'matricula' => '2024002',
        'serie' => '5º ano',
    ], 'shared');
});

it('rejects store when cpf and email belong to different usuarios', function () {
    $this->withoutMiddleware([
        HandleInertiaRequests::class,
        PermissionMiddleware::class,
        RoleMiddleware::class,
        RoleOrPermissionMiddleware::class,
    ]);

    $tenant = Tenant::factory()->create();
    $authUser = User::factory()->create();
    $authUser->tenants()->attach($tenant->id);

    User::factory()->create([
        'cpf' => '11122233344',
        'email' => null,
    ]);

    User::factory()->create([
        'cpf' => '55566677788',
        'email' => 'conflict@example.com',
    ]);

    $response = $this->actingAs($authUser)->post('/school/students', [
        'nome_completo' => 'Aluno conflito',
        'cpf' => '11122233344',
        'email' => 'conflict@example.com',
        'matricula' => '2024003',
        'serie' => '5º ano',
        'ativo' => '1',
    ]);

    $response->assertSessionHasErrors(['cpf', 'email']);
});

it('rejects store when usuario is already a student in the tenant', function () {
    $this->withoutMiddleware([
        HandleInertiaRequests::class,
        PermissionMiddleware::class,
        RoleMiddleware::class,
        RoleOrPermissionMiddleware::class,
    ]);

    $tenant = Tenant::factory()->create();
    $authUser = User::factory()->create();
    $authUser->tenants()->attach($tenant->id);

    $existingUser = User::factory()->create([
        'cpf' => '99988877766',
        'email' => 'existing@student.com',
    ]);

    Student::create([
        'tenant_id' => $tenant->id,
        'usuario_id' => $existingUser->id,
        'ativo' => true,
        'matricula' => '2024004',
        'serie' => '5º ano',
    ]);

    $response = $this->actingAs($authUser)->post('/school/students', [
        'nome_completo' => 'Aluno duplicado',
        'cpf' => '99988877766',
        'email' => 'existing@student.com',
        'matricula' => '2024005',
        'serie' => '5º ano',
        'ativo' => '1',
    ]);

    $response->assertSessionHasErrors(['cpf']);
});
