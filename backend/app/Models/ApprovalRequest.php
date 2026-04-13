<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalRequest extends Model
{
    protected $fillable = [
        'requested_by',
        'reviewed_by',
        'action',
        'subject_type',
        'subject_id',
        'payload',
        'status',
        'reviewer_note',
        'reviewed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /** Resolve the short model name for display, e.g. "Project" */
    public function subjectLabel(): string
    {
        return class_basename($this->subject_type);
    }
}
