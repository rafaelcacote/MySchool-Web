<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import Pagination from '@/components/Pagination.vue';
import DeletePermissionDialog from '@/components/permissions/DeletePermissionDialog.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { create, edit, index } from '@/routes/permissions';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Edit, Plus, KeyRound } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface Paginated<T> {
    data: T[];
    links: PaginationLink[];
    total: number;
}

interface PermissionRow {
    id: number;
    name: string;
    guard_name: string;
    created_at: string;
}

interface Props {
    permissions: Paginated<PermissionRow>;
    filters: {
        search?: string | null;
    };
}

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Permissões',
        href: index().url,
    },
];

const search = ref(props.filters.search ?? '');

const hasAnyFilter = computed(() => !!search.value);

function applyFilters() {
    router.get(
        index().url,
        {
            search: search.value || undefined,
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
    applyFilters();
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Permissões" />

        <div class="space-y-6">
            <div class="flex items-start justify-between gap-4">
                <div class="mt-2">
                    <Heading
                        title="Permissões"
                        description="Gerencie as permissões do sistema"
                        :icon="KeyRound"
                    />
                </div>

                <div class="mt-2">
                    <Button as-child>
                        <Link :href="create()" class="flex items-center gap-2">
                            <Plus class="h-4 w-4" />
                            Nova permissão
                        </Link>
                    </Button>
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
                                placeholder="Buscar por nome..."
                                @keyup.enter="applyFilters"
                            />
                        </div>
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
                                <th class="px-4 py-3">Nome</th>
                                <th class="px-4 py-3">Guard</th>
                                <th class="px-4 py-3 text-right">Ações</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr
                                v-for="p in props.permissions.data"
                                :key="p.id"
                                class="border-b last:border-0"
                            >
                                <td class="px-4 py-3">
                                    <div class="font-medium">{{ p.name }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <Badge variant="secondary">{{
                                        p.guard_name
                                    }}</Badge>
                                </td>
                                <td class="px-4 py-3">
                                    <div
                                        class="flex items-center justify-end gap-2"
                                    >
                                        <Button
                                            as-child
                                            size="sm"
                                            variant="ghost"
                                            class="hover:bg-transparent"
                                        >
                                            <Link
                                                :href="
                                                    edit({
                                                        permission: p.id,
                                                    })
                                                "
                                            >
                                                <Edit
                                                    class="h-4 w-4 text-amber-500 dark:text-amber-400"
                                                />
                                            </Link>
                                        </Button>

                                        <DeletePermissionDialog
                                            :permission="p"
                                        />
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="props.permissions.data.length === 0">
                                <td
                                    colspan="3"
                                    class="px-4 py-10 text-center text-sm text-muted-foreground"
                                >
                                    Nenhuma permissão encontrada.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    class="flex flex-col gap-3 border-t p-4 sm:flex-row sm:items-center sm:justify-between"
                >
                    <p class="text-sm text-muted-foreground">
                        Total:
                        <span class="font-medium">{{
                            props.permissions.total
                        }}</span>
                    </p>
                    <Pagination :links="props.permissions.links" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>


