<?php

namespace App\Http\Requests\School;

use App\Models\Turma;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExerciseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenantId = auth()->user()?->tenants()->first()?->id;

        return [
            'disciplina' => ['required', 'string', 'max:100'],
            'titulo' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'data_entrega' => ['required', 'date'],
            'anexo_url' => ['nullable', 'url', 'max:2048'],
            'turma_id' => [
                'required',
                'uuid',
                Rule::exists(Turma::class, 'id')
                    ->where('tenant_id', $tenantId)
                    ->whereNull('deleted_at'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'disciplina.required' => 'Informe a disciplina.',
            'disciplina.max' => 'A disciplina não pode ter mais de 100 caracteres.',
            'titulo.required' => 'Informe o título do exercício.',
            'titulo.max' => 'O título não pode ter mais de 255 caracteres.',
            'data_entrega.required' => 'Informe a data de entrega.',
            'data_entrega.date' => 'A data de entrega deve ser uma data válida.',
            'anexo_url.url' => 'A URL do anexo deve ser válida.',
            'anexo_url.max' => 'A URL do anexo não pode ter mais de 2048 caracteres.',
            'turma_id.required' => 'Selecione uma turma.',
            'turma_id.exists' => 'Turma não encontrada.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'anexo_url' => $this->anexo_url === '' ? null : $this->anexo_url,
            'descricao' => $this->descricao === '' ? null : $this->descricao,
        ]);
    }
}
