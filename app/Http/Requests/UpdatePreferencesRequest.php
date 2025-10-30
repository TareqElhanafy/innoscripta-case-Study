<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePreferencesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // When the field is included it must be a non-empty array and items validated
            'preferred_sources' => ['sometimes', 'array', 'min:1'],
            'preferred_sources.*' => ['required', 'string', 'distinct', 'exists:sources,key'],

            'preferred_categories' => ['sometimes', 'array', 'min:1'],
            'preferred_categories.*' => ['required', 'string', 'distinct', 'exists:categories,slug'],

            'preferred_authors' => ['sometimes', 'array', 'min:1'],
            'preferred_authors.*' => ['required', 'string', 'distinct'],
        ];
    }

    public function messages(): array
    {
        return [
            'preferred_sources.array' => 'Preferred sources must be an array.',
            'preferred_sources.min' => 'preferred_sources cannot be empty — include at least one source or omit the field.',
            'preferred_sources.*.required' => 'Each preferred source must be a non-empty string.',
            'preferred_sources.*.exists' => 'One or more preferred sources are not recognized.',

            'preferred_categories.array' => 'Preferred categories must be an array.',
            'preferred_categories.min' => 'preferred_categories cannot be empty — include at least one category or omit the field.',
            'preferred_categories.*.required' => 'Each preferred category must be a non-empty string.',
            'preferred_categories.*.exists' => 'One or more preferred categories are not valid.',

            'preferred_authors.array' => 'Preferred authors must be an array.',
            'preferred_authors.min' => 'preferred_authors cannot be empty — include at least one author or omit the field.',
            'preferred_authors.*.required' => 'Each preferred author must be a non-empty string.',
        ];
    }

    /**
     * Enforce: at least one of the three preference arrays must be present and non-empty.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $keys = [
                'preferred_sources',
                'preferred_categories',
                'preferred_authors',
            ];

            $hasNonEmpty = false;

            foreach ($keys as $key) {
                // $this->has($key) checks the key exists in the request input
                // then ensure it's an array with at least one item
                if ($this->has($key)) {
                    $value = $this->input($key);

                    if (is_array($value) && count($value) > 0) {
                        $hasNonEmpty = true;
                        break;
                    }
                }
            }

            if (! $hasNonEmpty) {
                $v->errors()->add('preferences', 'You must provide at least one of preferred_sources, preferred_categories or preferred_authors with at least one item.');
            }
        });
    }

    /**
     * Return a clean JSON response on validation failure.
     */
    protected function failedValidation(Validator $validator): void
    {
        $payload = [
            'message' => 'Validation failed. Check the errors for details.',
            'errors' => $validator->errors()->messages(),
        ];

        throw new HttpResponseException(response()->json($payload, 422));
    }
}
