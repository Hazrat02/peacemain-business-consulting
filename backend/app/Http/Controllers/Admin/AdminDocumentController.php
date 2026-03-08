<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentReviewLog;
use App\Models\DocumentRule;
use App\Models\DocumentSection;
use App\Models\DocumentSubmission;
use App\Models\User;
use App\Models\UserRequiredDocument;
use App\Services\DocumentChecklistService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminDocumentController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Documents', [
            'sections' => DocumentSection::query()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(),
        ]);
    }

    public function sectionRules(DocumentSection $section): Response
    {
        return Inertia::render('Admin/DocumentRules', [
            'section' => $section,
            'documents' => Document::query()
                ->with(['rules'])
                ->where('document_section_id', $section->id)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(),
        ]);
    }

    public function storeSection(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        DocumentSection::query()->create([
            'name' => $data['name'],
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $data['is_active'] ?? true,
        ]);

        return back()->with('status', 'Document section created.');
    }

    public function updateSection(Request $request, DocumentSection $section): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ]);

        $section->update([
            'name' => $data['name'],
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $data['is_active'],
        ]);

        return back()->with('status', 'Document section updated.');
    }

    public function deleteSection(DocumentSection $section): RedirectResponse
    {
        $section->delete();

        return back()->with('status', 'Document section deleted.');
    }

    public function storeDocument(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'document_section_id' => ['required', 'exists:document_sections,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_required' => ['required', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        Document::query()->create([
            'document_section_id' => $data['document_section_id'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'is_required' => $data['is_required'],
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $data['is_active'] ?? true,
        ]);

        return back()->with('status', 'Master document added.');
    }

    public function updateDocument(Request $request, Document $document): RedirectResponse
    {
        $data = $request->validate([
            'document_section_id' => ['required', 'exists:document_sections,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_required' => ['required', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ]);

        $document->update($data);

        return back()->with('status', 'Master document updated.');
    }

    public function deleteDocument(Document $document): RedirectResponse
    {
        $document->delete();

        return back()->with('status', 'Master document deleted.');
    }

    public function storeRule(Request $request, Document $document): RedirectResponse
    {
        $data = $request->validate([
            'source_country' => ['nullable', 'string', 'max:100'],
            'destination_country' => ['nullable', 'string', 'max:100'],
            'marital_status' => ['nullable', 'string', 'max:30'],
            'passport_type' => ['nullable', 'string', 'max:50'],
            'is_exclusion' => ['nullable', 'boolean'],
        ]);

        $document->rules()->create([
            'source_country' => $data['source_country'] ?? null,
            'destination_country' => $data['destination_country'] ?? null,
            'marital_status' => $data['marital_status'] ?? null,
            'passport_type' => $data['passport_type'] ?? null,
            'is_exclusion' => $data['is_exclusion'] ?? false,
        ]);

        return back()->with('status', 'Applicability rule created.');
    }

    public function deleteRule(DocumentRule $rule): RedirectResponse
    {
        $rule->delete();

        return back()->with('status', 'Applicability rule deleted.');
    }

    public function userChecklists(Request $request, DocumentChecklistService $service): Response
    {
        $filters = $request->validate([
            'country' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string', 'max:20'],
            'completion' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $query = User::query()->where('is_admin', false);

        if (! empty($filters['country'])) {
            $query->where('country', $filters['country']);
        }

        $users = $query->orderByDesc('id')->limit(100)->get();

        $rows = $users->map(function (User $user) use ($service) {
            $progress = $service->syncAndGetProgress($user);

            return [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'country' => $user->country,
                'destination_country' => $user->destination_country,
                'progress' => $progress,
            ];
        });

        if (! empty($filters['status'])) {
            $rows = $rows->filter(function (array $row) use ($filters) {
                return match ($filters['status']) {
                    'completed' => (float) $row['progress']['completion_percentage'] >= 100,
                    'pending' => $row['progress']['pending'] > 0,
                    'rejected' => $row['progress']['rejected'] > 0,
                    default => true,
                };
            })->values();
        }

        if (isset($filters['completion']) && $filters['completion'] !== null) {
            $rows = $rows->filter(fn (array $row) => (int) $row['progress']['completion_percentage'] >= (int) $filters['completion'])->values();
        }

        $countries = User::query()
            ->where('is_admin', false)
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->distinct()
            ->orderBy('country')
            ->pluck('country')
            ->values();

        return Inertia::render('Admin/UserDocuments', [
            'users' => $rows,
            'countries' => $countries,
            'filters' => [
                'country' => $filters['country'] ?? '',
                'status' => $filters['status'] ?? '',
                'completion' => isset($filters['completion']) ? (string) $filters['completion'] : '',
            ],
        ]);
    }

    public function recentDocs(Request $request): Response
    {
        $submissions = DocumentSubmission::query()
            ->with([
                'user:id,full_name,email',
                'requiredDocument.document:id,title',
            ])
            ->latest('id')
            ->paginate(50)
            ->withQueryString()
            ->through(function (DocumentSubmission $submission): array {
                return [
                    'id' => $submission->id,
                    'user' => [
                        'id' => $submission->user?->id,
                        'full_name' => $submission->user?->full_name,
                        'email' => $submission->user?->email,
                    ],
                    'document_title' => $submission->requiredDocument?->document?->title,
                    'file_name' => $submission->file_name,
                    'file_url' => $submission->file_url,
                    'version' => $submission->version,
                    'review_status' => $submission->review_status,
                    'created_at' => optional($submission->created_at)->toDateTimeString(),
                ];
            });

        return Inertia::render('Admin/RecentDocs', [
            'submissions' => $submissions,
        ]);
    }

    public function markSubmissionSeen(DocumentSubmission $submission): RedirectResponse
    {
        if (! $submission->is_seen) {
            $submission->update([
                'is_seen' => true,
                'seen_at' => now(),
            ]);
        }

        return back()->with('status', 'Notification marked as seen.');
    }

    public function showUserChecklist(User $user, DocumentChecklistService $service): Response
    {
        $requirements = $service->userChecklist($user)
            ->sortBy([
                fn (UserRequiredDocument $item) => $item->document?->section?->sort_order ?? 0,
                fn (UserRequiredDocument $item) => $item->document?->sort_order ?? 0,
            ])
            ->values()
            ->map(function (UserRequiredDocument $item) {
                return [
                    'id' => $item->id,
                    'status' => $item->status,
                    'is_required' => $item->is_required,
                    'document' => [
                        'id' => $item->document?->id,
                        'title' => $item->document?->title,
                        'section' => $item->document?->section?->name,
                    ],
                    'latest_submission' => $item->latestSubmission ? [
                        'id' => $item->latestSubmission->id,
                        'file_name' => $item->latestSubmission->file_name,
                        'file_url' => $item->latestSubmission->file_url,
                        'review_status' => $item->latestSubmission->review_status,
                        'review_note' => $item->latestSubmission->review_note,
                        'version' => $item->latestSubmission->version,
                        'created_at' => optional($item->latestSubmission->created_at)->toDateTimeString(),
                    ] : null,
                    'history' => $item->submissions->map(fn (DocumentSubmission $submission) => [
                        'id' => $submission->id,
                        'version' => $submission->version,
                        'file_name' => $submission->file_name,
                        'file_url' => $submission->file_url,
                        'review_status' => $submission->review_status,
                        'review_note' => $submission->review_note,
                        'created_at' => optional($submission->created_at)->toDateTimeString(),
                    ])->values(),
                ];
            });

        return Inertia::render('Admin/UserDocumentReview', [
            'user' => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'country' => $user->country,
            ],
            'progress' => $service->syncAndGetProgress($user),
            'requirements' => $requirements,
        ]);
    }

    public function reviewSubmission(Request $request, UserRequiredDocument $requirement): RedirectResponse
    {
        $data = $request->validate([
            'action' => ['required', 'in:approved,rejected,waived'],
            'note' => ['nullable', 'required_if:action,rejected', 'string', 'max:2000'],
        ]);

        $submission = $requirement->latestSubmission;

        if ($data['action'] !== 'waived' && ! $submission) {
            return back()->withErrors([
                'action' => 'No submission exists for this document.',
            ]);
        }

        if ($submission) {
            $submission->update([
                'review_status' => $data['action'] === 'waived' ? $submission->review_status : $data['action'],
                'review_note' => $data['note'] ?? null,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);
        }

        $requirement->update([
            'status' => $data['action'],
            'status_updated_at' => now(),
        ]);

        DocumentReviewLog::query()->create([
            'user_required_document_id' => $requirement->id,
            'document_submission_id' => $submission?->id,
            'reviewed_by' => $request->user()->id,
            'action' => $data['action'],
            'note' => $data['note'] ?? null,
        ]);

        return back()->with('status', 'Document review saved.');
    }
}
