<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Label } from '@/components/ui/label';

defineProps<{
    id: string;
    label: string;
    required?: boolean;
    error?: string;
}>();
</script>

<template>
    <div class="flex flex-col gap-1.5">
        <Label :for="id" class="gap-1">
            {{ label }}
            <span v-if="required" class="text-destructive">*</span>
        </Label>
        <slot />
        <InputError :message="error" />
    </div>
</template>
