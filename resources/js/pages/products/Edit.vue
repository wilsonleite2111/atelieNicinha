<script setup lang="ts">
import { Form, Head, router } from '@inertiajs/vue3';
import DangerZone from '@/components/DangerZone.vue';
import FormActions from '@/components/FormActions.vue';
import PageHeader from '@/components/PageHeader.vue';
import ProductFields from '@/components/products/ProductFields.vue';
import ProductImageUploader from '@/components/products/ProductImageUploader.vue';
import { destroy, edit, index, update } from '@/routes/products';
import type { Product } from '@/types';

type CategoryOption = { id: number; name: string };

type Props = {
    product: Product;
    categories: CategoryOption[];
};

const props = defineProps<Props>();

defineOptions({
    layout: (props: { product?: Product }) => ({
        breadcrumbs: [
            {
                title: 'Produtos',
                href: index(),
            },
            {
                title: props.product?.name ?? 'Editar produto',
                href: props.product ? edit(props.product.id) : '/',
            },
        ],
    }),
});

const confirmDelete = () => {
    if (
        !confirm(
            `Tem certeza que deseja excluir "${props.product.name}"? Esta ação não pode ser desfeita.`,
        )
    ) {
        return;
    }

    router.visit(destroy(props.product.id));
};
</script>

<template>
    <Head :title="`Editar: ${product.name}`" />

    <div class="flex flex-col gap-8 p-2">
        <PageHeader eyebrow="Edição" :title="product.name" />

        <Form
            :action="update.url(product.id)"
            method="post"
            :multipart="true"
            class="grid gap-8 lg:grid-cols-[1fr_360px]"
            v-slot="{ errors, processing }"
        >
            <input type="hidden" name="_method" value="PATCH" />

            <div class="flex flex-col gap-6">
                <ProductFields
                    :product="product"
                    :categories="categories"
                    :errors="errors"
                />

                <FormActions
                    submit-label="Salvar alterações"
                    :cancel-href="index().url"
                    :processing="processing"
                />

                <DangerZone
                    title="Excluir produto"
                    description="Esta ação não pode ser desfeita. O produto e todas as suas imagens serão removidos permanentemente."
                    @confirm="confirmDelete"
                />
            </div>

            <div>
                <ProductImageUploader
                    :existing-images="product.images ?? []"
                    :image-alt="product.name"
                    :errors="errors"
                />
            </div>
        </Form>
    </div>
</template>
