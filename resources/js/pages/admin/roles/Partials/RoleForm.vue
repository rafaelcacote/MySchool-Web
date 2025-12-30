<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Save } from 'lucide-vue-next';
import { computed, ref, watchEffect } from 'vue';

type PermissionRow = {
    id: number;
    name: string;
};

type RoleRow = {
    id: number;
    name: string;
    guard_name: string;
    permissions?: PermissionRow[];
};

const props = defineProps<{
    role?: RoleRow;
    permissions: PermissionRow[];
    submitLabel: string;
    processing: boolean;
    errors: Record<string, string>;
}>();

const selectedPermissionIds = ref<number[]>([]);

watchEffect(() => {
    if (props.role?.permissions) {
        selectedPermissionIds.value = props.role.permissions.map((p) => p.id);
    } else {
        selectedPermissionIds.value = [];
    }
});

const permissionSearch = ref('');

// Função para obter o nome do grupo a partir do nome da permissão
const getGroupName = (permissionName: string): string => {
    const parts = permissionName.split('.');
    if (parts.length > 1) {
        return parts[0];
    }
    return 'Outros';
};

// Função para obter o nome amigável do grupo
const getGroupLabel = (groupName: string): string => {
    const labels: Record<string, string> = {
        escolas: 'Escolas',
        usuarios: 'Usuários',
        roles: 'Roles',
        permissoes: 'Permissões',
        assinaturas: 'Assinaturas',
        planos: 'Planos',
        logs: 'Logs do Sistema',
    };
    return labels[groupName] || groupName.charAt(0).toUpperCase() + groupName.slice(1);
};

// Função para obter a ação da permissão (parte após o ponto)
const getPermissionAction = (permissionName: string): string => {
    const parts = permissionName.split('.');
    if (parts.length > 1) {
        return parts.slice(1).join('.');
    }
    return permissionName;
};

// Função para obter o label amigável da ação
const getActionLabel = (action: string): string => {
    const labels: Record<string, string> = {
        visualizar: 'Visualizar',
        criar: 'Criar',
        editar: 'Editar',
        excluir: 'Excluir',
    };
    return labels[action] || action;
};

// Agrupar permissões filtradas por módulo
const groupedPermissions = computed(() => {
    const term = permissionSearch.value.trim().toLowerCase();
    let filtered = props.permissions;
    
    if (term) {
        filtered = props.permissions.filter((p) => 
            p.name.toLowerCase().includes(term)
        );
    }

    // Agrupar por módulo
    const groups: Record<string, PermissionRow[]> = {};
    
    filtered.forEach((permission) => {
        const groupName = getGroupName(permission.name);
        if (!groups[groupName]) {
            groups[groupName] = [];
        }
        groups[groupName].push(permission);
    });

    // Ordenar grupos e permissões dentro de cada grupo
    const sortedGroups: Record<string, PermissionRow[]> = {};
    Object.keys(groups)
        .sort()
        .forEach((groupName) => {
            sortedGroups[groupName] = groups[groupName].sort((a, b) => 
                a.name.localeCompare(b.name)
            );
        });

    return sortedGroups;
});

// Função para lidar com a mudança de seleção de permissão
const handlePermissionChange = (permissionId: number, checked: boolean) => {
    if (checked) {
        if (!selectedPermissionIds.value.includes(permissionId)) {
            selectedPermissionIds.value.push(permissionId);
        }
    } else {
        selectedPermissionIds.value = selectedPermissionIds.value.filter(
            (id: number) => id !== permissionId,
        );
    }
};
</script>

<template>
    <div class="grid gap-6">
        <div class="grid gap-2">
            <Label for="name">Nome</Label>
            <Input
                id="name"
                name="name"
                :default-value="role?.name ?? ''"
                placeholder="Ex: admin"
                required
            />
            <InputError :message="errors.name" />
        </div>

        <div class="grid gap-2">
            <div class="flex items-end justify-between gap-3">
                <Label>Permissões</Label>
                <div class="w-64">
                    <Input
                        v-model="permissionSearch"
                        placeholder="Buscar permissões..."
                    />
                </div>
            </div>

            <div class="rounded-xl border bg-card p-4 shadow-sm">
                <div v-if="Object.keys(groupedPermissions).length > 0" class="space-y-6">
                    <div
                        v-for="(permissions, groupName) in groupedPermissions"
                        :key="groupName"
                        class="space-y-3"
                    >
                        <h3 class="text-sm font-semibold text-foreground">
                            {{ getGroupLabel(groupName) }}
                        </h3>
                        <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                            <label
                                v-for="p in permissions"
                                :key="p.id"
                                class="flex items-center gap-2 rounded-md border border-input bg-background px-3 py-2 text-sm hover:bg-accent transition-colors"
                            >
                                <input
                                    type="checkbox"
                                    name="permissions[]"
                                    :value="p.id"
                                    class="h-4 w-4 rounded border border-input"
                                    :checked="selectedPermissionIds.includes(p.id)"
                                    @change="
                                        (e) => {
                                            handlePermissionChange(
                                                p.id,
                                                (e.target as HTMLInputElement).checked,
                                            );
                                        }
                                    "
                                />
                                <span class="text-muted-foreground">
                                    {{ getActionLabel(getPermissionAction(p.name)) }}
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <p
                    v-else
                    class="py-6 text-center text-sm text-muted-foreground"
                >
                    Nenhuma permissão encontrada.
                </p>
            </div>

            <InputError :message="errors.permissions" />
        </div>

        <div class="flex items-center justify-end gap-2">
            <Button type="submit" :disabled="processing" class="flex items-center gap-2">
                <Save class="h-4 w-4" />
                {{ submitLabel }}
            </Button>
        </div>
    </div>
</template>


