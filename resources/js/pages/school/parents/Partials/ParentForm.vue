<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Save } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';

interface Parent {
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
    parentesco?: string | null;
    ativo?: boolean;
    observacoes?: string | null;
}

const props = defineProps<{
    parent?: Parent;
    submitLabel: string;
    processing: boolean;
    errors: Record<string, string>;
}>();

const phoneDisplay = ref('');
const cpfDisplay = ref('');

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

onMounted(() => {
    if (props.parent?.telefone) {
        phoneDisplay.value = formatPhone(props.parent.telefone);
    }
    if (props.parent?.cpf) {
        cpfDisplay.value = formatCPF(props.parent.cpf);
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
                    :default-value="parent?.nome_completo ?? ''"
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
                    :default-value="parent?.data_nascimento ?? ''"
                />
                <InputError :message="errors.data_nascimento" />
            </div>

            <div class="grid gap-2">
                <Label for="parentesco">Parentesco</Label>
                <Input
                    id="parentesco"
                    name="parentesco"
                    :default-value="parent?.parentesco ?? ''"
                    placeholder="Ex: Pai, Mãe, Avô, etc."
                />
                <InputError :message="errors.parentesco" />
            </div>
        </div>

        <div class="grid gap-6 sm:grid-cols-2">
            <div class="grid gap-2">
                <Label for="email">E-mail</Label>
                <Input
                    id="email"
                    name="email"
                    type="email"
                    :default-value="parent?.email ?? ''"
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

        <div class="grid gap-6 sm:grid-cols-2">
            <div class="grid gap-2">
                <Label for="ativo">Status</Label>
                <label
                    class="flex h-10 items-center gap-2 rounded-md border border-input bg-background px-3 text-sm"
                >
                    <input
                        type="hidden"
                        name="ativo"
                        :value="parent?.ativo === false ? '0' : '1'"
                    />
                    <input
                        id="ativo"
                        type="checkbox"
                        name="_ativo_toggle"
                        class="h-4 w-4 rounded border border-input"
                        :checked="parent?.ativo !== false"
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
                        {{ parent?.ativo === false ? 'Inativo' : 'Ativo' }}
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
                :default-value="parent?.observacoes ?? ''"
                placeholder="Observações adicionais sobre o responsável..."
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

