<script setup lang="ts">
import { Button } from '@/components/ui/button';

defineProps<{
    submitLabel: string;
    cancelHref: string;
    processing: boolean;
}>();
</script>

<template>
    <div class="flex items-center gap-4">
        <Button type="submit" size="pill" :disabled="processing">
            {{ submitLabel }}
        </Button>
        <a
            :href="cancelHref"
            class="text-sm text-muted-foreground transition-colors hover:text-foreground"
        >
            Cancelar
        </a>
    </div>
</template>
