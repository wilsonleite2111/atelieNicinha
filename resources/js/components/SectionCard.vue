<script setup lang="ts">
import { Card } from '@/components/ui/card';

defineProps<{
    title: string;
}>();
</script>

<template>
    <Card class="block p-6 shadow-none">
        <h2 class="mb-5 font-serif text-xl font-semibold text-foreground">
            {{ title }}
        </h2>
        <slot />
    </Card>
</template>
