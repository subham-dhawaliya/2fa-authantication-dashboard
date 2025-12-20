<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PasswordChangeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'admins' => User::where('role', 'admin')->count(),
            'users' => User::where('role', 'user')->count(),
        ];
        $recent_users = User::latest()->take(5)->get();
        
        return view('admin.dashboard', compact('stats', 'recent_users'));
    }

    public function users()
    {
        $users = User::latest()->paginate(10);
        return view('admin.users', compact('users'));
    }

    public function create()
    {
        return view('admin.users-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,user',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'two_factor_verified' => true,
        ]);

        return redirect()->route('admin.users')->with('success', 'User created successfully!');
    }

    public function edit(User $user)
    {
        return view('admin.users-edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,user',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        // If password is being changed, send OTP to user first
        if ($request->filled('password')) {
            return $this->initiatePasswordChange($user, $request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users')->with('success', 'User updated successfully!');
    }

    /**
     * Initiate password change - send OTP to user
     */
    private function initiatePasswordChange(User $user, string $newPassword)
    {
        // Delete any existing password change requests for this user
        PasswordChangeRequest::where('user_id', $user->id)->delete();

        // Generate 6-digit OTP
        $otp = rand(100000, 999999);

        // Create password change request
        PasswordChangeRequest::create([
            'user_id' => $user->id,
            'admin_id' => auth()->id(),
            'new_password_hash' => Hash::make($newPassword),
            'otp_code' => $otp,
            'expires_at' => now()->addMinutes(15),
        ]);

        // Send OTP to user's email
        Mail::raw(
            "Admin is trying to change your password.\n\nYour OTP code is: {$otp}\n\nThis code is valid for 15 minutes.\n\nIf you did not request this, please ignore this email.",
            function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Password Change OTP Verification');
            }
        );

        return redirect()->route('admin.users.verify-otp', $user)
            ->with('success', 'OTP sent to user\'s email. Please ask user for the OTP code.');
    }

    /**
     * Show OTP verification form
     */
    public function showVerifyOtp(User $user)
    {
        $request = PasswordChangeRequest::where('user_id', $user->id)
            ->where('verified', false)
            ->first();

        if (!$request) {
            return redirect()->route('admin.users.edit', $user)
                ->with('error', 'No pending password change request found.');
        }

        if ($request->isExpired()) {
            $request->delete();
            return redirect()->route('admin.users.edit', $user)
                ->with('error', 'OTP has expired. Please try again.');
        }

        return view('admin.users-verify-otp', compact('user', 'request'));
    }

    /**
     * Verify OTP and change password
     */
    public function verifyOtp(Request $request, User $user)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $passwordRequest = PasswordChangeRequest::where('user_id', $user->id)
            ->where('verified', false)
            ->first();

        if (!$passwordRequest) {
            return redirect()->route('admin.users.edit', $user)
                ->with('error', 'No pending password change request found.');
        }

        if ($passwordRequest->isExpired()) {
            $passwordRequest->delete();
            return redirect()->route('admin.users.edit', $user)
                ->with('error', 'OTP has expired. Please try again.');
        }

        if ($passwordRequest->otp_code != $request->otp) {
            return back()->with('error', 'Invalid OTP code.');
        }

        // OTP verified - update password
        $user->update([
            'password' => $passwordRequest->new_password_hash,
        ]);

        $passwordRequest->delete();

        return redirect()->route('admin.users')
            ->with('success', 'Password changed successfully after OTP verification!');
    }

    /**
     * Resend OTP
     */
    public function resendOtp(User $user)
    {
        $passwordRequest = PasswordChangeRequest::where('user_id', $user->id)
            ->where('verified', false)
            ->first();

        if (!$passwordRequest) {
            return redirect()->route('admin.users.edit', $user)
                ->with('error', 'No pending password change request found.');
        }

        // Generate new OTP
        $otp = rand(100000, 999999);
        $passwordRequest->update([
            'otp_code' => $otp,
            'expires_at' => now()->addMinutes(15),
        ]);

        // Send new OTP
        Mail::raw(
            "Admin is trying to change your password.\n\nYour new OTP code is: {$otp}\n\nThis code is valid for 15 minutes.",
            function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Password Change OTP Verification');
            }
        );

        return back()->with('success', 'New OTP sent to user\'s email.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users')->with('error', 'You cannot delete yourself!');
        }

        $user->twoFactorCode()->delete();
        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User deleted successfully!');
    }
}
