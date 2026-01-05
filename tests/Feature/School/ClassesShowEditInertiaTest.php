<?php

use App\Http\Middleware\HandleInertiaRequests;
use App\Models\Teacher;
use App\Models\Tenant;
use App\Models\Turma;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

it('renders the class show page with turma prop', function () {
    $this->withoutMiddleware([
        HandleInertiaRequests::class,
        PermissionMiddleware::class,
        RoleMiddleware::class,
        RoleOrPermissionMiddleware::class,
    ]);

    $tenant = Tenant::factory()->create();
    $authUser = User::factory()->create();
    $authUser->tenants()->attach($tenant->id);

    $teacher = Teacher::factory()->create([
        'tenant_id' => $tenant->id,
    ]);

    $class = Turma::create([
        'tenant_id' => $tenant->id,
        'professor_id' => $teacher->id,
        'nome' => 'Turma 1',
        'serie' => '5º ano',
        'turma_letra' => 'A',
        'ano_letivo' => '2026',
        'capacidade' => 30,
        'ativo' => true,
    ]);

    $response = $this->actingAs($authUser)->get("/school/classes/{$class->id}");

    $response->assertSuccessful();

    $response->assertInertia(fn (Assert $page) => $page
        ->component('school/classes/Show')
        ->has('turma', fn (Assert $turma) => $turma
            ->where('id', $class->id)
            ->where('nome', 'Turma 1')
            ->where('serie', '5º ano')
            ->where('turma_letra', 'A')
            ->where('capacidade', 30)
            ->where('ativo', true)
            ->has('professor', fn (Assert $professor) => $professor
                ->where('id', $teacher->id)
                ->has('usuario', fn (Assert $usuario) => $usuario
                    ->where('nome_completo', $teacher->usuario->nome_completo)
                    ->etc()
                )
                ->etc()
            )
            ->etc()
        )
    );
});

it('renders the class edit page with turma and teachers props', function () {
    $this->withoutMiddleware([
        HandleInertiaRequests::class,
        PermissionMiddleware::class,
        RoleMiddleware::class,
        RoleOrPermissionMiddleware::class,
    ]);

    $tenant = Tenant::factory()->create();
    $authUser = User::factory()->create();
    $authUser->tenants()->attach($tenant->id);

    $teacher = Teacher::factory()->create([
        'tenant_id' => $tenant->id,
    ]);

    $class = Turma::create([
        'tenant_id' => $tenant->id,
        'professor_id' => $teacher->id,
        'nome' => 'Turma Edit',
        'serie' => '6º ano',
        'turma_letra' => 'B',
        'ano_letivo' => '2026',
        'capacidade' => 25,
        'ativo' => true,
    ]);

    $response = $this->actingAs($authUser)->get("/school/classes/{$class->id}/edit");

    $response->assertSuccessful();

    $response->assertInertia(fn (Assert $page) => $page
        ->component('school/classes/Edit')
        ->has('turma', fn (Assert $turma) => $turma
            ->where('id', $class->id)
            ->where('nome', 'Turma Edit')
            ->where('professor_id', $teacher->id)
            ->etc()
        )
        ->has('teachers')
    );
});
