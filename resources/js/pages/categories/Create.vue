<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import FormActions from '@/components/FormActions.vue';
import FormField from '@/components/FormField.vue';
import PageHeader from '@/components/PageHeader.vue';
import SectionCard from '@/components/SectionCard.vue';
import { Input } from '@/components/ui/input';
import { create, index, store } from '@/routes/categories';

defineOptions({
    layout: () => ({
        breadcrumbs: [
            {
                title: 'Categorias',
                href: index(),
            },
            {
                title: 'Nova categoria',
                href: create(),
            },
        ],
    }),
});
</script>

<template>
    <Head title="Nova categoria" />

    <div class="flex flex-col gap-8 p-2">
        <PageHeader eyebrow="Cadastro" title="Nova categoria" />

        <Form
            v-bind="store.form()"
            class="flex max-w-lg flex-col gap-6"
            v-slot="{ errors, processing }"
        >
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
                            required
                            class="h-auto rounded-lg bg-background px-4 py-2.5"
                            placeholder="Ex.: Brinco"
                        />
                    </FormField>

                    <div class="flex items-center gap-3">
                        <input
                            id="active"
                            name="active"
                            type="checkbox"
                            value="1"
                            checked
                            class="h-4 w-4 rounded border-input text-primary focus:ring-ring/40"
                        />
                        <label for="active" class="text-sm text-foreground"
                            >Categoria ativa</label
                        >
                    </div>
                </div>
            </SectionCard>

            <FormActions
                submit-label="Salvar categoria"
                :cancel-href="index().url"
                :processing="processing"
            />
        </Form>
    </div>
</template>
