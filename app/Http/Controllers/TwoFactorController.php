<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TwoFactorCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TwoFactorController extends Controller
{
    public function show()
    {
        return view('auth.two-factor');
    }

    public function generateCode(User $user)
    {
        // Delete old codes
        $user->twoFactorCode()->delete();

        // Generate new 6-digit code and unique token for email link
        $code = rand(100000, 999999);
        $token = Str::random(64);

        TwoFactorCode::create([
            'user_id' => $user->id,
            'code' => $code,
            'token' => $token,
            'expires_at' => now()->addMinutes(10),
        ]);

        // Send verification email with link
        $this->sendVerificationEmail($user, $code, $token);

        return $code;
    }

    private function sendVerificationEmail(User $user, $code, $token)
    {
        $verifyUrl = route('2fa.verify.link', ['token' => $token]);
        
        $emailContent = "
Hello {$user->name},

Someone is trying to login to your account. If this was you, click the button below to continue.

Continue as {$user->email}:
{$verifyUrl}

Or enter this code manually: {$code}

This link is valid for 10 minutes.

If you did not attempt to login, please ignore this email and consider changing your password.
        ";

        Mail::raw($emailContent, function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Confirm Your Login');
        });
    }


    /**
     * Verify via email link
     */
    public function verifyViaLink(Request $request, $token)
    {
        $twoFactorCode = TwoFactorCode::where('token', $token)->first();

        if (!$twoFactorCode) {
            return redirect()->route('login')
                ->with('error', 'Invalid or expired verification link.');
        }

        if ($twoFactorCode->isExpired()) {
            $twoFactorCode->delete();
            return redirect()->route('login')
                ->with('error', 'Verification link has expired. Please login again.');
        }

        $user = $twoFactorCode->user;

        // Mark 2FA as verified
        $user->update(['two_factor_verified' => true]);
        $twoFactorCode->delete();

        // Login the user if not already logged in
        if (!Auth::check()) {
            Auth::login($user);
        }

        // Redirect based on role
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard')
                ->with('success', 'Login verified successfully!');
        }

        return redirect()->route('user.dashboard')
            ->with('success', 'Login verified successfully!');
    }

    /**
     * Verify via OTP code (manual entry)
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|numeric|digits:6',
        ]);

        $user = Auth::user();
        $twoFactorCode = $user->twoFactorCode;

        if (!$twoFactorCode) {
            return back()->withErrors(['code' => 'No verification code found. Please login again.']);
        }

        if ($twoFactorCode->isExpired()) {
            $twoFactorCode->delete();
            return back()->withErrors(['code' => 'Code expired. Please request a new one.']);
        }

        if ($twoFactorCode->code != $request->code) {
            return back()->withErrors(['code' => 'Invalid verification code.']);
        }

        // Mark 2FA as verified
        $user->update(['two_factor_verified' => true]);
        $twoFactorCode->delete();

        // Redirect based on role
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('user.dashboard');
    }

    public function resend()
    {
        $this->generateCode(Auth::user());
        return back()->with('success', 'New verification email sent.');
    }
}
