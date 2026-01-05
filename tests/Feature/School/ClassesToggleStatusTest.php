<?php

use App\Http\Middleware\HandleInertiaRequests;
use App\Models\Tenant;
use App\Models\Turma;
use App\Models\User;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

it('can toggle class status', function () {
    $this->withoutMiddleware([
        HandleInertiaRequests::class,
        PermissionMiddleware::class,
        RoleMiddleware::class,
        RoleOrPermissionMiddleware::class,
    ]);

    $tenant = Tenant::factory()->create();
    $authUser = User::factory()->create();
    $authUser->tenants()->attach($tenant->id);

    $class = Turma::create([
        'tenant_id' => $tenant->id,
        'nome' => 'Turma 1',
        'serie' => '5º ano',
        'ano_letivo' => '2026',
        'ativo' => true,
    ]);

    $response = $this->actingAs($authUser)->patch("/school/classes/{$class->id}/toggle-status", [
        'ativo' => false,
    ]);

    $response->assertRedirect();

    $class->refresh();
    expect($class->ativo)->toBeFalse();

    $response->assertSessionHas('toast', [
        'type' => 'success',
        'title' => 'Status atualizado',
        'message' => 'O status da turma foi atualizado com sucesso.',
    ]);
});
