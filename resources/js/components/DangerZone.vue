<script setup lang="ts">
import { Button } from '@/components/ui/button';

defineProps<{
    title: string;
    description: string;
}>();

defineEmits<{
    confirm: [];
}>();
</script>

<template>
    <div class="rounded-xl border border-destructive/30 bg-destructive/5 p-6">
        <p class="font-medium text-destructive">{{ title }}</p>
        <p class="mt-1 text-sm text-muted-foreground">{{ description }}</p>
        <Button
            type="button"
            variant="outline"
            size="pill"
            class="mt-4 h-9 border-destructive bg-transparent px-6 text-destructive hover:bg-destructive hover:text-destructive-foreground"
            @click="$emit('confirm')"
        >
            {{ title }}
        </Button>
    </div>
</template>
