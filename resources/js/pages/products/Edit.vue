<script setup lang="ts">
import { Form, Head, router, usePage } from '@inertiajs/vue3';
import { ImagePlus, X } from 'lucide-vue-next';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { destroy, edit, index, update } from '@/routes/products';
import type { Product, ProductImage, Team } from '@/types';

type Props = {
    product: Product;
};

const props = defineProps<Props>();

const page = usePage();

defineOptions({
    layout: (props: { currentTeam?: Team | null; product?: Product }) => ({
        breadcrumbs: [
            {
                title: 'Produtos',
                href: props.currentTeam ? index(props.currentTeam.slug) : '/',
            },
            {
                title: props.product?.name ?? 'Editar produto',
                href:
                    props.currentTeam && props.product
                        ? edit([props.currentTeam.slug, props.product.id])
                        : '/',
            },
        ],
    }),
});

const deletedImageIds = ref<number[]>([]);
const newPreviews = ref<string[]>([]);
const fileInput = ref<HTMLInputElement | null>(null);

const markDelete = (img: ProductImage) => {
    if (!deletedImageIds.value.includes(img.id)) {
        deletedImageIds.value.push(img.id);
    }
};

const unmarkDelete = (img: ProductImage) => {
    deletedImageIds.value = deletedImageIds.value.filter((id) => id !== img.id);
};

const onFilesSelected = (e: Event) => {
    const files = (e.target as HTMLInputElement).files;
    if (!files) return;
    newPreviews.value = [];
    for (const file of files) {
        newPreviews.value.push(URL.createObjectURL(file));
    }
};

const removeNewPreview = (index: number) => {
    newPreviews.value.splice(index, 1);
    if (!fileInput.value?.files) return;
    const dt = new DataTransfer();
    Array.from(fileInput.value.files).forEach((f, i) => {
        if (i !== index) dt.items.add(f);
    });
    fileInput.value.files = dt.files;
};

const confirmDelete = () => {
    if (!confirm(`Tem certeza que deseja excluir "${props.product.name}"? Esta ação não pode ser desfeita.`)) {
        return;
    }
    router.visit(destroy([page.props.currentTeam!.slug, props.product.id]));
};
</script>

<template>
    <Head :title="`Editar: ${product.name}`" />

    <div class="flex flex-col gap-8 p-2">
        <!-- Header -->
        <div>
            <p class="text-xs tracking-[0.25em] uppercase text-muted-foreground">Edição</p>
            <h1 class="font-['Cormorant_Garamond',serif] text-4xl font-light text-foreground">
                {{ product.name }}
            </h1>
        </div>

        <Form
            v-bind="update.form([page.props.currentTeam!.slug, product.id])"
            :multipart="true"
            class="grid gap-8 lg:grid-cols-[1fr_360px]"
            v-slot="{ errors, processing }"
        >
            <!-- Hidden delete_images inputs -->
            <input
                v-for="id in deletedImageIds"
                :key="id"
                type="hidden"
                name="delete_images[]"
                :value="id"
            />

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
                                :value="product.name"
                                required
                                class="rounded-lg border border-input bg-background px-4 py-2.5 text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring/40"
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
                            >{{ product.description ?? '' }}</textarea>
                            <InputError :message="errors.description" />
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label for="size" class="text-sm font-medium text-foreground">Tamanho</label>
                            <input
                                id="size"
                                name="size"
                                :value="product.size ?? ''"
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
                                    :value="product.price"
                                    required
                                    class="rounded-lg border border-input bg-background px-4 py-2.5 text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring/40"
                                />
                                <InputError :message="errors.price" />
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label for="sku" class="text-sm font-medium text-foreground">SKU</label>
                                <input
                                    id="sku"
                                    name="sku"
                                    :value="product.sku ?? ''"
                                    class="rounded-lg border border-input bg-background px-4 py-2.5 text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring/40"
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
                                :checked="product.active"
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
                        Salvar alterações
                    </button>
                    <a
                        :href="index(page.props.currentTeam!.slug).url"
                        class="text-sm text-muted-foreground transition-colors hover:text-foreground"
                    >
                        Cancelar
                    </a>
                </div>

                <!-- Danger Zone -->
                <div class="rounded-xl border border-destructive/30 bg-destructive/5 p-6">
                    <p class="font-medium text-destructive">Excluir produto</p>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Esta ação não pode ser desfeita. O produto e todas as suas imagens serão removidos permanentemente.
                    </p>
                    <button
                        type="button"
                        data-test="delete-product-button"
                        class="mt-4 rounded-full border border-destructive px-6 py-2 text-xs tracking-widest uppercase text-destructive transition-colors hover:bg-destructive hover:text-destructive-foreground"
                        @click="confirmDelete"
                    >
                        Excluir produto
                    </button>
                </div>
            </div>

            <!-- Right: Images -->
            <div class="flex flex-col gap-4">
                <div class="rounded-xl border border-border bg-card p-6">
                    <h2 class="mb-5 font-['Cormorant_Garamond',serif] text-xl font-semibold text-foreground">
                        Imagens
                    </h2>

                    <!-- Existing images -->
                    <div v-if="product.images && product.images.length > 0" class="mb-4">
                        <p class="mb-2 text-xs text-muted-foreground uppercase tracking-wider">Imagens salvas</p>
                        <div class="grid grid-cols-2 gap-2">
                            <div
                                v-for="img in product.images"
                                :key="img.id"
                                class="relative aspect-square overflow-hidden rounded-lg bg-secondary"
                                :class="{ 'opacity-40': deletedImageIds.includes(img.id) }"
                            >
                                <img :src="img.url" :alt="product.name" class="h-full w-full object-cover" />
                                <button
                                    v-if="!deletedImageIds.includes(img.id)"
                                    type="button"
                                    class="absolute right-1.5 top-1.5 rounded-full bg-foreground/70 p-1 text-primary-foreground transition-opacity hover:bg-foreground"
                                    @click="markDelete(img)"
                                >
                                    <X class="h-3 w-3" />
                                </button>
                                <button
                                    v-else
                                    type="button"
                                    class="absolute inset-0 flex items-center justify-center text-xs font-medium text-foreground underline"
                                    @click="unmarkDelete(img)"
                                >
                                    Desfazer
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- New previews -->
                    <div v-if="newPreviews.length > 0" class="mb-4">
                        <p class="mb-2 text-xs text-muted-foreground uppercase tracking-wider">Novas imagens</p>
                        <div class="grid grid-cols-2 gap-2">
                            <div
                                v-for="(src, i) in newPreviews"
                                :key="i"
                                class="relative aspect-square overflow-hidden rounded-lg bg-secondary"
                            >
                                <img :src="src" class="h-full w-full object-cover" alt="" />
                                <button
                                    type="button"
                                    class="absolute right-1.5 top-1.5 rounded-full bg-foreground/70 p-1 text-primary-foreground transition-opacity hover:bg-foreground"
                                    @click="removeNewPreview(i)"
                                >
                                    <X class="h-3 w-3" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Upload button -->
                    <label
                        class="flex cursor-pointer flex-col items-center gap-3 rounded-lg border border-dashed border-border py-8 text-center transition-colors hover:border-primary/50 hover:bg-secondary/50"
                    >
                        <ImagePlus class="h-8 w-8 text-muted-foreground" />
                        <span class="text-sm text-muted-foreground">Adicionar mais imagens</span>
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
