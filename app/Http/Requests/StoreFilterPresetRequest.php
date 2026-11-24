<?php

namespace App\Http\Requests;

use App\Models\FilterPreset;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class StoreFilterPresetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:80'],
            'filters' => ['array'],
            'filters.search' => ['nullable', 'string', 'max:255'],
            'filters.category' => ['nullable', 'string', 'max:100'],
            'filters.tags' => ['nullable', 'array', 'max:20'],
            'filters.tags.*' => ['string', 'max:100', Rule::exists('tags', 'slug')],
            'filters.author' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'filters.status' => ['nullable', Rule::in(['published', 'draft'])],
            'filters.sort' => ['nullable', Rule::in(['popular', 'latest', 'oldest', 'title'])],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nazwa zestawu jest wymagana.',
            'name.max' => 'Nazwa zestawu może mieć maksymalnie 80 znaków.',
            'filters.tags.*.exists' => 'Wybrany tag nie istnieje.',
        ];
    }

    /**
     * Strip unsupported filter keys before validation.
     */
    protected function prepareForValidation(): void
    {
        $filters = Arr::only((array) $this->input('filters', []), FilterPreset::FILTER_KEYS);

        if (isset($filters['tags'])) {
            $filters['tags'] = array_values(array_unique(array_filter(
                (array) $filters['tags'],
                static fn (mixed $slug): bool => is_string($slug) && $slug !== '',
            )));
        }

        $this->merge(['filters' => $filters]);
    }
}
