<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserRequiredDocument;
use App\Services\DocumentChecklistService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class UserDashboardController extends Controller
{
    public function __invoke(Request $request, DocumentChecklistService $service): Response|RedirectResponse
    {
        if ($request->user()?->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        $progress = $service->syncAndGetProgress($request->user());

        return Inertia::render('User/Dashboard', [
            'stats' => [
                'applications' => 0,
                'approved_documents' => $progress['approved'],
                'pending_documents' => $progress['pending'],
                'waived_documents' => $progress['waived'],
                'required_documents' => $progress['required'],
                'completion_percentage' => $progress['completion_percentage'],
            ],
        ]);
    }

    public function documents(Request $request, DocumentChecklistService $service): Response|RedirectResponse
    {
        if ($request->user()?->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        $progress = $service->syncAndGetProgress($request->user());
        $checklist = $service->userChecklist($request->user());

        $sections = $checklist
            ->sortBy([
                fn (UserRequiredDocument $item) => $item->document?->section?->sort_order ?? 0,
                fn (UserRequiredDocument $item) => $item->document?->sort_order ?? 0,
            ])
            ->groupBy(fn (UserRequiredDocument $item) => $item->document?->section?->name ?? 'Other')
            ->map(function (Collection $rows) {
                return $rows->values()->map(function (UserRequiredDocument $item) {
                    return [
                        'requirement_id' => $item->id,
                        'status' => $item->status,
                        'is_required' => $item->is_required,
                        'title' => $item->document?->title,
                        'description' => $item->document?->description,
                        'latest_submission' => $item->latestSubmission ? [
                            'id' => $item->latestSubmission->id,
                            'file_name' => $item->latestSubmission->file_name,
                            'file_url' => $item->latestSubmission->file_url,
                            'version' => $item->latestSubmission->version,
                            'review_note' => $item->latestSubmission->review_note,
                            'review_status' => $item->latestSubmission->review_status,
                            'created_at' => optional($item->latestSubmission->created_at)->toDateTimeString(),
                        ] : null,
                        'history' => $item->submissions->map(function ($submission) {
                            return [
                                'id' => $submission->id,
                                'version' => $submission->version,
                                'file_name' => $submission->file_name,
                                'file_url' => $submission->file_url,
                                'review_status' => $submission->review_status,
                                'review_note' => $submission->review_note,
                                'created_at' => optional($submission->created_at)->toDateTimeString(),
                            ];
                        })->values(),
                    ];
                });
            })
            ->map(fn (Collection $documents, string $name) => [
                'name' => $name,
                'documents' => $documents,
            ])
            ->values();

        return Inertia::render('User/Documents', [
            'sections' => $sections,
            'progress' => $progress,
        ]);
    }
}
