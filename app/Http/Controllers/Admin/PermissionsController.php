<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;

class PermissionsController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->only(['search']);

        $permissions = Permission::query()
            ->when($filters['search'] ?? null, function ($query, string $search) {
                $search = trim($search);
                $query->where('name', 'ilike', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('admin/permissions/Index', [
            'permissions' => $permissions,
            'filters' => $filters,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/permissions/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique(config('permission.table_names.permissions', 'permissions'), 'name')
                    ->where('guard_name', 'web'),
            ],
        ]);

        Permission::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        return redirect()
            ->route('permissions.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Permissão criada',
                'message' => 'A permissão foi cadastrada com sucesso.',
            ]);
    }

    public function edit(Permission $permission): Response
    {
        return Inertia::render('admin/permissions/Edit', [
            'permission' => [
                'id' => $permission->id,
                'name' => $permission->name,
                'guard_name' => $permission->guard_name,
            ],
        ]);
    }

    public function update(Request $request, Permission $permission): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique(config('permission.table_names.permissions', 'permissions'), 'name')
                    ->where('guard_name', 'web')
                    ->ignore($permission->id),
            ],
        ]);

        $permission->name = $validated['name'];
        $permission->guard_name = 'web';
        $permission->save();

        return redirect()
            ->route('permissions.edit', $permission)
            ->with('toast', [
                'type' => 'success',
                'title' => 'Permissão atualizada',
                'message' => 'As alterações foram salvas com sucesso.',
            ]);
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        $permission->delete();

        return redirect()
            ->route('permissions.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Permissão excluída',
                'message' => 'A permissão foi removida com sucesso.',
            ]);
    }
}


