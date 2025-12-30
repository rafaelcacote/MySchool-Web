<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PlansController extends Controller
{
    /**
     * Display a listing of the plans.
     */
    public function index(Request $request): Response
    {
        $filters = $request->only(['search', 'active']);

        $plans = Plan::query()
            ->when($filters['search'] ?? null, function ($query, string $search) {
                $search = trim($search);
                $query->where(function ($q) use ($search) {
                    $q->where('nome', 'ilike', "%{$search}%")
                        ->orWhere('descricao', 'ilike', "%{$search}%");
                });
            })
            ->when(isset($filters['active']) && $filters['active'] !== '' && $filters['active'] !== null, function ($query) use ($filters) {
                $active = filter_var($filters['active'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                if ($active !== null) {
                    $query->where('ativo', $active);
                }
            })
            ->orderBy('nome')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('plans/Index', [
            'plans' => $plans,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new plan.
     */
    public function create(): Response
    {
        return Inertia::render('plans/Create');
    }

    /**
     * Store a newly created plan.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string', 'max:1000'],
            'preco_mensal' => ['required', 'numeric', 'min:0'],
            'preco_anual' => ['nullable', 'numeric', 'min:0'],
            'ativo' => ['nullable', 'boolean'],
            'max_alunos' => ['nullable', 'integer', 'min:0'],
            'max_professores' => ['nullable', 'integer', 'min:0'],
            'max_armazenamento_mb' => ['nullable', 'integer', 'min:0'],
            'caracteristicas' => ['nullable', 'array'],
        ]);

        $plan = new Plan();
        $plan->nome = $validated['nome'];
        $plan->descricao = $validated['descricao'] ?? null;
        $plan->preco_mensal = $validated['preco_mensal'];
        $plan->preco_anual = $validated['preco_anual'] ?? null;
        $plan->ativo = isset($validated['ativo']) 
            ? filter_var($validated['ativo'], FILTER_VALIDATE_BOOLEAN) 
            : true;
        $plan->max_alunos = $validated['max_alunos'] ?? null;
        $plan->max_professores = $validated['max_professores'] ?? null;
        $plan->max_armazenamento_mb = $validated['max_armazenamento_mb'] ?? null;
        $plan->caracteristicas = $validated['caracteristicas'] ?? null;
        $plan->save();

        return redirect()
            ->route('plans.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Plano criado',
                'message' => 'O plano foi cadastrado com sucesso.',
            ]);
    }

    /**
     * Show the form for editing the specified plan.
     */
    public function edit(Plan $plan): Response
    {
        return Inertia::render('plans/Edit', [
            'plan' => $plan,
        ]);
    }

    /**
     * Update the specified plan.
     */
    public function update(Request $request, Plan $plan): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string', 'max:1000'],
            'preco_mensal' => ['required', 'numeric', 'min:0'],
            'preco_anual' => ['nullable', 'numeric', 'min:0'],
            'ativo' => ['nullable', 'boolean'],
            'max_alunos' => ['nullable', 'integer', 'min:0'],
            'max_professores' => ['nullable', 'integer', 'min:0'],
            'max_armazenamento_mb' => ['nullable', 'integer', 'min:0'],
            'caracteristicas' => ['nullable', 'array'],
        ]);

        $plan->nome = $validated['nome'];
        $plan->descricao = $validated['descricao'] ?? null;
        $plan->preco_mensal = $validated['preco_mensal'];
        $plan->preco_anual = $validated['preco_anual'] ?? null;
        $plan->ativo = isset($validated['ativo']) 
            ? filter_var($validated['ativo'], FILTER_VALIDATE_BOOLEAN) 
            : $plan->ativo;
        $plan->max_alunos = $validated['max_alunos'] ?? null;
        $plan->max_professores = $validated['max_professores'] ?? null;
        $plan->max_armazenamento_mb = $validated['max_armazenamento_mb'] ?? null;
        $plan->caracteristicas = $validated['caracteristicas'] ?? null;
        $plan->save();

        return redirect()
            ->route('plans.edit', $plan)
            ->with('toast', [
                'type' => 'success',
                'title' => 'Plano atualizado',
                'message' => 'As alterações foram salvas com sucesso.',
            ]);
    }
}

