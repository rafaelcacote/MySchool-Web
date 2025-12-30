<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';
import type { User } from '@/types';
import { computed } from 'vue';

interface Props {
    user: User;
    showEmail?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    showEmail: false,
});

const { getInitials } = useInitials();

// Compute whether we should show the avatar image
const showAvatar = computed(
    () => props.user.avatar_url && props.user.avatar_url !== '',
);
</script>

<template>
    <div class="flex items-center gap-2 min-w-0 text-sidebar-foreground">
        <Avatar class="h-8 w-8 shrink-0 overflow-hidden rounded-lg">
            <AvatarImage
                v-if="showAvatar"
                :src="user.avatar_url!"
                :alt="user.nome_completo || user.name"
            />
            <AvatarFallback class="rounded-lg text-black dark:text-white">
                {{ getInitials(user.nome_completo || user.name) }}
            </AvatarFallback>
        </Avatar>

        <div class="grid flex-1 text-left text-sm leading-tight min-w-0">
            <span class="truncate font-medium">{{
                user.nome_completo || user.name
            }}</span>
            <span
                v-if="showEmail"
                class="truncate text-xs opacity-70"
            >{{
                user.email
            }}</span>
        </div>
    </div>
</template>
