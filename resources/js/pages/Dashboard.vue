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
    BookOpen,
    CreditCard,
    FileText,
    GraduationCap,
    School,
    UserCheck,
    Users,
} from 'lucide-vue-next';

interface AdminGeralStats {
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

interface AdminEscolaStats {
    professores: {
        total: number;
        ativos: number;
        inativos: number;
    };
    alunos: {
        total: number;
        ativos: number;
        inativos: number;
    };
    turmas: {
        total: number;
        ativas: number;
        inativas: number;
    };
    responsaveis: {
        total: number;
        ativos: number;
        inativos: number;
    };
}

interface Props {
    dashboardType: 'admin_geral' | 'admin_escola';
    stats: AdminGeralStats | AdminEscolaStats;
    tenant?: {
        id: string;
        nome: string;
        logo_url?: string | null;
    } | null;
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

const isAdminGeral = computed(() => props.dashboardType === 'admin_geral');
const isAdminEscola = computed(() => props.dashboardType === 'admin_escola');

const adminGeralStats = computed(() => (isAdminGeral.value ? (props.stats as AdminGeralStats) : null));
const adminEscolaStats = computed(() => (isAdminEscola.value ? (props.stats as AdminEscolaStats) : null));
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
                            <template v-if="isAdminGeral">
                                Bem-vindo ao painel de administração geral.
                            </template>
                            <template v-else>
                                Bem-vindo ao painel de administração da escola.
                            </template>
                        </p>

                        <div class="mt-4 flex items-center gap-2">
                            <span class="text-sm text-white/80">Perfil</span>
                            <Badge
                                variant="secondary"
                                class="border-white/20 bg-white/15 text-white"
                            >
                                <template v-if="isAdminGeral">Administrador Geral</template>
                                <template v-else>Administrador Escola</template>
                            </Badge>
                            <template v-if="isAdminEscola && props.tenant">
                                <span class="text-sm text-white/80">•</span>
                                <span class="text-sm text-white/80">{{ props.tenant.nome }}</span>
                            </template>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Dashboard Administrador Geral -->
            <template v-if="isAdminGeral && adminGeralStats">
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
                                    {{ adminGeralStats.escolas.total }}
                                </p>
                                <div class="mt-3 flex gap-2 text-xs">
                                    <span class="text-muted-foreground">
                                        {{ adminGeralStats.escolas.ativas }} ativas
                                    </span>
                                    <span class="text-muted-foreground">•</span>
                                    <span class="text-muted-foreground">
                                        {{ adminGeralStats.escolas.inativas }} inativas
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
                                    {{ adminGeralStats.planos.total }}
                                </p>
                                <div class="mt-3 flex gap-2 text-xs">
                                    <span class="text-muted-foreground">
                                        {{ adminGeralStats.planos.ativos }} ativos
                                    </span>
                                    <span class="text-muted-foreground">•</span>
                                    <span class="text-muted-foreground">
                                        {{ adminGeralStats.planos.inativos }} inativos
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
                                    {{ adminGeralStats.assinaturas.total }}
                                </p>
                                <div class="mt-3 flex flex-wrap gap-x-2 gap-y-1 text-xs">
                                    <span class="text-muted-foreground">
                                        {{ adminGeralStats.assinaturas.ativas }} ativas
                                    </span>
                                    <span class="text-muted-foreground">•</span>
                                    <span class="text-muted-foreground">
                                        {{ adminGeralStats.assinaturas.pendentes }} pendentes
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
                                    {{ adminGeralStats.usuarios.total }}
                                </p>
                                <div class="mt-3 flex gap-2 text-xs">
                                    <span class="text-muted-foreground">
                                        {{ adminGeralStats.usuarios.ativos }} ativos
                                    </span>
                                    <span class="text-muted-foreground">•</span>
                                    <span class="text-muted-foreground">
                                        {{ adminGeralStats.usuarios.inativos }} inativos
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
                                    {{ adminGeralStats.escolas.ativas }}
                                </p>
                                <p class="mt-1 text-xs text-muted-foreground">
                                    de {{ adminGeralStats.escolas.total }} total
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
                                    {{ adminGeralStats.planos.ativos }}
                                </p>
                                <p class="mt-1 text-xs text-muted-foreground">
                                    de {{ adminGeralStats.planos.total }} total
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
                                    {{ adminGeralStats.assinaturas.ativas }}
                                </p>
                                <p class="mt-1 text-xs text-muted-foreground">
                                    de {{ adminGeralStats.assinaturas.total }} total
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
                                    {{ adminGeralStats.usuarios.ativos }}
                                </p>
                                <p class="mt-1 text-xs text-muted-foreground">
                                    de {{ adminGeralStats.usuarios.total }} total
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
            </template>

            <!-- Dashboard Administrador Escola -->
            <template v-else-if="isAdminEscola && adminEscolaStats">
                <!-- KPIs - Estatísticas Principais -->
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <!-- Card Professores -->
                    <Card class="p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <p class="text-sm text-muted-foreground">
                                    Total de Professores
                                </p>
                                <p class="mt-2 text-3xl font-semibold">
                                    {{ adminEscolaStats.professores.total }}
                                </p>
                                <div class="mt-3 flex gap-2 text-xs">
                                    <span class="text-muted-foreground">
                                        {{ adminEscolaStats.professores.ativos }} ativos
                                    </span>
                                    <span class="text-muted-foreground">•</span>
                                    <span class="text-muted-foreground">
                                        {{ adminEscolaStats.professores.inativos }} inativos
                                    </span>
                                </div>
                            </div>
                            <div
                                class="flex size-12 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-900/30"
                            >
                                <UserCheck class="size-6 text-blue-600 dark:text-blue-400" />
                            </div>
                        </div>
                        <div class="mt-4">
                            <Link href="/school/teachers">
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

                    <!-- Card Alunos -->
                    <Card class="p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <p class="text-sm text-muted-foreground">
                                    Total de Alunos
                                </p>
                                <p class="mt-2 text-3xl font-semibold">
                                    {{ adminEscolaStats.alunos.total }}
                                </p>
                                <div class="mt-3 flex gap-2 text-xs">
                                    <span class="text-muted-foreground">
                                        {{ adminEscolaStats.alunos.ativos }} ativos
                                    </span>
                                    <span class="text-muted-foreground">•</span>
                                    <span class="text-muted-foreground">
                                        {{ adminEscolaStats.alunos.inativos }} inativos
                                    </span>
                                </div>
                            </div>
                            <div
                                class="flex size-12 items-center justify-center rounded-xl bg-green-100 dark:bg-green-900/30"
                            >
                                <GraduationCap class="size-6 text-green-600 dark:text-green-400" />
                            </div>
                        </div>
                        <div class="mt-4">
                            <Link href="/school/students">
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

                    <!-- Card Turmas -->
                    <Card class="p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <p class="text-sm text-muted-foreground">
                                    Total de Turmas
                                </p>
                                <p class="mt-2 text-3xl font-semibold">
                                    {{ adminEscolaStats.turmas.total }}
                                </p>
                                <div class="mt-3 flex gap-2 text-xs">
                                    <span class="text-muted-foreground">
                                        {{ adminEscolaStats.turmas.ativas }} ativas
                                    </span>
                                    <span class="text-muted-foreground">•</span>
                                    <span class="text-muted-foreground">
                                        {{ adminEscolaStats.turmas.inativas }} inativas
                                    </span>
                                </div>
                            </div>
                            <div
                                class="flex size-12 items-center justify-center rounded-xl bg-purple-100 dark:bg-purple-900/30"
                            >
                                <BookOpen class="size-6 text-purple-600 dark:text-purple-400" />
                            </div>
                        </div>
                        <div class="mt-4">
                            <Link href="/school/classes">
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

                    <!-- Card Responsáveis -->
                    <Card class="p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <p class="text-sm text-muted-foreground">
                                    Total de Responsáveis
                                </p>
                                <p class="mt-2 text-3xl font-semibold">
                                    {{ adminEscolaStats.responsaveis.total }}
                                </p>
                                <div class="mt-3 flex gap-2 text-xs">
                                    <span class="text-muted-foreground">
                                        {{ adminEscolaStats.responsaveis.ativos }} ativos
                                    </span>
                                    <span class="text-muted-foreground">•</span>
                                    <span class="text-muted-foreground">
                                        {{ adminEscolaStats.responsaveis.inativos }} inativos
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
                            <Link href="/school/parents">
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
                                    Resumo da Escola
                                </p>
                                <p class="text-sm text-muted-foreground">
                                    Visão geral das principais métricas
                                </p>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="rounded-xl border border-border/70 p-4">
                                <div class="flex items-center gap-2 mb-2">
                                    <UserCheck class="size-4 text-blue-600 dark:text-blue-400" />
                                    <p class="text-xs font-medium text-muted-foreground">
                                        Professores Ativos
                                    </p>
                                </div>
                                <p class="text-2xl font-semibold">
                                    {{ adminEscolaStats.professores.ativos }}
                                </p>
                                <p class="mt-1 text-xs text-muted-foreground">
                                    de {{ adminEscolaStats.professores.total }} total
                                </p>
                            </div>

                            <div class="rounded-xl border border-border/70 p-4">
                                <div class="flex items-center gap-2 mb-2">
                                    <GraduationCap class="size-4 text-green-600 dark:text-green-400" />
                                    <p class="text-xs font-medium text-muted-foreground">
                                        Alunos Ativos
                                    </p>
                                </div>
                                <p class="text-2xl font-semibold">
                                    {{ adminEscolaStats.alunos.ativos }}
                                </p>
                                <p class="mt-1 text-xs text-muted-foreground">
                                    de {{ adminEscolaStats.alunos.total }} total
                                </p>
                            </div>

                            <div class="rounded-xl border border-border/70 p-4">
                                <div class="flex items-center gap-2 mb-2">
                                    <BookOpen class="size-4 text-purple-600 dark:text-purple-400" />
                                    <p class="text-xs font-medium text-muted-foreground">
                                        Turmas Ativas
                                    </p>
                                </div>
                                <p class="text-2xl font-semibold">
                                    {{ adminEscolaStats.turmas.ativas }}
                                </p>
                                <p class="mt-1 text-xs text-muted-foreground">
                                    de {{ adminEscolaStats.turmas.total }} total
                                </p>
                            </div>

                            <div class="rounded-xl border border-border/70 p-4">
                                <div class="flex items-center gap-2 mb-2">
                                    <Users class="size-4 text-orange-600 dark:text-orange-400" />
                                    <p class="text-xs font-medium text-muted-foreground">
                                        Responsáveis Ativos
                                    </p>
                                </div>
                                <p class="text-2xl font-semibold">
                                    {{ adminEscolaStats.responsaveis.ativos }}
                                </p>
                                <p class="mt-1 text-xs text-muted-foreground">
                                    de {{ adminEscolaStats.responsaveis.total }} total
                                </p>
                            </div>
                        </div>
                    </Card>

                    <!-- Ações Rápidas -->
                    <Card class="p-6">
                        <p class="text-lg font-semibold mb-4">Ações Rápidas</p>
                        <div class="grid gap-2">
                            <Link href="/school/teachers">
                                <Button variant="outline" class="w-full justify-start">
                                    <UserCheck class="size-4 mr-2" />
                                    Gerenciar Professores
                                </Button>
                            </Link>
                            <Link href="/school/students">
                                <Button variant="outline" class="w-full justify-start">
                                    <GraduationCap class="size-4 mr-2" />
                                    Gerenciar Alunos
                                </Button>
                            </Link>
                            <Link href="/school/classes">
                                <Button variant="outline" class="w-full justify-start">
                                    <BookOpen class="size-4 mr-2" />
                                    Gerenciar Turmas
                                </Button>
                            </Link>
                            <Link href="/school/parents">
                                <Button variant="outline" class="w-full justify-start">
                                    <Users class="size-4 mr-2" />
                                    Gerenciar Responsáveis
                                </Button>
                            </Link>
                        </div>
                    </Card>
                </div>
            </template>
        </div>
    </AppLayout>
</template>
