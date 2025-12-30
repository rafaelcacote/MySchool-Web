<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeachersController extends Controller
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
     * Display a listing of the teachers.
     */
    public function index(Request $request): Response
    {
        $tenant = $this->getTenant();
        $filters = $request->only(['search', 'active']);

        $teachers = Teacher::query()
            ->where('tenant_id', $tenant->id)
            ->when($filters['search'] ?? null, function ($query, string $search) {
                $search = trim($search);
                $cpfSearch = preg_replace('/[^0-9]/', '', $search);
                $query->where(function ($q) use ($search, $cpfSearch) {
                    $q->where('nome_completo', 'ilike', "%{$search}%")
                        ->orWhere('email', 'ilike', "%{$search}%")
                        ->orWhere('telefone', 'ilike', "%{$search}%")
                        ->orWhere('cpf', 'ilike', "%{$cpfSearch}%");
                });
            })
            ->when(isset($filters['active']) && $filters['active'] !== '' && $filters['active'] !== null, function ($query) use ($filters) {
                $active = filter_var($filters['active'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                if ($active !== null) {
                    $query->where('ativo', $active);
                }
            })
            ->orderBy('nome_completo')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('school/teachers/Index', [
            'teachers' => $teachers,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new teacher.
     */
    public function create(): Response
    {
        return Inertia::render('school/teachers/Create');
    }

    /**
     * Store a newly created teacher.
     */
    public function store(Request $request): RedirectResponse
    {
        $tenant = $this->getTenant();

        $validated = $request->validate([
            'nome_completo' => ['required', 'string', 'max:255'],
            'cpf' => ['nullable', 'string', 'regex:/^[0-9]{11}$|^[0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{2}$/'],
            'data_nascimento' => ['nullable', 'date'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'endereco' => ['nullable', 'string'],
            'endereco_numero' => ['nullable', 'string', 'max:20'],
            'endereco_complemento' => ['nullable', 'string', 'max:100'],
            'endereco_bairro' => ['nullable', 'string', 'max:100'],
            'endereco_cep' => ['nullable', 'string', 'regex:/^[0-9]{8}$|^[0-9]{5}-[0-9]{3}$/', 'max:10'],
            'endereco_cidade' => ['nullable', 'string', 'max:100'],
            'endereco_estado' => ['nullable', 'string', 'max:2'],
            'endereco_pais' => ['nullable', 'string', 'max:50'],
            'formacao' => ['nullable', 'string', 'max:255'],
            'especializacao' => ['nullable', 'string', 'max:255'],
            'ativo' => ['nullable', 'boolean'],
            'observacoes' => ['nullable', 'string'],
        ]);

        // Remove formatação do CPF
        if (!empty($validated['cpf'])) {
            $validated['cpf'] = preg_replace('/[^0-9]/', '', $validated['cpf']);
        }

        // Remove formatação do CEP
        if (!empty($validated['endereco_cep'])) {
            $validated['endereco_cep'] = preg_replace('/[^0-9]/', '', $validated['endereco_cep']);
        }

        $teacher = Teacher::create([
            ...$validated,
            'tenant_id' => $tenant->id,
            'ativo' => $validated['ativo'] ?? true,
        ]);

        return redirect()
            ->route('school.teachers.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Professor criado',
                'message' => 'O professor foi cadastrado com sucesso.',
            ]);
    }

    /**
     * Display the specified teacher.
     */
    public function show(Teacher $teacher): Response
    {
        $tenant = $this->getTenant();

        if ($teacher->tenant_id !== $tenant->id) {
            abort(404);
        }

        return Inertia::render('school/teachers/Show', [
            'teacher' => $teacher,
        ]);
    }
}

