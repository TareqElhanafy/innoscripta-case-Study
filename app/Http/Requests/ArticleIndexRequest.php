<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Public endpoint
    }

    public function rules(): array
    {
        return [
            'q' => ['sometimes', 'string', 'max:200'],
            'from' => ['sometimes', 'date'],
            'to' => ['sometimes', 'date', 'after_or_equal:from'],
            'source' => ['sometimes', 'string'], // comma-separated
            'category' => ['sometimes', 'string'], // comma-separated
            'author' => ['sometimes', 'string'], // comma-separated
            'language' => ['sometimes', 'string', 'max:10'],
            'sort_by' => ['sometimes', 'string', 'in:published_at,title,created_at'],
            'sort_direction' => ['sometimes', 'string', 'in:asc,desc'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'to.after_or_equal' => 'The "to" date must be after or equal to "from" date.',
            'per_page.max' => 'Maximum 100 articles per page.',
        ];
    }

    /**
     * Get validated filters
     */
    public function filters(): array
    {
        return $this->only([
            'q',
            'from',
            'to',
            'source',
            'category',
            'author',
            'language',
            'sort_by',
            'sort_direction',
        ]);
    }

    /**
     * Get per_page value
     */
    public function perPage(): int
    {
        return (int) $this->input('per_page', 20);
    }
}
