<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Edit, GraduationCap, User } from 'lucide-vue-next';

interface Student {
    id: string;
    nome_completo: string;
    cpf?: string | null;
    data_nascimento?: string | null;
    data_matricula?: string | null;
    telefone?: string | null;
    email?: string | null;
    matricula?: string | null;
    serie?: string | null;
    turma?: string | null;
    ativo: boolean;
    informacoes_medicas?: string | null;
    parents?: Array<{
        id: string;
        nome_completo: string;
        parentesco?: string | null;
    }>;
}

interface Props {
    student: Student;
}

const props = defineProps<Props>();

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
        title: 'Alunos',
        href: '/school/students',
    },
    {
        title: props.student.nome_completo,
        href: `/school/students/${props.student.id}`,
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="`Aluno: ${props.student.nome_completo}`" />

        <div class="space-y-6">
            <div class="flex items-start justify-between gap-4">
                <div class="mt-2">
                    <div class="mb-8 space-y-0.5">
                        <h2 class="flex items-center gap-2 text-xl font-semibold tracking-tight">
                            <GraduationCap class="h-5 w-5" />
                            {{ props.student.nome_completo }}
                        </h2>
                        <p class="text-sm text-muted-foreground">
                            Perfil do aluno
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <Button
                        variant="outline"
                        as-child
                        class="rounded-lg"
                    >
                        <Link href="/school/students" class="flex items-center gap-2">
                            <ArrowLeft class="h-4 w-4" />
                            Voltar
                        </Link>
                    </Button>
                    <Button as-child>
                        <Link :href="`/school/students/${props.student.id}/edit`" class="flex items-center gap-2">
                            <Edit class="h-4 w-4" />
                            Editar
                        </Link>
                    </Button>
                </div>
            </div>

            <div class="rounded-xl border bg-card p-6 shadow-sm">
                <div class="space-y-6">
                    <div>
                        <h3 class="mb-4 text-lg font-semibold">Informações Básicas</h3>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Nome completo</p>
                                <p class="mt-1">{{ props.student.nome_completo }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">CPF</p>
                                <p class="mt-1">{{ formatCPF(props.student.cpf) }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Data de nascimento</p>
                                <p class="mt-1">
                                    {{ props.student.data_nascimento ? new Date(props.student.data_nascimento).toLocaleDateString('pt-BR') : '—' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Matrícula</p>
                                <p class="mt-1">{{ props.student.matricula || '—' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Série</p>
                                <p class="mt-1">{{ props.student.serie || '—' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Turma</p>
                                <p class="mt-1">{{ props.student.turma || '—' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Data da matrícula</p>
                                <p class="mt-1">
                                    {{ props.student.data_matricula ? new Date(props.student.data_matricula).toLocaleDateString('pt-BR') : '—' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">E-mail</p>
                                <p class="mt-1">{{ props.student.email || '—' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Telefone</p>
                                <p class="mt-1">{{ formatPhone(props.student.telefone) }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Status</p>
                                <div class="mt-1">
                                    <Badge
                                        :variant="props.student.ativo ? 'default' : 'destructive'"
                                    >
                                        {{ props.student.ativo ? 'Ativo' : 'Inativo' }}
                                    </Badge>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="props.student.parents && props.student.parents.length > 0">
                        <h3 class="mb-4 text-lg font-semibold">Responsáveis</h3>
                        <div class="space-y-2">
                            <div
                                v-for="parent in props.student.parents"
                                :key="parent.id"
                                class="rounded-lg border p-3"
                            >
                                <p class="font-medium">{{ parent.nome_completo }}</p>
                                <p v-if="parent.parentesco" class="text-sm text-muted-foreground">
                                    {{ parent.parentesco }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div v-if="props.student.informacoes_medicas">
                        <h3 class="mb-4 text-lg font-semibold">Informações médicas</h3>
                        <p class="text-sm whitespace-pre-wrap">{{ props.student.informacoes_medicas }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

