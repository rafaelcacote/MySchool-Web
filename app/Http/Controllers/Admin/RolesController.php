<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->only(['search']);

        $roles = Role::query()
            ->when($filters['search'] ?? null, function ($query, string $search) {
                $search = trim($search);
                $query->where('name', 'ilike', "%{$search}%");
            })
            ->withCount('permissions')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('admin/roles/Index', [
            'roles' => $roles,
            'filters' => $filters,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/roles/Create', [
            'permissions' => Permission::query()
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique(config('permission.table_names.roles', 'roles'), 'name')
                    ->where('guard_name', 'web'),
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => [
                'integer',
                Rule::exists(config('permission.table_names.permissions', 'permissions'), 'id'),
            ],
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        $permissionIds = $validated['permissions'] ?? [];
        if (! empty($permissionIds)) {
            $permissions = Permission::query()
                ->whereIn('id', $permissionIds)
                ->get();
            $role->syncPermissions($permissions);
        }

        return redirect()
            ->route('roles.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Perfil criado',
                'message' => 'O perfil (role) foi cadastrado com sucesso.',
            ]);
    }

    public function edit(Role $role): Response
    {
        $role->load('permissions:id,name');

        return Inertia::render('admin/roles/Edit', [
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'guard_name' => $role->guard_name,
                'permissions' => $role->permissions->map(fn (Permission $p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                ])->values(),
            ],
            'permissions' => Permission::query()
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique(config('permission.table_names.roles', 'roles'), 'name')
                    ->where('guard_name', 'web')
                    ->ignore($role->id),
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => [
                'integer',
                Rule::exists(config('permission.table_names.permissions', 'permissions'), 'id'),
            ],
        ]);

        $role->name = $validated['name'];
        $role->guard_name = 'web';
        $role->save();

        $permissionIds = $validated['permissions'] ?? [];
        $permissions = Permission::query()
            ->whereIn('id', $permissionIds)
            ->get();
        $role->syncPermissions($permissions);

        return redirect()
            ->route('roles.edit', $role)
            ->with('toast', [
                'type' => 'success',
                'title' => 'Perfil atualizado',
                'message' => 'As alterações foram salvas com sucesso.',
            ]);
    }

    public function destroy(Role $role): RedirectResponse
    {
        if (in_array($role->name, ['admin', 'super-admin', 'super_admin'], true)) {
            return redirect()
                ->route('roles.index')
                ->with('toast', [
                    'type' => 'error',
                    'title' => 'Ação não permitida',
                    'message' => 'Este perfil é protegido e não pode ser excluído.',
                ]);
        }

        $role->delete();

        return redirect()
            ->route('roles.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Perfil excluído',
                'message' => 'O perfil foi removido com sucesso.',
            ]);
    }
}


