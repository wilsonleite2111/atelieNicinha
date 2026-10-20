<script setup lang="ts">
import { ImagePlus, X } from 'lucide-vue-next';
import { ref } from 'vue';
import { toast } from 'vue-sonner';
import InputError from '@/components/InputError.vue';
import SectionCard from '@/components/SectionCard.vue';
import type { ProductImage } from '@/types';

const MAX_IMAGE_MB = 5;

const props = defineProps<{
    errors: Partial<Record<string, string>>;
    existingImages?: ProductImage[];
    imageAlt?: string;
}>();

const isEditing = props.existingImages !== undefined;

const deletedImageIds = ref<number[]>([]);
const previews = ref<string[]>([]);
const fileInput = ref<HTMLInputElement | null>(null);

const toggleDelete = (img: ProductImage): void => {
    deletedImageIds.value = deletedImageIds.value.includes(img.id)
        ? deletedImageIds.value.filter((id) => id !== img.id)
        : [...deletedImageIds.value, img.id];
};

const onFilesSelected = (e: Event): void => {
    const input = e.target as HTMLInputElement;
    const files = input.files;

    if (!files) {
        return;
    }

    const oversized = Array.from(files).filter(
        (f) => f.size > MAX_IMAGE_MB * 1024 * 1024,
    );

    if (oversized.length > 0) {
        toast.error(
            `${oversized.length === 1 ? 'Uma imagem excede' : `${oversized.length} imagens excedem`} o limite de ${MAX_IMAGE_MB} MB.`,
        );
        input.value = '';
        previews.value = [];

        return;
    }

    previews.value = Array.from(files, (file) => URL.createObjectURL(file));
};

const removePreview = (index: number): void => {
    previews.value.splice(index, 1);

    if (!fileInput.value?.files) {
        return;
    }

    const dt = new DataTransfer();
    Array.from(fileInput.value.files).forEach((f, i) => {
        if (i !== index) {
            dt.items.add(f);
        }
    });
    fileInput.value.files = dt.files;
};
</script>

<template>
    <SectionCard title="Imagens">
        <input
            v-for="id in deletedImageIds"
            :key="id"
            type="hidden"
            name="delete_images[]"
            :value="id"
        />

        <div v-if="existingImages && existingImages.length > 0" class="mb-4">
            <p
                class="mb-2 text-xs tracking-wider text-muted-foreground uppercase"
            >
                Imagens salvas
            </p>
            <div class="grid grid-cols-2 gap-2">
                <div
                    v-for="img in existingImages"
                    :key="img.id"
                    class="relative aspect-square overflow-hidden rounded-lg bg-secondary"
                    :class="{ 'opacity-40': deletedImageIds.includes(img.id) }"
                >
                    <img
                        :src="img.url"
                        :alt="imageAlt"
                        class="h-full w-full object-cover"
                    />
                    <button
                        v-if="!deletedImageIds.includes(img.id)"
                        type="button"
                        class="absolute top-1.5 right-1.5 rounded-full bg-foreground/70 p-1 text-primary-foreground transition-opacity hover:bg-foreground"
                        @click="toggleDelete(img)"
                    >
                        <X class="h-3 w-3" />
                    </button>
                    <button
                        v-else
                        type="button"
                        class="absolute inset-0 flex items-center justify-center text-xs font-medium text-foreground underline"
                        @click="toggleDelete(img)"
                    >
                        Desfazer
                    </button>
                </div>
            </div>
        </div>

        <div v-if="previews.length > 0" class="mb-4">
            <p
                v-if="isEditing"
                class="mb-2 text-xs tracking-wider text-muted-foreground uppercase"
            >
                Novas imagens
            </p>
            <div class="grid grid-cols-2 gap-2">
                <div
                    v-for="(src, i) in previews"
                    :key="i"
                    class="relative aspect-square overflow-hidden rounded-lg bg-secondary"
                >
                    <img :src="src" class="h-full w-full object-cover" alt="" />
                    <button
                        type="button"
                        class="absolute top-1.5 right-1.5 rounded-full bg-foreground/70 p-1 text-primary-foreground transition-opacity hover:bg-foreground"
                        @click="removePreview(i)"
                    >
                        <X class="h-3 w-3" />
                    </button>
                </div>
            </div>
        </div>

        <label
            class="flex cursor-pointer flex-col items-center gap-3 rounded-lg border border-dashed border-border py-8 text-center transition-colors hover:border-primary/50 hover:bg-secondary/50"
        >
            <ImagePlus class="h-8 w-8 text-muted-foreground" />
            <span class="text-sm text-muted-foreground">
                {{
                    isEditing
                        ? 'Adicionar mais imagens'
                        : 'Clique para adicionar imagens'
                }}
            </span>
            <span class="text-xs text-muted-foreground/70"
                >JPEG, PNG ou WebP · máx. {{ MAX_IMAGE_MB }} MB cada</span
            >
            <input
                ref="fileInput"
                type="file"
                name="images[]"
                multiple
                accept="image/jpeg,image/png,image/webp"
                class="sr-only"
                @change="onFilesSelected"
            />
        </label>
        <InputError :message="errors['images']" />
        <InputError :message="errors['images.*']" />
    </SectionCard>
</template>
