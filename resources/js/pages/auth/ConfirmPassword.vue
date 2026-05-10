<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { store } from '@/routes/password/confirm';

defineOptions({
    layout: {
        title: 'Confirmar senha',
        description: 'Esta é uma área segura. Por favor, confirme sua senha antes de continuar.',
    },
});
</script>

<template>
    <Head title="Confirmar senha" />

    <Form
        v-bind="store.form()"
        reset-on-success
        v-slot="{ errors, processing }"
    >
        <div class="space-y-6">
            <div class="grid gap-1.5">
                <Label htmlFor="password" class="text-xs tracking-wider uppercase text-[#5C4A32]">Senha</Label>
                <PasswordInput
                    id="password"
                    name="password"
                    class="mt-1 block w-full border-[#D9CDBF] bg-white focus:border-[#7C5C3A] focus:ring-[#7C5C3A]/20"
                    required
                    autocomplete="current-password"
                    autofocus
                />

                <InputError :message="errors.password" />
            </div>

            <div class="flex items-center">
                <Button
                    class="w-full rounded-full bg-[#7C5C3A] py-3 text-xs tracking-[0.15em] uppercase text-white transition-all duration-300 hover:bg-[#5C4028] hover:shadow-lg disabled:opacity-60"
                    :disabled="processing"
                    data-test="confirm-password-button"
                >
                    <Spinner v-if="processing" />
                    {{ processing ? 'Confirmando...' : 'Confirmar senha' }}
                </Button>
            </div>
        </div>
    </Form>
</template>
