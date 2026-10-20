<?php

namespace App\Http\Requests\Teams;

use App\Rules\TeamName;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SaveTeamRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if (! $this->filled('whatsapp')) {
            return;
        }

        $digits = preg_replace('/\D/', '', (string) $this->input('whatsapp'));

        // Numbers typed without the country code (DDD + number) are assumed to be Brazilian.
        if (strlen($digits) === 10 || strlen($digits) === 11) {
            $digits = '55'.$digits;
        }

        $this->merge(['whatsapp' => $digits]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', new TeamName],
            'whatsapp' => ['nullable', 'string', 'regex:/^55\d{10,11}$/'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'whatsapp.regex' => 'Informe um número de WhatsApp válido com DDD, por exemplo (41) 99999-9999.',
        ];
    }
}
