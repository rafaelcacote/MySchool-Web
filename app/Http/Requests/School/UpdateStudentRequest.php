<?php

namespace App\Http\Requests\School;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $studentParam = $this->route('student');

        /** @var \App\Models\Student|null $student */
        $student = $studentParam instanceof Student
            ? $studentParam
            : Student::query()->find($studentParam);
        $tenantId = auth()->user()?->tenants()->first()?->id;

        $usuarioId = $student?->usuario_id;

        return [
            'nome_completo' => ['required', 'string', 'max:255'],
            'cpf' => [
                'required_without:email',
                'nullable',
                'string',
                'regex:/^[0-9]{11}$/',
                Rule::unique(User::class, 'cpf')->ignore($usuarioId, 'id'),
            ],
            'data_nascimento' => ['nullable', 'date'],
            'data_matricula' => ['nullable', 'date'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'email' => ['required_without:cpf', 'nullable', 'string', 'email', 'max:255', Rule::unique(User::class, 'email')->ignore($usuarioId, 'id')],
            'matricula' => [
                'required',
                'string',
                'max:50',
                Rule::unique(Student::class, 'matricula')
                    ->where(fn ($query) => $query->where('tenant_id', $tenantId))
                    ->ignore($student?->id, 'id'),
            ],
            'serie' => ['required', 'string', 'max:50'],
            'turma' => ['nullable', 'string', 'max:10'],
            'ativo' => ['nullable', 'boolean'],
            'informacoes_medicas' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'cpf.required_without' => 'Informe CPF ou e-mail.',
            'cpf.regex' => 'O CPF deve conter 11 dígitos (somente números).',
            'cpf.unique' => 'Este CPF já está cadastrado para outro usuário.',
            'email.required_without' => 'Informe CPF ou e-mail.',
            'email.unique' => 'Este e-mail já está cadastrado para outro usuário.',
            'matricula.required' => 'Informe a matrícula.',
            'matricula.unique' => 'Já existe um aluno com esta matrícula nesta escola.',
            'serie.required' => 'Informe a série.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $cpf = $this->input('cpf');
        $telefone = $this->input('telefone');

        $this->merge([
            'cpf' => is_string($cpf) ? preg_replace('/[^0-9]/', '', $cpf) : $cpf,
            'telefone' => is_string($telefone) ? preg_replace('/[^0-9]/', '', $telefone) : $telefone,
        ]);
    }
}
