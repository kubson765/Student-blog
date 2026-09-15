<?php

namespace App\Http\Requests;

use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePostRequest extends FormRequest
{
    /**
     * Czy użytkownik jest uprawniony do tego żądania?
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Post::class);
    }

    /**
     * Zasady walidacji
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:5', 'max:255'],
            'content' => ['required', 'string', 'min:50', 'max:5000'],
            'category' => ['required', 'string', 'max:100'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'published_at' => ['nullable', 'date', 'after_or_equal:now'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
        ];
    }

    /**
     * Komunikaty błędów
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Tytuł jest wymagany.',
            'title.min' => 'Tytuł musi mieć co najmniej 5 znaków.',
            'content.required' => 'Treść posta jest wymagana.',
            'content.min' => 'Treść musi mieć co najmniej 50 znaków.',
            'status.in' => 'Status musi być jednym z: draft, published.',
            'published_at.after_or_equal' => 'Data publikacji nie może być z przeszłości.',
        ];
    }
}
