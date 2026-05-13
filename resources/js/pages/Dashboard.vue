<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Package, Plus, ShoppingBag, Users } from 'lucide-vue-next';
import { dashboard } from '@/routes';
import { create, index as productsIndex } from '@/routes/products';
import { index as usersIndex } from '@/routes/admin/users';
import { show as productShow } from '@/routes/products/public';
import type { Team } from '@/types';

type PublicProduct = {
    id: number;
    name: string;
    description: string | null;
    size: string | null;
    price: string;
    thumbnail: string;
};

defineProps<{
    products: PublicProduct[];
    canManageProducts: boolean;
}>();

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

const canManageUsers =
    page.props.currentTeam?.role === 'owner' || page.props.currentTeam?.role === 'admin';

const formatPrice = (price: string) =>
    new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(price));
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
                Explore nossa coleção e gerencie o ateliê pelo painel abaixo.
            </p>
        </div>

        <!-- Quick actions -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <Link
                v-if="page.props.currentTeam"
                :href="productsIndex(page.props.currentTeam.slug).url"
                class="flex items-center gap-4 rounded-xl border border-border bg-card p-5 transition-shadow hover:shadow-md"
            >
                <div class="rounded-full bg-primary/10 p-2.5">
                    <Package class="h-5 w-5 text-primary" />
                </div>
                <div>
                    <p class="font-medium text-foreground">Catálogo</p>
                    <p class="text-xs text-muted-foreground">Ver todos os produtos</p>
                </div>
            </Link>

            <Link
                v-if="canManageProducts && page.props.currentTeam"
                :href="create(page.props.currentTeam.slug).url"
                class="flex items-center gap-4 rounded-xl border border-border bg-card p-5 transition-shadow hover:shadow-md"
            >
                <div class="rounded-full bg-primary/10 p-2.5">
                    <Plus class="h-5 w-5 text-primary" />
                </div>
                <div>
                    <p class="font-medium text-foreground">Novo Produto</p>
                    <p class="text-xs text-muted-foreground">Cadastrar produto</p>
                </div>
            </Link>

            <Link
                v-if="canManageUsers && page.props.currentTeam"
                :href="usersIndex(page.props.currentTeam.slug).url"
                class="flex items-center gap-4 rounded-xl border border-border bg-card p-5 transition-shadow hover:shadow-md"
            >
                <div class="rounded-full bg-primary/10 p-2.5">
                    <Users class="h-5 w-5 text-primary" />
                </div>
                <div>
                    <p class="font-medium text-foreground">Usuários</p>
                    <p class="text-xs text-muted-foreground">Gerenciar cadastros</p>
                </div>
            </Link>
        </div>

        <!-- Products section -->
        <div>
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <p class="text-xs tracking-[0.25em] uppercase text-muted-foreground">Disponíveis</p>
                    <h2 class="font-['Cormorant_Garamond',serif] text-2xl font-light text-foreground">Produtos Ativos</h2>
                </div>
                <Link
                    v-if="page.props.currentTeam"
                    :href="productsIndex(page.props.currentTeam.slug).url"
                    class="text-xs tracking-wider text-muted-foreground uppercase transition-colors hover:text-foreground"
                >
                    Ver todos
                </Link>
            </div>

            <!-- Empty -->
            <div v-if="products.length === 0" class="flex flex-col items-center gap-3 py-12 text-center">
                <span class="text-4xl text-muted-foreground/40">✦</span>
                <p class="font-['Cormorant_Garamond',serif] text-xl font-light text-muted-foreground">
                    Nenhum produto ativo no momento.
                </p>
                <Link
                    v-if="canManageProducts && page.props.currentTeam"
                    :href="create(page.props.currentTeam.slug).url"
                    class="mt-2 rounded-full bg-primary px-6 py-2.5 text-xs tracking-widest uppercase text-primary-foreground transition-colors hover:opacity-90"
                >
                    Cadastrar primeiro produto
                </Link>
            </div>

            <!-- Product grid -->
            <div v-else class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <div
                    v-for="product in products"
                    :key="product.id"
                    class="group relative flex flex-col overflow-hidden rounded-xl border border-border bg-card transition-shadow hover:shadow-md"
                >
                    <!-- Image -->
                    <Link :href="productShow(product.id).url" class="relative aspect-square overflow-hidden bg-secondary block">
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
                    </Link>

                    <!-- Info -->
                    <div class="flex flex-1 flex-col gap-1 p-4">
                        <Link :href="productShow(product.id).url">
                            <p class="font-['Cormorant_Garamond',serif] text-lg font-semibold leading-tight text-foreground hover:text-primary transition-colors">
                                {{ product.name }}
                            </p>
                        </Link>
                        <p v-if="product.size" class="text-xs text-muted-foreground">{{ product.size }}</p>
                        <p class="text-sm font-medium text-primary">{{ formatPrice(product.price) }}</p>
                        <p
                            v-if="product.description"
                            class="mt-1 line-clamp-2 text-xs text-muted-foreground"
                        >
                            {{ product.description }}
                        </p>
                    </div>

                    <!-- Buy button -->
                    <div class="border-t border-border px-4 py-3">
                        <Link
                            :href="productShow(product.id).url"
                            class="flex w-full items-center justify-center gap-2 rounded-full bg-primary px-4 py-2 text-xs tracking-widest uppercase text-primary-foreground transition-colors hover:opacity-90"
                        >
                            <ShoppingBag class="h-3.5 w-3.5" />
                            Comprar
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Decorative divider -->
        <div class="flex items-center gap-4">
            <div class="h-px flex-1 bg-border" />
            <span class="text-xs tracking-widest uppercase text-muted-foreground">Nice Rolim Ateliê</span>
            <div class="h-px flex-1 bg-border" />
        </div>

        <!-- Brand cards -->
        <div class="grid gap-6 md:grid-cols-3">
            <div class="rounded-xl border border-border bg-secondary/50 p-6">
                <span class="text-2xl">✦</span>
                <p class="mt-3 font-['Cormorant_Garamond',serif] text-lg font-semibold text-foreground">Peças Únicas</p>
                <p class="mt-1 text-sm text-muted-foreground">Cada joia criada com dedicação artesanal</p>
            </div>
            <div class="rounded-xl border border-border bg-secondary/50 p-6">
                <span class="text-2xl">🌿</span>
                <p class="mt-3 font-['Cormorant_Garamond',serif] text-lg font-semibold text-foreground">Materiais Naturais</p>
                <p class="mt-1 text-sm text-muted-foreground">Flores preservadas, resina e algodão natural</p>
            </div>
            <div class="rounded-xl border border-border bg-secondary/50 p-6">
                <span class="text-2xl">💛</span>
                <p class="mt-3 font-['Cormorant_Garamond',serif] text-lg font-semibold text-foreground">Feito com Amor</p>
                <p class="mt-1 text-sm text-muted-foreground">Do coração do nosso ateliê para o seu</p>
            </div>
        </div>
    </div>
</template>
