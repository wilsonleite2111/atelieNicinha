<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { logout } from '@/routes';
import { send } from '@/routes/verification';

defineOptions({
    layout: {
        title: 'Verifique seu e-mail',
        description:
            'Enviamos um link de verificação para o seu e-mail. Por favor, clique no link para ativar sua conta.',
    },
});

defineProps<{
    status?: string;
}>();
</script>

<template>
    <Head title="Verificar e-mail" />

    <div
        v-if="status === 'verification-link-sent'"
        class="mb-4 rounded-lg bg-[#7C5C3A]/10 px-4 py-3 text-center text-sm font-medium text-[#7C5C3A]"
    >
        Um novo link de verificação foi enviado para o e-mail informado no cadastro.
    </div>

    <Form
        v-bind="send.form()"
        class="space-y-6 text-center"
        v-slot="{ processing }"
    >
        <Button
            :disabled="processing"
            class="w-full rounded-full bg-[#7C5C3A] py-3 text-xs tracking-[0.15em] uppercase text-white transition-all duration-300 hover:bg-[#5C4028] hover:shadow-lg disabled:opacity-60"
        >
            <Spinner v-if="processing" />
            {{ processing ? 'Enviando...' : 'Reenviar e-mail de verificação' }}
        </Button>

        <TextLink
            :href="logout()"
            as="button"
            class="mx-auto block text-sm text-[#A8896C] transition-colors hover:text-[#7C5C3A]"
        >
            Sair
        </TextLink>
    </Form>
</template>
