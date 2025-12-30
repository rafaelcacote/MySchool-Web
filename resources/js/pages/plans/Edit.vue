<script setup lang="ts">
import PlansController from '@/actions/App/Http/Controllers/PlansController';
import { Button } from '@/components/ui/button';
import Heading from '@/components/Heading.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { edit as plansEdit, index } from '@/routes/plans';
import type { BreadcrumbItem } from '@/types';
import { Form, Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, CreditCard } from 'lucide-vue-next';
import PlanForm from './Partials/PlanForm.vue';

interface Plan {
    id: string;
    nome: string;
    descricao?: string;
    preco_mensal: number;
    preco_anual?: number;
    ativo: boolean;
    max_alunos?: number;
    max_professores?: number;
    max_armazenamento_mb?: number;
    caracteristicas?: Record<string, any>;
    created_at: string;
    updated_at: string;
}

interface Props {
    plan: Plan;
}

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Planos',
        href: index().url,
    },
    {
        title: 'Editar',
        href: plansEdit({ plan: props.plan.id }).url,
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="`Editar plano: ${props.plan.nome}`" />

        <div class="space-y-6">
            <div class="flex items-start justify-between gap-4">
                <div class="mt-2">
                    <Heading
                        :title="props.plan.nome"
                        description="Atualize os dados do plano"
                        :icon="CreditCard"
                    />
                </div>

                <Button
                    variant="ghost"
                    as-child
                    class="mt-4 rounded-lg border border-input bg-background shadow-sm transition-all hover:bg-accent hover:text-accent-foreground hover:shadow-md"
                >
                    <Link :href="index()" class="flex items-center gap-2 px-4 py-2">
                        <ArrowLeft class="h-4 w-4" />
                        Voltar
                    </Link>
                </Button>
            </div>

            <div class="rounded-xl border bg-card p-6 shadow-sm">
                <Form
                    v-bind="PlansController.update.form({ plan: props.plan.id })"
                    class="space-y-6"
                    v-slot="{ errors, processing }"
                >
                    <PlanForm
                        :plan="props.plan"
                        submit-label="Salvar alterações"
                        :processing="processing"
                        :errors="errors"
                    />
                </Form>

                <div class="mt-6 border-t pt-6">
                    <p class="text-sm font-medium">Dados do sistema</p>
                    <div
                        class="mt-2 grid gap-2 text-sm text-muted-foreground sm:grid-cols-2"
                    >
                        <div>
                            <span class="font-medium text-foreground">Criado em:</span>
                            {{ new Date(props.plan.created_at).toLocaleString('pt-BR') }}
                        </div>
                        <div>
                            <span class="font-medium text-foreground">Atualizado em:</span>
                            {{ new Date(props.plan.updated_at).toLocaleString('pt-BR') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

