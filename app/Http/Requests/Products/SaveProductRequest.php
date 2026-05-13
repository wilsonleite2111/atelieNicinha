<?php

namespace App\Http\Requests\Products;

use App\Models\Product;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Validator;

class SaveProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        $team = $this->route('current_team');

        return $team !== null && Gate::check('create', [Product::class, $team]);
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('price')) {
            $this->merge([
                'price' => str_replace(',', '.', (string) $this->input('price')),
            ]);
        }
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'size' => ['nullable', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'sku' => ['nullable', 'string', 'max:100'],
            'active' => ['boolean'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpeg,png,webp', 'max:5120'],
            'delete_images' => ['nullable', 'array'],
            'delete_images.*' => ['integer'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'images.*.image' => 'O arquivo enviado não é uma imagem válida.',
            'images.*.mimes' => 'Use apenas imagens JPEG, PNG ou WebP.',
            'images.*.max' => 'Cada imagem deve ter no máximo 5 MB.',
            'images.*.uploaded' => 'Falha ao enviar a imagem. O arquivo pode ser muito grande ou estar corrompido.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $imageKeys = collect($validator->errors()->keys())
                ->filter(fn (string $key): bool => str_starts_with($key, 'images.'));

            foreach ($imageKeys as $key) {
                $validator->errors()->add('images', $validator->errors()->first($key));
            }
        });
    }
}
