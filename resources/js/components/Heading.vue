<script setup lang="ts">
import type { Component } from 'vue';

interface Props {
    title: string;
    description?: string;
    icon?: Component;
}

defineProps<Props>();
</script>

<template>
    <div class="mb-8 space-y-0.5">
        <h2 class="flex items-center gap-2 text-xl font-semibold tracking-tight">
            <component v-if="icon" :is="icon" class="h-5 w-5" />
            {{ title }}
        </h2>
        <p v-if="description" class="text-sm text-muted-foreground">
            {{ description }}
        </p>
    </div>
</template>
