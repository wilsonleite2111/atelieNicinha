<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { onMounted, ref, computed } from 'vue';
import { Toaster } from '@/components/ui/sonner';
import { login, logout, register } from '@/routes';
import { show as productShow } from '@/routes/products/public';

type PublicProduct = {
    id: number;
    name: string;
    description: string | null;
    size: string | null;
    price: string;
    thumbnail: string;
};

const props = withDefaults(
    defineProps<{
        canRegister: boolean;
        products: PublicProduct[];
        storeTeamSlug: string | null;
    }>(),
    { canRegister: true, products: () => [], storeTeamSlug: null }
);

const isScrolled = ref(false);
const activeCategory = ref('todos');
const navRef = ref<HTMLElement | null>(null);

onMounted(() => {
    window.addEventListener('scroll', () => {
        isScrolled.value = window.scrollY > 50;
    });
});

const scrollToSection = (sectionId: string) => {
    const element = document.getElementById(sectionId);
    if (!element) return;
    const navHeight = navRef.value?.offsetHeight ?? 80;
    const top = element.getBoundingClientRect().top + window.scrollY - navHeight;
    window.scrollTo({ top, behavior: 'smooth' });
};

const formatPrice = (price: string) =>
    new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(price));

const filteredProducts = computed(() => props.products);
</script>

<template>
    <Head title="Nice Rolim Ateliê — Joias em Resina e Macramê Artesanal">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="" />
        <link
            href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Jost:wght@300;400;500&display=swap"
            rel="stylesheet"
        />
    </Head>

    <div class="min-h-screen bg-[#FAF8F5] font-['Jost',sans-serif] text-[#2C2416]">
        <!-- NAV -->
        <nav
            ref="navRef"
            :class="[
                'fixed top-0 left-0 right-0 z-50 transition-all duration-500',
                isScrolled ? 'bg-[#FAF8F5]/95 backdrop-blur-md shadow-sm py-3' : 'bg-transparent py-5',
            ]"
        >
            <div class="mx-auto flex max-w-6xl items-center justify-between px-6">
                <div class="flex flex-col leading-none">
                    <span class="font-['Cormorant_Garamond',serif] text-2xl font-semibold italic tracking-wide text-[#7C5C3A]">
                        Nice Rolim Ateliê
                    </span>
                    <span class="text-[10px] tracking-[0.25em] uppercase text-[#A8896C]">Resina & Macramê</span>
                </div>
                <div class="flex items-center gap-6 text-sm tracking-widest uppercase text-[#5C4A32]">
                    <a href="#produtos" class="hidden transition-colors hover:text-[#7C5C3A] md:block" @click.prevent="scrollToSection('produtos')">Produtos</a>
                    <a href="#sobre" class="hidden transition-colors hover:text-[#7C5C3A] md:block" @click.prevent="scrollToSection('sobre')">Sobre</a>
                    <a href="#contato" class="hidden transition-colors hover:text-[#7C5C3A] md:block" @click.prevent="scrollToSection('contato')">Contato</a>
                    <template v-if="$page.props.auth.user">
                        <Link
                            v-if="$page.props.currentTeam"
                            :href="`/${$page.props.currentTeam.slug}/dashboard`"
                            class="rounded-full bg-[#7C5C3A] px-5 py-2 text-xs text-white transition-all hover:bg-[#5C4028]"
                        >
                            Painel
                        </Link>
                        <Form v-bind="logout.form()">
                            <button
                                type="submit"
                                class="rounded-full border border-[#7C5C3A] px-5 py-2 text-xs text-[#7C5C3A] transition-all hover:bg-[#7C5C3A] hover:text-white"
                            >
                                Sair
                            </button>
                        </Form>
                    </template>
                    <template v-else>
                        <Link
                            :href="login().url"
                            class="text-xs transition-colors hover:text-[#7C5C3A]"
                        >
                            Entrar
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="register().url"
                            class="rounded-full bg-[#7C5C3A] px-5 py-2 text-xs text-white transition-all hover:bg-[#5C4028]"
                        >
                            Cadastre-se
                        </Link>
                    </template>
                </div>
            </div>
        </nav>

        <!-- HERO -->
        <section class="relative flex min-h-screen items-center overflow-hidden">
            <div class="absolute inset-0 z-0">
                <img src="/images/products/hero.png" alt="Peças artesanais" class="h-full w-full object-cover" />
                <div class="absolute inset-0 bg-gradient-to-r from-[#FAF8F5]/95 via-[#FAF8F5]/70 to-transparent"></div>
            </div>

            <div class="relative z-10 mx-auto max-w-6xl px-6 py-32">
                <div class="max-w-xl">
                    <p class="mb-4 text-xs tracking-[0.3em] uppercase text-[#A8896C]">Feito à mão com amor ✦</p>
                    <h1 class="mb-6 font-['Cormorant_Garamond',serif] text-6xl font-light leading-[1.1] text-[#2C2416] md:text-7xl">
                        Arte com você
                    </h1>
                    <p class="mb-10 text-lg font-light leading-relaxed text-[#5C4A32]">
                        Joias únicas em resina com flores preservadas e peças delicadas em macramê, criadas artesanalmente no coração do nosso ateliê.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a
                            href="#produtos"
                            class="rounded-full bg-[#7C5C3A] px-8 py-4 text-sm tracking-widest uppercase text-white transition-all duration-300 hover:bg-[#5C4028] hover:shadow-lg"
                            @click.prevent="scrollToSection('produtos')"
                        >
                            Ver Coleção
                        </a>
                        <a
                            href="#sobre"
                            class="rounded-full border border-[#7C5C3A] px-8 py-4 text-sm tracking-widest uppercase text-[#7C5C3A] transition-all duration-300 hover:bg-[#7C5C3A]/10"
                            @click.prevent="scrollToSection('sobre')"
                        >
                            Nosso Ateliê
                        </a>
                    </div>
                </div>
            </div>

            <!-- Scroll indicator -->
            <div class="absolute bottom-10 left-1/2 -translate-x-1/2 animate-bounce">
                <div class="flex flex-col items-center gap-2 text-[#A8896C]">
                    <span class="text-[10px] tracking-[0.2em] uppercase">Explorar</span>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>
        </section>

        <!-- DESTAQUES -->
        <section class="bg-[#F2ECE4] py-16">
            <div class="mx-auto grid max-w-6xl grid-cols-1 gap-0 px-6 md:grid-cols-3">
                <div class="flex items-center gap-4 border-b border-[#D9CDBF] p-8 md:border-b-0 md:border-r">
                    <span class="text-3xl">✦</span>
                    <div>
                        <p class="font-['Cormorant_Garamond',serif] text-lg font-semibold text-[#2C2416]">Peças Únicas</p>
                        <p class="text-sm text-[#7C6A56]">Cada joia é irrepetível</p>
                    </div>
                </div>
                <div class="flex items-center gap-4 border-b border-[#D9CDBF] p-8 md:border-b-0 md:border-r">
                    <span class="text-3xl">🌿</span>
                    <div>
                        <p class="font-['Cormorant_Garamond',serif] text-lg font-semibold text-[#2C2416]">Flores Naturais</p>
                        <p class="text-sm text-[#7C6A56]">Preservadas para sempre</p>
                    </div>
                </div>
                <div class="flex items-center gap-4 p-8">
                    <span class="text-3xl">💛</span>
                    <div>
                        <p class="font-['Cormorant_Garamond',serif] text-lg font-semibold text-[#2C2416]">Personalização</p>
                        <p class="text-sm text-[#7C6A56]">Encomendas especiais</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- PRODUCTS -->
        <section id="produtos" class="py-24">
            <div class="mx-auto max-w-6xl px-6">
                <div class="mb-16 text-center">
                    <p class="mb-3 text-xs tracking-[0.3em] uppercase text-[#A8896C]">Nossa Coleção</p>
                    <h2 class="font-['Cormorant_Garamond',serif] text-5xl font-light text-[#2C2416]">
                        Feito com <em class="italic text-[#7C5C3A]">carinho</em>
                    </h2>
                </div>

                <!-- Empty state -->
                <div v-if="filteredProducts.length === 0" class="py-16 text-center">
                    <p class="font-['Cormorant_Garamond',serif] text-2xl font-light text-[#A8896C]">
                        Em breve nossa coleção estará disponível aqui.
                    </p>
                </div>

                <!-- Products Grid -->
                <div v-else class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    <Link
                        v-for="product in filteredProducts"
                        :key="product.id"
                        :href="productShow(product.id).url"
                        class="group cursor-pointer overflow-hidden rounded-2xl bg-white shadow-sm transition-all duration-500 hover:-translate-y-1 hover:shadow-xl"
                    >
                        <div class="relative overflow-hidden bg-[#F5EFE8]" style="aspect-ratio: 4/3;">
                            <img
                                v-if="product.thumbnail"
                                :src="product.thumbnail"
                                :alt="product.name"
                                class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"
                            />
                            <div
                                v-else
                                class="flex h-full w-full items-center justify-center text-5xl text-[#D9CDBF]"
                            >
                                ✦
                            </div>
                            <div class="absolute inset-0 flex items-center justify-center bg-[#2C2416]/20 opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                                <span class="rounded-full bg-white px-6 py-3 text-sm font-medium text-[#2C2416] shadow-lg">
                                    Ver Detalhes
                                </span>
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="mb-2 font-['Cormorant_Garamond',serif] text-xl font-semibold text-[#2C2416]">
                                {{ product.name }}
                            </h3>
                            <p v-if="product.size" class="mb-1 text-xs text-[#A8896C]">{{ product.size }}</p>
                            <p v-if="product.description" class="mb-4 line-clamp-2 text-sm text-[#7C6A56]">{{ product.description }}</p>
                            <div class="flex items-center justify-between">
                                <span class="font-['Cormorant_Garamond',serif] text-2xl font-semibold text-[#7C5C3A]">
                                    {{ formatPrice(product.price) }}
                                </span>
                                <span class="rounded-full border border-[#7C5C3A] px-4 py-2 text-xs text-[#7C5C3A] transition-all group-hover:bg-[#7C5C3A] group-hover:text-white">
                                    Ver mais
                                </span>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </section>

        <!-- ABOUT -->
        <section id="sobre" class="bg-[#F2ECE4] py-24">
            <div class="mx-auto max-w-6xl px-6">
                <div class="grid grid-cols-1 items-center gap-16 md:grid-cols-2">
                    <div class="relative">
                        <div class="overflow-hidden rounded-2xl">
                            <img src="/images/products/macrame.png" alt="Nice Rolim Ateliê" class="h-full w-full object-cover" />
                        </div>
                        <div class="absolute -bottom-6 -right-6 hidden rounded-2xl bg-[#7C5C3A] p-6 text-white md:block">
                            <p class="font-['Cormorant_Garamond',serif] text-4xl font-light">5+</p>
                            <p class="text-xs tracking-wider uppercase">Anos de arte</p>
                        </div>
                    </div>
                    <div>
                        <p class="mb-4 text-xs tracking-[0.3em] uppercase text-[#A8896C]">Nossa História</p>
                        <h2 class="mb-6 font-['Cormorant_Garamond',serif] text-4xl font-light leading-tight text-[#2C2416]">
                            Um ateliê nascido da <em class="italic text-[#7C5C3A]">paixão</em> pela arte manual
                        </h2>
                        <p class="mb-4 leading-relaxed text-[#5C4A32]">
                            Nice Rolim Ateliê surgiu do amor pelas artes manuais e do desejo de criar peças únicas que contem histórias. Cada joia em resina captura a delicadeza de flores naturais, e cada peça de macramê carrega o ritmo paciente de mãos que tecem com afeto.
                        </p>
                        <p class="mb-8 leading-relaxed text-[#5C4A32]">
                            Trabalhamos com materiais de qualidade, técnicas artesanais e muita dedicação para que você leve um pedaço do nosso ateliê com você — seja como joia, decoração ou lembrança especial.
                        </p>
                        <div class="flex gap-8">
                            <div>
                                <p class="font-['Cormorant_Garamond',serif] text-3xl text-[#7C5C3A]">200+</p>
                                <p class="text-sm text-[#7C6A56]">Peças criadas</p>
                            </div>
                            <div>
                                <p class="font-['Cormorant_Garamond',serif] text-3xl text-[#7C5C3A]">100%</p>
                                <p class="text-sm text-[#7C6A56]">Artesanal</p>
                            </div>
                            <div>
                                <p class="font-['Cormorant_Garamond',serif] text-3xl text-[#7C5C3A]">⭐ 5.0</p>
                                <p class="text-sm text-[#7C6A56]">Avaliação</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- TESTIMONIALS -->
        <section class="py-24">
            <div class="mx-auto max-w-6xl px-6">
                <div class="mb-16 text-center">
                    <p class="mb-3 text-xs tracking-[0.3em] uppercase text-[#A8896C]">Depoimentos</p>
                    <h2 class="font-['Cormorant_Garamond',serif] text-4xl font-light text-[#2C2416]">
                        O que dizem nossas <em class="italic text-[#7C5C3A]">clientes</em>
                    </h2>
                </div>
                <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                    <div
                        v-for="(t, i) in [
                            { name: 'Ana Luiza', text: 'Os brincos são lindos demais! Recebi vários elogios. A qualidade é impecável e o packaging veio perfeito.', stars: 5 },
                            { name: 'Mariana Costa', text: 'Pedi um colar personalizado com flores do meu casamento. Ficou além do que eu esperava. Vou guardar para sempre!', stars: 5 },
                            { name: 'Fernanda Reis', text: 'A pulseira de macramê é delicada e super resistente. Uso todo dia e continua linda. Já encomendei mais!', stars: 5 },
                        ]"
                        :key="i"
                        class="rounded-2xl bg-[#FAF8F5] p-8 shadow-sm"
                    >
                        <div class="mb-4 flex gap-1 text-[#C4925A]">
                            <span v-for="s in t.stars" :key="s">★</span>
                        </div>
                        <p class="mb-6 leading-relaxed text-[#5C4A32] italic">"{{ t.text }}"</p>
                        <p class="font-['Cormorant_Garamond',serif] font-semibold text-[#2C2416]">{{ t.name }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section id="contato" class="bg-[#7C5C3A] py-24 text-white">
            <div class="mx-auto max-w-2xl px-6 text-center">
                <p class="mb-3 text-xs tracking-[0.3em] uppercase text-[#D4B896]">Encomendas Especiais</p>
                <h2 class="mb-6 font-['Cormorant_Garamond',serif] text-4xl font-light leading-tight">
                    Crie sua peça <em class="italic">exclusiva</em>
                </h2>
                <p class="mb-10 leading-relaxed text-[#D4B896]">
                    Quer uma joia com as flores do seu buquê, uma lembrança de data especial ou uma peça de macramê personalizada? Entre em contato — vamos criar algo único para você.
                </p>
                <a
                    href="https://wa.me/5500000000000"
                    target="_blank"
                    class="inline-flex items-center gap-3 rounded-full bg-white px-8 py-4 text-sm font-medium tracking-widest uppercase text-[#7C5C3A] transition-all hover:bg-[#F5EFE8] hover:shadow-xl"
                >
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                        <path d="M12 0C5.373 0 0 5.373 0 12c0 2.127.555 4.122 1.528 5.855L0 24l6.335-1.562A11.94 11.94 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.885 0-3.65-.51-5.17-1.4l-.37-.22-3.76.928.964-3.678-.24-.38A9.942 9.942 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
                    </svg>
                    Falar no WhatsApp
                </a>
            </div>
        </section>

        <Toaster />

        <!-- FOOTER -->
        <footer class="bg-[#2C2416] py-12 text-[#A8896C]">
            <div class="mx-auto max-w-6xl px-6">
                <div class="mb-8 flex flex-col items-center justify-between gap-6 md:flex-row">
                    <div>
                        <p class="font-['Cormorant_Garamond',serif] text-2xl font-light italic text-[#D4B896]">
                            Nice Rolim Ateliê
                        </p>
                        <p class="text-xs tracking-widest uppercase">Resina & Macramê Artesanal</p>
                    </div>
                    <div class="flex gap-8 text-sm">
                        <a href="#produtos" class="transition-colors hover:text-[#D4B896]">Produtos</a>
                        <a href="#sobre" class="transition-colors hover:text-[#D4B896]">Sobre</a>
                        <a href="#contato" class="transition-colors hover:text-[#D4B896]">Contato</a>
                    </div>
                </div>
                <div class="border-t border-[#3C3020] pt-8 text-center text-xs">
                    <p>© {{ new Date().getFullYear() }} Nice Rolim Ateliê. Feito com ♥ e muito cuidado artesanal.</p>
                </div>
            </div>
        </footer>
    </div>
</template>
