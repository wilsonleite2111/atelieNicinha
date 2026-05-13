<script setup lang="ts">
import { Form, Head, usePage } from '@inertiajs/vue3';
import { ImagePlus, X } from 'lucide-vue-next';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { create, index, store } from '@/routes/products';
import type { Team } from '@/types';

const page = usePage();

defineOptions({
    layout: (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Produtos',
                href: props.currentTeam ? index(props.currentTeam.slug) : '/',
            },
            {
                title: 'Novo produto',
                href: props.currentTeam ? create(props.currentTeam.slug) : '/',
            },
        ],
    }),
});

const previews = ref<string[]>([]);
const fileInput = ref<HTMLInputElement | null>(null);

const onFilesSelected = (e: Event) => {
    const files = (e.target as HTMLInputElement).files;
    if (!files) return;
    previews.value = [];
    for (const file of files) {
        previews.value.push(URL.createObjectURL(file));
    }
};

const removePreview = (index: number) => {
    previews.value.splice(index, 1);
    if (!fileInput.value?.files) return;
    const dt = new DataTransfer();
    Array.from(fileInput.value.files).forEach((f, i) => {
        if (i !== index) dt.items.add(f);
    });
    fileInput.value.files = dt.files;
};
</script>

<template>
    <Head title="Novo produto" />

    <div class="flex flex-col gap-8 p-2">
        <!-- Header -->
        <div>
            <p class="text-xs tracking-[0.25em] uppercase text-muted-foreground">Cadastro</p>
            <h1 class="font-['Cormorant_Garamond',serif] text-4xl font-light text-foreground">Novo produto</h1>
        </div>

        <Form
            v-bind="store.form(page.props.currentTeam!.slug)"
            :multipart="true"
            class="grid gap-8 lg:grid-cols-[1fr_360px]"
            v-slot="{ errors, processing }"
        >
            <!-- Left: Fields -->
            <div class="flex flex-col gap-6">
                <div class="rounded-xl border border-border bg-card p-6">
                    <h2 class="mb-5 font-['Cormorant_Garamond',serif] text-xl font-semibold text-foreground">
                        Informações
                    </h2>

                    <div class="flex flex-col gap-5">
                        <div class="flex flex-col gap-1.5">
                            <label for="name" class="text-sm font-medium text-foreground">
                                Nome <span class="text-destructive">*</span>
                            </label>
                            <input
                                id="name"
                                name="name"
                                required
                                class="rounded-lg border border-input bg-background px-4 py-2.5 text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring/40"
                                placeholder="Ex.: Colar Pétalas Eternas"
                            />
                            <InputError :message="errors.name" />
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label for="description" class="text-sm font-medium text-foreground">Descrição / Detalhes</label>
                            <textarea
                                id="description"
                                name="description"
                                rows="4"
                                class="rounded-lg border border-input bg-background px-4 py-2.5 text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring/40"
                                placeholder="Descreva o produto…"
                            />
                            <InputError :message="errors.description" />
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label for="size" class="text-sm font-medium text-foreground">Tamanho</label>
                            <input
                                id="size"
                                name="size"
                                class="rounded-lg border border-input bg-background px-4 py-2.5 text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring/40"
                                placeholder="Ex.: P, M, G ou 40cm x 20cm"
                            />
                            <InputError :message="errors.size" />
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div class="flex flex-col gap-1.5">
                                <label for="price" class="text-sm font-medium text-foreground">
                                    Preço <span class="text-destructive">*</span>
                                </label>
                                <input
                                    id="price"
                                    name="price"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    required
                                    class="rounded-lg border border-input bg-background px-4 py-2.5 text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring/40"
                                    placeholder="0.00"
                                />
                                <InputError :message="errors.price" />
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label for="sku" class="text-sm font-medium text-foreground">SKU</label>
                                <input
                                    id="sku"
                                    name="sku"
                                    class="rounded-lg border border-input bg-background px-4 py-2.5 text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring/40"
                                    placeholder="Ex.: COL-001"
                                />
                                <InputError :message="errors.sku" />
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <input
                                id="active"
                                name="active"
                                type="checkbox"
                                value="1"
                                checked
                                class="h-4 w-4 rounded border-input text-primary focus:ring-ring/40"
                            />
                            <label for="active" class="text-sm text-foreground">Produto ativo</label>
                            <InputError :message="errors.active" />
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-4">
                    <button
                        type="submit"
                        :disabled="processing"
                        class="rounded-full bg-primary px-8 py-2.5 text-xs tracking-widest uppercase text-primary-foreground transition-colors hover:opacity-90 disabled:opacity-50"
                    >
                        Salvar produto
                    </button>
                    <a
                        :href="index(page.props.currentTeam!.slug).url"
                        class="text-sm text-muted-foreground transition-colors hover:text-foreground"
                    >
                        Cancelar
                    </a>
                </div>
            </div>

            <!-- Right: Images -->
            <div class="flex flex-col gap-4">
                <div class="rounded-xl border border-border bg-card p-6">
                    <h2 class="mb-5 font-['Cormorant_Garamond',serif] text-xl font-semibold text-foreground">
                        Imagens
                    </h2>

                    <!-- Previews -->
                    <div v-if="previews.length > 0" class="mb-4 grid grid-cols-2 gap-2">
                        <div
                            v-for="(src, i) in previews"
                            :key="i"
                            class="relative aspect-square overflow-hidden rounded-lg bg-secondary"
                        >
                            <img :src="src" class="h-full w-full object-cover" alt="" />
                            <button
                                type="button"
                                class="absolute right-1.5 top-1.5 rounded-full bg-foreground/70 p-1 text-primary-foreground transition-opacity hover:bg-foreground"
                                @click="removePreview(i)"
                            >
                                <X class="h-3 w-3" />
                            </button>
                        </div>
                    </div>

                    <!-- Upload button -->
                    <label
                        class="flex cursor-pointer flex-col items-center gap-3 rounded-lg border border-dashed border-border py-8 text-center transition-colors hover:border-primary/50 hover:bg-secondary/50"
                    >
                        <ImagePlus class="h-8 w-8 text-muted-foreground" />
                        <span class="text-sm text-muted-foreground">
                            Clique para adicionar imagens
                        </span>
                        <span class="text-xs text-muted-foreground/70">JPEG, PNG ou WebP · máx. 5 MB cada</span>
                        <input
                            ref="fileInput"
                            type="file"
                            name="images[]"
                            multiple
                            accept="image/jpeg,image/png,image/webp"
                            class="sr-only"
                            @change="onFilesSelected"
                        />
                    </label>
                    <InputError :message="errors['images']" />
                    <InputError :message="errors['images.*']" />
                </div>
            </div>
        </Form>
    </div>
</template>
