<script setup lang="ts">
import { Button } from '@/components/ui/button';
import Heading from '@/components/Heading.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Form, Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, GraduationCap } from 'lucide-vue-next';
import StudentForm from './Partials/StudentForm.vue';

interface Student {
    id: string;
    nome_completo: string;
    cpf?: string | null;
    data_nascimento?: string | null;
    data_matricula?: string | null;
    telefone?: string | null;
    email?: string | null;
    matricula?: string | null;
    serie?: string | null;
    turma?: string | null;
    ativo: boolean;
    informacoes_medicas?: string | null;
}

interface Props {
    student: Student;
}

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Alunos',
        href: '/school/students',
    },
    {
        title: 'Editar',
        href: `/school/students/${props.student.id}/edit`,
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="`Editar aluno: ${props.student.nome_completo}`" />

        <div class="space-y-6">
            <div class="flex items-start justify-between gap-4">
                <div class="mt-2">
                    <Heading
                        :title="props.student.nome_completo"
                        description="Atualize os dados do aluno"
                        :icon="GraduationCap"
                    />
                </div>

                <Button
                    variant="ghost"
                    as-child
                    class="mt-4 rounded-lg border border-input bg-background shadow-sm transition-all hover:bg-accent hover:text-accent-foreground hover:shadow-md"
                >
                    <Link href="/school/students" class="flex items-center gap-2 px-4 py-2">
                        <ArrowLeft class="h-4 w-4" />
                        Voltar
                    </Link>
                </Button>
            </div>

            <div class="rounded-xl border bg-card p-6 shadow-sm">
                <Form
                    :action="`/school/students/${props.student.id}`"
                    method="patch"
                    class="space-y-6"
                    v-slot="{ errors, processing }"
                >
                    <StudentForm
                        :student="props.student"
                        submit-label="Salvar alterações"
                        :processing="processing"
                        :errors="errors"
                    />
                </Form>
            </div>
        </div>
    </AppLayout>
</template>

