<?php

namespace App\Http\Controllers\School;

use App\Actions\School\CreateStudentAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\School\StoreStudentRequest;
use App\Http\Requests\School\UpdateStudentRequest;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class StudentsController extends Controller
{
    public function __construct(protected CreateStudentAction $createStudentAction) {}

    protected function alunosTable(): string
    {
        return DB::connection('shared')->getDriverName() === 'sqlite'
            ? 'alunos'
            : 'escola.alunos';
    }

    protected function usuariosTable(): string
    {
        return DB::connection('shared')->getDriverName() === 'sqlite'
            ? 'usuarios'
            : 'shared.usuarios';
    }

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

        $connection = DB::connection('shared');
        $driver = $connection->getDriverName();
        $likeOperator = $driver === 'pgsql' ? 'ilike' : 'like';

        $students = $connection
            ->table($this->alunosTable().' as alunos')
            ->join($this->usuariosTable().' as usuarios', 'usuarios.id', '=', 'alunos.usuario_id')
            ->where('alunos.tenant_id', $tenant->id)
            ->when($filters['search'] ?? null, function ($query, string $search) use ($likeOperator) {
                $search = trim($search);
                $cpfSearch = preg_replace('/[^0-9]/', '', $search);

                $query->where(function ($q) use ($search, $cpfSearch, $likeOperator) {
                    $q->where('usuarios.nome_completo', $likeOperator, "%{$search}%")
                        ->orWhere('usuarios.email', $likeOperator, "%{$search}%")
                        ->orWhere('usuarios.telefone', $likeOperator, "%{$search}%")
                        ->orWhere('alunos.matricula', $likeOperator, "%{$search}%")
                        ->orWhere('usuarios.cpf', $likeOperator, "%{$cpfSearch}%");
                });
            })
            ->when(isset($filters['active']) && $filters['active'] !== '' && $filters['active'] !== null, function ($query) use ($filters) {
                $active = filter_var($filters['active'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                if ($active !== null) {
                    $query->where('alunos.ativo', $active);
                }
            })
            ->orderBy('usuarios.nome_completo')
            ->select([
                'alunos.id',
                'alunos.matricula',
                'alunos.ativo',
                'usuarios.nome_completo',
                'usuarios.cpf',
                'usuarios.email',
                'usuarios.telefone',
            ])
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
    public function store(StoreStudentRequest $request): RedirectResponse
    {
        $tenant = $this->getTenant();
        $validated = $request->validated();

        DB::connection('shared')->transaction(function () use ($tenant, $validated) {
            $this->createStudentAction->execute($validated, $tenant);
        });

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

        $student->load(['user', 'parents.user']);

        return Inertia::render('school/students/Show', [
            'student' => [
                'id' => $student->id,
                'nome_completo' => $student->user?->nome_completo,
                'cpf' => $student->user?->cpf,
                'email' => $student->user?->email,
                'telefone' => $student->user?->telefone,
                'matricula' => $student->matricula,
                'serie' => $student->serie,
                'turma' => $student->turma,
                'data_nascimento' => optional($student->data_nascimento)->toDateString(),
                'data_matricula' => optional($student->data_matricula)->toDateString(),
                'informacoes_medicas' => $student->informacoes_medicas,
                'ativo' => (bool) $student->ativo,
                'parents' => $student->parents->map(fn ($parent) => [
                    'id' => $parent->id,
                    'nome_completo' => $parent->user?->nome_completo,
                    'parentesco' => $parent->parentesco,
                ])->values(),
            ],
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

        $student->load(['user', 'parents.user']);

        return Inertia::render('school/students/Edit', [
            'student' => [
                'id' => $student->id,
                'nome_completo' => $student->user?->nome_completo,
                'cpf' => $student->user?->cpf,
                'email' => $student->user?->email,
                'telefone' => $student->user?->telefone,
                'matricula' => $student->matricula,
                'serie' => $student->serie,
                'turma' => $student->turma,
                'data_nascimento' => optional($student->data_nascimento)->toDateString(),
                'data_matricula' => optional($student->data_matricula)->toDateString(),
                'informacoes_medicas' => $student->informacoes_medicas,
                'ativo' => (bool) $student->ativo,
                'parents' => $student->parents->map(fn ($parent) => [
                    'id' => $parent->id,
                    'nome_completo' => $parent->user?->nome_completo,
                    'parentesco' => $parent->parentesco,
                ])->values(),
            ],
        ]);
    }

    /**
     * Update the specified student.
     */
    public function update(UpdateStudentRequest $request, Student $student): RedirectResponse
    {
        $tenant = $this->getTenant();

        if ($student->tenant_id !== $tenant->id) {
            abort(404);
        }

        $validated = $request->validated();

        DB::connection('shared')->transaction(function () use ($tenant, $student, $validated) {
            $user = null;

            if (! empty($student->usuario_id)) {
                $user = User::query()->find($student->usuario_id);
            }

            if (! $user) {
                $user = new User;
                $user->password_hash = Hash::make(! empty($validated['cpf']) ? $validated['cpf'] : Str::random(32));
            }

            $user->nome_completo = $validated['nome_completo'];
            $user->cpf = $validated['cpf'] ?? null;
            $user->email = $validated['email'] ?? null;
            $user->telefone = $validated['telefone'] ?? null;
            $user->ativo = $validated['ativo'] ?? $user->ativo ?? true;
            $user->save();
            $user->tenants()->syncWithoutDetaching([$tenant->id]);

            $student->update([
                'usuario_id' => $user->id,
                'matricula' => $validated['matricula'],
                'serie' => $validated['serie'],
                'turma' => $validated['turma'] ?? null,
                'data_nascimento' => $validated['data_nascimento'] ?? null,
                'data_matricula' => $validated['data_matricula'] ?? null,
                'informacoes_medicas' => $validated['informacoes_medicas'] ?? null,
                'ativo' => $validated['ativo'] ?? true,
            ]);
        });

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
