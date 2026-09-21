<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';
import { create, destroy, edit, index } from '@/routes/categories';
import type { Category, Team } from '@/types';

type Props = {
    categories: Category[];
    canManageCategories: boolean;
};

const props = defineProps<Props>();

const page = usePage();

defineOptions({
    layout: (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Categorias',
                href: props.currentTeam ? index(props.currentTeam.slug) : '/',
            },
        ],
    }),
});

const deletingId = ref<number | null>(null);

const confirmDelete = (category: Category) => {
    if (!confirm(`Tem certeza que deseja excluir "${category.name}"? Esta ação não pode ser desfeita.`)) {
        return;
    }

    deletingId.value = category.id;

    router.visit(destroy([page.props.currentTeam!.slug, category.id]), {
        onFinish: () => {
            deletingId.value = null;
        },
    });
};
</script>

<template>
    <Head title="Categorias" />

    <div class="flex flex-col gap-8 p-2">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs tracking-[0.25em] uppercase text-muted-foreground">Catálogo</p>
                <h1 class="font-['Cormorant_Garamond',serif] text-4xl font-light text-foreground">Categorias</h1>
            </div>
            <a
                v-if="canManageCategories"
                :href="create(page.props.currentTeam!.slug).url"
                class="inline-flex items-center gap-2 rounded-full bg-primary px-6 py-2.5 text-xs tracking-widest uppercase text-primary-foreground transition-colors hover:opacity-90"
            >
                <Plus class="h-3.5 w-3.5" />
                Nova categoria
            </a>
        </div>

        <!-- Empty state -->
        <div v-if="categories.length === 0" class="flex flex-col items-center gap-3 py-16 text-center">
            <span class="text-4xl text-muted-foreground/40">✦</span>
            <p class="font-['Cormorant_Garamond',serif] text-xl font-light text-muted-foreground">
                Nenhuma categoria cadastrada ainda.
            </p>
        </div>

        <!-- List -->
        <div v-else class="flex flex-col gap-2">
            <div
                v-for="category in categories"
                :key="category.id"
                data-test="category-row"
                class="flex items-center justify-between rounded-xl border border-border bg-card px-5 py-4"
            >
                <div class="flex items-center gap-3">
                    <span class="font-medium text-foreground">{{ category.name }}</span>
                    <span
                        v-if="!category.active"
                        class="rounded-full bg-muted px-2.5 py-0.5 text-[10px] tracking-widest uppercase text-muted-foreground"
                    >
                        Inativa
                    </span>
                </div>

                <div v-if="canManageCategories" class="flex items-center gap-1">
                    <a
                        :href="edit([page.props.currentTeam!.slug, category.id]).url"
                        class="flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs text-muted-foreground transition-colors hover:bg-secondary hover:text-foreground"
                    >
                        <Pencil class="h-3.5 w-3.5" />
                        Editar
                    </a>
                    <button
                        :disabled="deletingId === category.id"
                        class="flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs text-muted-foreground transition-colors hover:bg-destructive/10 hover:text-destructive disabled:opacity-50"
                        @click="confirmDelete(category)"
                    >
                        <Trash2 class="h-3.5 w-3.5" />
                        Excluir
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
