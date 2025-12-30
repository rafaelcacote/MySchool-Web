<script setup lang="ts">
import RolesController from '@/actions/App/Http/Controllers/Admin/RolesController';
import Heading from '@/components/Heading.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { index } from '@/routes/roles';
import type { BreadcrumbItem } from '@/types';
import { Form, Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Shield } from 'lucide-vue-next';
import RoleForm from './Partials/RoleForm.vue';
import { Button } from '@/components/ui/button';

interface PermissionRow {
    id: number;
    name: string;
}

interface Props {
    permissions: PermissionRow[];
}

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Perfis (Roles)',
        href: index().url,
    },
    {
        title: 'Novo perfil',
        href: '#',
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Novo perfil" />

        <div class="space-y-6">
            <div class="flex items-start justify-between gap-4">
                <div class="mt-2">
                    <Heading
                        title="Novo perfil"
                        description="Crie um novo perfil de acesso"
                        :icon="Shield"
                    />
                </div>

                <Button variant="outline" as-child class="rounded-lg">
                    <Link :href="index()" class="flex items-center gap-2">
                        <ArrowLeft class="h-4 w-4" />
                        Voltar
                    </Link>
                </Button>
            </div>

            <div class="rounded-xl border bg-card p-6 shadow-sm">
                <Form
                    v-bind="RolesController.store.form()"
                    reset-on-success
                    class="space-y-6"
                    v-slot="{ errors, processing }"
                >
                    <RoleForm
                        :permissions="props.permissions"
                        submit-label="Criar perfil"
                        :processing="processing"
                        :errors="errors"
                    />
                </Form>
            </div>
        </div>
    </AppLayout>
</template>


