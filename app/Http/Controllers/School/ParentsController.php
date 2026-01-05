<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Http\Requests\School\StoreParentRequest;
use App\Http\Requests\School\UpdateParentRequest;
use App\Models\Responsavel;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class ParentsController extends Controller
{
    /**
     * Get the current user's tenant.
     */
    protected function getTenant()
    {
        $user = auth()->user();
        $tenant = $user->tenants()->first();

        if (! $tenant) {
            abort(404, 'Escola não encontrada');
        }

        return $tenant;
    }

    /**
     * Display a listing of the parents.
     */
    public function index(Request $request): Response
    {
        $tenant = $this->getTenant();
        $filters = $request->only(['search', 'active']);

        $parents = Responsavel::query()
            ->with('user')
            ->where('tenant_id', $tenant->id)
            ->when($filters['search'] ?? null, function ($query, string $search) {
                $search = trim($search);
                $cpfSearch = preg_replace('/[^0-9]/', '', $search);
                $query->where(function ($q) use ($search, $cpfSearch) {
                    $q->whereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('nome_completo', 'ilike', "%{$search}%")
                            ->orWhere('email', 'ilike', "%{$search}%")
                            ->orWhere('telefone', 'ilike', "%{$search}%")
                            ->orWhere('cpf', 'ilike', "%{$cpfSearch}%");
                    })
                        ->orWhere('cpf', 'ilike', "%{$cpfSearch}%");
                });
            })
            ->when(isset($filters['active']) && $filters['active'] !== '' && $filters['active'] !== null, function ($query) use ($filters) {
                $active = filter_var($filters['active'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                if ($active !== null) {
                    $query->whereHas('user', function ($userQuery) use ($active) {
                        $userQuery->where('ativo', $active);
                    });
                }
            })
            ->get()
            ->sortBy(function ($parent) {
                return $parent->user?->nome_completo ?? '';
            })
            ->values();

        // Paginação manual
        $page = (int) $request->get('page', 1);
        $perPage = 10;
        $total = $parents->count();
        $items = $parents->slice(($page - 1) * $perPage, $perPage)->values();

        // Transformar os dados para incluir campos do user
        $transformedItems = $items->map(function ($parent) {
            return [
                'id' => $parent->id,
                'nome_completo' => $parent->user?->nome_completo ?? null,
                'cpf' => $parent->cpf ?? $parent->user?->cpf ?? null,
                'email' => $parent->user?->email ?? null,
                'telefone' => $parent->user?->telefone ?? null,
                'parentesco' => $parent->parentesco ?? null,
                'ativo' => $parent->user?->ativo ?? false,
            ];
        })->toArray();

        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $transformedItems,
            $total,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        // Garantir que os links sejam gerados corretamente
        $paginated->withPath($request->url());

        return Inertia::render('school/parents/Index', [
            'parents' => $paginated,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new parent.
     */
    public function create(): Response
    {
        return Inertia::render('school/parents/Create');
    }

    /**
     * Store a newly created parent.
     */
    public function store(StoreParentRequest $request): RedirectResponse
    {
        $tenant = $this->getTenant();
        $validated = $request->validated();

        DB::transaction(function () use ($tenant, $validated) {
            // Remove CPF formatting
            if (! empty($validated['cpf'])) {
                $validated['cpf'] = preg_replace('/[^0-9]/', '', $validated['cpf']);
            }

            // Determine password: use provided password, or CPF, or default
            $password = $validated['password'] ?? $validated['cpf'] ?? 'password';

            // Create the user first
            $user = User::create([
                'nome_completo' => $validated['nome_completo'],
                'cpf' => $validated['cpf'] ?? null,
                'email' => $validated['email'] ?? null,
                'telefone' => $validated['telefone'] ?? null,
                'password_hash' => Hash::make($password),
                'ativo' => $validated['ativo'] ?? true,
            ]);

            // Assign the "Responsável Aluno" role to the user
            $user->assignRole('Responsável Aluno');

            // Link the user to the tenant
            $user->tenants()->syncWithoutDetaching([$tenant->id]);

            // Create the parent linked to the user
            Responsavel::create([
                'tenant_id' => $tenant->id,
                'usuario_id' => $user->id,
                'parentesco' => $validated['parentesco'] ?? null,
                'cpf' => $validated['cpf'] ?? null,
                'profissao' => $validated['profissao'] ?? null,
            ]);
        });

        return redirect()
            ->route('school.parents.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Responsável criado',
                'message' => 'O responsável foi cadastrado com sucesso.',
            ]);
    }

    /**
     * Display the specified parent.
     */
    public function show(Responsavel $parent): Response
    {
        $tenant = $this->getTenant();

        if ($parent->tenant_id !== $tenant->id) {
            abort(404);
        }

        $parent->load([
            'user:id,nome_completo,cpf,email,telefone,ativo',
            'students.user:id,nome_completo,cpf,email,telefone',
        ]);

        return Inertia::render('school/parents/Show', [
            'parent' => [
                'id' => $parent->id,
                'nome_completo' => $parent->user?->nome_completo,
                'cpf' => $parent->cpf ?? $parent->user?->cpf ?? null,
                'email' => $parent->user?->email,
                'telefone' => $parent->user?->telefone,
                'parentesco' => $parent->parentesco,
                'profissao' => $parent->profissao,
                'ativo' => $parent->user?->ativo ?? false,
                'students' => $parent->students->map(function ($student) {
                    return [
                        'id' => $student->id,
                        'nome_completo' => $student->user?->nome_completo ?? 'Sem nome',
                        'cpf' => $student->user?->cpf,
                        'email' => $student->user?->email,
                        'telefone' => $student->user?->telefone,
                        'matricula' => $student->matricula,
                        'serie' => $student->serie,
                        'turma' => $student->turma,
                        'ativo' => (bool) $student->ativo,
                    ];
                })->values(),
            ],
        ]);
    }

    /**
     * Show the form for editing the specified parent.
     */
    public function edit(Responsavel $parent): Response
    {
        $tenant = $this->getTenant();

        if ($parent->tenant_id !== $tenant->id) {
            abort(404);
        }

        $parent->load('user:id,nome_completo,cpf,email,telefone,ativo');

        return Inertia::render('school/parents/Edit', [
            'parent' => [
                'id' => $parent->id,
                'nome_completo' => $parent->user?->nome_completo,
                'cpf' => $parent->cpf ?? $parent->user?->cpf ?? null,
                'email' => $parent->user?->email,
                'telefone' => $parent->user?->telefone,
                'parentesco' => $parent->parentesco,
                'profissao' => $parent->profissao,
                'ativo' => $parent->user?->ativo ?? false,
            ],
        ]);
    }

    /**
     * Update the specified parent.
     */
    public function update(UpdateParentRequest $request, Responsavel $parent): RedirectResponse
    {
        $tenant = $this->getTenant();

        if ($parent->tenant_id !== $tenant->id) {
            abort(404);
        }

        $validated = $request->validated();

        DB::transaction(function () use ($parent, $validated) {
            // Update the user (CPF não é atualizado no update, como solicitado)
            $parent->user->update([
                'nome_completo' => $validated['nome_completo'],
                'email' => $validated['email'] ?? null,
                'telefone' => $validated['telefone'] ?? null,
                'ativo' => $validated['ativo'] ?? $parent->user->ativo,
            ]);

            // Update password if provided
            if (! empty($validated['password'])) {
                $parent->user->update([
                    'password_hash' => Hash::make($validated['password']),
                ]);
            }

            // Update the parent
            $parent->update([
                'parentesco' => $validated['parentesco'] ?? null,
                'profissao' => $validated['profissao'] ?? null,
            ]);
        });

        return redirect()
            ->route('school.parents.edit', $parent)
            ->with('toast', [
                'type' => 'success',
                'title' => 'Responsável atualizado',
                'message' => 'As alterações foram salvas com sucesso.',
            ]);
    }
}
