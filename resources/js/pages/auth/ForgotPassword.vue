<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { email } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Esqueceu a senha?',
        description: 'Informe seu e-mail e enviaremos um link para redefinir sua senha',
    },
});

defineProps<{
    status?: string;
}>();
</script>

<template>
    <Head title="Recuperar senha" />

    <div
        v-if="status"
        class="mb-4 rounded-lg bg-[#7C5C3A]/10 px-4 py-3 text-center text-sm font-medium text-[#7C5C3A]"
    >
        {{ status }}
    </div>

    <div class="space-y-6">
        <Form v-bind="email.form()" v-slot="{ errors, processing }">
            <div class="grid gap-1.5">
                <Label for="email" class="text-xs tracking-wider uppercase text-[#5C4A32]">E-mail</Label>
                <Input
                    id="email"
                    type="email"
                    name="email"
                    autocomplete="off"
                    autofocus
                    placeholder="seu@email.com"
                    class="border-[#D9CDBF] bg-white placeholder:text-[#C4B5A5] focus:border-[#7C5C3A] focus:ring-[#7C5C3A]/20"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="my-6 flex items-center justify-start">
                <Button
                    class="w-full rounded-full bg-[#7C5C3A] py-3 text-xs tracking-[0.15em] uppercase text-white transition-all duration-300 hover:bg-[#5C4028] hover:shadow-lg disabled:opacity-60"
                    :disabled="processing"
                    data-test="email-password-reset-link-button"
                >
                    <Spinner v-if="processing" />
                    {{ processing ? 'Enviando...' : 'Enviar link de recuperação' }}
                </Button>
            </div>
        </Form>

        <div class="space-x-1 text-center text-sm text-[#7C6A56]">
            <span>Ou voltar para</span>
            <TextLink
                :href="login()"
                class="font-medium text-[#7C5C3A] underline-offset-4 transition-colors hover:text-[#5C4028]"
            >
                entrar
            </TextLink>
        </div>
    </div>
</template>
