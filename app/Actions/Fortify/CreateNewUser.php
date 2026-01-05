<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'nome_completo' => ['required', 'string', 'max:255'],
            'cpf' => [
                'required',
                'string',
                'regex:/^[0-9]{11}$|^[0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{2}$/',
                Rule::unique(User::class, 'cpf'),
            ],
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique(User::class, 'email')],
            'password' => $this->passwordRules(),
        ])->validate();

        $cpf = preg_replace('/[^0-9]/', '', $input['cpf']);

        $user = User::create([
            'nome_completo' => $input['nome_completo'],
            'cpf' => $cpf,
            'email' => $input['email'] ?? null,
            'password_hash' => Hash::make($input['password']),
            'ativo' => true,
        ]);

        // Atribuir role padrão "Administrador Geral" para novos usuários registrados
        $user->assignRole('Administrador Geral');

        return $user;
    }
}
