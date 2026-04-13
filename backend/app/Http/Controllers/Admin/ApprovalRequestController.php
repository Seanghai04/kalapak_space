<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ApprovalRequest;
use App\Models\Project;
use App\Models\Tag;
use App\Models\BlogCategory;
use App\Services\SupabaseStorage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprovalRequestController extends Controller
{
    /** List pending (or all) approval requests */
    public function index(Request $request): JsonResponse
    {
        $query = ApprovalRequest::with(['requester', 'reviewer'])
            ->orderByDesc('created_at');

        if ($status = $request->get('status', 'pending')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        $items = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $items->map(fn($r) => $this->format($r)),
            'meta' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
        ]);
    }

    /** Approve a pending request and apply its action to the main table */
    public function approve(Request $request, int $id): JsonResponse
    {
        $approvalRequest = ApprovalRequest::where('status', 'pending')->findOrFail($id);

        DB::transaction(function () use ($approvalRequest, $request) {
            $this->applyAction($approvalRequest);

            $approvalRequest->update([
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'reviewer_note' => $request->input('note'),
            ]);

            ActivityLog::log(
                'approved',
                "Approved {$approvalRequest->action} request for {$approvalRequest->subjectLabel()} by {$approvalRequest->requester?->name}",
                $approvalRequest
            );
        });

        return response()->json([
            'success' => true,
            'message' => 'Request approved and applied.',
        ]);
    }

    /** Reject a pending request without applying */
    public function reject(Request $request, int $id): JsonResponse
    {
        $approvalRequest = ApprovalRequest::where('status', 'pending')->findOrFail($id);

        $approvalRequest->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'reviewer_note' => $request->input('note'),
        ]);

        ActivityLog::log(
            'rejected',
            "Rejected {$approvalRequest->action} request for {$approvalRequest->subjectLabel()} by {$approvalRequest->requester?->name}",
            $approvalRequest
        );

        return response()->json([
            'success' => true,
            'message' => 'Request rejected.',
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────

    /** Apply the stored action using withoutObservers() to bypass the observer */
    private function applyAction(ApprovalRequest $approvalRequest): void
    {
        $modelClass = $approvalRequest->subject_type;
        $payload = $approvalRequest->payload;

        match ($approvalRequest->action) {
            'create' => $modelClass::withoutObservers(fn() => $modelClass::create($payload)),
            'update' => $modelClass::withoutObservers(function () use ($modelClass, $approvalRequest, $payload) {
                    $model = $modelClass::findOrFail($approvalRequest->subject_id);
                    $model->update($payload);
                }),
            'delete' => $modelClass::withoutObservers(function () use ($modelClass, $approvalRequest) {
                    $model = $modelClass::findOrFail($approvalRequest->subject_id);
                    $model->delete();
                }),
            default => throw new \InvalidArgumentException("Unknown action: {$approvalRequest->action}"),
        };
    }

    private function format(ApprovalRequest $r): array
    {
        return [
            'id' => $r->id,
            'action' => $r->action,
            'subject_type' => $r->subjectLabel(),
            'subject_id' => $r->subject_id,
            'subject_title' => $r->payload['title'] ?? $r->payload['name'] ?? "#{$r->subject_id}",
            'payload' => $r->payload,
            'status' => $r->status,
            'reviewer_note' => $r->reviewer_note,
            'reviewed_at' => $r->reviewed_at?->toIso8601String(),
            'created_at' => $r->created_at->toIso8601String(),
            'requester' => $r->requester ? [
                'id' => $r->requester->id,
                'name' => $r->requester->name,
            ] : null,
            'reviewer' => $r->reviewer ? [
                'id' => $r->reviewer->id,
                'name' => $r->reviewer->name,
            ] : null,
        ];
    }
}
