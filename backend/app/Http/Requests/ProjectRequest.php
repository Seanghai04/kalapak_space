<?php

namespace App\Http\Requests;

use App\Models\ContentCollection;
use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Schema;

class ProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('collection_id') && ($this->collection_id === '' || $this->collection_id === 'null')) {
            $this->merge(['collection_id' => null]);
        }
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
            'collection_id' => array_values(array_filter([
                'nullable',
                'integer',
                Schema::hasTable('collections') ? 'exists:collections,id' : null,
            ])),
            'storage_provider' => ['nullable', 'in:supabase,cloudinary'],
        ];

        if (!$isUpdate) {
            $rules['slug'] = ['required', 'string', 'unique:projects,slug'];
        } else {
            $rules['slug'] = ['sometimes', 'string', 'unique:projects,slug,' . $this->route('id')];
        }

        return $rules;
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $cid = $this->input('collection_id');
            if ($cid === null || $cid === '') {
                return;
            }
            if (! Schema::hasTable('collections')) {
                $validator->errors()->add('collection_id', 'Collections are not available until database migrations are applied.');

                return;
            }
            $createdBy = (int) auth()->id();
            $projectId = $this->route('id');
            if ($projectId) {
                $project = Project::query()->find($projectId);
                if ($project) {
                    $createdBy = (int) $project->created_by;
                }
            }
            $collection = ContentCollection::query()->find((int) $cid);
            if (! $collection) {
                return;
            }
            if ((int) $collection->author_id !== $createdBy) {
                $validator->errors()->add('collection_id', 'The collection must belong to the same creator as the project.');
            }
        });
    }
}
