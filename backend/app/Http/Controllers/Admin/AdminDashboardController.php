<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use App\Models\SiteContent;
use App\Models\User;
use App\Services\DocumentChecklistService;
use App\Support\SiteContentDefaults;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class AdminDashboardController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_users' => User::count(),
                'admin_users' => User::where('is_admin', true)->count(),
                'active_users' => User::whereDate('updated_at', '>=', now()->subDays(7))->count(),
            ],
            'recent_users' => User::query()
                ->latest('id')
                ->limit(6)
                ->get(['id', 'full_name', 'email', 'country', 'is_admin']),
        ]);
    }

    public function users(Request $request, DocumentChecklistService $service): Response
    {
        $filters = $request->validate([
            'country' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:admin,user,active,banned'],
            'completion' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $query = User::query()->latest('id');

        if (! empty($filters['country'])) {
            $query->where('country', 'like', '%' . $filters['country'] . '%');
        }

        if (! empty($filters['status'])) {
            if ($filters['status'] === 'admin') {
                $query->where('is_admin', true);
            } elseif ($filters['status'] === 'user') {
                $query->where('is_admin', false);
            } elseif ($filters['status'] === 'active') {
                $query->where('is_banned', false);
            } elseif ($filters['status'] === 'banned') {
                $query->where('is_banned', true);
            }
        }

        $users = $query
            ->limit(100)
            ->get(['id', 'full_name', 'email', 'phone', 'country', 'is_admin', 'is_banned']);

        $rows = $users->map(function (User $user) use ($service): array {
            $completion = $user->is_admin ? 0 : (int) ($service->syncAndGetProgress($user)['completion_percentage'] ?? 0);

            return [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'country' => $user->country,
                'is_admin' => $user->is_admin,
                'is_banned' => $user->is_banned,
                'completion_percentage' => $completion,
            ];
        });

        if (isset($filters['completion']) && $filters['completion'] !== null) {
            $rows = $rows->filter(fn (array $row) => (int) $row['completion_percentage'] >= (int) $filters['completion'])->values();
        }

        $countries = User::query()
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->distinct()
            ->orderBy('country')
            ->pluck('country')
            ->values();

        return Inertia::render('Admin/Users', [
            'users' => $rows->values(),
            'countries' => $countries,
            'filters' => [
                'country' => $filters['country'] ?? '',
                'status' => $filters['status'] ?? '',
                'completion' => isset($filters['completion']) ? (string) $filters['completion'] : '',
            ],
        ]);
    }

    public function userView(User $user): Response
    {
        $user->loadCount([
            'requiredDocuments as documents_total',
            'requiredDocuments as documents_missing' => fn ($query) => $query->where('status', 'missing'),
            'requiredDocuments as documents_approved' => fn ($query) => $query->where('status', 'approved'),
            'requiredDocuments as documents_rejected' => fn ($query) => $query->where('status', 'rejected'),
            'requiredDocuments as documents_waived' => fn ($query) => $query->where('status', 'waived'),
            'documentSubmissions as submissions_total',
        ]);

        $recentSubmissions = $user->documentSubmissions()
            ->latest('id')
            ->limit(8)
            ->get(['id', 'file_name', 'review_status', 'review_note', 'created_at']);

        return Inertia::render('Admin/UserView', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'country' => $user->country,
                'marital_status' => $user->marital_status,
                'passport_type' => $user->passport_type,
                'destination_country' => $user->destination_country,
                'is_admin' => $user->is_admin,
                'is_banned' => $user->is_banned,
                'banned_at' => optional($user->banned_at)->toDateTimeString(),
                'profile_image_url' => $user->profile_image_url,
                'email_verified_at' => optional($user->email_verified_at)->toDateTimeString(),
                'created_at' => optional($user->created_at)->toDateTimeString(),
                'updated_at' => optional($user->updated_at)->toDateTimeString(),
            ],
            'document_summary' => [
                'total' => (int) $user->documents_total,
                'missing' => (int) $user->documents_missing,
                'approved' => (int) $user->documents_approved,
                'rejected' => (int) $user->documents_rejected,
                'waived' => (int) $user->documents_waived,
                'submissions_total' => (int) $user->submissions_total,
            ],
            'recent_submissions' => $recentSubmissions,
        ]);
    }

    public function loginAsUser(Request $request, User $user): RedirectResponse
    {
        if ($user->is_admin) {
            return back()->withErrors(['user' => 'Cannot log in as an admin user.']);
        }

        if ($user->is_banned) {
            return back()->withErrors(['user' => 'Cannot log in as a banned user.']);
        }

        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        return redirect()->route('user.dashboard');
    }

    public function toggleBanUser(User $user): RedirectResponse
    {
        if ($user->is_admin) {
            return back()->withErrors(['user' => 'Admin users cannot be banned from this action.']);
        }

        $user->update([
            'is_banned' => ! $user->is_banned,
            'banned_at' => $user->is_banned ? null : now(),
        ]);

        return back()->with('status', $user->is_banned ? 'User banned successfully.' : 'User unbanned successfully.');
    }

    public function contactUs(): Response
    {
        return Inertia::render('Admin/ContactUs', [
            'contacts' => ContactSubmission::query()
                ->latest('id')
                ->limit(200)
                ->get(['id', 'name', 'email', 'subject', 'message', 'status', 'is_read', 'read_at', 'replied_at', 'created_at']),
        ]);
    }

    public function contactReply(ContactSubmission $contact): Response
    {
        $this->markAsRead($contact);

        return Inertia::render('Admin/ContactReply', [
            'contact' => $contact->only([
                'id',
                'name',
                'email',
                'subject',
                'message',
                'status',
                'is_read',
                'read_at',
                'replied_at',
                'created_at',
            ]),
        ]);
    }

    public function markContactRead(ContactSubmission $contact): RedirectResponse
    {
        $this->markAsRead($contact);

        return back()->with('status', 'Marked as read.');
    }

    public function updateContactStatus(Request $request, ContactSubmission $contact): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:New,In Progress,Resolved'],
        ]);

        $contact->update([
            'status' => $data['status'],
        ]);

        return back()->with('status', 'Contact status updated.');
    }

    public function sendContactReply(Request $request, ContactSubmission $contact): RedirectResponse
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:10000'],
            'mark_resolved' => ['nullable', 'boolean'],
        ]);

        $this->sendReplyMail($contact, $data['subject'], $data['message']);

        $contact->update([
            'replied_at' => now(),
            'status' => ($data['mark_resolved'] ?? false) ? 'Resolved' : $contact->status,
            'is_read' => true,
            'read_at' => $contact->read_at ?? now(),
        ]);

        return redirect()->route('admin.contact-us.reply', $contact)->with('status', 'Reply sent successfully.');
    }

    public function roles(): Response
    {
        return Inertia::render('Admin/Roles', [
            'roles' => [
                ['name' => 'Super Admin', 'users' => 1, 'permissions' => 'All access'],
                ['name' => 'Manager', 'users' => 2, 'permissions' => 'Users, Content, Contact'],
                ['name' => 'Editor', 'users' => 3, 'permissions' => 'Content only'],
            ],
        ]);
    }

    public function contentBanner(): Response
    {
        return Inertia::render('Admin/Content/Banner', [
            'banners' => $this->contentValue('banner', SiteContentDefaults::banners()),
        ]);
    }

    public function contentSidebar(): Response
    {
        return Inertia::render('Admin/Content/Sidebar', [
            'links' => $this->contentValue('sidebar', SiteContentDefaults::sidebarLinks()),
        ]);
    }

    public function contentFaq(): Response
    {
        return Inertia::render('Admin/Content/Faq', [
            'faqs' => $this->contentValue('faq', SiteContentDefaults::faqs()),
        ]);
    }

    public function contentContactInfo(): Response
    {
        return Inertia::render('Admin/Content/ContactInfo', [
            'info' => $this->contentValue('contact_info', SiteContentDefaults::contactInfo()),
        ]);
    }

    public function updateContentBanner(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*.title' => ['required', 'string', 'max:255'],
            'items.*.subtitle' => ['nullable', 'string', 'max:255'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.button_text' => ['nullable', 'string', 'max:100'],
            'items.*.button_url' => ['nullable', 'string', 'max:500'],
            'items.*.image_url' => ['nullable', 'string', 'max:2000'],
            'items.*.position' => ['nullable', 'string', 'max:100'],
            'items.*.status' => ['nullable', 'string', 'max:20'],
            'items.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $defaultImages = collect(SiteContentDefaults::banners())->pluck('image_url')->filter()->values();
        $items = collect($data['items'])->map(function (array $item, int $index) use ($defaultImages): array {
            $fallbackImage = $defaultImages->get($index) ?? $defaultImages->first() ?? '';

            return [
                'title' => $item['title'],
                'subtitle' => $item['subtitle'] ?? '',
                'description' => $item['description'] ?? '',
                'button_text' => $item['button_text'] ?? 'Contact Us',
                'button_url' => $item['button_url'] ?? '/contact',
                'image_url' => $item['image_url'] ?: $fallbackImage,
                'position' => $item['position'] ?? 'Home Top',
                'status' => $item['status'] ?? 'Active',
                'sort_order' => $item['sort_order'] ?? ($index + 1),
            ];
        })->sortBy('sort_order')->values()->all();

        $this->storeContentValue('banner', $items);

        return back()->with('status', 'Banner content updated.');
    }

    public function updateContentSidebar(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*.label' => ['required', 'string', 'max:100'],
            'items.*.url' => ['required', 'string', 'max:500'],
            'items.*.status' => ['nullable', 'string', 'max:20'],
            'items.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $items = collect($data['items'])->map(function (array $item, int $index): array {
            return [
                'label' => $item['label'],
                'url' => $item['url'],
                'status' => $item['status'] ?? 'Active',
                'sort_order' => $item['sort_order'] ?? ($index + 1),
            ];
        })->sortBy('sort_order')->values()->all();

        $this->storeContentValue('sidebar', $items);

        return back()->with('status', 'Sidebar content updated.');
    }

    public function updateContentFaq(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*.question' => ['required', 'string', 'max:500'],
            'items.*.answer' => ['required', 'string'],
            'items.*.category' => ['nullable', 'string', 'max:100'],
            'items.*.status' => ['nullable', 'string', 'max:20'],
        ]);

        $items = collect($data['items'])->map(function (array $item): array {
            return [
                'question' => $item['question'],
                'answer' => $item['answer'],
                'category' => $item['category'] ?? 'General',
                'status' => $item['status'] ?? 'Published',
            ];
        })->values()->all();

        $this->storeContentValue('faq', $items);

        return back()->with('status', 'FAQ content updated.');
    }

    public function updateContentContactInfo(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:1000'],
            'map_url' => ['nullable', 'string', 'max:2000'],
            'map_embed_url' => ['nullable', 'string', 'max:5000'],
            'welcome_subject' => ['nullable', 'string', 'max:255'],
            'welcome_message' => ['nullable', 'string', 'max:5000'],
        ]);

        $existing = $this->contentValue('contact_info', SiteContentDefaults::contactInfo());
        $value = array_merge($existing, [
            'phone' => $data['phone'],
            'email' => $data['email'],
            'address' => $data['address'],
            'map_url' => $data['map_url'] ?? '',
            'map_embed_url' => $data['map_embed_url'] ?? ($existing['map_embed_url'] ?? ''),
            'welcome_subject' => $data['welcome_subject'] ?? ($existing['welcome_subject'] ?? 'Welcome to PEACEMAIN'),
            'welcome_message' => $data['welcome_message'] ?? ($existing['welcome_message'] ?? 'Thanks for contacting us. Our team will get back to you shortly.'),
        ]);

        $this->storeContentValue('contact_info', $value);

        return back()->with('status', 'Contact info updated.');
    }

    public function settings(): Response
    {
        return Inertia::render('Admin/Settings', [
            'settings' => $this->contentValue('general_settings', SiteContentDefaults::generalSettings()),
            'smtp' => $this->contentValue('smtp_settings', SiteContentDefaults::smtpSettings()),
        ]);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'site_name' => ['required', 'string', 'max:100'],
            'timezone' => ['required', 'string', 'max:100'],
            'default_country' => ['nullable', 'string', 'max:100'],
            'support_email' => ['required', 'email', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'smtp_host' => ['nullable', 'string', 'max:255'],
            'smtp_port' => ['nullable', 'integer', 'min:1'],
            'smtp_username' => ['nullable', 'string', 'max:255'],
            'smtp_password' => ['nullable', 'string', 'max:255'],
            'smtp_encryption' => ['nullable', 'in:none,ssl,tls'],
            'from_email' => ['nullable', 'email', 'max:255'],
            'from_name' => ['nullable', 'string', 'max:255'],
            'mail_template_html' => ['nullable', 'string'],
        ]);

        $existingGeneral = $this->contentValue('general_settings', SiteContentDefaults::generalSettings());
        $logoUrl = $existingGeneral['logo_url'] ?? '';

        if ($request->hasFile('logo')) {
            if ($logoUrl && str_contains($logoUrl, '/storage/')) {
                $logoPath = parse_url($logoUrl, PHP_URL_PATH) ?: $logoUrl;
                $oldPath = ltrim(str_replace('/storage/', '', $logoPath), '/');
                if ($oldPath !== '' && Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $path = $request->file('logo')->store('settings', 'public');
            $logoUrl = '/storage/' . ltrim($path, '/');
        }

        $this->storeContentValue('general_settings', [
            'site_name' => $data['site_name'],
            'timezone' => $data['timezone'],
            'default_country' => $data['default_country'] ?? '',
            'support_email' => $data['support_email'],
            'logo_url' => $logoUrl,
        ]);

        $this->storeContentValue('smtp_settings', [
            'host' => $data['smtp_host'] ?? '',
            'port' => (int) ($data['smtp_port'] ?? 587),
            'username' => $data['smtp_username'] ?? '',
            'password' => $data['smtp_password'] ?? '',
            'encryption' => $data['smtp_encryption'] ?? 'tls',
            'from_email' => $data['from_email'] ?? '',
            'from_name' => $data['from_name'] ?? '',
            'mail_template_html' => $data['mail_template_html'] ?? '',
        ]);

        return back()->with('status', 'Settings updated.');
    }

    private function contentValue(string $key, mixed $default): mixed
    {
        $record = SiteContent::query()->where('key', $key)->first();

        return $record?->value ?? $default;
    }

    private function storeContentValue(string $key, mixed $value): void
    {
        SiteContent::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    private function markAsRead(ContactSubmission $contact): void
    {
        if (! $contact->is_read) {
            $contact->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    private function sendReplyMail(ContactSubmission $contact, string $subject, string $body): void
    {
        $smtp = $this->contentValue('smtp_settings', SiteContentDefaults::smtpSettings());

        if (empty($smtp['host']) || empty($smtp['port']) || empty($smtp['from_email'])) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'subject' => 'SMTP settings are incomplete. Please configure mail settings first.',
            ]);
        }

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.transport' => 'smtp',
            'mail.mailers.smtp.host' => $smtp['host'],
            'mail.mailers.smtp.port' => (int) $smtp['port'],
            'mail.mailers.smtp.username' => $smtp['username'] ?? '',
            'mail.mailers.smtp.password' => $smtp['password'] ?? '',
            'mail.mailers.smtp.encryption' => ($smtp['encryption'] ?? 'tls') === 'none' ? null : ($smtp['encryption'] ?? 'tls'),
            'mail.from.address' => $smtp['from_email'],
            'mail.from.name' => $smtp['from_name'] ?: 'PEACEMAIN',
        ]);

        $html = '<div style="font-family:Arial,sans-serif;line-height:1.6">' . nl2br(e($body)) . '</div>';

        try {
            Mail::html($html, function ($message) use ($contact, $subject): void {
                $message->to($contact->email)->subject($subject);
            });
        } catch (\Throwable $exception) {
            Log::error('Admin reply mail send failed', [
                'contact_submission_id' => $contact->id,
                'error' => $exception->getMessage(),
            ]);

            throw \Illuminate\Validation\ValidationException::withMessages([
                'subject' => 'Failed to send email. Check SMTP settings and try again.',
            ]);
        }
    }
}
