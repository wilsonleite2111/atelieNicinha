<script setup lang="ts">
import { Form, Head, router } from '@inertiajs/vue3';
import DangerZone from '@/components/DangerZone.vue';
import FormActions from '@/components/FormActions.vue';
import FormField from '@/components/FormField.vue';
import PageHeader from '@/components/PageHeader.vue';
import SectionCard from '@/components/SectionCard.vue';
import { Input } from '@/components/ui/input';
import { destroy, edit, index, update } from '@/routes/categories';
import type { Category } from '@/types';

type Props = {
    category: Category;
};

const props = defineProps<Props>();

defineOptions({
    layout: (props: { category?: Category }) => ({
        breadcrumbs: [
            {
                title: 'Categorias',
                href: index(),
            },
            {
                title: props.category?.name ?? 'Editar categoria',
                href: props.category ? edit(props.category.id) : '/',
            },
        ],
    }),
});

const confirmDelete = () => {
    if (
        !confirm(
            `Tem certeza que deseja excluir "${props.category.name}"? Esta ação não pode ser desfeita.`,
        )
    ) {
        return;
    }

    router.visit(destroy(props.category.id));
};
</script>

<template>
    <Head :title="`Editar: ${category.name}`" />

    <div class="flex flex-col gap-8 p-2">
        <PageHeader eyebrow="Edição" :title="category.name" />

        <Form
            :action="update.url(category.id)"
            method="post"
            class="flex max-w-lg flex-col gap-6"
            v-slot="{ errors, processing }"
        >
            <input type="hidden" name="_method" value="PATCH" />

            <SectionCard title="Informações">
                <div class="flex flex-col gap-5">
                    <FormField
                        id="name"
                        label="Nome"
                        required
                        :error="errors.name"
                    >
                        <Input
                            id="name"
                            name="name"
                            :default-value="category.name"
                            required
                            class="h-auto rounded-lg bg-background px-4 py-2.5"
                        />
                    </FormField>

                    <div class="flex items-center gap-3">
                        <input
                            id="active"
                            name="active"
                            type="checkbox"
                            value="1"
                            :checked="category.active"
                            class="h-4 w-4 rounded border-input text-primary focus:ring-ring/40"
                        />
                        <label for="active" class="text-sm text-foreground"
                            >Categoria ativa</label
                        >
                    </div>
                </div>
            </SectionCard>

            <FormActions
                submit-label="Salvar alterações"
                :cancel-href="index().url"
                :processing="processing"
            />

            <DangerZone
                title="Excluir categoria"
                description="Esta ação não pode ser desfeita. Os produtos vinculados perderão a categoria."
                @confirm="confirmDelete"
            />
        </Form>
    </div>
</template>
