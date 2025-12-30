<script setup lang="ts">
import RolesController from '@/actions/App/Http/Controllers/Admin/RolesController';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { edit as editRoute, index } from '@/routes/roles';
import type { BreadcrumbItem } from '@/types';
import { Form, Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Shield } from 'lucide-vue-next';
import RoleForm from './Partials/RoleForm.vue';

interface PermissionRow {
    id: number;
    name: string;
}

interface RoleRow {
    id: number;
    name: string;
    guard_name: string;
    permissions: PermissionRow[];
}

interface Props {
    role: RoleRow;
    permissions: PermissionRow[];
}

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Perfis (Roles)',
        href: index().url,
    },
    {
        title: 'Editar perfil',
        href: editRoute({ role: props.role.id }).url,
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Editar perfil" />

        <div class="space-y-6">
            <div class="flex items-start justify-between gap-4">
                <div class="mt-2">
                    <Heading
                        title="Editar perfil"
                        description="Atualize as permissões do perfil"
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
                    v-bind="RolesController.update.form({ role: props.role.id })"
                    class="space-y-6"
                    v-slot="{ errors, processing }"
                >
                    <RoleForm
                        :role="props.role"
                        :permissions="props.permissions"
                        submit-label="Salvar"
                        :processing="processing"
                        :errors="errors"
                    />
                </Form>
            </div>
        </div>
    </AppLayout>
</template>


