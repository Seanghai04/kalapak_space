<?php

namespace App\Observers\Concerns;

use App\Models\ApprovalRequest;
use App\Models\UserPermission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Used by observers on Project, Tag, and BlogCategory.
 * When an admin lacks the per-resource permission, the mutation is intercepted
 * and queued as a pending approval request for Super Admin review.
 *
 * Each concrete observer must declare:
 *   protected string $resource = 'projects'; // or 'categories' or 'tags'
 */
trait InterceptsAdminActions
{
    /**
     * Returns true if the action should be intercepted (queued for approval).
     * $action: 'create' | 'update' | 'delete'
     */
    private function shouldIntercept(string $action): bool
    {
        $user = Auth::user();

        if (!$user || !$user->isAdmin()) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return false;
        }

        $permission = UserPermission::where('user_id', $user->id)
            ->where('resource', $this->resource)
            ->first();

        $column = 'can_' . $action; // can_create | can_update | can_delete
        return !($permission?->$column ?? false);
    }

    /**
     * Queue a create approval request and abort the actual save.
     */
    public function creating(Model $model): bool
    {
        if (!$this->shouldIntercept('create')) {
            return true;
        }

        ApprovalRequest::create([
            'requested_by' => Auth::id(),
            'action' => 'create',
            'subject_type' => get_class($model),
            'subject_id' => null,
            'payload' => $model->getAttributes(),
            'status' => 'pending',
        ]);

        return false;
    }

    /**
     * Queue an update approval request and abort the actual save.
     */
    public function updating(Model $model): bool
    {
        if (!$this->shouldIntercept('update')) {
            return true;
        }

        ApprovalRequest::create([
            'requested_by' => Auth::id(),
            'action' => 'update',
            'subject_type' => get_class($model),
            'subject_id' => $model->getKey(),
            'payload' => $model->getDirty(),
            'status' => 'pending',
        ]);

        return false;
    }

    /**
     * Queue a delete approval request and abort the actual delete.
     */
    public function deleting(Model $model): bool
    {
        if (!$this->shouldIntercept('delete')) {
            return true;
        }

        ApprovalRequest::create([
            'requested_by' => Auth::id(),
            'action' => 'delete',
            'subject_type' => get_class($model),
            'subject_id' => $model->getKey(),
            'payload' => ['id' => $model->getKey(), 'title' => $model->title ?? $model->name ?? $model->getKey()],
            'status' => 'pending',
        ]);

        return false;
    }
}

