<script setup lang="ts">
import { Form, Head, router, usePage } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { destroy, edit, index, update } from '@/routes/categories';
import type { Category, Team } from '@/types';

type Props = {
    category: Category;
};

const props = defineProps<Props>();

const page = usePage();

defineOptions({
    layout: (props: { currentTeam?: Team | null; category?: Category }) => ({
        breadcrumbs: [
            {
                title: 'Categorias',
                href: props.currentTeam ? index(props.currentTeam.slug) : '/',
            },
            {
                title: props.category?.name ?? 'Editar categoria',
                href:
                    props.currentTeam && props.category
                        ? edit([props.currentTeam.slug, props.category.id])
                        : '/',
            },
        ],
    }),
});

const confirmDelete = () => {
    if (!confirm(`Tem certeza que deseja excluir "${props.category.name}"? Esta ação não pode ser desfeita.`)) {
        return;
    }
    router.visit(destroy([page.props.currentTeam!.slug, props.category.id]));
};
</script>

<template>
    <Head :title="`Editar: ${category.name}`" />

    <div class="flex flex-col gap-8 p-2">
        <div>
            <p class="text-xs tracking-[0.25em] uppercase text-muted-foreground">Edição</p>
            <h1 class="font-['Cormorant_Garamond',serif] text-4xl font-light text-foreground">
                {{ category.name }}
            </h1>
        </div>

        <Form
            :action="update.url([page.props.currentTeam!.slug, category.id])"
            method="post"
            class="max-w-lg"
            v-slot="{ errors, processing }"
        >
            <input type="hidden" name="_method" value="PATCH" />

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
                            :value="category.name"
                            required
                            class="rounded-lg border border-input bg-background px-4 py-2.5 text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring/40"
                        />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="flex items-center gap-3">
                        <input
                            id="active"
                            name="active"
                            type="checkbox"
                            value="1"
                            :checked="category.active"
                            class="h-4 w-4 rounded border-input text-primary focus:ring-ring/40"
                        />
                        <label for="active" class="text-sm text-foreground">Categoria ativa</label>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center gap-4">
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
            <div class="mt-6 rounded-xl border border-destructive/30 bg-destructive/5 p-6">
                <p class="font-medium text-destructive">Excluir categoria</p>
                <p class="mt-1 text-sm text-muted-foreground">
                    Esta ação não pode ser desfeita. Os produtos vinculados perderão a categoria.
                </p>
                <button
                    type="button"
                    class="mt-4 rounded-full border border-destructive px-6 py-2 text-xs tracking-widest uppercase text-destructive transition-colors hover:bg-destructive hover:text-destructive-foreground"
                    @click="confirmDelete"
                >
                    Excluir categoria
                </button>
            </div>
        </Form>
    </div>
</template>
