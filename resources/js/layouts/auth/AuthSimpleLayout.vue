<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Toaster } from '@/components/ui/sonner';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="relative flex min-h-svh flex-col items-center justify-center overflow-hidden bg-[#FAF8F5] p-6 font-['Jost',sans-serif] md:p-10"
    >
        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="" />
        <link
            href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Jost:wght@300;400;500&display=swap"
            rel="stylesheet"
        />

        <!-- Decorative background orbs -->
        <div
            class="pointer-events-none absolute -top-32 -right-32 h-96 w-96 rounded-full bg-[#C4925A]/10 blur-3xl"
        ></div>
        <div
            class="pointer-events-none absolute -bottom-32 -left-32 h-96 w-96 rounded-full bg-[#7C5C3A]/10 blur-3xl"
        ></div>

        <!-- Subtle pattern overlay -->
        <div class="pointer-events-none absolute inset-0 opacity-[0.015]" style="background-image: radial-gradient(circle, #7C5C3A 1px, transparent 1px); background-size: 32px 32px;"></div>

        <div class="relative w-full max-w-sm">
            <div class="flex flex-col gap-8">

                <!-- Logo / Brand -->
                <div class="flex flex-col items-center gap-5">
                    <Link
                        :href="home()"
                        class="group flex flex-col items-center gap-1 transition-opacity hover:opacity-80"
                    >
                        <!-- Ornamental icon -->
                        <div class="mb-1 flex h-14 w-14 items-center justify-center rounded-full border border-[#D9CDBF] bg-white shadow-sm transition-all duration-300 group-hover:shadow-md group-hover:border-[#C4925A]">
                            <span class="font-['Cormorant_Garamond',serif] text-2xl italic text-[#7C5C3A]">N</span>
                        </div>
                        <span class="font-['Cormorant_Garamond',serif] text-xl font-semibold italic tracking-wide text-[#7C5C3A]">
                            Nice Rolim Ateliê
                        </span>
                        <span class="text-[9px] tracking-[0.25em] uppercase text-[#A8896C]">Resina &amp; Macramê</span>
                    </Link>

                    <!-- Title & description -->
                    <div v-if="title || description" class="space-y-1.5 text-center">
                        <h1
                            v-if="title"
                            class="font-['Cormorant_Garamond',serif] text-2xl font-semibold text-[#2C2416]"
                        >
                            {{ title }}
                        </h1>
                        <p
                            v-if="description"
                            class="text-sm leading-relaxed text-[#7C6A56]"
                        >
                            {{ description }}
                        </p>
                    </div>
                </div>

                <!-- Decorative divider -->
                <div class="flex items-center gap-3">
                    <div class="h-px flex-1 bg-[#D9CDBF]"></div>
                    <span class="text-[#C4925A] text-xs">✦</span>
                    <div class="h-px flex-1 bg-[#D9CDBF]"></div>
                </div>

                <!-- Form slot -->
                <slot />
            </div>
        </div>
    </div>
    <Toaster />
</template>
