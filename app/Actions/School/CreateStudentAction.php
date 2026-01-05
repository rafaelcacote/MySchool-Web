<?php

namespace App\Actions\School;

use App\Models\Student;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateStudentAction
{
    public function execute(array $validated, Tenant $tenant): Student
    {
        $cpf = $validated['cpf'] ?? null;
        $email = $validated['email'] ?? null;

        $user = $this->findExistingUser($cpf, $email);
        $userIsNew = ! $user;

        if (! $user) {
            $user = new User;
            $user->password_hash = Hash::make(! empty($cpf) ? $cpf : Str::random(32));
        }

        $user->nome_completo = $validated['nome_completo'];
        $user->cpf = $cpf;
        $user->email = $email;
        $user->telefone = $validated['telefone'] ?? null;
        $user->ativo = $validated['ativo'] ?? $user->ativo ?? true;
        $user->save();

        if ($userIsNew && $this->rolesTableExists($user) && $user->roles()->count() === 0) {
            $user->assignRole('Administrador Escola');
        }

        $user->tenants()->syncWithoutDetaching([$tenant->id]);

        $alreadyStudentInTenant = Student::query()
            ->where('tenant_id', $tenant->id)
            ->where('usuario_id', $user->id)
            ->exists();

        if ($alreadyStudentInTenant) {
            throw ValidationException::withMessages([
                'cpf' => 'Este usuário já está cadastrado como aluno nesta escola.',
            ]);
        }

        return Student::create([
            'tenant_id' => $tenant->id,
            'usuario_id' => $user->id,
            'matricula' => $validated['matricula'],
            'serie' => $validated['serie'],
            'turma' => $validated['turma'] ?? null,
            'data_nascimento' => $validated['data_nascimento'] ?? null,
            'data_matricula' => $validated['data_matricula'] ?? null,
            'informacoes_medicas' => $validated['informacoes_medicas'] ?? null,
            'ativo' => $validated['ativo'] ?? true,
        ]);
    }

    private function findExistingUser(?string $cpf, ?string $email): ?User
    {
        $user = null;

        if (! empty($cpf)) {
            $user = User::query()->where('cpf', $cpf)->first();
        }

        if (! $user && ! empty($email)) {
            $user = User::query()->where('email', $email)->first();
        }

        return $user;
    }

    private function rolesTableExists(User $user): bool
    {
        $rolesTable = config('permission.table_names.roles', 'roles');

        return Schema::connection($user->getConnectionName())->hasTable($rolesTable);
    }
}
