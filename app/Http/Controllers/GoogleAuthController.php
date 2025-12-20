<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirect()
    {
        // Debug: Show what redirect URI is being used
        $redirectUrl = config('services.google.redirect');
        
        // Uncomment below line to see the redirect URL
        // dd('Redirect URL: ' . $redirectUrl);
        
        return Socialite::driver('google')
            ->redirectUrl($redirectUrl)
            ->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Check if user exists
            $user = User::where('email', $googleUser->getEmail())->first();
            
            if ($user) {
                // Existing user - update google_id and avatar if not set
                $user->update([
                    'two_factor_verified' => true,
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]);
                Auth::login($user);
                
                if ($user->isAdmin()) {
                    return redirect()->route('admin.dashboard')
                        ->with('success', 'Welcome back, ' . $user->name . '!');
                }
                return redirect()->route('user.dashboard')
                    ->with('success', 'Welcome back, ' . $user->name . '!');
            }
            
            // New user - create account
            $role = User::count() === 0 ? 'admin' : 'user';
            
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'password' => Hash::make(Str::random(24)),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'role' => $role,
                'two_factor_verified' => true,
            ]);
            
            Auth::login($user);
            
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard')
                    ->with('success', 'Account created successfully!');
            }
            return redirect()->route('user.dashboard')
                ->with('success', 'Account created successfully!');
                
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Google login failed. Please try again.');
        }
    }
}
