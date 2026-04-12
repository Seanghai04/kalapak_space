<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_featured' => filter_var($this->is_featured, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            'is_open_source' => filter_var($this->is_open_source, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
        ]);
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        $rules = [
            'title' => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],
            'description' => [$isUpdate ? 'sometimes' : 'required', 'string'],
            'long_description' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'image', 'max:10240'],
            'repo_url' => ['nullable', 'url'],
            'demo_url' => ['nullable', 'url'],
            'tech_stack' => ['nullable', 'array'],
            'status' => [$isUpdate ? 'sometimes' : 'required', 'in:active,archived,wip'],
            'is_featured' => ['boolean'],
            'is_open_source' => ['boolean'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
        ];

        if (!$isUpdate) {
            $rules['slug'] = ['required', 'string', 'unique:projects,slug'];
        } else {
            $rules['slug'] = ['sometimes', 'string', 'unique:projects,slug,' . $this->route('id')];
        }

        return $rules;
    }
}
