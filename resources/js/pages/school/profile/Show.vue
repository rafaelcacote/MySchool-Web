<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Tenant } from '@/types';
import { Head } from '@inertiajs/vue3';
import { School } from 'lucide-vue-next';

function formatCEP(cep: string): string {
    if (!cep) return '';
    const numbers = cep.replace(/\D/g, '');
    if (numbers.length === 8) {
        return `${numbers.slice(0, 5)}-${numbers.slice(5, 8)}`;
    }
    return cep;
}

interface Props {
    tenant: Tenant;
}

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Perfil da Escola',
        href: '/school/profile',
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="`Perfil: ${props.tenant.name}`" />

        <div class="space-y-6">
            <div class="flex items-start justify-between gap-4">
                <div class="mt-2">
                    <div class="mb-8 space-y-0.5">
                        <h2 class="flex items-center gap-2 text-xl font-semibold tracking-tight">
                            <School class="h-5 w-5" />
                            {{ props.tenant.name }}
                        </h2>
                        <p class="text-sm text-muted-foreground">
                            Informações da escola
                        </p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border bg-card p-6 shadow-sm">
                <div class="space-y-6">
                    <div>
                        <h3 class="mb-4 text-lg font-semibold">Informações Básicas</h3>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Nome</p>
                                <p class="mt-1">{{ props.tenant.name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">E-mail</p>
                                <p class="mt-1">{{ props.tenant.email }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Subdomínio</p>
                                <p class="mt-1">{{ props.tenant.subdomain ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">CNPJ</p>
                                <p class="mt-1">{{ props.tenant.cnpj ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Telefone</p>
                                <p class="mt-1">{{ props.tenant.phone ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Status</p>
                                <div class="mt-1">
                                    <Badge
                                        :variant="props.tenant.is_active ? 'default' : 'secondary'"
                                    >
                                        {{ props.tenant.is_active ? 'Ativo' : 'Inativo' }}
                                    </Badge>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="props.tenant.address || props.tenant.endereco_numero || props.tenant.endereco_bairro || props.tenant.endereco_cidade">
                        <h3 class="mb-4 text-lg font-semibold">Endereço</h3>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div v-if="props.tenant.address">
                                <p class="text-sm font-medium text-muted-foreground">Logradouro</p>
                                <p class="mt-1">{{ props.tenant.address }}</p>
                            </div>
                            <div v-if="props.tenant.endereco_numero">
                                <p class="text-sm font-medium text-muted-foreground">Número</p>
                                <p class="mt-1">{{ props.tenant.endereco_numero }}</p>
                            </div>
                            <div v-if="props.tenant.endereco_complemento">
                                <p class="text-sm font-medium text-muted-foreground">Complemento</p>
                                <p class="mt-1">{{ props.tenant.endereco_complemento }}</p>
                            </div>
                            <div v-if="props.tenant.endereco_bairro">
                                <p class="text-sm font-medium text-muted-foreground">Bairro</p>
                                <p class="mt-1">{{ props.tenant.endereco_bairro }}</p>
                            </div>
                            <div v-if="props.tenant.endereco_cep">
                                <p class="text-sm font-medium text-muted-foreground">CEP</p>
                                <p class="mt-1">{{ formatCEP(props.tenant.endereco_cep) }}</p>
                            </div>
                            <div v-if="props.tenant.endereco_cidade">
                                <p class="text-sm font-medium text-muted-foreground">Cidade</p>
                                <p class="mt-1">{{ props.tenant.endereco_cidade }}</p>
                            </div>
                            <div v-if="props.tenant.endereco_estado">
                                <p class="text-sm font-medium text-muted-foreground">Estado</p>
                                <p class="mt-1">{{ props.tenant.endereco_estado }}</p>
                            </div>
                            <div v-if="props.tenant.endereco_pais">
                                <p class="text-sm font-medium text-muted-foreground">País</p>
                                <p class="mt-1">{{ props.tenant.endereco_pais }}</p>
                            </div>
                        </div>
                    </div>

                    <div v-if="props.tenant.logo_url">
                        <h3 class="mb-4 text-lg font-semibold">Logo</h3>
                        <img
                            :src="props.tenant.logo_url"
                            :alt="`Logo de ${props.tenant.name}`"
                            class="h-32 w-auto rounded-lg border"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

