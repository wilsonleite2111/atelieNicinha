<script setup lang="ts">
import { Form, Head, usePage } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { create, index, store } from '@/routes/categories';
import type { Team } from '@/types';

const page = usePage();

defineOptions({
    layout: (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Categorias',
                href: props.currentTeam ? index(props.currentTeam.slug) : '/',
            },
            {
                title: 'Nova categoria',
                href: props.currentTeam ? create(props.currentTeam.slug) : '/',
            },
        ],
    }),
});
</script>

<template>
    <Head title="Nova categoria" />

    <div class="flex flex-col gap-8 p-2">
        <div>
            <p class="text-xs tracking-[0.25em] uppercase text-muted-foreground">Cadastro</p>
            <h1 class="font-['Cormorant_Garamond',serif] text-4xl font-light text-foreground">Nova categoria</h1>
        </div>

        <Form
            v-bind="store.form(page.props.currentTeam!.slug)"
            class="max-w-lg"
            v-slot="{ errors, processing }"
        >
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
                            placeholder="Ex.: Brinco"
                        />
                        <InputError :message="errors.name" />
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
                    Salvar categoria
                </button>
                <a
                    :href="index(page.props.currentTeam!.slug).url"
                    class="text-sm text-muted-foreground transition-colors hover:text-foreground"
                >
                    Cancelar
                </a>
            </div>
        </Form>
    </div>
</template>
