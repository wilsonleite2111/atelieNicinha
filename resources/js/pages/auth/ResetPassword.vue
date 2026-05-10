<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { update } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Nova senha',
        description: 'Escolha uma senha segura para sua conta',
    },
});

const props = defineProps<{
    token: string;
    email: string;
}>();

const inputEmail = ref(props.email);
</script>

<template>
    <Head title="Redefinir senha" />

    <Form
        v-bind="update.form()"
        :transform="(data) => ({ ...data, token, email })"
        :reset-on-success="['password', 'password_confirmation']"
        v-slot="{ errors, processing }"
    >
        <div class="grid gap-5">
            <div class="grid gap-1.5">
                <Label for="email" class="text-xs tracking-wider uppercase text-[#5C4A32]">E-mail</Label>
                <Input
                    id="email"
                    type="email"
                    name="email"
                    autocomplete="email"
                    v-model="inputEmail"
                    class="mt-1 block w-full border-[#D9CDBF] bg-[#F5F0EA] text-[#7C6A56]"
                    readonly
                />
                <InputError :message="errors.email" class="mt-2" />
            </div>

            <div class="grid gap-1.5">
                <Label for="password" class="text-xs tracking-wider uppercase text-[#5C4A32]">Nova senha</Label>
                <PasswordInput
                    id="password"
                    name="password"
                    autocomplete="new-password"
                    class="mt-1 block w-full border-[#D9CDBF] bg-white focus:border-[#7C5C3A] focus:ring-[#7C5C3A]/20"
                    autofocus
                    placeholder="Mínimo 8 caracteres"
                />
                <InputError :message="errors.password" />
            </div>

            <div class="grid gap-1.5">
                <Label for="password_confirmation" class="text-xs tracking-wider uppercase text-[#5C4A32]">
                    Confirmar nova senha
                </Label>
                <PasswordInput
                    id="password_confirmation"
                    name="password_confirmation"
                    autocomplete="new-password"
                    class="mt-1 block w-full border-[#D9CDBF] bg-white focus:border-[#7C5C3A] focus:ring-[#7C5C3A]/20"
                    placeholder="Repita a nova senha"
                />
                <InputError :message="errors.password_confirmation" />
            </div>

            <Button
                type="submit"
                class="mt-2 w-full rounded-full bg-[#7C5C3A] py-3 text-xs tracking-[0.15em] uppercase text-white transition-all duration-300 hover:bg-[#5C4028] hover:shadow-lg disabled:opacity-60"
                :disabled="processing"
                data-test="reset-password-button"
            >
                <Spinner v-if="processing" />
                {{ processing ? 'Salvando...' : 'Redefinir senha' }}
            </Button>
        </div>
    </Form>
</template>
