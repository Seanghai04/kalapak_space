<?php

namespace App\Observers\Concerns;

use App\Models\ApprovalRequest;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Used by observers on Project, Tag, and BlogCategory.
 * When 'admin_direct_action' is disabled and the current user is a regular admin
 * (not superadmin), write mutations are intercepted and queued for approval.
 */
trait InterceptsAdminActions
{
    /**
     * Returns true if the action should be intercepted (queued for approval).
     */
    private function shouldIntercept(): bool
    {
        $user = Auth::user();

        if (!$user || !$user->isAdmin()) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return false;
        }

        // Intercept only when the direct-action setting is disabled
        return !Setting::getValue('admin_direct_action', true);
    }

    /**
     * Queue a create approval request and abort the actual save.
     * We hook into `creating` and throw an exception to cancel the insert.
     */
    public function creating(Model $model): bool
    {
        if (!$this->shouldIntercept()) {
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

        // Returning false from an Eloquent observer's creating() cancels the save
        return false;
    }

    /**
     * Queue an update approval request and abort the actual save.
     */
    public function updating(Model $model): bool
    {
        if (!$this->shouldIntercept()) {
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
        if (!$this->shouldIntercept()) {
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
