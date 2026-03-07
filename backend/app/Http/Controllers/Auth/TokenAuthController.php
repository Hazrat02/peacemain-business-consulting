<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class TokenAuthController extends Controller
{
    private function dashboardRouteFor(User $user): string
    {
        return $user->is_admin ? 'admin.dashboard' : 'user.dashboard';
    }

    public function showLogin(): Response
    {
        return Inertia::render('Auth/Login');
    }

    public function showRegister(): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function showForgotPassword(): Response
    {
        return Inertia::render('Auth/ForgotPassword');
    }

    public function webForgotPassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::query()->where('email', $data['email'])->first();
        if (! $user) {
            return back()->with('status', 'If that email exists, a reset code has been sent.');
        }

        $code = (string) random_int(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $data['email']],
            [
                'token' => Hash::make($code),
                'created_at' => now(),
            ]
        );

        try {
            Mail::raw(
                "Your password reset code is {$code}. It expires in 15 minutes.",
                function ($message) use ($data): void {
                    $message->to($data['email'])->subject('Your Password Reset Code');
                }
            );
        } catch (Throwable) {
            return back()->withErrors([
                'email' => 'Unable to send reset code email right now.',
            ]);
        }

        return back()->with('status', 'Reset code sent. Please check your inbox.');
    }

    public function webLogin(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Invalid credentials.',
            ]);
        }

        $request->session()->regenerate();
        /** @var User $user */
        $user = $request->user();

        return redirect()->intended(route($this->dashboardRouteFor($user)));
    }

    public function webRegister(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'country' => ['required', 'string', 'max:100'],
            'marital_status' => ['nullable', 'string', 'max:30'],
            'passport_type' => ['nullable', 'string', 'max:50'],
            'destination_country' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'profile_image' => ['nullable', 'image', 'max:2048'],
        ]);

        $profileImagePath = null;
        if ($request->hasFile('profile_image')) {
            $profileImagePath = $request->file('profile_image')->store('profile-images', 'public');
        }

        $user = User::create([
            'name' => $data['full_name'],
            'full_name' => $data['full_name'],
            'phone' => $data['phone'],
            'country' => $data['country'],
            'marital_status' => $data['marital_status'] ?? null,
            'passport_type' => $data['passport_type'] ?? null,
            'destination_country' => $data['destination_country'] ?? null,
            'profile_image' => $profileImagePath,
            'email' => $data['email'],
            'password' => $data['password'],
            'is_admin' => false,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route($this->dashboardRouteFor($user));
    }

    public function webLogout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'country' => ['required', 'string', 'max:100'],
            'marital_status' => ['nullable', 'string', 'max:30'],
            'passport_type' => ['nullable', 'string', 'max:50'],
            'destination_country' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'profile_image' => ['nullable', 'image', 'max:2048'],
        ]);

        $profileImagePath = null;
        if ($request->hasFile('profile_image')) {
            $profileImagePath = $request->file('profile_image')->store('profile-images', 'public');
        }

        $user = User::create([
            'name' => $data['name'] ?? $data['full_name'],
            'full_name' => $data['full_name'],
            'phone' => $data['phone'],
            'country' => $data['country'],
            'marital_status' => $data['marital_status'] ?? null,
            'passport_type' => $data['passport_type'] ?? null,
            'destination_country' => $data['destination_country'] ?? null,
            'profile_image' => $profileImagePath,
            'email' => $data['email'],
            'password' => $data['password'],
            'is_admin' => false,
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Registered successfully.',
            'token' => $token,
            'authUser' => $user,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => 'Invalid credentials.',
            ]);
        }

        /** @var User $user */
        $user = $request->user();
        $user->tokens()->delete();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'token' => $token,
            'authUser' => $user,
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'authUser' => $request->user(),
        ]);
    }

    public function allUsers(): JsonResponse
    {
        return response()->json([
            'alluser' => User::query()->latest('id')->get(),
        ]);
    }

    public function referredUsers(Request $request): JsonResponse
    {
        $users = User::query()
            ->where('id', '!=', $request->user()->id)
            ->latest('id')
            ->get();

        return response()->json([
            'alluser' => $users,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Logged out.',
        ]);
    }

    public function requestPasswordResetCode(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::query()->where('email', $data['email'])->first();
        if (! $user) {
            return response()->json([
                'message' => 'If that email exists, a reset code has been sent.',
            ]);
        }

        $code = (string) random_int(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $data['email']],
            [
                'token' => Hash::make($code),
                'created_at' => now(),
            ]
        );

        try {
            Mail::raw(
                "Your password reset code is {$code}. It expires in 15 minutes.",
                function ($message) use ($data): void {
                    $message->to($data['email'])->subject('Your Password Reset Code');
                }
            );
        } catch (Throwable) {
            return response()->json([
                'message' => 'Unable to send reset code email right now.',
            ], 500);
        }

        return response()->json([
            'message' => 'If that email exists, a reset code has been sent.',
        ]);
    }

    public function verifyPasswordResetCode(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'digits:6'],
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $data['email'])->first();
        if (! $record) {
            throw ValidationException::withMessages([
                'code' => 'Invalid code.',
            ]);
        }

        $isExpired = Carbon::parse($record->created_at)->lt(now()->subMinutes(15));
        if ($isExpired || ! Hash::check($data['code'], $record->token)) {
            throw ValidationException::withMessages([
                'code' => 'Invalid or expired code.',
            ]);
        }

        return response()->json([
            'message' => 'Code verified.',
        ]);
    }

    public function resetPasswordWithCode(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'digits:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $data['email'])->first();
        if (! $record) {
            throw ValidationException::withMessages([
                'code' => 'Invalid code.',
            ]);
        }

        $isExpired = Carbon::parse($record->created_at)->lt(now()->subMinutes(15));
        if ($isExpired || ! Hash::check($data['code'], $record->token)) {
            throw ValidationException::withMessages([
                'code' => 'Invalid or expired code.',
            ]);
        }

        $user = User::query()->where('email', $data['email'])->first();
        if (! $user) {
            throw ValidationException::withMessages([
                'email' => 'User not found.',
            ]);
        }

        $user->password = $data['password'];
        $user->save();
        $user->tokens()->delete();

        DB::table('password_reset_tokens')->where('email', $data['email'])->delete();

        return response()->json([
            'message' => 'Password reset successfully.',
        ]);
    }
}
