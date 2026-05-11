<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import { Pencil, Plus, Search, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';
import { create, destroy, edit, index } from '@/routes/products';
import type { Product, Team } from '@/types';

type Props = {
    products: Product[];
    canManageProducts: boolean;
    search: string;
};

const props = defineProps<Props>();

const page = usePage();

defineOptions({
    layout: (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Produtos',
                href: props.currentTeam ? index(props.currentTeam.slug) : '/',
            },
        ],
    }),
});

const searchQuery = ref(props.search ?? '');
const deletingId = ref<number | null>(null);

const doSearch = () => {
    router.get(
        index(page.props.currentTeam!.slug).url,
        { search: searchQuery.value },
        { replace: true, preserveState: true },
    );
};

const confirmDelete = (product: Product) => {
    if (!confirm(`Tem certeza que deseja excluir "${product.name}"? Esta ação não pode ser desfeita.`)) {
        return;
    }

    deletingId.value = product.id;

    router.visit(destroy([page.props.currentTeam!.slug, product.id]), {
        onFinish: () => {
            deletingId.value = null;
        },
    });
};

const formatPrice = (price: string) =>
    new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(price));
</script>

<template>
    <Head title="Produtos" />

    <div class="flex flex-col gap-8 p-2">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs tracking-[0.25em] uppercase text-muted-foreground">Catálogo</p>
                <h1 class="font-['Cormorant_Garamond',serif] text-4xl font-light text-foreground">Produtos</h1>
            </div>
            <a
                v-if="canManageProducts"
                :href="create(page.props.currentTeam!.slug).url"
                data-test="new-product-button"
                class="inline-flex items-center gap-2 rounded-full bg-primary px-6 py-2.5 text-xs tracking-widest uppercase text-primary-foreground transition-colors hover:opacity-90"
            >
                <Plus class="h-3.5 w-3.5" />
                Novo produto
            </a>
        </div>

        <!-- Search -->
        <form @submit.prevent="doSearch" class="relative">
            <Search class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
            <input
                v-model="searchQuery"
                type="search"
                placeholder="Buscar por nome ou SKU…"
                class="w-full rounded-full border border-border bg-card py-3 pl-11 pr-4 text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring/40 sm:max-w-sm"
            />
        </form>

        <!-- Empty state -->
        <div v-if="products.length === 0" class="flex flex-col items-center gap-3 py-16 text-center">
            <span class="text-4xl text-muted-foreground/40">✦</span>
            <p class="font-['Cormorant_Garamond',serif] text-xl font-light text-muted-foreground">
                {{ search ? 'Nenhum produto encontrado para a busca.' : 'Nenhum produto cadastrado ainda.' }}
            </p>
        </div>

        <!-- Product cards -->
        <div v-else class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            <div
                v-for="product in products"
                :key="product.id"
                data-test="product-row"
                class="group relative flex flex-col overflow-hidden rounded-xl border border-border bg-card transition-shadow hover:shadow-md"
            >
                <!-- Image -->
                <div class="relative aspect-square overflow-hidden bg-secondary">
                    <img
                        v-if="product.thumbnail"
                        :src="product.thumbnail"
                        :alt="product.name"
                        class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                    />
                    <div
                        v-else
                        class="flex h-full w-full items-center justify-center text-4xl text-muted-foreground/20"
                    >
                        ✦
                    </div>

                    <!-- Inactive badge -->
                    <span
                        v-if="!product.active"
                        class="absolute left-3 top-3 rounded-full bg-foreground/70 px-2.5 py-0.5 text-[10px] tracking-widest uppercase text-primary-foreground"
                    >
                        Inativo
                    </span>
                </div>

                <!-- Info -->
                <div class="flex flex-1 flex-col gap-1 p-4">
                    <p class="font-['Cormorant_Garamond',serif] text-lg font-semibold leading-tight text-foreground">
                        {{ product.name }}
                    </p>
                    <p class="text-sm font-medium text-primary">{{ formatPrice(product.price) }}</p>
                    <p v-if="product.sku" class="text-xs text-muted-foreground">SKU: {{ product.sku }}</p>
                    <p
                        v-if="product.description"
                        class="mt-1 line-clamp-2 text-xs text-muted-foreground"
                    >
                        {{ product.description }}
                    </p>
                </div>

                <!-- Actions -->
                <div v-if="canManageProducts" class="flex items-center gap-1 border-t border-border px-4 py-3">
                    <a
                        :href="edit([page.props.currentTeam!.slug, product.id]).url"
                        data-test="product-edit-button"
                        class="flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs text-muted-foreground transition-colors hover:bg-secondary hover:text-foreground"
                    >
                        <Pencil class="h-3.5 w-3.5" />
                        Editar
                    </a>
                    <button
                        data-test="product-delete-button"
                        :disabled="deletingId === product.id"
                        class="flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs text-muted-foreground transition-colors hover:bg-destructive/10 hover:text-destructive disabled:opacity-50"
                        @click="confirmDelete(product)"
                    >
                        <Trash2 class="h-3.5 w-3.5" />
                        Excluir
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
