<script setup lang="ts">
import FormField from '@/components/FormField.vue';
import InputError from '@/components/InputError.vue';
import SectionCard from '@/components/SectionCard.vue';
import { Input } from '@/components/ui/input';
import { NativeSelect } from '@/components/ui/native-select';
import { Textarea } from '@/components/ui/textarea';
import type { Product } from '@/types';

type CategoryOption = { id: number; name: string };

defineProps<{
    categories: CategoryOption[];
    errors: Partial<Record<string, string>>;
    product?: Product;
}>();

const fieldClass = 'h-auto rounded-lg bg-background px-4 py-2.5';
</script>

<template>
    <SectionCard title="Informações">
        <div class="flex flex-col gap-5">
            <FormField id="name" label="Nome" required :error="errors.name">
                <Input
                    id="name"
                    name="name"
                    :default-value="product?.name"
                    required
                    :class="fieldClass"
                    placeholder="Ex.: Colar Pétalas Eternas"
                />
            </FormField>

            <FormField
                id="description"
                label="Descrição / Detalhes"
                :error="errors.description"
            >
                <Textarea
                    id="description"
                    name="description"
                    rows="4"
                    :default-value="product?.description ?? ''"
                    :class="fieldClass"
                    placeholder="Descreva o produto…"
                />
            </FormField>

            <FormField id="size" label="Tamanho" :error="errors.size">
                <Input
                    id="size"
                    name="size"
                    :default-value="product?.size ?? ''"
                    :class="fieldClass"
                    placeholder="Ex.: P, M, G ou 40cm x 20cm"
                />
            </FormField>

            <FormField
                id="category_id"
                label="Categoria"
                :error="errors.category_id"
            >
                <NativeSelect
                    id="category_id"
                    name="category_id"
                    :class="fieldClass"
                >
                    <option value="">Sem categoria</option>
                    <option
                        v-for="cat in categories"
                        :key="cat.id"
                        :value="cat.id"
                        :selected="product?.category_id === cat.id"
                    >
                        {{ cat.name }}
                    </option>
                </NativeSelect>
            </FormField>

            <FormField id="price" label="Preço" required :error="errors.price">
                <Input
                    id="price"
                    name="price"
                    type="number"
                    step="0.01"
                    min="0"
                    :default-value="product?.price"
                    required
                    :class="fieldClass"
                    placeholder="0.00"
                />
            </FormField>

            <div class="flex items-center gap-3">
                <input
                    id="active"
                    name="active"
                    type="checkbox"
                    value="1"
                    :checked="product?.active ?? true"
                    class="h-4 w-4 rounded border-input text-primary focus:ring-ring/40"
                />
                <label for="active" class="text-sm text-foreground"
                    >Produto ativo</label
                >
                <InputError :message="errors.active" />
            </div>
        </div>
    </SectionCard>
</template>
