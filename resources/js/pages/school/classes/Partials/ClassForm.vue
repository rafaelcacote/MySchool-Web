<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Save } from 'lucide-vue-next';
import { ref, watch, onMounted } from 'vue';

interface ClassData {
    id?: string;
    nome?: string;
    serie?: string | null;
    turma_letra?: string | null;
    capacidade?: number | null;
    ano_letivo?: number | null;
    professor_id?: string | null;
    ativo?: boolean;
}

interface Teacher {
    id: string;
    nome_completo: string;
}

const props = defineProps<{
    classData?: ClassData;
    teachers?: Teacher[];
    submitLabel: string;
    processing: boolean;
    errors: Record<string, string>;
}>();

const nome = ref(props.classData?.nome || '');
const serie = ref(props.classData?.serie || '');
const turmaLetra = ref(props.classData?.turma_letra || '');
const anoLetivo = ref(props.classData?.ano_letivo || new Date().getFullYear());
const professorId = ref(props.classData?.professor_id || '');
const capacidade = ref(props.classData?.capacidade || '');
const ativo = ref(props.classData?.ativo !== false);

watch(() => props.classData, (newData) => {
    if (newData) {
        nome.value = newData.nome || '';
        serie.value = newData.serie || '';
        turmaLetra.value = newData.turma_letra || '';
        anoLetivo.value = newData.ano_letivo || new Date().getFullYear();
        professorId.value = newData.professor_id || '';
        capacidade.value = newData.capacidade || '';
        ativo.value = newData.ativo !== false;
    }
}, { immediate: true, deep: true });

onMounted(() => {
    console.log('ClassForm mounted with data:', props.classData);
});
</script>

<template>
    <div class="grid gap-6">
        <div class="grid gap-6 sm:grid-cols-2">
            <div class="grid gap-2">
                <Label for="nome">Nome da turma</Label>
                <input
                    id="nome"
                    name="nome"
                    v-model="nome"
                    type="text"
                    placeholder="Ex: 5º Ano A"
                    required
                    class="flex h-10 w-full min-w-0 rounded-lg border border-input bg-muted/60 px-3 py-2 text-base shadow-sm transition-[color,box-shadow,background] outline-none disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] focus-visible:bg-card"
                />
                <InputError :message="errors.nome" />
            </div>

            <div class="grid gap-2">
                <Label for="serie">Série</Label>
                <input
                    id="serie"
                    name="serie"
                    v-model="serie"
                    type="text"
                    placeholder="Ex: 5º ano"
                    class="flex h-10 w-full min-w-0 rounded-lg border border-input bg-muted/60 px-3 py-2 text-base shadow-sm transition-[color,box-shadow,background] outline-none disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] focus-visible:bg-card"
                />
                <InputError :message="errors.serie" />
            </div>
        </div>

        <div class="grid gap-6 sm:grid-cols-2">
            <div class="grid gap-2">
                <Label for="turma_letra">Turma (Letra)</Label>
                <select
                    id="turma_letra"
                    name="turma_letra"
                    v-model="turmaLetra"
                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <option value="">Selecione a turma</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                    <option value="E">E</option>
                    <option value="F">F</option>
                </select>
                <InputError :message="errors.turma_letra" />
            </div>

            <div class="grid gap-2">
                <Label for="ano_letivo">Ano letivo</Label>
                <input
                    id="ano_letivo"
                    name="ano_letivo"
                    v-model="anoLetivo"
                    type="number"
                    placeholder="Ex: 2024"
                    min="2000"
                    max="2100"
                    required
                    class="flex h-10 w-full min-w-0 rounded-lg border border-input bg-muted/60 px-3 py-2 text-base shadow-sm transition-[color,box-shadow,background] outline-none disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] focus-visible:bg-card"
                />
                <InputError :message="errors.ano_letivo" />
            </div>
        </div>

        <div class="grid gap-6 sm:grid-cols-2">
            <div class="grid gap-2">
                <Label for="professor_id">Professor Responsável</Label>
                <select
                    id="professor_id"
                    name="professor_id"
                    v-model="professorId"
                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <option value="">Nenhum professor atribuído</option>
                    <option v-for="teacher in teachers" :key="teacher.id" :value="teacher.id">
                        {{ teacher.nome_completo }}
                    </option>
                </select>
                <InputError :message="errors.professor_id" />
            </div>

            <div class="grid gap-2">
                <Label for="capacidade">Capacidade</Label>
                <input
                    id="capacidade"
                    name="capacidade"
                    v-model="capacidade"
                    type="number"
                    placeholder="Ex: 30"
                    min="1"
                    class="flex h-10 w-full min-w-0 rounded-lg border border-input bg-muted/60 px-3 py-2 text-base shadow-sm transition-[color,box-shadow,background] outline-none disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] focus-visible:bg-card"
                />
                <InputError :message="errors.capacidade" />
            </div>
        </div>

        <div class="grid gap-6 sm:grid-cols-2">
            <div class="grid gap-2">
                <Label for="ativo">Status</Label>
                <label
                    class="flex h-10 items-center gap-2 rounded-md border border-input bg-background px-3 text-sm cursor-pointer"
                >
                    <input
                        type="hidden"
                        name="ativo"
                        :value="ativo ? '1' : '0'"
                    />
                    <input
                        id="ativo"
                        type="checkbox"
                        v-model="ativo"
                        class="h-4 w-4 rounded border border-input cursor-pointer"
                    />
                    <span class="text-muted-foreground">
                        {{ ativo ? 'Ativa' : 'Inativa' }}
                    </span>
                </label>
                <InputError :message="errors.ativo" />
            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <Button type="submit" :disabled="processing" class="flex items-center gap-2">
                <Save class="h-4 w-4" />
                {{ submitLabel }}
            </Button>
        </div>
    </div>
</template>

