<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Save } from 'lucide-vue-next';

interface Turma {
    id: string;
    nome: string;
    serie?: string | null;
    turma_letra?: string | null;
    ano_letivo?: string | null;
}

interface Student {
    id?: string;
    nome?: string | null;
    nome_social?: string | null;
    foto_url?: string | null;
    data_nascimento?: string | null;
    ativo?: boolean;
    informacoes_medicas?: string | null;
}

const props = defineProps<{
    student?: Student;
    turmas?: Turma[];
    submitLabel: string;
    processing: boolean;
    errors: Record<string, string>;
}>();
</script>

<template>
    <div class="grid gap-6">
        <div class="grid gap-6 sm:grid-cols-2">
            <div class="grid gap-2">
                <Label for="nome">Nome completo</Label>
                <Input
                    id="nome"
                    name="nome"
                    :default-value="student?.nome ?? ''"
                    placeholder="Ex: João Silva Santos"
                    required
                />
                <InputError :message="errors.nome" />
            </div>

            <div class="grid gap-2">
                <Label for="nome_social">Nome social</Label>
                <Input
                    id="nome_social"
                    name="nome_social"
                    :default-value="student?.nome_social ?? ''"
                    placeholder="Ex: Maria"
                />
                <InputError :message="errors.nome_social" />
            </div>
        </div>

        <div class="grid gap-2">
            <Label for="turma_id">Turma</Label>
            <select
                id="turma_id"
                name="turma_id"
                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                required
            >
                <option value="">Selecione uma turma</option>
                <option
                    v-for="turma in props.turmas"
                    :key="turma.id"
                    :value="turma.id"
                    :selected="student?.turma_id === turma.id"
                >
                    {{ turma.nome }}
                    <template v-if="turma.serie || turma.turma_letra">
                        ({{ [turma.serie, turma.turma_letra].filter(Boolean).join(' - ') }})
                    </template>
                    <template v-if="turma.ano_letivo">
                        - {{ turma.ano_letivo }}
                    </template>
                </option>
            </select>
            <InputError :message="errors.turma_id" />
        </div>

        <div class="grid gap-6 sm:grid-cols-2">
            <div class="grid gap-2">
                <Label for="data_nascimento">Data de nascimento</Label>
                <Input
                    id="data_nascimento"
                    name="data_nascimento"
                    type="date"
                    :default-value="student?.data_nascimento ?? ''"
                />
                <InputError :message="errors.data_nascimento" />
            </div>

            <div class="grid gap-2">
                <Label for="foto_url">URL da foto</Label>
                <Input
                    id="foto_url"
                    name="foto_url"
                    type="url"
                    :default-value="student?.foto_url ?? ''"
                    placeholder="https://exemplo.com/foto.jpg"
                />
                <InputError :message="errors.foto_url" />
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
                    :value="student?.ativo === false ? '0' : '1'"
                />
                <input
                    id="ativo"
                    type="checkbox"
                    name="_ativo_toggle"
                    class="h-4 w-4 rounded border border-input"
                    :checked="student?.ativo !== false"
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
                    {{ student?.ativo === false ? 'Inativo' : 'Ativo' }}
                </span>
            </label>
            <InputError :message="errors.ativo" />
        </div>

        <div class="grid gap-2">
            <Label for="informacoes_medicas">Informações médicas</Label>
            <textarea
                id="informacoes_medicas"
                name="informacoes_medicas"
                rows="3"
                class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                :default-value="student?.informacoes_medicas ?? ''"
                placeholder="Informações médicas relevantes (alergias, restrições, etc.)"
            />
            <InputError :message="errors.informacoes_medicas" />
        </div>

        <div class="flex items-center justify-end gap-2">
            <Button type="submit" :disabled="processing" class="flex items-center gap-2">
                <Save class="h-4 w-4" />
                {{ submitLabel }}
            </Button>
        </div>
    </div>
</template>

