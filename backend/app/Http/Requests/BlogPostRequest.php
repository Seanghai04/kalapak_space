<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BlogPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['required', 'string'],
            'content' => ['required', 'string'],
            'cover_image' => ['nullable', 'image', 'max:2048'],
            'category_id' => ['required', 'exists:blog_categories,id'],
            'status' => ['required', 'in:draft,published,archived'],
            'is_featured' => ['boolean'],
            'reading_time' => ['nullable', 'integer', 'min:1'],
            'published_at' => ['nullable', 'date'],
        ];

        $slugRule = Rule::unique('blog_posts', 'slug')->whereNull('deleted_at');

        if ($this->isMethod('POST')) {
            $rules['slug'] = ['required', 'string', $slugRule];
        } else {
            $rules['slug'] = ['required', 'string', $slugRule->ignore($this->route('id'))];
        }

        return $rules;
    }
}
