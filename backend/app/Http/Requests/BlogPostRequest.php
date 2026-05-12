<?php

namespace App\Http\Requests;

use App\Models\BlogPost;
use App\Models\Series;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class BlogPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('series_id') && ($this->series_id === '' || $this->series_id === 'null')) {
            $this->merge(['series_id' => null]);
        }
    }

    public function rules(): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['required', 'string'],
            'content' => ['required', 'string'],
            'cover_image' => ['nullable', 'image', 'max:10240'],
            'storage_provider' => ['nullable', 'in:supabase,cloudinary'],
            'category_id' => ['required', 'exists:blog_categories,id'],
            'series_id' => array_values(array_filter([
                'nullable',
                'integer',
                Schema::hasTable('series') ? 'exists:series,id' : null,
            ])),
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

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $sid = $this->input('series_id');
            if ($sid === null || $sid === '') {
                return;
            }
            if (! Schema::hasTable('series')) {
                $validator->errors()->add('series_id', 'Series is not available until database migrations are applied.');

                return;
            }
            $authorId = (int) auth()->id();
            $postId = $this->route('id');
            if ($postId) {
                $post = BlogPost::query()->find($postId);
                if ($post) {
                    $authorId = (int) $post->author_id;
                }
            }
            $series = Series::query()->find((int) $sid);
            if (! $series) {
                return;
            }
            if ((int) $series->author_id !== $authorId) {
                $validator->errors()->add('series_id', 'The series must belong to the same author as the post.');
            }
        });
    }
}
