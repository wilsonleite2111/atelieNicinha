<script setup lang="ts">
import { Form, Head, setLayoutProps } from '@inertiajs/vue3';
import { computed, ref, watchEffect } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    InputOTP,
    InputOTPGroup,
    InputOTPSlot,
} from '@/components/ui/input-otp';
import { store } from '@/routes/two-factor/login';
import type { TwoFactorConfigContent } from '@/types';

const authConfigContent = computed<TwoFactorConfigContent>(() => {
    if (showRecoveryInput.value) {
        return {
            title: 'Código de recuperação',
            description:
                'Confirme o acesso à sua conta usando um dos seus códigos de recuperação de emergência.',
            buttonText: 'usar código de autenticação',
        };
    }

    return {
        title: 'Autenticação em dois fatores',
        description:
            'Insira o código gerado pelo seu aplicativo autenticador.',
        buttonText: 'usar código de recuperação',
    };
});

watchEffect(() => {
    setLayoutProps({
        title: authConfigContent.value.title,
        description: authConfigContent.value.description,
    });
});

const showRecoveryInput = ref<boolean>(false);

const toggleRecoveryMode = (clearErrors: () => void): void => {
    showRecoveryInput.value = !showRecoveryInput.value;
    clearErrors();
    code.value = '';
};

const code = ref<string>('');
</script>

<template>
    <Head title="Autenticação em dois fatores" />

    <div class="space-y-6">
        <template v-if="!showRecoveryInput">
            <Form
                v-bind="store.form()"
                class="space-y-5"
                reset-on-error
                @error="code = ''"
                #default="{ errors, processing, clearErrors }"
            >
                <input type="hidden" name="code" :value="code" />
                <div
                    class="flex flex-col items-center justify-center space-y-3 text-center"
                >
                    <div class="flex w-full items-center justify-center">
                        <InputOTP
                            id="otp"
                            v-model="code"
                            :maxlength="6"
                            :disabled="processing"
                            autofocus
                        >
                            <InputOTPGroup>
                                <InputOTPSlot
                                    v-for="index in 6"
                                    :key="index"
                                    :index="index - 1"
                                />
                            </InputOTPGroup>
                        </InputOTP>
                    </div>
                    <InputError :message="errors.code" />
                </div>
                <Button
                    type="submit"
                    class="w-full rounded-full bg-[#7C5C3A] py-3 text-xs tracking-[0.15em] uppercase text-white transition-all duration-300 hover:bg-[#5C4028] hover:shadow-lg disabled:opacity-60"
                    :disabled="processing"
                >
                    Continuar
                </Button>
                <div class="text-center text-sm text-[#7C6A56]">
                    <span>ou </span>
                    <button
                        type="button"
                        class="font-medium text-[#7C5C3A] underline-offset-4 transition-colors hover:text-[#5C4028] hover:underline"
                        @click="() => toggleRecoveryMode(clearErrors)"
                    >
                        {{ authConfigContent.buttonText }}
                    </button>
                </div>
            </Form>
        </template>

        <template v-else>
            <Form
                v-bind="store.form()"
                class="space-y-5"
                reset-on-error
                #default="{ errors, processing, clearErrors }"
            >
                <Input
                    name="recovery_code"
                    type="text"
                    placeholder="Insira o código de recuperação"
                    :autofocus="showRecoveryInput"
                    required
                    class="border-[#D9CDBF] bg-white placeholder:text-[#C4B5A5] focus:border-[#7C5C3A] focus:ring-[#7C5C3A]/20"
                />
                <InputError :message="errors.recovery_code" />
                <Button
                    type="submit"
                    class="w-full rounded-full bg-[#7C5C3A] py-3 text-xs tracking-[0.15em] uppercase text-white transition-all duration-300 hover:bg-[#5C4028] hover:shadow-lg disabled:opacity-60"
                    :disabled="processing"
                >
                    Continuar
                </Button>

                <div class="text-center text-sm text-[#7C6A56]">
                    <span>ou </span>
                    <button
                        type="button"
                        class="font-medium text-[#7C5C3A] underline-offset-4 transition-colors hover:text-[#5C4028] hover:underline"
                        @click="() => toggleRecoveryMode(clearErrors)"
                    >
                        {{ authConfigContent.buttonText }}
                    </button>
                </div>
            </Form>
        </template>
    </div>
</template>
