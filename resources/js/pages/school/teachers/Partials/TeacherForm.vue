<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Save } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';

interface Teacher {
    id?: string;
    nome_completo?: string;
    cpf?: string | null;
    data_nascimento?: string | null;
    telefone?: string | null;
    email?: string | null;
    endereco?: string | null;
    endereco_numero?: string | null;
    endereco_complemento?: string | null;
    endereco_bairro?: string | null;
    endereco_cep?: string | null;
    endereco_cidade?: string | null;
    endereco_estado?: string | null;
    endereco_pais?: string | null;
    formacao?: string | null;
    especializacao?: string | null;
    ativo?: boolean;
    observacoes?: string | null;
}

const props = defineProps<{
    teacher?: Teacher;
    submitLabel: string;
    processing: boolean;
    errors: Record<string, string>;
}>();

const phoneDisplay = ref('');
const cpfDisplay = ref('');
const cepDisplay = ref('');

function formatCPF(value: string): string {
    const numbers = value.replace(/\D/g, '');
    const limitedNumbers = numbers.slice(0, 11);
    if (limitedNumbers.length <= 3) {
        return limitedNumbers;
    } else if (limitedNumbers.length <= 6) {
        return `${limitedNumbers.slice(0, 3)}.${limitedNumbers.slice(3)}`;
    } else if (limitedNumbers.length <= 9) {
        return `${limitedNumbers.slice(0, 3)}.${limitedNumbers.slice(3, 6)}.${limitedNumbers.slice(6)}`;
    } else {
        return `${limitedNumbers.slice(0, 3)}.${limitedNumbers.slice(3, 6)}.${limitedNumbers.slice(6, 9)}-${limitedNumbers.slice(9, 11)}`;
    }
}

function handleCPFInput(value: string | number) {
    const numbers = String(value).replace(/\D/g, '');
    const limitedNumbers = numbers.slice(0, 11);
    cpfDisplay.value = formatCPF(limitedNumbers);
    const hiddenInput = document.querySelector('input[name="cpf"]') as HTMLInputElement;
    if (hiddenInput) {
        hiddenInput.value = limitedNumbers;
    }
}

function formatPhone(value: string): string {
    const numbers = value.replace(/\D/g, '');
    if (numbers.length <= 2) {
        return numbers.length > 0 ? `(${numbers}` : '';
    } else if (numbers.length <= 6) {
        return `(${numbers.slice(0, 2)}) ${numbers.slice(2)}`;
    } else if (numbers.length <= 10) {
        return `(${numbers.slice(0, 2)}) ${numbers.slice(2, 6)}-${numbers.slice(6)}`;
    } else {
        return `(${numbers.slice(0, 2)}) ${numbers.slice(2, 7)}-${numbers.slice(7, 11)}`;
    }
}

function handlePhoneInput(value: string | number) {
    const numbers = String(value).replace(/\D/g, '');
    const limitedNumbers = numbers.slice(0, 11);
    phoneDisplay.value = formatPhone(limitedNumbers);
    const hiddenInput = document.querySelector('input[name="telefone"]') as HTMLInputElement;
    if (hiddenInput) {
        hiddenInput.value = limitedNumbers;
    }
}

function formatCEP(value: string): string {
    const numbers = value.replace(/\D/g, '');
    const limitedNumbers = numbers.slice(0, 8);
    if (limitedNumbers.length <= 5) {
        return limitedNumbers;
    } else {
        return `${limitedNumbers.slice(0, 5)}-${limitedNumbers.slice(5, 8)}`;
    }
}

function handleCEPInput(value: string | number) {
    const numbers = String(value).replace(/\D/g, '');
    const limitedNumbers = numbers.slice(0, 8);
    cepDisplay.value = formatCEP(limitedNumbers);
    const hiddenInput = document.querySelector('input[name="endereco_cep"]') as HTMLInputElement;
    if (hiddenInput) {
        hiddenInput.value = limitedNumbers;
    }
}

onMounted(() => {
    if (props.teacher?.telefone) {
        phoneDisplay.value = formatPhone(props.teacher.telefone);
    }
    if (props.teacher?.cpf) {
        cpfDisplay.value = formatCPF(props.teacher.cpf);
    }
    if (props.teacher?.endereco_cep) {
        cepDisplay.value = formatCEP(props.teacher.endereco_cep);
    }
});
</script>

<template>
    <div class="grid gap-6">
        <div class="grid gap-6 sm:grid-cols-2">
            <div class="grid gap-2">
                <Label for="nome_completo">Nome completo</Label>
                <Input
                    id="nome_completo"
                    name="nome_completo"
                    :default-value="teacher?.nome_completo ?? ''"
                    placeholder="Ex: Maria Silva"
                    required
                    autocomplete="name"
                />
                <InputError :message="errors.nome_completo" />
            </div>

            <div class="grid gap-2">
                <Label for="cpf">CPF</Label>
                <div class="relative">
                    <Input
                        id="cpf"
                        :model-value="cpfDisplay"
                        placeholder="000.000.000-00"
                        autocomplete="off"
                        @update:model-value="handleCPFInput"
                    />
                    <input
                        type="hidden"
                        name="cpf"
                        :value="cpfDisplay.replace(/\D/g, '')"
                    />
                </div>
                <InputError :message="errors.cpf" />
            </div>
        </div>

        <div class="grid gap-6 sm:grid-cols-2">
            <div class="grid gap-2">
                <Label for="data_nascimento">Data de nascimento</Label>
                <Input
                    id="data_nascimento"
                    name="data_nascimento"
                    type="date"
                    :default-value="teacher?.data_nascimento ?? ''"
                />
                <InputError :message="errors.data_nascimento" />
            </div>

            <div class="grid gap-2">
                <Label for="formacao">Formação</Label>
                <Input
                    id="formacao"
                    name="formacao"
                    :default-value="teacher?.formacao ?? ''"
                    placeholder="Ex: Licenciatura em Matemática"
                />
                <InputError :message="errors.formacao" />
            </div>
        </div>

        <div class="grid gap-6 sm:grid-cols-2">
            <div class="grid gap-2">
                <Label for="especializacao">Especialização</Label>
                <Input
                    id="especializacao"
                    name="especializacao"
                    :default-value="teacher?.especializacao ?? ''"
                    placeholder="Ex: Educação Especial"
                />
                <InputError :message="errors.especializacao" />
            </div>
        </div>

        <div class="grid gap-6 sm:grid-cols-2">
            <div class="grid gap-2">
                <Label for="email">E-mail</Label>
                <Input
                    id="email"
                    name="email"
                    type="email"
                    :default-value="teacher?.email ?? ''"
                    placeholder="maria@exemplo.com"
                    autocomplete="email"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-2">
                <Label for="telefone">Telefone</Label>
                <div class="relative">
                    <Input
                        id="telefone"
                        :model-value="phoneDisplay"
                        placeholder="(11) 99999-9999 ou (11) 3333-4444"
                        autocomplete="tel"
                        @update:model-value="handlePhoneInput"
                    />
                    <input
                        type="hidden"
                        name="telefone"
                        :value="phoneDisplay.replace(/\D/g, '')"
                    />
                </div>
                <InputError :message="errors.telefone" />
            </div>
        </div>

        <div class="grid gap-2">
            <Label for="endereco">Endereço</Label>
            <Input
                id="endereco"
                name="endereco"
                :default-value="teacher?.endereco ?? ''"
                placeholder="Rua, Avenida, etc."
            />
            <InputError :message="errors.endereco" />
        </div>

        <div class="grid gap-6 sm:grid-cols-3">
            <div class="grid gap-2">
                <Label for="endereco_numero">Número</Label>
                <Input
                    id="endereco_numero"
                    name="endereco_numero"
                    :default-value="teacher?.endereco_numero ?? ''"
                    placeholder="123"
                />
                <InputError :message="errors.endereco_numero" />
            </div>

            <div class="grid gap-2">
                <Label for="endereco_complemento">Complemento</Label>
                <Input
                    id="endereco_complemento"
                    name="endereco_complemento"
                    :default-value="teacher?.endereco_complemento ?? ''"
                    placeholder="Apto, Bloco, etc."
                />
                <InputError :message="errors.endereco_complemento" />
            </div>

            <div class="grid gap-2">
                <Label for="endereco_bairro">Bairro</Label>
                <Input
                    id="endereco_bairro"
                    name="endereco_bairro"
                    :default-value="teacher?.endereco_bairro ?? ''"
                    placeholder="Centro"
                />
                <InputError :message="errors.endereco_bairro" />
            </div>
        </div>

        <div class="grid gap-6 sm:grid-cols-3">
            <div class="grid gap-2">
                <Label for="endereco_cep">CEP</Label>
                <div class="relative">
                    <Input
                        id="endereco_cep"
                        :model-value="cepDisplay"
                        placeholder="00000-000"
                        autocomplete="postal-code"
                        @update:model-value="handleCEPInput"
                    />
                    <input
                        type="hidden"
                        name="endereco_cep"
                        :value="cepDisplay.replace(/\D/g, '')"
                    />
                </div>
                <InputError :message="errors.endereco_cep" />
            </div>

            <div class="grid gap-2">
                <Label for="endereco_cidade">Cidade</Label>
                <Input
                    id="endereco_cidade"
                    name="endereco_cidade"
                    :default-value="teacher?.endereco_cidade ?? ''"
                    placeholder="São Paulo"
                />
                <InputError :message="errors.endereco_cidade" />
            </div>

            <div class="grid gap-2">
                <Label for="endereco_estado">Estado</Label>
                <Input
                    id="endereco_estado"
                    name="endereco_estado"
                    :default-value="teacher?.endereco_estado ?? ''"
                    placeholder="SP"
                    maxlength="2"
                />
                <InputError :message="errors.endereco_estado" />
            </div>
        </div>

        <div class="grid gap-6 sm:grid-cols-2">
            <div class="grid gap-2">
                <Label for="ativo">Status</Label>
                <label
                    class="flex h-10 items-center gap-2 rounded-md border border-input bg-background px-3 text-sm"
                >
                    <input
                        type="hidden"
                        name="ativo"
                        :value="teacher?.ativo === false ? '0' : '1'"
                    />
                    <input
                        id="ativo"
                        type="checkbox"
                        name="_ativo_toggle"
                        class="h-4 w-4 rounded border border-input"
                        :checked="teacher?.ativo !== false"
                        @change="
                            (e) => {
                                const checked = (e.target as HTMLInputElement).checked;
                                const hidden = (e.currentTarget as HTMLInputElement)
                                    .closest('label')
                                    ?.querySelector('input[type=hidden][name=ativo]') as HTMLInputElement | null;
                                if (hidden) hidden.value = checked ? '1' : '0';
                            }
                        "
                    />
                    <span class="text-muted-foreground">
                        {{ teacher?.ativo === false ? 'Inativo' : 'Ativo' }}
                    </span>
                </label>
                <InputError :message="errors.ativo" />
            </div>
        </div>

        <div class="grid gap-2">
            <Label for="observacoes">Observações</Label>
            <textarea
                id="observacoes"
                name="observacoes"
                rows="3"
                class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                :default-value="teacher?.observacoes ?? ''"
                placeholder="Observações adicionais sobre o professor..."
            />
            <InputError :message="errors.observacoes" />
        </div>

        <div class="flex items-center justify-end gap-2">
            <Button type="submit" :disabled="processing" class="flex items-center gap-2">
                <Save class="h-4 w-4" />
                {{ submitLabel }}
            </Button>
        </div>
    </div>
</template>

