<?php

namespace App\Http\Requests\School;

use App\Models\Turma;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReenrollStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $tenantId = auth()->user()?->tenants()->first()?->id;

        return [
            'turma_id' => [
                'required',
                'string',
                Rule::exists(Turma::class, 'id')
                    ->where('tenant_id', $tenantId)
                    ->where('ativo', true),
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'turma_id.required' => 'Selecione uma turma para rematrícula.',
            'turma_id.exists' => 'A turma selecionada não é válida.',
        ];
    }
}
