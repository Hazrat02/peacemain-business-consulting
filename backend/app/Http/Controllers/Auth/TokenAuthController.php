<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SiteContent;
use App\Models\User;
use App\Support\SiteContentDefaults;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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

        if (! $this->sendPasswordResetCodeMail($user, $code)) {
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
        if ($user->is_banned) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Your account has been banned. Please contact support.',
            ]);
        }

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
        if ($user->is_banned) {
            Auth::guard('web')->logout();
            throw ValidationException::withMessages([
                'email' => 'Your account has been banned. Please contact support.',
            ]);
        }
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

        if (! $this->sendPasswordResetCodeMail($user, $code)) {
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

    private function sendPasswordResetCodeMail(User $user, string $code): bool
    {
        $smtp = $this->value('smtp_settings', SiteContentDefaults::smtpSettings());

        if (empty($smtp['host']) || empty($smtp['port']) || empty($smtp['from_email'])) {
            return false;
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

        $subject = 'Your Password Reset Code';
        $template = $smtp['mail_template_html'] ?? '';

        if (! $template) {
            $template = '<div style="font-family:Arial,sans-serif;padding:24px"><h2>Password Reset</h2><p>Hello [[name]],</p><p>Your password reset code is <strong>[[code]]</strong>.</p><p>This code expires in 15 minutes.</p><p>Regards,<br>[[from_name]]</p></div>';
        }

        $name = $user->full_name ?: $user->name ?: 'User';
        $message = 'Your password reset code is <strong>' . e($code) . '</strong>. It expires in 15 minutes.';
        $values = [
            $name,
            $user->email,
            e($code),
            $message,
            $message,
            $subject,
            $smtp['from_name'] ?: 'PEACEMAIN',
        ];

        $html = str_replace(
            ['[[name]]', '[[email]]', '[[code]]', '[[message]]', '[[welcome_message]]', '[[subject]]', '[[from_name]]'],
            $values,
            $template
        );
        $html = str_replace(
            ['{{name}}', '{{email}}', '{{code}}', '{{message}}', '{{welcome_message}}', '{{subject}}', '{{from_name}}'],
            $values,
            $html
        );

        if (! str_contains($html, e($code))) {
            $html .= '<p style="font-family:Arial,sans-serif;padding:0 24px 24px;margin:0;">Reset code: <strong>' . e($code) . '</strong></p>';
        }

        try {
            Mail::html($html, function ($mailMessage) use ($user, $subject): void {
                $mailMessage->to($user->email)->subject($subject);
            });

            return true;
        } catch (Throwable $exception) {
            Log::error('Password reset code mail send failed', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    private function value(string $key, mixed $default): mixed
    {
        $record = SiteContent::query()->where('key', $key)->first();

        return $record?->value ?? $default;
    }
}
