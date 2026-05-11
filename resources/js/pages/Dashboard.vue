<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { dashboard } from '@/routes';
import type { Team } from '@/types';

defineOptions({
    layout: (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: props.currentTeam ? dashboard(props.currentTeam.slug) : '/',
            },
        ],
    }),
});

const page = usePage();
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex flex-col gap-8 p-2">
        <!-- Welcome -->
        <div class="rounded-xl border border-border bg-card px-8 py-10">
            <p class="mb-2 text-xs tracking-[0.25em] uppercase text-muted-foreground">Bem-vindo ao painel</p>
            <h1 class="font-['Cormorant_Garamond',serif] text-4xl font-light text-foreground">
                {{ page.props.auth.user?.name }}
            </h1>
            <p class="mt-3 text-sm text-muted-foreground">
                Gerencie seus produtos e sua equipe pelo painel abaixo.
            </p>
        </div>

        <!-- Decorative divider -->
        <div class="flex items-center gap-4">
            <div class="h-px flex-1 bg-border" />
            <span class="text-muted-foreground text-xs tracking-widest uppercase">Nice Rolim Ateliê</span>
            <div class="h-px flex-1 bg-border" />
        </div>

        <!-- Brand message -->
        <div class="grid gap-6 md:grid-cols-3">
            <div class="rounded-xl border border-border bg-secondary/50 p-6">
                <span class="text-2xl">✦</span>
                <p class="mt-3 font-['Cormorant_Garamond',serif] text-lg font-semibold text-foreground">
                    Peças Únicas
                </p>
                <p class="mt-1 text-sm text-muted-foreground">Cada joia criada com dedicação artesanal</p>
            </div>
            <div class="rounded-xl border border-border bg-secondary/50 p-6">
                <span class="text-2xl">🌿</span>
                <p class="mt-3 font-['Cormorant_Garamond',serif] text-lg font-semibold text-foreground">
                    Materiais Naturais
                </p>
                <p class="mt-1 text-sm text-muted-foreground">Flores preservadas, resina e algodão natural</p>
            </div>
            <div class="rounded-xl border border-border bg-secondary/50 p-6">
                <span class="text-2xl">💛</span>
                <p class="mt-3 font-['Cormorant_Garamond',serif] text-lg font-semibold text-foreground">
                    Feito com Amor
                </p>
                <p class="mt-1 text-sm text-muted-foreground">Do coração do nosso ateliê para o seu</p>
            </div>
        </div>
    </div>
</template>
