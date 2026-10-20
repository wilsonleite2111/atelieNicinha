<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import FormActions from '@/components/FormActions.vue';
import PageHeader from '@/components/PageHeader.vue';
import ProductFields from '@/components/products/ProductFields.vue';
import ProductImageUploader from '@/components/products/ProductImageUploader.vue';
import { create, index, store } from '@/routes/products';

type CategoryOption = { id: number; name: string };

defineProps<{ categories: CategoryOption[] }>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            {
                title: 'Produtos',
                href: index(),
            },
            {
                title: 'Novo produto',
                href: create(),
            },
        ],
    }),
});
</script>

<template>
    <Head title="Novo produto" />

    <div class="flex flex-col gap-8 p-2">
        <PageHeader eyebrow="Cadastro" title="Novo produto" />

        <Form
            v-bind="store.form()"
            :multipart="true"
            class="grid gap-8 lg:grid-cols-[1fr_360px]"
            v-slot="{ errors, processing }"
        >
            <div class="flex flex-col gap-6">
                <ProductFields :categories="categories" :errors="errors" />

                <FormActions
                    submit-label="Salvar produto"
                    :cancel-href="index().url"
                    :processing="processing"
                />
            </div>

            <div>
                <ProductImageUploader :errors="errors" />
            </div>
        </Form>
    </div>
</template>
