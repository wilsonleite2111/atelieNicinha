<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Bem-vinda de volta',
        description: 'Acesse sua conta para ver seus pedidos e favoritos',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();
</script>

<template>
    <Head title="Entrar" />

    <div
        v-if="status"
        class="mb-4 rounded-lg bg-[#7C5C3A]/10 px-4 py-3 text-center text-sm font-medium text-[#7C5C3A]"
    >
        {{ status }}
    </div>

    <Form
        v-bind="store.form()"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-5"
    >
        <div class="grid gap-5">
            <div class="grid gap-1.5">
                <Label for="email" class="text-xs tracking-wider uppercase text-[#5C4A32]">E-mail</Label>
                <Input
                    id="email"
                    type="email"
                    name="email"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="email"
                    placeholder="seu@email.com"
                    class="border-[#D9CDBF] bg-white placeholder:text-[#C4B5A5] focus:border-[#7C5C3A] focus:ring-[#7C5C3A]/20"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-1.5">
                <div class="flex items-center justify-between">
                    <Label for="password" class="text-xs tracking-wider uppercase text-[#5C4A32]">Senha</Label>
                    <TextLink
                        v-if="canResetPassword"
                        :href="request()"
                        class="text-xs text-[#A8896C] transition-colors hover:text-[#7C5C3A]"
                        :tabindex="5"
                    >
                        Esqueceu a senha?
                    </TextLink>
                </div>
                <PasswordInput
                    id="password"
                    name="password"
                    required
                    :tabindex="2"
                    autocomplete="current-password"
                    placeholder="Sua senha"
                    class="border-[#D9CDBF] bg-white focus:border-[#7C5C3A] focus:ring-[#7C5C3A]/20"
                />
                <InputError :message="errors.password" />
            </div>

            <div class="flex items-center justify-between">
                <Label for="remember" class="flex cursor-pointer items-center gap-2.5 text-sm text-[#5C4A32]">
                    <Checkbox id="remember" name="remember" :tabindex="3" />
                    <span>Lembrar de mim</span>
                </Label>
            </div>

            <Button
                type="submit"
                class="mt-2 w-full rounded-full bg-[#7C5C3A] py-3 text-xs tracking-[0.15em] uppercase text-white transition-all duration-300 hover:bg-[#5C4028] hover:shadow-lg disabled:opacity-60"
                :tabindex="4"
                :disabled="processing"
                data-test="login-button"
            >
                <Spinner v-if="processing" />
                {{ processing ? 'Entrando...' : 'Entrar' }}
            </Button>
        </div>

        <div
            class="text-center text-sm text-[#7C6A56]"
            v-if="canRegister"
        >
            Não tem conta?
            <TextLink
                :href="register()"
                :tabindex="5"
                class="font-medium text-[#7C5C3A] underline-offset-4 transition-colors hover:text-[#5C4028]"
            >
                Cadastre-se
            </TextLink>
        </div>
    </Form>
</template>
