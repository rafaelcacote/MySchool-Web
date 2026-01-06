<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Form, Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, Users } from 'lucide-vue-next';
import { ref } from 'vue';
import StudentForm from '../students/Partials/StudentForm.vue';

interface Turma {
    id: string;
    nome: string;
    serie?: string | null;
    turma_letra?: string | null;
    ano_letivo?: string | null;
}

interface Student {
    id: string;
    nome: string;
    nome_social?: string | null;
    foto_url?: string | null;
    data_nascimento?: string | null;
    ativo: boolean;
    turma?: {
        id: string;
        nome: string;
        serie?: string | null;
        turma_letra?: string | null;
        ano_letivo?: string | null;
    } | null;
}

interface Parent {
    id: string;
    nome_completo?: string | null;
    cpf?: string | null;
    email?: string | null;
    telefone?: string | null;
    parentesco?: string | null;
    profissao?: string | null;
    ativo: boolean;
    students?: Student[];
}

interface Props {
    parent: Parent;
    turmas?: Turma[];
}

const props = defineProps<Props>();
const createDialogOpen = ref(false);

function formatPhone(phone: string | null | undefined): string {
    if (!phone) return '—';
    const numbers = phone.replace(/\D/g, '');
    if (numbers.length === 10) {
        return `(${numbers.slice(0, 2)}) ${numbers.slice(2, 6)}-${numbers.slice(6)}`;
    } else if (numbers.length === 11) {
        return `(${numbers.slice(0, 2)}) ${numbers.slice(2, 7)}-${numbers.slice(7)}`;
    }
    return phone;
}

function formatCPF(cpf: string | null | undefined): string {
    if (!cpf) return '—';
    const numbers = cpf.replace(/\D/g, '');
    if (numbers.length === 11) {
        return `${numbers.slice(0, 3)}.${numbers.slice(3, 6)}.${numbers.slice(6, 9)}-${numbers.slice(9, 11)}`;
    }
    return cpf;
}

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Responsáveis',
        href: '/school/parents',
    },
    {
        title: props.parent.nome_completo || 'Responsável',
        href: `/school/parents/${props.parent.id}`,
    },
];

function detachStudent(studentId: string) {
    if (!confirm('Remover o vínculo deste aluno com o responsável?')) {
        return;
    }

    router.delete(`/school/parents/${props.parent.id}/students/${studentId}`, {
        preserveScroll: true,
    });
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="`Responsável: ${props.parent.nome_completo || 'Sem nome'}`" />

        <div class="space-y-6">
            <div class="flex items-start justify-between gap-4">
                <div class="mt-2">
                    <div class="mb-8 space-y-0.5">
                        <h2 class="flex items-center gap-2 text-xl font-semibold tracking-tight">
                            <Users class="h-5 w-5" />
                            {{ props.parent.nome_completo || 'Sem nome' }}
                        </h2>
                        <p class="text-sm text-muted-foreground">
                            {{ props.parent.parentesco || 'Responsável' }}
                        </p>
                    </div>
                </div>

                <div class="flex gap-2">
                    <Button
                        variant="outline"
                        as-child
                        class="rounded-lg"
                    >
                        <Link :href="`/school/parents/${props.parent.id}/edit`" class="flex items-center gap-2">
                            Editar
                        </Link>
                    </Button>
                    <Button
                        variant="ghost"
                        as-child
                        class="rounded-lg"
                    >
                        <Link href="/school/parents" class="flex items-center gap-2">
                            <ArrowLeft class="h-4 w-4" />
                            Voltar
                        </Link>
                    </Button>
                </div>
            </div>

            <div class="rounded-xl border bg-card p-6 shadow-sm">
                <div class="space-y-6">
                    <div>
                        <h3 class="mb-4 text-lg font-semibold">Dados Pessoais</h3>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Nome completo</p>
                                <p class="mt-1">{{ props.parent.nome_completo || '—' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">CPF</p>
                                <p class="mt-1">{{ formatCPF(props.parent.cpf) }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">E-mail</p>
                                <p class="mt-1">{{ props.parent.email || '—' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Telefone</p>
                                <p class="mt-1">{{ formatPhone(props.parent.telefone) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="border-t pt-6">
                        <h3 class="mb-4 text-lg font-semibold">Dados do Responsável</h3>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Parentesco</p>
                                <p class="mt-1">{{ props.parent.parentesco || '—' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Profissão</p>
                                <p class="mt-1">{{ props.parent.profissao || '—' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Status</p>
                                <div class="mt-1">
                                    <Badge
                                        :variant="props.parent.ativo ? 'default' : 'destructive'"
                                    >
                                        {{ props.parent.ativo ? 'Ativo' : 'Inativo' }}
                                    </Badge>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-xl border bg-card p-6 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold">Alunos vinculados</h3>
                    <p class="text-sm text-muted-foreground">
                        Visualize ou cadastre alunos ligados a este responsável.
                    </p>
                </div>

                <Dialog v-model:open="createDialogOpen">
                    <DialogTrigger as-child>
                        <Button>
                            Adicionar aluno
                        </Button>
                    </DialogTrigger>
                    <DialogContent class="max-w-3xl">
                        <DialogHeader>
                            <DialogTitle>Novo aluno para este responsável</DialogTitle>
                            <DialogDescription>
                                Crie o aluno e o vínculo será feito automaticamente.
                            </DialogDescription>
                        </DialogHeader>

                        <Form
                            :action="`/school/parents/${props.parent.id}/students`"
                            method="post"
                            reset-on-success
                            @success="createDialogOpen = false"
                            class="mt-4 space-y-6"
                            v-slot="{ processing, errors }"
                        >
                            <StudentForm
                                :turmas="props.turmas"
                                submit-label="Adicionar aluno"
                                :processing="processing"
                                :errors="errors"
                            />
                        </Form>
                    </DialogContent>
                </Dialog>
            </div>

            <div v-if="props.parent.students && props.parent.students.length" class="mt-6 space-y-3">
                <div
                    v-for="student in props.parent.students"
                    :key="student.id"
                    class="flex flex-col gap-3 rounded-lg border p-4 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <p class="font-medium">{{ student.nome }}</p>
                            <Badge :variant="student.ativo ? 'default' : 'destructive'">
                                {{ student.ativo ? 'Ativo' : 'Inativo' }}
                            </Badge>
                        </div>
                        <p v-if="student.nome_social" class="text-sm text-muted-foreground">
                            Nome social: {{ student.nome_social }}
                        </p>
                        <p v-if="student.turma" class="text-sm text-muted-foreground">
                            Turma: {{ student.turma.nome }}
                            <template v-if="student.turma.serie || student.turma.turma_letra">
                                ({{ [student.turma.serie, student.turma.turma_letra].filter(Boolean).join(' - ') }})
                            </template>
                            <template v-if="student.turma.ano_letivo">
                                - {{ student.turma.ano_letivo }}
                            </template>
                        </p>
                        <p v-if="student.data_nascimento" class="text-sm text-muted-foreground">
                            Data de nascimento: {{ new Date(student.data_nascimento).toLocaleDateString('pt-BR') }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <Button
                            as-child
                            size="sm"
                            variant="outline"
                        >
                            <Link :href="`/school/students/${student.id}`">
                                Ver aluno
                            </Link>
                        </Button>
                        <Button
                            size="sm"
                            variant="destructive"
                            @click="detachStudent(student.id)"
                        >
                            Remover vínculo
                        </Button>
                    </div>
                </div>
            </div>

            <div
                v-else
                class="mt-6 rounded-lg border border-dashed p-6 text-center text-sm text-muted-foreground"
            >
                Nenhum aluno vinculado. Clique em "Adicionar aluno" para cadastrar o primeiro.
            </div>
        </div>
    </AppLayout>
</template>

