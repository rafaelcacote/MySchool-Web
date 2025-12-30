<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, UserCheck } from 'lucide-vue-next';

interface Teacher {
    id: string;
    nome_completo: string;
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
    ativo: boolean;
    observacoes?: string | null;
}

interface Props {
    teacher: Teacher;
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

function formatCEP(cep: string | null | undefined): string {
    if (!cep) return '—';
    const numbers = cep.replace(/\D/g, '');
    if (numbers.length === 8) {
        return `${numbers.slice(0, 5)}-${numbers.slice(5, 8)}`;
    }
    return cep;
}

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Professores',
        href: '/school/teachers',
    },
    {
        title: props.teacher.nome_completo,
        href: `/school/teachers/${props.teacher.id}`,
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="`Professor: ${props.teacher.nome_completo}`" />

        <div class="space-y-6">
            <div class="flex items-start justify-between gap-4">
                <div class="mt-2">
                    <div class="mb-8 space-y-0.5">
                        <h2 class="flex items-center gap-2 text-xl font-semibold tracking-tight">
                            <UserCheck class="h-5 w-5" />
                            {{ props.teacher.nome_completo }}
                        </h2>
                        <p class="text-sm text-muted-foreground">
                            Perfil do professor
                        </p>
                    </div>
                </div>

                <Button
                    variant="outline"
                    as-child
                    class="rounded-lg"
                >
                    <Link href="/school/teachers" class="flex items-center gap-2">
                        <ArrowLeft class="h-4 w-4" />
                        Voltar
                    </Link>
                </Button>
            </div>

            <div class="rounded-xl border bg-card p-6 shadow-sm">
                <div class="space-y-6">
                    <div>
                        <h3 class="mb-4 text-lg font-semibold">Informações Básicas</h3>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Nome completo</p>
                                <p class="mt-1">{{ props.teacher.nome_completo }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">CPF</p>
                                <p class="mt-1">{{ formatCPF(props.teacher.cpf) }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Data de nascimento</p>
                                <p class="mt-1">
                                    {{ props.teacher.data_nascimento ? new Date(props.teacher.data_nascimento).toLocaleDateString('pt-BR') : '—' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">E-mail</p>
                                <p class="mt-1">{{ props.teacher.email || '—' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Telefone</p>
                                <p class="mt-1">{{ formatPhone(props.teacher.telefone) }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Status</p>
                                <div class="mt-1">
                                    <Badge
                                        :variant="props.teacher.ativo ? 'default' : 'destructive'"
                                    >
                                        {{ props.teacher.ativo ? 'Ativo' : 'Inativo' }}
                                    </Badge>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="props.teacher.formacao || props.teacher.especializacao">
                        <h3 class="mb-4 text-lg font-semibold">Formação</h3>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div v-if="props.teacher.formacao">
                                <p class="text-sm font-medium text-muted-foreground">Formação</p>
                                <p class="mt-1">{{ props.teacher.formacao }}</p>
                            </div>
                            <div v-if="props.teacher.especializacao">
                                <p class="text-sm font-medium text-muted-foreground">Especialização</p>
                                <p class="mt-1">{{ props.teacher.especializacao }}</p>
                            </div>
                        </div>
                    </div>

                    <div v-if="props.teacher.endereco || props.teacher.endereco_numero || props.teacher.endereco_bairro || props.teacher.endereco_cidade">
                        <h3 class="mb-4 text-lg font-semibold">Endereço</h3>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div v-if="props.teacher.endereco">
                                <p class="text-sm font-medium text-muted-foreground">Logradouro</p>
                                <p class="mt-1">{{ props.teacher.endereco }}</p>
                            </div>
                            <div v-if="props.teacher.endereco_numero">
                                <p class="text-sm font-medium text-muted-foreground">Número</p>
                                <p class="mt-1">{{ props.teacher.endereco_numero }}</p>
                            </div>
                            <div v-if="props.teacher.endereco_complemento">
                                <p class="text-sm font-medium text-muted-foreground">Complemento</p>
                                <p class="mt-1">{{ props.teacher.endereco_complemento }}</p>
                            </div>
                            <div v-if="props.teacher.endereco_bairro">
                                <p class="text-sm font-medium text-muted-foreground">Bairro</p>
                                <p class="mt-1">{{ props.teacher.endereco_bairro }}</p>
                            </div>
                            <div v-if="props.teacher.endereco_cep">
                                <p class="text-sm font-medium text-muted-foreground">CEP</p>
                                <p class="mt-1">{{ formatCEP(props.teacher.endereco_cep) }}</p>
                            </div>
                            <div v-if="props.teacher.endereco_cidade">
                                <p class="text-sm font-medium text-muted-foreground">Cidade</p>
                                <p class="mt-1">{{ props.teacher.endereco_cidade }}</p>
                            </div>
                            <div v-if="props.teacher.endereco_estado">
                                <p class="text-sm font-medium text-muted-foreground">Estado</p>
                                <p class="mt-1">{{ props.teacher.endereco_estado }}</p>
                            </div>
                            <div v-if="props.teacher.endereco_pais">
                                <p class="text-sm font-medium text-muted-foreground">País</p>
                                <p class="mt-1">{{ props.teacher.endereco_pais }}</p>
                            </div>
                        </div>
                    </div>

                    <div v-if="props.teacher.observacoes">
                        <h3 class="mb-4 text-lg font-semibold">Observações</h3>
                        <p class="text-sm whitespace-pre-wrap">{{ props.teacher.observacoes }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

