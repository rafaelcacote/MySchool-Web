<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import Pagination from '@/components/Pagination.vue';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { index } from '@/routes/subscriptions';
import type { BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { FileText, Search } from 'lucide-vue-next';
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
        }).format(date);
    } catch {
        return dateString;
    }
}

function formatPrice(price: number | null | undefined): string {
    if (!price && price !== 0) return '—';
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(price);
}

function getStatusVariant(status: string | null | undefined): 'default' | 'secondary' | 'destructive' | 'outline' {
    const statusMap: Record<string, 'default' | 'secondary' | 'destructive' | 'outline'> = {
        'ativa': 'default',
        'cancelada': 'destructive',
        'suspensa': 'secondary',
        'expirada': 'outline',
    };
    return status ? (statusMap[status] || 'secondary') : 'secondary';
}

function formatStatus(status: string | null | undefined): string {
    if (!status) return '—';
    return status.charAt(0).toUpperCase() + status.slice(1);
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

interface Tenant {
    id: string;
    nome: string;
}

interface Plan {
    id: string;
    nome: string;
    preco: number;
}

interface Subscription {
    id: string;
    tenant_id: string;
    plano_id: string;
    status: string;
    data_inicio: string;
    data_fim: string | null;
    valor: number;
    periodo: string;
    created_at: string;
    updated_at: string;
    tenant?: Tenant;
    plan?: Plan;
}

interface Props {
    subscriptions: Paginated<Subscription>;
    filters: {
        search?: string | null;
        status?: string | null;
    };
}

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Assinaturas',
        href: index().url,
    },
];

const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? '');

const hasAnyFilter = computed(() => !!search.value || !!status.value);

function applyFilters() {
    router.get(
        index().url,
        {
            search: search.value || undefined,
            status: status.value || undefined,
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
    status.value = '';
    applyFilters();
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Assinaturas" />

        <div class="space-y-6">
            <div class="flex items-start justify-between gap-4">
                <div class="mt-2">
                    <Heading
                        title="Assinaturas"
                        description="Visualize todas as assinaturas cadastradas"
                        :icon="FileText"
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
                                placeholder="Buscar por escola ou plano..."
                                @keyup.enter="applyFilters"
                            />
                        </div>

                        <select
                            v-model="status"
                            class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm sm:w-48"
                            @change="applyFilters"
                        >
                            <option value="">Todos status</option>
                            <option value="ativa">Ativa</option>
                            <option value="cancelada">Cancelada</option>
                            <option value="suspensa">Suspensa</option>
                            <option value="expirada">Expirada</option>
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
                                <th class="px-4 py-3">Escola</th>
                                <th class="px-4 py-3">Plano</th>
                                <th class="px-4 py-3">Valor</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Início</th>
                                <th class="px-4 py-3">Fim</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr
                                v-for="subscription in props.subscriptions.data"
                                :key="subscription.id"
                                class="border-b last:border-0"
                            >
                                <td class="px-4 py-3">
                                    <div class="font-medium">
                                        {{ subscription.tenant?.nome || '—' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    {{ subscription.plan?.nome || '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ formatPrice(subscription.valor) }}
                                </td>
                                <td class="px-4 py-3">
                                    <Badge :variant="getStatusVariant(subscription.status)">
                                        {{ formatStatus(subscription.status) }}
                                    </Badge>
                                </td>
                                <td class="px-4 py-3">
                                    {{ formatDate(subscription.data_inicio) }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ formatDate(subscription.data_fim) }}
                                </td>
                            </tr>

                            <tr v-if="props.subscriptions.data.length === 0">
                                <td
                                    colspan="6"
                                    class="px-4 py-10 text-center text-sm text-muted-foreground"
                                >
                                    Nenhuma assinatura encontrada.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    class="flex flex-col gap-3 border-t p-4 sm:flex-row sm:items-center sm:justify-between"
                >
                    <p class="text-sm text-muted-foreground">
                        Total: <span class="font-medium">{{ props.subscriptions.total }}</span>
                    </p>
                    <Pagination :links="props.subscriptions.links" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

