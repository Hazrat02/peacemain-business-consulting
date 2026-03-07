<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function userEdit(Request $request): Response|RedirectResponse
    {
        if ($request->user()?->is_admin) {
            return redirect()->route('admin.profile.edit');
        }

        return Inertia::render('User/Profile', [
            'user' => [
                'full_name' => $request->user()->full_name,
                'email' => $request->user()->email,
                'phone' => $request->user()->phone,
                'country' => $request->user()->country,
                'marital_status' => $request->user()->marital_status,
                'passport_type' => $request->user()->passport_type,
                'destination_country' => $request->user()->destination_country,
                'profile_image_url' => $request->user()->profile_image_url,
            ],
        ]);
    }

    public function adminEdit(Request $request): Response
    {
        return Inertia::render('Admin/Profile', [
            'user' => [
                'full_name' => $request->user()->full_name,
                'email' => $request->user()->email,
                'phone' => $request->user()->phone,
                'country' => $request->user()->country,
                'marital_status' => $request->user()->marital_status,
                'passport_type' => $request->user()->passport_type,
                'destination_country' => $request->user()->destination_country,
                'profile_image_url' => $request->user()->profile_image_url,
            ],
        ]);
    }

    public function userUpdate(Request $request): RedirectResponse
    {
        if ($request->user()?->is_admin) {
            return redirect()->route('admin.profile.edit');
        }

        return $this->updateProfile($request);
    }

    public function adminUpdate(Request $request): RedirectResponse
    {
        return $this->updateProfile($request);
    }

    public function userUpdatePassword(Request $request): RedirectResponse
    {
        if ($request->user()?->is_admin) {
            return redirect()->route('admin.profile.edit');
        }

        return $this->updatePassword($request);
    }

    public function adminUpdatePassword(Request $request): RedirectResponse
    {
        return $this->updatePassword($request);
    }

    private function updateProfile(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
            'phone' => ['nullable', 'string', 'max:50'],
            'country' => ['nullable', 'string', 'max:100'],
            'marital_status' => ['nullable', 'string', 'max:30'],
            'passport_type' => ['nullable', 'string', 'max:50'],
            'destination_country' => ['nullable', 'string', 'max:100'],
            'profile_image' => ['nullable', 'image', 'max:2048'],
        ]);

        $user = $request->user();

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $data['profile_image'] = $request->file('profile_image')->store('profile-images', 'public');
        } else {
            unset($data['profile_image']);
        }

        $user->update($data);

        return back()->with('status', 'Profile updated successfully.');
    }

    private function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if (! Hash::check($data['current_password'], $request->user()->password)) {
            return back()->withErrors([
                'current_password' => 'Current password is incorrect.',
            ]);
        }

        $request->user()->update([
            'password' => $data['password'],
        ]);

        return back()->with('status', 'Password updated successfully.');
    }
}
