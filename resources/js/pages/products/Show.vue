<script setup lang="ts">
import { Form, Head, Link, usePage } from '@inertiajs/vue3';
import { ArrowLeft, ShoppingBag } from 'lucide-vue-next';
import { ref } from 'vue';
import { login } from '@/routes';
import type { ProductImage } from '@/types';

type Props = {
    product: {
        id: number;
        name: string;
        description: string | null;
        size: string | null;
        price: string;
        images: ProductImage[];
    };
};

const props = defineProps<Props>();
const page = usePage();

const activeImage = ref(props.product.images[0]?.url ?? null);

const formatPrice = (price: string) =>
    new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(price));
</script>

<template>
    <Head :title="product.name">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="" />
        <link
            href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Jost:wght@300;400;500&display=swap"
            rel="stylesheet"
        />
    </Head>

    <div class="min-h-screen bg-[#FAF8F5] font-['Jost',sans-serif] text-[#2C2416]">
        <!-- NAV -->
        <nav class="fixed left-0 right-0 top-0 z-50 bg-[#FAF8F5]/95 py-4 shadow-sm backdrop-blur-md">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-6">
                <Link href="/" class="flex flex-col leading-none">
                    <span class="font-['Cormorant_Garamond',serif] text-2xl font-semibold italic tracking-wide text-[#7C5C3A]">
                        Nice Rolim Ateliê
                    </span>
                    <span class="text-[10px] tracking-[0.25em] uppercase text-[#A8896C]">Resina & Macramê</span>
                </Link>
                <div class="flex items-center gap-4 text-sm">
                    <Link href="/" class="flex items-center gap-1.5 text-[#5C4A32] transition-colors hover:text-[#7C5C3A]">
                        <ArrowLeft class="h-4 w-4" />
                        Voltar
                    </Link>
                </div>
            </div>
        </nav>

        <!-- CONTENT -->
        <div class="mx-auto max-w-6xl px-6 pb-24 pt-32">
            <div class="grid gap-12 lg:grid-cols-2">
                <!-- Images -->
                <div class="flex flex-col gap-4">
                    <div class="overflow-hidden rounded-2xl bg-[#F5EFE8]" style="aspect-ratio: 1/1;">
                        <img
                            v-if="activeImage"
                            :src="activeImage"
                            :alt="product.name"
                            class="h-full w-full object-cover"
                        />
                        <div
                            v-else
                            class="flex h-full w-full items-center justify-center text-6xl text-[#D9CDBF]"
                        >
                            ✦
                        </div>
                    </div>

                    <!-- Thumbnails -->
                    <div v-if="product.images.length > 1" class="flex gap-3 overflow-x-auto pb-1">
                        <button
                            v-for="img in product.images"
                            :key="img.id"
                            class="h-20 w-20 flex-shrink-0 overflow-hidden rounded-lg border-2 transition-all"
                            :class="activeImage === img.url ? 'border-[#7C5C3A]' : 'border-transparent opacity-60 hover:opacity-100'"
                            @click="activeImage = img.url"
                        >
                            <img :src="img.url" :alt="product.name" class="h-full w-full object-cover" />
                        </button>
                    </div>
                </div>

                <!-- Info -->
                <div class="flex flex-col justify-center gap-8">
                    <div>
                        <p class="mb-2 text-xs tracking-[0.3em] uppercase text-[#A8896C]">Nice Rolim Ateliê</p>
                        <h1 class="font-['Cormorant_Garamond',serif] text-5xl font-light leading-tight text-[#2C2416]">
                            {{ product.name }}
                        </h1>
                    </div>

                    <div class="font-['Cormorant_Garamond',serif] text-4xl font-semibold text-[#7C5C3A]">
                        {{ formatPrice(product.price) }}
                    </div>

                    <!-- Details -->
                    <div class="flex flex-col gap-3 rounded-2xl bg-[#F2ECE4] p-6">
                        <div v-if="product.size" class="flex items-center justify-between border-b border-[#D9CDBF] pb-3">
                            <span class="text-sm tracking-wider text-[#7C6A56]">Tamanho</span>
                            <span class="font-medium text-[#2C2416]">{{ product.size }}</span>
                        </div>
                        <div v-if="product.description" class="pt-1">
                            <p class="mb-2 text-xs tracking-widest uppercase text-[#A8896C]">Descrição</p>
                            <p class="leading-relaxed text-[#5C4A32]">{{ product.description }}</p>
                        </div>
                    </div>

                    <!-- Buy / Login -->
                    <div v-if="$page.props.auth?.user">
                        <a
                            :href="`https://wa.me/5500000000000?text=Olá! Tenho interesse no produto: ${product.name} (${formatPrice(product.price)})`"
                            target="_blank"
                            class="inline-flex w-full items-center justify-center gap-3 rounded-full bg-[#7C5C3A] px-8 py-4 text-sm tracking-widest uppercase text-white transition-all duration-300 hover:bg-[#5C4028] hover:shadow-lg"
                        >
                            <ShoppingBag class="h-5 w-5" />
                            Comprar via WhatsApp
                        </a>
                        <p class="mt-3 text-center text-xs text-[#A8896C]">
                            Você será direcionado para o WhatsApp para finalizar seu pedido.
                        </p>
                    </div>
                    <div v-else class="flex flex-col gap-3">
                        <p class="text-center text-sm text-[#5C4A32]">
                            Faça login para comprar este produto.
                        </p>
                        <Link
                            :href="login().url"
                            class="inline-flex w-full items-center justify-center gap-3 rounded-full bg-[#7C5C3A] px-8 py-4 text-sm tracking-widest uppercase text-white transition-all duration-300 hover:bg-[#5C4028] hover:shadow-lg"
                        >
                            Entrar para Comprar
                        </Link>
                    </div>

                    <!-- Trust badges -->
                    <div class="grid grid-cols-3 gap-4 border-t border-[#D9CDBF] pt-6">
                        <div class="flex flex-col items-center gap-1 text-center">
                            <span class="text-xl">✦</span>
                            <span class="text-xs text-[#7C6A56]">Peça única</span>
                        </div>
                        <div class="flex flex-col items-center gap-1 text-center">
                            <span class="text-xl">🌿</span>
                            <span class="text-xs text-[#7C6A56]">Artesanal</span>
                        </div>
                        <div class="flex flex-col items-center gap-1 text-center">
                            <span class="text-xl">💛</span>
                            <span class="text-xs text-[#7C6A56]">Feito com amor</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FOOTER -->
        <footer class="bg-[#2C2416] py-8 text-[#A8896C]">
            <div class="mx-auto max-w-6xl px-6 text-center text-xs">
                <p>© {{ new Date().getFullYear() }} Nice Rolim Ateliê. Feito com ♥ e muito cuidado artesanal.</p>
            </div>
        </footer>
    </div>
</template>
