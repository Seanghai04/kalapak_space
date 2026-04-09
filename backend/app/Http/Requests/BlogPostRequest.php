<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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

        if ($this->isMethod('POST')) {
            $rules['slug'] = ['required', 'string', 'unique:blog_posts,slug'];
        } else {
            $rules['slug'] = ['required', 'string', 'unique:blog_posts,slug,' . $this->route('id')];
        }

        return $rules;
    }
}
