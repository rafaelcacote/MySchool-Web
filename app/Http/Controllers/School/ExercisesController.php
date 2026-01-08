<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Http\Requests\School\StoreExerciseRequest;
use App\Http\Requests\School\UpdateExerciseRequest;
use App\Models\Exercise;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ExercisesController extends Controller
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
     * Get the current teacher from the authenticated user.
     */
    protected function getCurrentTeacher()
    {
        $user = auth()->user();
        $tenant = $this->getTenant();

        $teacher = Teacher::query()
            ->where('tenant_id', $tenant->id)
            ->where('usuario_id', $user->id)
            ->where('ativo', true)
            ->first();

        if (! $teacher) {
            abort(403, 'Acesso negado. Você precisa ser um professor para acessar esta área.');
        }

        return $teacher;
    }

    /**
     * Display a listing of the exercises.
     */
    public function index(Request $request): Response
    {
        $tenant = $this->getTenant();
        $teacher = $this->getCurrentTeacher();
        $filters = $request->only(['search', 'turma_id', 'disciplina']);

        $exercises = Exercise::query()
            ->where('tenant_id', $tenant->id)
            ->where('professor_id', $teacher->id)
            ->with(['turma:id,nome,serie,turma_letra,ano_letivo'])
            ->when($filters['search'] ?? null, function ($query, string $search) {
                $search = trim($search);
                $query->where(function ($q) use ($search) {
                    $q->where('titulo', 'ilike', "%{$search}%")
                        ->orWhere('disciplina', 'ilike', "%{$search}%")
                        ->orWhere('descricao', 'ilike', "%{$search}%");
                });
            })
            ->when($filters['turma_id'] ?? null, function ($query, string $turmaId) {
                $query->where('turma_id', $turmaId);
            })
            ->when($filters['disciplina'] ?? null, function ($query, string $disciplina) {
                $query->where('disciplina', $disciplina);
            })
            ->orderBy('data_entrega', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString()
            ->through(function (Exercise $exercise) {
                return [
                    'id' => $exercise->id,
                    'titulo' => $exercise->titulo,
                    'disciplina' => $exercise->disciplina,
                    'data_entrega' => $exercise->data_entrega->format('d/m/Y'),
                    'turma' => $exercise->turma
                        ? [
                            'id' => $exercise->turma->id,
                            'nome' => $exercise->turma->nome,
                            'serie' => $exercise->turma->serie,
                            'turma_letra' => $exercise->turma->turma_letra,
                            'ano_letivo' => $exercise->turma->ano_letivo,
                        ]
                        : null,
                ];
            });

        $turmas = \App\Models\Turma::query()
            ->where('tenant_id', $tenant->id)
            ->where('professor_id', $teacher->id)
            ->where('ativo', true)
            ->orderBy('nome')
            ->get()
            ->map(function ($turma) {
                return [
                    'id' => $turma->id,
                    'nome' => $turma->nome,
                ];
            });

        $disciplinas = Exercise::query()
            ->where('tenant_id', $tenant->id)
            ->where('professor_id', $teacher->id)
            ->distinct()
            ->pluck('disciplina')
            ->sort()
            ->values();

        return Inertia::render('school/exercises/Index', [
            'exercises' => $exercises,
            'turmas' => $turmas,
            'disciplinas' => $disciplinas,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new exercise.
     */
    public function create(): Response
    {
        $tenant = $this->getTenant();
        $teacher = $this->getCurrentTeacher();

        $turmas = \App\Models\Turma::query()
            ->where('tenant_id', $tenant->id)
            ->where('professor_id', $teacher->id)
            ->where('ativo', true)
            ->orderBy('nome')
            ->get()
            ->map(function ($turma) {
                return [
                    'id' => $turma->id,
                    'nome' => $turma->nome,
                    'serie' => $turma->serie,
                    'ano_letivo' => $turma->ano_letivo,
                ];
            });

        return Inertia::render('school/exercises/Create', [
            'turmas' => $turmas,
        ]);
    }

    /**
     * Store a newly created exercise.
     */
    public function store(StoreExerciseRequest $request): RedirectResponse
    {
        $tenant = $this->getTenant();
        $teacher = $this->getCurrentTeacher();
        $validated = $request->validated();

        Exercise::create([
            ...$validated,
            'tenant_id' => $tenant->id,
            'professor_id' => $teacher->id,
        ]);

        return redirect()
            ->route('school.exercises.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Exercício criado',
                'message' => 'O exercício foi cadastrado com sucesso.',
            ]);
    }

    /**
     * Display the specified exercise.
     */
    public function show(Exercise $exercise): Response
    {
        $tenant = $this->getTenant();
        $teacher = $this->getCurrentTeacher();

        if ($exercise->tenant_id !== $tenant->id || $exercise->professor_id !== $teacher->id) {
            abort(404);
        }

        $exercise->load(['turma:id,nome,serie,ano_letivo', 'professor.usuario:id,nome_completo']);

        return Inertia::render('school/exercises/Show', [
            'exercise' => [
                'id' => $exercise->id,
                'disciplina' => $exercise->disciplina,
                'titulo' => $exercise->titulo,
                'descricao' => $exercise->descricao,
                'data_entrega' => $exercise->data_entrega->format('Y-m-d'),
                'data_entrega_formatted' => $exercise->data_entrega->format('d/m/Y'),
                'anexo_url' => $exercise->anexo_url,
                'turma' => $exercise->turma
                    ? [
                        'id' => $exercise->turma->id,
                        'nome' => $exercise->turma->nome,
                        'serie' => $exercise->turma->serie,
                        'ano_letivo' => $exercise->turma->ano_letivo,
                    ]
                    : null,
                'professor' => $exercise->professor
                    ? [
                        'id' => $exercise->professor->id,
                        'usuario' => $exercise->professor->usuario
                            ? [
                                'nome_completo' => $exercise->professor->usuario->nome_completo,
                            ]
                            : null,
                    ]
                    : null,
            ],
        ]);
    }

    /**
     * Show the form for editing the specified exercise.
     */
    public function edit(Exercise $exercise): Response
    {
        $tenant = $this->getTenant();
        $teacher = $this->getCurrentTeacher();

        if ($exercise->tenant_id !== $tenant->id || $exercise->professor_id !== $teacher->id) {
            abort(404);
        }

        $turmas = \App\Models\Turma::query()
            ->where('tenant_id', $tenant->id)
            ->where('professor_id', $teacher->id)
            ->where('ativo', true)
            ->orderBy('nome')
            ->get()
            ->map(function ($turma) {
                return [
                    'id' => $turma->id,
                    'nome' => $turma->nome,
                    'serie' => $turma->serie,
                    'ano_letivo' => $turma->ano_letivo,
                ];
            });

        return Inertia::render('school/exercises/Edit', [
            'exercise' => [
                'id' => $exercise->id,
                'disciplina' => $exercise->disciplina,
                'titulo' => $exercise->titulo,
                'descricao' => $exercise->descricao,
                'data_entrega' => $exercise->data_entrega->format('Y-m-d'),
                'anexo_url' => $exercise->anexo_url,
                'turma_id' => $exercise->turma_id,
            ],
            'turmas' => $turmas,
        ]);
    }

    /**
     * Update the specified exercise.
     */
    public function update(UpdateExerciseRequest $request, Exercise $exercise): RedirectResponse
    {
        $tenant = $this->getTenant();
        $teacher = $this->getCurrentTeacher();

        if ($exercise->tenant_id !== $tenant->id || $exercise->professor_id !== $teacher->id) {
            abort(404);
        }

        $validated = $request->validated();

        $exercise->update($validated);

        return redirect()
            ->route('school.exercises.edit', $exercise)
            ->with('toast', [
                'type' => 'success',
                'title' => 'Exercício atualizado',
                'message' => 'As alterações foram salvas com sucesso.',
            ]);
    }

    /**
     * Remove the specified exercise.
     */
    public function destroy(Exercise $exercise): RedirectResponse
    {
        $tenant = $this->getTenant();
        $teacher = $this->getCurrentTeacher();

        if ($exercise->tenant_id !== $tenant->id || $exercise->professor_id !== $teacher->id) {
            abort(404);
        }

        $exercise->delete();

        return redirect()
            ->route('school.exercises.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Exercício excluído',
                'message' => 'O exercício foi removido com sucesso.',
            ]);
    }
}
