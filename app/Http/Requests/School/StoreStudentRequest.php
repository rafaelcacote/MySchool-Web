<?php

namespace App\Http\Requests\School;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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
            'nome_completo' => ['required', 'string', 'max:255'],
            'cpf' => [
                'required_without:email',
                'nullable',
                'string',
                'regex:/^[0-9]{11}$/',
            ],
            'data_nascimento' => ['nullable', 'date'],
            'data_matricula' => ['nullable', 'date'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'email' => ['required_without:cpf', 'nullable', 'string', 'email', 'max:255'],
            'matricula' => [
                'required',
                'string',
                'max:50',
                Rule::unique(Student::class, 'matricula')->where(fn ($query) => $query->where('tenant_id', $tenantId)),
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
            'email.required_without' => 'Informe CPF ou e-mail.',
            'matricula.required' => 'Informe a matrícula.',
            'matricula.unique' => 'Já existe um aluno com esta matrícula nesta escola.',
            'serie.required' => 'Informe a série.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $tenantId = auth()->user()?->tenants()->first()?->id;

            if (! $tenantId) {
                return;
            }

            $cpf = $this->input('cpf');
            $email = $this->input('email');

            $userByCpf = null;
            $userByEmail = null;

            if (is_string($cpf) && $cpf !== '') {
                $userByCpf = User::query()->where('cpf', $cpf)->first();
            }

            if (is_string($email) && $email !== '') {
                $userByEmail = User::query()->where('email', $email)->first();
            }

            if ($userByCpf && $userByEmail && $userByCpf->id !== $userByEmail->id) {
                $validator->errors()->add('cpf', 'O CPF informado pertence a outro usuário. Verifique os dados e tente novamente.');
                $validator->errors()->add('email', 'O e-mail informado pertence a outro usuário. Verifique os dados e tente novamente.');

                return;
            }

            $user = $userByCpf ?? $userByEmail;

            if (! $user) {
                return;
            }

            $alreadyStudentInTenant = Student::query()
                ->where('tenant_id', $tenantId)
                ->where('usuario_id', $user->id)
                ->exists();

            if ($alreadyStudentInTenant) {
                $field = ! empty($cpf) ? 'cpf' : 'email';
                $validator->errors()->add($field, 'Este usuário já está cadastrado como aluno nesta escola.');
            }
        });
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
