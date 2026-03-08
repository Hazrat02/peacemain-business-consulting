<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DocumentSubmission;
use App\Models\UserRequiredDocument;
use App\Services\DocumentChecklistService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserDocumentController extends Controller
{
    public function store(Request $request, UserRequiredDocument $requirement): RedirectResponse
    {
        if ($request->user()?->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        abort_unless((int) $requirement->user_id === (int) $request->user()->id, 403);

        if ($requirement->status === DocumentChecklistService::STATUS_APPROVED) {
            return back()->withErrors([
                'file' => 'Approved document cannot be re-uploaded.',
            ]);
        }

        $data = $request->validate([
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:5120'],
        ]);

        $nextVersion = ((int) $requirement->submissions()->max('version')) + 1;
        $file = $data['file'];

        $directory = sprintf('documents/%d/%d', $request->user()->id, $requirement->document_id);
        $randomStoredName = $file->hashName();
        $storedPath = $file->storeAs($directory, $randomStoredName, 'public');

        $submission = DocumentSubmission::query()->create([
            'user_required_document_id' => $requirement->id,
            'user_id' => $request->user()->id,
            'version' => $nextVersion,
            'file_path' => $storedPath,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize() ?? 0,
            'mime_type' => $file->getClientMimeType(),
            'review_status' => 'pending',
        ]);

        $requirement->update([
            'latest_submission_id' => $submission->id,
            'status' => DocumentChecklistService::STATUS_UPLOADED,
            'status_updated_at' => now(),
        ]);

        return back()->with('status', 'Document uploaded successfully.');
    }

    public function download(Request $request, DocumentSubmission $submission): StreamedResponse
    {
        $user = $request->user();

        $isOwner = (int) $submission->user_id === (int) $user->id;
        $isAdmin = (bool) $user->is_admin;

        abort_unless($isOwner || $isAdmin, 403);
        abort_unless(Storage::disk('public')->exists($submission->file_path), 404);

        return Storage::disk('public')->download(
            $submission->file_path,
            $submission->file_name
        );
    }
}
