<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { store } from '@/routes/register';

defineOptions({
    layout: {
        title: 'Crie sua conta',
        description: 'Junte-se ao Nice Rolim Ateliê e descubra peças únicas feitas com amor',
    },
});
</script>

<template>
    <Head title="Cadastro" />

    <Form
        v-bind="store.form()"
        :reset-on-success="['password', 'password_confirmation']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-5"
    >
        <div class="grid gap-5">
            <div class="grid gap-1.5">
                <Label for="name" class="text-xs tracking-wider uppercase text-[#5C4A32]">Nome</Label>
                <Input
                    id="name"
                    type="text"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="name"
                    name="name"
                    placeholder="Seu nome completo"
                    class="border-[#D9CDBF] bg-white placeholder:text-[#C4B5A5] focus:border-[#7C5C3A] focus:ring-[#7C5C3A]/20"
                />
                <InputError :message="errors.name" />
            </div>

            <div class="grid gap-1.5">
                <Label for="email" class="text-xs tracking-wider uppercase text-[#5C4A32]">E-mail</Label>
                <Input
                    id="email"
                    type="email"
                    required
                    :tabindex="2"
                    autocomplete="email"
                    name="email"
                    placeholder="seu@email.com"
                    class="border-[#D9CDBF] bg-white placeholder:text-[#C4B5A5] focus:border-[#7C5C3A] focus:ring-[#7C5C3A]/20"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-1.5">
                <Label for="password" class="text-xs tracking-wider uppercase text-[#5C4A32]">Senha</Label>
                <PasswordInput
                    id="password"
                    required
                    :tabindex="3"
                    autocomplete="new-password"
                    name="password"
                    placeholder="Mínimo 8 caracteres"
                    class="border-[#D9CDBF] bg-white focus:border-[#7C5C3A] focus:ring-[#7C5C3A]/20"
                />
                <InputError :message="errors.password" />
            </div>

            <div class="grid gap-1.5">
                <Label for="password_confirmation" class="text-xs tracking-wider uppercase text-[#5C4A32]">Confirmar senha</Label>
                <PasswordInput
                    id="password_confirmation"
                    required
                    :tabindex="4"
                    autocomplete="new-password"
                    name="password_confirmation"
                    placeholder="Repita a senha"
                    class="border-[#D9CDBF] bg-white focus:border-[#7C5C3A] focus:ring-[#7C5C3A]/20"
                />
                <InputError :message="errors.password_confirmation" />
            </div>

            <Button
                type="submit"
                class="mt-2 w-full rounded-full bg-[#7C5C3A] py-3 text-xs tracking-[0.15em] uppercase text-white transition-all duration-300 hover:bg-[#5C4028] hover:shadow-lg disabled:opacity-60"
                tabindex="5"
                :disabled="processing"
                data-test="register-user-button"
            >
                <Spinner v-if="processing" />
                {{ processing ? 'Criando conta...' : 'Criar conta' }}
            </Button>
        </div>

        <div class="text-center text-sm text-[#7C6A56]">
            Já tem uma conta?
            <TextLink
                :href="login()"
                class="font-medium text-[#7C5C3A] underline-offset-4 transition-colors hover:text-[#5C4028]"
                :tabindex="6"
            >
                Entrar
            </TextLink>
        </div>
    </Form>
</template>
