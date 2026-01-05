<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, BookOpen, Edit } from 'lucide-vue-next';

interface Professor {
    id: string;
    usuario?: {
        nome_completo: string;
    } | null;
}

interface Class {
    id: string;
    nome: string;
    serie?: string | null;
    turma_letra?: string | null;
    ano_letivo?: number | null;
    capacidade?: number | null;
    ativo: boolean;
    professor?: Professor | null;
}

interface Props {
    turma: Class;
}

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Turmas',
        href: '/school/classes',
    },
    {
        title: props.turma.nome,
        href: `/school/classes/${props.turma.id}`,
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="`Turma: ${props.turma.nome}`" />

        <div class="space-y-6">
            <div class="flex items-start justify-between gap-4">
                <div class="mt-2">
                    <div class="mb-8 space-y-0.5">
                        <h2 class="flex items-center gap-2 text-xl font-semibold tracking-tight">
                            <BookOpen class="h-5 w-5" />
                            {{ props.turma.nome }}
                        </h2>
                        <p class="text-sm text-muted-foreground">
                            Detalhes da turma
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <Button
                        variant="outline"
                        as-child
                        class="rounded-lg"
                    >
                        <Link href="/school/classes" class="flex items-center gap-2">
                            <ArrowLeft class="h-4 w-4" />
                            Voltar
                        </Link>
                    </Button>
                    <Button as-child>
                        <Link :href="`/school/classes/${props.turma.id}/edit`" class="flex items-center gap-2">
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
                                <p class="text-sm font-medium text-muted-foreground">Nome</p>
                                <p class="mt-1">{{ props.turma.nome }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Série</p>
                                <p class="mt-1">{{ props.turma.serie || '—' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Turma (Letra)</p>
                                <p class="mt-1">{{ props.turma.turma_letra || '—' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Ano letivo</p>
                                <p class="mt-1">{{ props.turma.ano_letivo || '—' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Professor Responsável</p>
                                <p class="mt-1">{{ props.turma.professor?.usuario?.nome_completo || '—' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Capacidade</p>
                                <p class="mt-1">{{ props.turma.capacidade || '—' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Status</p>
                                <div class="mt-1">
                                    <Badge
                                        :variant="props.turma.ativo ? 'default' : 'destructive'"
                                    >
                                        {{ props.turma.ativo ? 'Ativa' : 'Inativa' }}
                                    </Badge>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>




