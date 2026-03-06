<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserDashboardController extends Controller
{
    public function __invoke(Request $request): Response|RedirectResponse
    {
        if ($request->user()?->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        return Inertia::render('User/Dashboard', [
            'stats' => [
                'applications' => 4,
                'approved_documents' => 9,
                'pending_documents' => 2,
            ],
        ]);
    }

    public function documents(Request $request): Response|RedirectResponse
    {
        if ($request->user()?->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        return Inertia::render('User/Documents', [
            'documents' => [
                ['name' => 'Passport Copy', 'type' => 'Identity', 'status' => 'Approved'],
                ['name' => 'Bank Statement', 'type' => 'Financial', 'status' => 'Pending'],
                ['name' => 'Academic Transcript', 'type' => 'Education', 'status' => 'Approved'],
            ],
        ]);
    }

    public function overseas(Request $request): Response|RedirectResponse
    {
        if ($request->user()?->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        return Inertia::render('User/Overseas', [
            'applications' => [
                ['country' => 'Canada', 'program' => 'PG Diploma', 'intake' => 'Fall 2026', 'status' => 'Under Review'],
                ['country' => 'Australia', 'program' => 'MBA', 'intake' => 'Spring 2027', 'status' => 'Submitted'],
            ],
        ]);
    }
}
