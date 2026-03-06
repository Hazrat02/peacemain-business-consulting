<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
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

    public function users(): Response
    {
        return Inertia::render('Admin/Users', [
            'users' => User::query()
                ->latest('id')
                ->limit(25)
                ->get(['id', 'full_name', 'email', 'phone', 'country', 'is_admin']),
        ]);
    }

    public function contactUs(): Response
    {
        return Inertia::render('Admin/ContactUs', [
            'contacts' => [
                ['id' => 1, 'name' => 'Rahul Singh', 'email' => 'rahul@example.com', 'subject' => 'Visa guidance', 'status' => 'New'],
                ['id' => 2, 'name' => 'Ayesha Khan', 'email' => 'ayesha@example.com', 'subject' => 'Document check', 'status' => 'In Progress'],
                ['id' => 3, 'name' => 'Mark Lee', 'email' => 'mark@example.com', 'subject' => 'Tuition support', 'status' => 'Resolved'],
            ],
        ]);
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
            'banners' => [
                ['title' => 'Study Abroad Programs', 'position' => 'Home Top', 'status' => 'Active'],
                ['title' => 'Scholarship 2026', 'position' => 'Overseas Page', 'status' => 'Draft'],
            ],
        ]);
    }

    public function contentSidebar(): Response
    {
        return Inertia::render('Admin/Content/Sidebar', [
            'links' => [
                ['label' => 'Application Status', 'url' => '/dashboard/overseas', 'status' => 'Active'],
                ['label' => 'Document Checklist', 'url' => '/dashboard/documents', 'status' => 'Active'],
            ],
        ]);
    }

    public function contentFaq(): Response
    {
        return Inertia::render('Admin/Content/Faq', [
            'faqs' => [
                ['question' => 'How long does visa processing take?', 'category' => 'Visa', 'status' => 'Published'],
                ['question' => 'Which documents are mandatory?', 'category' => 'Documents', 'status' => 'Published'],
            ],
        ]);
    }

    public function contentContactInfo(): Response
    {
        return Inertia::render('Admin/Content/ContactInfo', [
            'info' => [
                'phone' => '+1-234-567-8901',
                'email' => 'support@peacemain.com',
                'address' => '25 Market Street, San Francisco, CA',
                'map_url' => 'https://maps.google.com',
            ],
        ]);
    }

    public function settings(): Response
    {
        return Inertia::render('Admin/Settings', [
            'settings' => [
                'site_name' => 'PeaceMain',
                'timezone' => 'America/Los_Angeles',
                'default_country' => 'India',
                'support_email' => 'support@peacemain.com',
            ],
        ]);
    }
}
