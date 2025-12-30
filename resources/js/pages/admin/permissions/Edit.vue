<script setup lang="ts">
import PermissionsController from '@/actions/App/Http/Controllers/Admin/PermissionsController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { edit as editRoute, index } from '@/routes/permissions';
import type { BreadcrumbItem } from '@/types';
import { Form, Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Save, KeyRound } from 'lucide-vue-next';

interface PermissionRow {
    id: number;
    name: string;
    guard_name: string;
}

interface Props {
    permission: PermissionRow;
}

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Permissões',
        href: index().url,
    },
    {
        title: 'Editar permissão',
        href: editRoute({ permission: props.permission.id }).url,
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Editar permissão" />

        <div class="space-y-6">
            <div class="flex items-start justify-between gap-4">
                <div class="mt-2">
                    <Heading
                        title="Editar permissão"
                        description="Atualize os dados da permissão"
                        :icon="KeyRound"
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
                    v-bind="
                        PermissionsController.update.form({
                            permission: props.permission.id,
                        })
                    "
                    class="space-y-6"
                    v-slot="{ errors, processing }"
                >
                    <div class="grid gap-2">
                        <Label for="name">Nome</Label>
                        <Input
                            id="name"
                            name="name"
                            :default-value="props.permission.name"
                            required
                        />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="flex items-center justify-end gap-2">
                        <Button
                            type="submit"
                            :disabled="processing"
                            class="flex items-center gap-2"
                        >
                            <Save class="h-4 w-4" />
                            Salvar
                        </Button>
                    </div>
                </Form>
            </div>
        </div>
    </AppLayout>
</template>


