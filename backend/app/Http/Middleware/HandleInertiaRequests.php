<?php

namespace App\Http\Middleware;

use App\Models\ContactSubmission;
use App\Models\DocumentSubmission;
use App\Models\SiteContent;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'brand' => fn () => [
                'logoUrl' => $this->brandLogoUrl(),
            ],
            'adminHeader' => fn () => $this->adminHeaderData($request),
            'flash' => [
                'status' => fn () => $request->session()->get('status'),
            ],
            'currentPath' => fn () => $request->path(),
        ];
    }

    private function adminHeaderData(Request $request): array
    {
        $user = $request->user();
        $brandLogoUrl = $this->brandLogoUrl();

        if (! $user || ! $user->is_admin) {
            return [
                'contactMessages' => [],
                'unreadContactCount' => 0,
                'recentDocs' => [],
                'unreadRecentDocsCount' => 0,
                'brandLogoUrl' => $brandLogoUrl,
            ];
        }

        $messages = ContactSubmission::query()
            ->latest('id')
            ->limit(5)
            ->get(['id', 'name', 'email', 'subject', 'message', 'is_read', 'created_at'])
            ->map(function (ContactSubmission $message): array {
                return [
                    'id' => $message->id,
                    'name' => $message->name,
                    'email' => $message->email,
                    'subject' => $message->subject,
                    'message' => $message->message,
                    'is_read' => $message->is_read,
                    'created_at' => $message->created_at?->toIso8601String(),
                ];
            })
            ->values()
            ->all();

        $unreadCount = ContactSubmission::query()
            ->where('is_read', false)
            ->count();

        $recentDocs = DocumentSubmission::query()
            ->with([
                'user:id,full_name,email',
                'requiredDocument.document:id,title',
            ])
            ->latest('id')
            ->limit(4)
            ->get(['id', 'user_id', 'user_required_document_id', 'file_name', 'version', 'review_status', 'is_seen', 'created_at'])
            ->map(function (DocumentSubmission $submission): array {
                return [
                    'id' => $submission->id,
                    'user_name' => $submission->user?->full_name ?: 'Unknown User',
                    'user_email' => $submission->user?->email ?: '',
                    'document_title' => $submission->requiredDocument?->document?->title ?: 'Document',
                    'file_name' => $submission->file_name,
                    'version' => $submission->version,
                    'review_status' => $submission->review_status,
                    'is_seen' => $submission->is_seen,
                    'file_url' => $submission->file_url,
                    'created_at' => $submission->created_at?->toIso8601String(),
                ];
            })
            ->values()
            ->all();

        $unreadRecentDocsCount = DocumentSubmission::query()
            ->where('is_seen', false)
            ->count();

        return [
            'contactMessages' => $messages,
            'unreadContactCount' => $unreadCount,
            'recentDocs' => $recentDocs,
            'unreadRecentDocsCount' => $unreadRecentDocsCount,
            'brandLogoUrl' => $brandLogoUrl,
        ];
    }

    private function brandLogoUrl(): string
    {
        $generalSettings = SiteContent::query()->where('key', 'general_settings')->first()?->value ?? [];
        $brandLogoUrl = $generalSettings['logo_url'] ?? '';

        if ($brandLogoUrl && str_starts_with($brandLogoUrl, 'http')) {
            $brandLogoUrl = parse_url($brandLogoUrl, PHP_URL_PATH) ?: $brandLogoUrl;
        }

        return $brandLogoUrl;
    }
}
