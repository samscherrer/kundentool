<?php

namespace App\Http\Controllers;

use App\Models\Invite;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request, AuditService $audit): RedirectResponse
    {
        $key = 'login:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withErrors(['email' => 'Zu viele Versuche. Bitte später erneut versuchen.']);
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            RateLimiter::clear($key);
            $audit->log(Auth::user(), 'login_success', 'user', Auth::id());

            return redirect()->intended('/app/dashboard');
        }

        $audit->log(null, 'login_failed', 'user', null, ['email' => $credentials['email']]);
        RateLimiter::hit($key, 60);

        return back()->withErrors(['email' => 'Anmeldung fehlgeschlagen.']);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function showInvite(string $token): View
    {
        return view('auth.invite', ['token' => $token]);
    }

    public function acceptInvite(Request $request, string $token, AuditService $audit): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $invite = Invite::where('token_hash', hash('sha256', $token))
            ->whereNull('accepted_at')
            ->firstOrFail();

        $user = User::create([
            'tenant_id' => $invite->tenant_id,
            'organization_id' => $invite->organization_id,
            'name' => $data['name'],
            'email' => $invite->email,
            'password' => Hash::make($data['password']),
            'is_internal' => false,
        ]);

        $role = Role::firstOrCreate(['name' => $invite->role]);
        $user->roles()->sync([$role->id]);

        $invite->update(['accepted_at' => now()]);

        $audit->log($user, 'invite_accepted', 'invite', $invite->id);

        Auth::login($user);

        return redirect('/portal/dashboard')->with('status', 'Einladung akzeptiert.');
    }

    public function showForgotPassword(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request, AuditService $audit): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'email']]);

        $token = Str::random(64);
        $hash = hash('sha256', $token);

        \DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $data['email']],
            ['token_hash' => $hash, 'created_at' => now(), 'used_at' => null]
        );

        $audit->log(null, 'password_reset_requested', 'password_reset', null, ['email' => $data['email']]);

        // Mail would be sent here in a real app.

        return back()->with('status', 'Falls die Adresse existiert, wurde ein Link gesendet.');
    }

    public function showResetPassword(string $token): View
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request, AuditService $audit): RedirectResponse
    {
        $data = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $record = \DB::table('password_reset_tokens')
            ->where('email', $data['email'])
            ->where('token_hash', hash('sha256', $data['token']))
            ->whereNull('used_at')
            ->first();

        if (! $record) {
            return back()->withErrors(['email' => 'Token ungültig oder abgelaufen.']);
        }

        $user = User::where('email', $data['email'])->firstOrFail();
        $user->update(['password' => Hash::make($data['password'])]);

        \DB::table('password_reset_tokens')
            ->where('email', $data['email'])
            ->update(['used_at' => now()]);

        $audit->log($user, 'password_reset_completed', 'user', $user->id);

        return redirect('/login')->with('status', 'Passwort zurückgesetzt.');
    }
}
