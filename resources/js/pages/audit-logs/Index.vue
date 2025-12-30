<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import Pagination from '@/components/Pagination.vue';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { index } from '@/routes/audit-logs';
import type { BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { FileSearch } from 'lucide-vue-next';
import { computed, ref } from 'vue';

function formatDate(dateString: string | null | undefined): string {
    if (!dateString) return '—';
    
    try {
        const date = new Date(dateString);
        return new Intl.DateTimeFormat('pt-BR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
        }).format(date);
    } catch {
        return dateString;
    }
}

function getAcaoVariant(acao: string | null | undefined): 'default' | 'secondary' | 'destructive' | 'outline' {
    const acaoMap: Record<string, 'default' | 'secondary' | 'destructive' | 'outline'> = {
        'created': 'default',
        'updated': 'secondary',
        'deleted': 'destructive',
        'criado': 'default',
        'atualizado': 'secondary',
        'excluído': 'destructive',
        'excluido': 'destructive',
    };
    return acao ? (acaoMap[acao.toLowerCase()] || 'outline') : 'outline';
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface Paginated<T> {
    data: T[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

interface User {
    id: string;
    nome_completo: string;
    email: string;
}

interface Tenant {
    id: string;
    nome: string;
}

interface AuditLog {
    id: string;
    tenant_id: string;
    usuario_id: string;
    acao: string;
    tipo_entidade: string;
    entidade_id: string;
    valores_antigos?: Record<string, any>;
    valores_novos?: Record<string, any>;
    endereco_ip?: string;
    user_agent?: string;
    created_at: string;
    user?: User;
    tenant?: Tenant;
}

interface Props {
    logs: Paginated<AuditLog>;
    filters: {
        search?: string | null;
        acao?: string | null;
        tipo_entidade?: string | null;
    };
    acoes: string[];
    tiposEntidade: string[];
}

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Logs de Auditoria',
        href: index().url,
    },
];

const search = ref(props.filters.search ?? '');
const acao = ref(props.filters.acao ?? '');
const tipoEntidade = ref(props.filters.tipo_entidade ?? '');

const hasAnyFilter = computed(() => !!search.value || !!acao.value || !!tipoEntidade.value);

function applyFilters() {
    router.get(
        index().url,
        {
            search: search.value || undefined,
            acao: acao.value || undefined,
            tipo_entidade: tipoEntidade.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
}

function clearFilters() {
    search.value = '';
    acao.value = '';
    tipoEntidade.value = '';
    applyFilters();
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Logs de Auditoria" />

        <div class="space-y-6">
            <div class="flex items-start justify-between gap-4">
                <div class="mt-2">
                    <Heading
                        title="Logs de Auditoria"
                        description="Visualize todos os logs de ações realizadas no sistema"
                        :icon="FileSearch"
                    />
                </div>
            </div>

            <div class="rounded-xl border bg-card p-4 shadow-sm">
                <div
                    class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div class="flex flex-1 flex-col gap-3 sm:flex-row">
                        <div class="flex-1">
                            <Input
                                v-model="search"
                                placeholder="Buscar por ação, entidade, usuário ou escola..."
                                @keyup.enter="applyFilters"
                            />
                        </div>

                        <select
                            v-model="acao"
                            class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm sm:w-48"
                            @change="applyFilters"
                        >
                            <option value="">Todas ações</option>
                            <option
                                v-for="a in props.acoes"
                                :key="a"
                                :value="a"
                            >
                                {{ a }}
                            </option>
                        </select>

                        <select
                            v-model="tipoEntidade"
                            class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm sm:w-48"
                            @change="applyFilters"
                        >
                            <option value="">Todos tipos</option>
                            <option
                                v-for="tipo in props.tiposEntidade"
                                :key="tipo"
                                :value="tipo"
                            >
                                {{ tipo }}
                            </option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <Button variant="secondary" @click="applyFilters">
                            Filtrar
                        </Button>
                        <Button
                            v-if="hasAnyFilter"
                            variant="ghost"
                            @click="clearFilters"
                        >
                            Limpar
                        </Button>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border bg-card shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead
                            class="border-b bg-neutral-50 text-xs uppercase tracking-wide text-neutral-500 dark:bg-neutral-900/40 dark:text-neutral-400"
                        >
                            <tr>
                                <th class="px-4 py-3">Data/Hora</th>
                                <th class="px-4 py-3">Usuário</th>
                                <th class="px-4 py-3">Escola</th>
                                <th class="px-4 py-3">Ação</th>
                                <th class="px-4 py-3">Tipo de Entidade</th>
                                <th class="px-4 py-3">IP</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr
                                v-for="log in props.logs.data"
                                :key="log.id"
                                class="border-b last:border-0"
                            >
                                <td class="px-4 py-3">
                                    {{ formatDate(log.created_at) }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium">
                                        {{ log.user?.nome_completo || '—' }}
                                    </div>
                                    <div
                                        v-if="log.user?.email"
                                        class="mt-0.5 text-xs text-muted-foreground"
                                    >
                                        {{ log.user.email }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    {{ log.tenant?.nome || '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    <Badge :variant="getAcaoVariant(log.acao)">
                                        {{ log.acao }}
                                    </Badge>
                                </td>
                                <td class="px-4 py-3">
                                    {{ log.tipo_entidade || '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-xs text-muted-foreground">
                                        {{ log.endereco_ip || '—' }}
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="props.logs.data.length === 0">
                                <td
                                    colspan="6"
                                    class="px-4 py-10 text-center text-sm text-muted-foreground"
                                >
                                    Nenhum log encontrado.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    class="flex flex-col gap-3 border-t p-4 sm:flex-row sm:items-center sm:justify-between"
                >
                    <p class="text-sm text-muted-foreground">
                        Total: <span class="font-medium">{{ props.logs.total }}</span>
                    </p>
                    <Pagination :links="props.logs.links" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

