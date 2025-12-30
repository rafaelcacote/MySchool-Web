<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';
import { Form, Head } from '@inertiajs/vue3';
import { School } from 'lucide-vue-next';
import { computed, ref } from 'vue';

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();

const cpfInput = ref('');
const selectedTenantId = ref('');
const tenants = ref<Array<{ id: string; name: string }>>([]);
const isAdminGeral = ref(false);
const isLoadingTenants = ref(false);
const showTenantField = ref(false);

function formatCPF(value: string): string {
    const numbers = value.replace(/\D/g, '');
    const limitedNumbers = numbers.slice(0, 11);
    if (limitedNumbers.length <= 3) {
        return limitedNumbers;
    } else if (limitedNumbers.length <= 6) {
        return `${limitedNumbers.slice(0, 3)}.${limitedNumbers.slice(3)}`;
    } else if (limitedNumbers.length <= 9) {
        return `${limitedNumbers.slice(0, 3)}.${limitedNumbers.slice(3, 6)}.${limitedNumbers.slice(6)}`;
    } else {
        return `${limitedNumbers.slice(0, 3)}.${limitedNumbers.slice(3, 6)}.${limitedNumbers.slice(6, 9)}-${limitedNumbers.slice(9, 11)}`;
    }
}

function handleCPFInput(value: string) {
    const numbers = value.replace(/\D/g, '');
    const limitedNumbers = numbers.slice(0, 11);
    cpfInput.value = formatCPF(limitedNumbers);
    
    // Buscar escolas quando o CPF tiver 11 dígitos
    if (limitedNumbers.length === 11) {
        fetchTenants(limitedNumbers);
    } else {
        // Limpar dados se CPF incompleto
        tenants.value = [];
        selectedTenantId.value = '';
        showTenantField.value = false;
        isAdminGeral.value = false;
    }
}

async function fetchTenants(cpf: string) {
    isLoadingTenants.value = true;
    try {
        const response = await fetch(`/api/auth/tenants-by-cpf?cpf=${encodeURIComponent(cpf)}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
            },
        });
        
        if (!response.ok) {
            throw new Error('Erro ao buscar escolas');
        }
        
        const data = await response.json();
        
        tenants.value = data.tenants || [];
        isAdminGeral.value = data.is_admin_geral || false;
        
        // Se for admin geral sem escolas, não mostrar o campo
        if (isAdminGeral.value && tenants.value.length === 0) {
            showTenantField.value = false;
            selectedTenantId.value = '';
        }
        // Se tiver apenas uma escola, pré-selecionar
        else if (tenants.value.length === 1) {
            showTenantField.value = true;
            selectedTenantId.value = tenants.value[0].id;
        }
        // Se tiver múltiplas escolas, mostrar o campo
        else if (tenants.value.length > 1) {
            showTenantField.value = true;
            selectedTenantId.value = '';
        }
        // Se não tiver escolas e não for admin geral, não mostrar
        else {
            showTenantField.value = false;
            selectedTenantId.value = '';
        }
    } catch (error) {
        tenants.value = [];
        selectedTenantId.value = '';
        showTenantField.value = false;
        isAdminGeral.value = false;
    } finally {
        isLoadingTenants.value = false;
    }
}

const isTenantRequired = computed(() => {
    return showTenantField.value && tenants.value.length > 1;
});
</script>

<template>
    <AuthBase
        title="Acesse sua conta"
        description="Informe seu CPF e senha para entrar"
    >
        <Head title="Entrar" />

        <div
            v-if="status"
            class="mb-4 text-center text-sm font-medium text-green-600"
        >
            {{ status }}
        </div>

        <Form
            v-bind="store.form()"
            :reset-on-success="['password']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="cpf">CPF</Label>
                    <Input
                        id="cpf"
                        type="text"
                        name="cpf"
                        v-model="cpfInput"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="username"
                        placeholder="000.000.000-00"
                        @update:model-value="handleCPFInput"
                    />
                    <InputError :message="errors.cpf" />
                    <input
                        type="hidden"
                        name="cpf"
                        :value="cpfInput.replace(/\D/g, '')"
                    />
                </div>

                <div
                    v-if="showTenantField"
                    class="grid gap-2 transition-all"
                >
                    <Label for="tenant_id" class="flex items-center gap-2">
                        <School class="h-4 w-4" />
                        Escola
                    </Label>
                    <select
                        id="tenant_id"
                        name="tenant_id"
                        v-model="selectedTenantId"
                        :required="isTenantRequired"
                        :tabindex="2"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <option value="" disabled>
                            {{ isLoadingTenants ? 'Carregando...' : 'Selecione uma escola' }}
                        </option>
                        <option
                            v-for="tenant in tenants"
                            :key="tenant.id"
                            :value="tenant.id"
                        >
                            {{ tenant.name }}
                        </option>
                    </select>
                    <InputError :message="errors.tenant_id" />
                    <p
                        v-if="tenants.length === 1"
                        class="text-xs text-muted-foreground"
                    >
                        Escola pré-selecionada automaticamente
                    </p>
                    <!-- Campo hidden para garantir que o tenant_id seja enviado -->
                    <input
                        v-if="selectedTenantId"
                        type="hidden"
                        name="tenant_id"
                        :value="selectedTenantId"
                    />
                </div>

                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <Label for="password">Senha</Label>
                        <TextLink
                            v-if="canResetPassword"
                            :href="request()"
                            class="text-sm"
                            :tabindex="5"
                        >
                            Esqueceu a senha?
                        </TextLink>
                    </div>
                    <Input
                        id="password"
                        type="password"
                        name="password"
                        required
                        :tabindex="showTenantField ? 3 : 2"
                        autocomplete="current-password"
                        placeholder="Sua senha"
                    />
                    <InputError :message="errors.password" />
                </div>

                <div class="flex items-center justify-between">
                    <Label for="remember" class="flex items-center space-x-3">
                        <Checkbox id="remember" name="remember" :tabindex="showTenantField ? 4 : 3" />
                        <span>Lembrar de mim</span>
                    </Label>
                </div>

                <Button
                    type="submit"
                    class="mt-4 w-full"
                    :tabindex="showTenantField ? 5 : 4"
                    :disabled="processing || (isTenantRequired && !selectedTenantId)"
                    data-test="login-button"
                >
                    <Spinner v-if="processing" />
                    Entrar
                </Button>
            </div>

            <div
                class="text-center text-sm text-muted-foreground"
                v-if="canRegister"
            >
                Não tem uma conta?
                <TextLink :href="register()" :tabindex="5">Criar conta</TextLink>
            </div>
        </Form>
    </AuthBase>
</template>
