<?php

namespace App\Http\Requests;

use App\Models\Comment;
use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Comment::class);
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'min:1', 'max:2000'],
            'parent_id' => [
                'nullable',
                'integer',
                'exists:comments,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'Treść komentarza jest wymagana.',
            'content.min' => 'Komentarz musi mieć co najmniej 1 znak.',
            'content.max' => 'Komentarz może mieć maksymalnie 2000 znaków.',
            'parent_id.exists' => 'Komentarz nadrzędny nie istnieje.',
        ];
    }
}
