<?php

namespace App\Http\Requests\School;

use Illuminate\Foundation\Http\FormRequest;

class UpdateParentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // User fields
            'nome_completo' => ['required', 'string', 'max:255'],
            'cpf' => ['nullable', 'string', 'regex:/^[0-9]{11}$|^[0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{2}$/'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:6'],

            // Parent fields
            'parentesco' => ['nullable', 'string', 'max:50'],
            'profissao' => ['nullable', 'string', 'max:100'],
            'ativo' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome_completo.required' => 'Informe o nome completo do responsável.',
            'nome_completo.max' => 'O nome completo não pode ter mais de 255 caracteres.',
            'cpf.regex' => 'O CPF deve estar no formato 000.000.000-00 ou apenas números.',
            'email.email' => 'Informe um e-mail válido.',
            'parentesco.max' => 'O parentesco não pode ter mais de 50 caracteres.',
            'profissao.max' => 'A profissão não pode ter mais de 100 caracteres.',
            'password.min' => 'A senha deve ter no mínimo 6 caracteres.',
        ];
    }
}
