<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { index as tenantsIndex } from '@/routes/tenants';
import { index as plansIndex } from '@/routes/plans';
import { index as subscriptionsIndex } from '@/routes/subscriptions';
import { index as usersIndex } from '@/routes/users';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { computed } from 'vue';
import {
    ArrowRight,
    CreditCard,
    FileText,
    School,
    TrendingUp,
    Users,
} from 'lucide-vue-next';

interface Stats {
    escolas: {
        total: number;
        ativas: number;
        inativas: number;
    };
    planos: {
        total: number;
        ativos: number;
        inativos: number;
    };
    assinaturas: {
        total: number;
        ativas: number;
        canceladas: number;
        pendentes: number;
    };
    usuarios: {
        total: number;
        ativos: number;
        inativos: number;
    };
}

interface Props {
    stats: Stats;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const page = usePage();
const auth = computed(() => page.props.auth as any);

const userName = computed(() => {
    return auth.value?.user?.nome_completo || auth.value?.user?.name || 'Usuário';
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6">
            <!-- Hero -->
            <section class="bg-brand-gradient rounded-2xl p-6 text-white shadow-sm">
                <div
                    class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between"
                >
                    <div class="min-w-0">
                        <h1 class="text-2xl font-semibold md:text-3xl">
                            Olá, {{ userName }}!
                        </h1>
                        <p class="mt-1 text-white/80">
                            Bem-vindo ao painel de administração geral.
                        </p>

                        <div class="mt-4 flex items-center gap-2">
                            <span class="text-sm text-white/80">Perfil</span>
                            <Badge
                                variant="secondary"
                                class="border-white/20 bg-white/15 text-white"
                            >
                                Administrador Geral
                            </Badge>
                        </div>
                    </div>
                </div>
            </section>

            <!-- KPIs - Estatísticas Principais -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <!-- Card Escolas -->
                <Card class="p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <p class="text-sm text-muted-foreground">
                                Total de Escolas
                            </p>
                            <p class="mt-2 text-3xl font-semibold">
                                {{ stats.escolas.total }}
                            </p>
                            <div class="mt-3 flex gap-2 text-xs">
                                <span class="text-muted-foreground">
                                    {{ stats.escolas.ativas }} ativas
                                </span>
                                <span class="text-muted-foreground">•</span>
                                <span class="text-muted-foreground">
                                    {{ stats.escolas.inativas }} inativas
                                </span>
                            </div>
                        </div>
                        <div
                            class="flex size-12 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-900/30"
                        >
                            <School class="size-6 text-blue-600 dark:text-blue-400" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <Link :href="tenantsIndex()">
                            <Button
                                variant="ghost"
                                size="sm"
                                class="w-full justify-between text-xs"
                            >
                                Ver todas
                                <ArrowRight class="size-3" />
                            </Button>
                        </Link>
                    </div>
                </Card>

                <!-- Card Planos -->
                <Card class="p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <p class="text-sm text-muted-foreground">
                                Total de Planos
                            </p>
                            <p class="mt-2 text-3xl font-semibold">
                                {{ stats.planos.total }}
                            </p>
                            <div class="mt-3 flex gap-2 text-xs">
                                <span class="text-muted-foreground">
                                    {{ stats.planos.ativos }} ativos
                                </span>
                                <span class="text-muted-foreground">•</span>
                                <span class="text-muted-foreground">
                                    {{ stats.planos.inativos }} inativos
                                </span>
                            </div>
                        </div>
                        <div
                            class="flex size-12 items-center justify-center rounded-xl bg-green-100 dark:bg-green-900/30"
                        >
                            <CreditCard class="size-6 text-green-600 dark:text-green-400" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <Link :href="plansIndex()">
                            <Button
                                variant="ghost"
                                size="sm"
                                class="w-full justify-between text-xs"
                            >
                                Ver todos
                                <ArrowRight class="size-3" />
                            </Button>
                        </Link>
                    </div>
                </Card>

                <!-- Card Assinaturas -->
                <Card class="p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <p class="text-sm text-muted-foreground">
                                Total de Assinaturas
                            </p>
                            <p class="mt-2 text-3xl font-semibold">
                                {{ stats.assinaturas.total }}
                            </p>
                            <div class="mt-3 flex flex-wrap gap-x-2 gap-y-1 text-xs">
                                <span class="text-muted-foreground">
                                    {{ stats.assinaturas.ativas }} ativas
                                </span>
                                <span class="text-muted-foreground">•</span>
                                <span class="text-muted-foreground">
                                    {{ stats.assinaturas.pendentes }} pendentes
                                </span>
                            </div>
                        </div>
                        <div
                            class="flex size-12 items-center justify-center rounded-xl bg-purple-100 dark:bg-purple-900/30"
                        >
                            <FileText class="size-6 text-purple-600 dark:text-purple-400" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <Link :href="subscriptionsIndex()">
                            <Button
                                variant="ghost"
                                size="sm"
                                class="w-full justify-between text-xs"
                            >
                                Ver todas
                                <ArrowRight class="size-3" />
                            </Button>
                        </Link>
                    </div>
                </Card>

                <!-- Card Usuários -->
                <Card class="p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <p class="text-sm text-muted-foreground">
                                Total de Usuários
                            </p>
                            <p class="mt-2 text-3xl font-semibold">
                                {{ stats.usuarios.total }}
                            </p>
                            <div class="mt-3 flex gap-2 text-xs">
                                <span class="text-muted-foreground">
                                    {{ stats.usuarios.ativos }} ativos
                                </span>
                                <span class="text-muted-foreground">•</span>
                                <span class="text-muted-foreground">
                                    {{ stats.usuarios.inativos }} inativos
                                </span>
                            </div>
                        </div>
                        <div
                            class="flex size-12 items-center justify-center rounded-xl bg-orange-100 dark:bg-orange-900/30"
                        >
                            <Users class="size-6 text-orange-600 dark:text-orange-400" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <Link :href="usersIndex()">
                            <Button
                                variant="ghost"
                                size="sm"
                                class="w-full justify-between text-xs"
                            >
                                Ver todos
                                <ArrowRight class="size-3" />
                            </Button>
                        </Link>
                    </div>
                </Card>
            </div>

            <!-- Detalhes e Ações Rápidas -->
            <div class="grid gap-4 lg:grid-cols-3">
                <!-- Resumo Detalhado -->
                <Card class="p-6 lg:col-span-2">
                    <div class="flex items-start justify-between gap-4 mb-6">
                        <div class="min-w-0">
                            <p class="text-lg font-semibold">
                                Resumo do Sistema
                            </p>
                            <p class="text-sm text-muted-foreground">
                                Visão geral das principais métricas
                            </p>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-xl border border-border/70 p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <School class="size-4 text-blue-600 dark:text-blue-400" />
                                <p class="text-xs font-medium text-muted-foreground">
                                    Escolas Ativas
                                </p>
                            </div>
                            <p class="text-2xl font-semibold">
                                {{ stats.escolas.ativas }}
                            </p>
                            <p class="mt-1 text-xs text-muted-foreground">
                                de {{ stats.escolas.total }} total
                            </p>
                        </div>

                        <div class="rounded-xl border border-border/70 p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <CreditCard class="size-4 text-green-600 dark:text-green-400" />
                                <p class="text-xs font-medium text-muted-foreground">
                                    Planos Ativos
                                </p>
                            </div>
                            <p class="text-2xl font-semibold">
                                {{ stats.planos.ativos }}
                            </p>
                            <p class="mt-1 text-xs text-muted-foreground">
                                de {{ stats.planos.total }} total
                            </p>
                        </div>

                        <div class="rounded-xl border border-border/70 p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <FileText class="size-4 text-purple-600 dark:text-purple-400" />
                                <p class="text-xs font-medium text-muted-foreground">
                                    Assinaturas Ativas
                                </p>
                            </div>
                            <p class="text-2xl font-semibold">
                                {{ stats.assinaturas.ativas }}
                            </p>
                            <p class="mt-1 text-xs text-muted-foreground">
                                de {{ stats.assinaturas.total }} total
                            </p>
                        </div>

                        <div class="rounded-xl border border-border/70 p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <Users class="size-4 text-orange-600 dark:text-orange-400" />
                                <p class="text-xs font-medium text-muted-foreground">
                                    Usuários Ativos
                                </p>
                            </div>
                            <p class="text-2xl font-semibold">
                                {{ stats.usuarios.ativos }}
                            </p>
                            <p class="mt-1 text-xs text-muted-foreground">
                                de {{ stats.usuarios.total }} total
                            </p>
                        </div>
                    </div>
                </Card>

                <!-- Ações Rápidas -->
                <Card class="p-6">
                    <p class="text-lg font-semibold mb-4">Ações Rápidas</p>
                    <div class="grid gap-2">
                        <Link :href="tenantsIndex()">
                            <Button variant="outline" class="w-full justify-start">
                                <School class="size-4 mr-2" />
                                Gerenciar Escolas
                            </Button>
                        </Link>
                        <Link :href="plansIndex()">
                            <Button variant="outline" class="w-full justify-start">
                                <CreditCard class="size-4 mr-2" />
                                Gerenciar Planos
                            </Button>
                        </Link>
                        <Link :href="subscriptionsIndex()">
                            <Button variant="outline" class="w-full justify-start">
                                <FileText class="size-4 mr-2" />
                                Gerenciar Assinaturas
                            </Button>
                        </Link>
                        <Link :href="usersIndex()">
                            <Button variant="outline" class="w-full justify-start">
                                <Users class="size-4 mr-2" />
                                Gerenciar Usuários
                            </Button>
                        </Link>
                    </div>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
