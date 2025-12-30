<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class StudentsController extends Controller
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
     * Display a listing of the students.
     */
    public function index(Request $request): Response
    {
        $tenant = $this->getTenant();
        $filters = $request->only(['search', 'active']);

        $students = Student::query()
            ->where('tenant_id', $tenant->id)
            ->when($filters['search'] ?? null, function ($query, string $search) {
                $search = trim($search);
                $cpfSearch = preg_replace('/[^0-9]/', '', $search);
                $query->where(function ($q) use ($search, $cpfSearch) {
                    $q->where('nome_completo', 'ilike', "%{$search}%")
                        ->orWhere('email', 'ilike', "%{$search}%")
                        ->orWhere('telefone', 'ilike', "%{$search}%")
                        ->orWhere('matricula', 'ilike', "%{$search}%")
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

        return Inertia::render('school/students/Index', [
            'students' => $students,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new student.
     */
    public function create(): Response
    {
        return Inertia::render('school/students/Create');
    }

    /**
     * Store a newly created student.
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
            'matricula' => ['nullable', 'string', 'max:100'],
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

        $student = Student::create([
            ...$validated,
            'tenant_id' => $tenant->id,
            'ativo' => $validated['ativo'] ?? true,
        ]);

        return redirect()
            ->route('school.students.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Aluno criado',
                'message' => 'O aluno foi cadastrado com sucesso.',
            ]);
    }

    /**
     * Display the specified student.
     */
    public function show(Student $student): Response
    {
        $tenant = $this->getTenant();

        if ($student->tenant_id !== $tenant->id) {
            abort(404);
        }

        $student->load('parents');

        return Inertia::render('school/students/Show', [
            'student' => $student,
        ]);
    }

    /**
     * Show the form for editing the specified student.
     */
    public function edit(Student $student): Response
    {
        $tenant = $this->getTenant();

        if ($student->tenant_id !== $tenant->id) {
            abort(404);
        }

        $student->load('parents');

        return Inertia::render('school/students/Edit', [
            'student' => $student,
        ]);
    }

    /**
     * Update the specified student.
     */
    public function update(Request $request, Student $student): RedirectResponse
    {
        $tenant = $this->getTenant();

        if ($student->tenant_id !== $tenant->id) {
            abort(404);
        }

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
            'matricula' => ['nullable', 'string', 'max:100'],
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

        $student->update($validated);

        return redirect()
            ->route('school.students.edit', $student)
            ->with('toast', [
                'type' => 'success',
                'title' => 'Aluno atualizado',
                'message' => 'As alterações foram salvas com sucesso.',
            ]);
    }

    /**
     * Remove the specified student.
     */
    public function destroy(Student $student): RedirectResponse
    {
        $tenant = $this->getTenant();

        if ($student->tenant_id !== $tenant->id) {
            abort(404);
        }

        $student->delete();

        return redirect()
            ->route('school.students.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Aluno excluído',
                'message' => 'O aluno foi removido com sucesso.',
            ]);
    }
}

