<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Save } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';

interface Plan {
    id?: string;
    nome?: string;
    descricao?: string;
    preco_mensal?: number;
    preco_anual?: number;
    ativo?: boolean;
    max_alunos?: number;
    max_professores?: number;
    max_armazenamento_mb?: number;
    caracteristicas?: Record<string, any>;
}

const props = defineProps<{
    plan?: Plan;
    submitLabel: string;
    processing: boolean;
    errors: Record<string, string>;
}>();

function formatPriceValue(value: number | string | null | undefined): string {
    if (!value && value !== 0) return '0.00';
    const numValue = typeof value === 'string' ? parseFloat(value) : value;
    if (isNaN(numValue)) return '0.00';
    return numValue.toFixed(2);
}

const priceMensalDisplay = ref(formatPriceValue(props.plan?.preco_mensal));
const priceAnualDisplay = ref(formatPriceValue(props.plan?.preco_anual));

function formatPrice(value: string): string {
    const numbers = value.replace(/[^0-9]/g, '');
    if (!numbers) return '0.00';
    const cents = parseInt(numbers, 10);
    return (cents / 100).toFixed(2);
}

function handlePriceMensalInput(value: string | number) {
    const numbers = String(value).replace(/[^0-9]/g, '');
    priceMensalDisplay.value = formatPrice(numbers);
    
    const hiddenInput = document.querySelector('input[name="preco_mensal"]') as HTMLInputElement;
    if (hiddenInput) {
        hiddenInput.value = (parseInt(numbers, 10) / 100).toString();
    }
}

function handlePriceAnualInput(value: string | number) {
    const numbers = String(value).replace(/[^0-9]/g, '');
    priceAnualDisplay.value = formatPrice(numbers);
    
    const hiddenInput = document.querySelector('input[name="preco_anual"]') as HTMLInputElement;
    if (hiddenInput) {
        hiddenInput.value = (parseInt(numbers, 10) / 100).toString();
    }
}

onMounted(() => {
    if (props.plan?.preco_mensal !== null && props.plan?.preco_mensal !== undefined) {
        priceMensalDisplay.value = formatPriceValue(props.plan.preco_mensal);
    }
    if (props.plan?.preco_anual !== null && props.plan?.preco_anual !== undefined) {
        priceAnualDisplay.value = formatPriceValue(props.plan.preco_anual);
    }
});
</script>

<template>
    <div class="grid gap-6">
        <div class="grid gap-2">
            <Label for="nome">Nome do Plano</Label>
            <Input
                id="nome"
                name="nome"
                :default-value="plan?.nome ?? ''"
                placeholder="Ex: Plano Básico"
                required
            />
            <InputError :message="errors.nome" />
        </div>

        <div class="grid gap-2">
            <Label for="descricao">Descrição</Label>
            <textarea
                id="descricao"
                name="descricao"
                :default-value="plan?.descricao ?? ''"
                placeholder="Descreva as características do plano..."
                rows="3"
                class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
            ></textarea>
            <InputError :message="errors.descricao" />
        </div>

        <div class="grid gap-6 sm:grid-cols-2">
            <div class="grid gap-2">
                <Label for="preco_mensal">Preço Mensal (R$)</Label>
                <div class="relative">
                    <Input
                        id="preco_mensal"
                        :model-value="priceMensalDisplay"
                        placeholder="0.00"
                        @update:model-value="handlePriceMensalInput"
                    />
                    <input
                        type="hidden"
                        name="preco_mensal"
                        :value="priceMensalDisplay"
                    />
                </div>
                <InputError :message="errors.preco_mensal" />
            </div>

            <div class="grid gap-2">
                <Label for="preco_anual">Preço Anual (R$)</Label>
                <div class="relative">
                    <Input
                        id="preco_anual"
                        :model-value="priceAnualDisplay"
                        placeholder="0.00"
                        @update:model-value="handlePriceAnualInput"
                    />
                    <input
                        type="hidden"
                        name="preco_anual"
                        :value="priceAnualDisplay"
                    />
                </div>
                <InputError :message="errors.preco_anual" />
            </div>
        </div>

        <div class="grid gap-6 sm:grid-cols-3">
            <div class="grid gap-2">
                <Label for="max_alunos">Máx. Alunos</Label>
                <Input
                    id="max_alunos"
                    name="max_alunos"
                    type="number"
                    min="0"
                    :default-value="plan?.max_alunos ?? ''"
                    placeholder="0 = ilimitado"
                />
                <InputError :message="errors.max_alunos" />
            </div>

            <div class="grid gap-2">
                <Label for="max_professores">Máx. Professores</Label>
                <Input
                    id="max_professores"
                    name="max_professores"
                    type="number"
                    min="0"
                    :default-value="plan?.max_professores ?? ''"
                    placeholder="0 = ilimitado"
                />
                <InputError :message="errors.max_professores" />
            </div>

            <div class="grid gap-2">
                <Label for="max_armazenamento_mb">Máx. Armazenamento (MB)</Label>
                <Input
                    id="max_armazenamento_mb"
                    name="max_armazenamento_mb"
                    type="number"
                    min="0"
                    :default-value="plan?.max_armazenamento_mb ?? ''"
                    placeholder="0 = ilimitado"
                />
                <InputError :message="errors.max_armazenamento_mb" />
            </div>
        </div>

        <div class="grid gap-2">
            <Label for="ativo">Status</Label>
            <label
                class="flex h-10 items-center gap-2 rounded-md border border-input bg-background px-3 text-sm"
            >
                <input
                    type="hidden"
                    name="ativo"
                    :value="plan?.ativo === false ? '0' : '1'"
                />
                <input
                    id="ativo"
                    type="checkbox"
                    name="_ativo_toggle"
                    class="h-4 w-4 rounded border border-input"
                    :checked="plan?.ativo !== false"
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
                    {{ plan?.ativo === false ? 'Inativo' : 'Ativo' }}
                </span>
            </label>
            <InputError :message="errors.ativo" />
        </div>

        <div class="flex items-center justify-end gap-2">
            <Button type="submit" :disabled="processing" class="flex items-center gap-2">
                <Save class="h-4 w-4" />
                {{ submitLabel }}
            </Button>
        </div>
    </div>
</template>
