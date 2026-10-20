<script setup lang="ts">
defineProps<{
    eyebrow: string;
    title: string;
}>();
</script>

<template>
    <div>
        <p class="text-xs tracking-[0.25em] text-muted-foreground uppercase">
            {{ eyebrow }}
        </p>
        <h1 class="font-serif text-4xl font-light text-foreground">
            {{ title }}
        </h1>
    </div>
</template>
