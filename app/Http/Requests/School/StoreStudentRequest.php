<?php

namespace App\Http\Requests\School;

use App\Models\Turma;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenantId = auth()->user()?->tenants()->first()?->id;

        return [
            'nome' => ['required', 'string', 'max:255'],
            'nome_social' => ['nullable', 'string', 'max:255'],
            'foto_url' => ['nullable', 'string', 'max:2048', 'url'],
            'turma_id' => [
                'required',
                'string',
                Rule::exists(Turma::class, 'id')->where('tenant_id', $tenantId)->where('ativo', true),
            ],
            'data_nascimento' => ['nullable', 'date'],
            'ativo' => ['nullable', 'boolean'],
            'informacoes_medicas' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'Informe o nome do aluno.',
            'turma_id.required' => 'Selecione uma turma.',
            'turma_id.exists' => 'A turma selecionada não é válida.',
            'foto_url.url' => 'A URL da foto deve ser válida.',
        ];
    }
}
