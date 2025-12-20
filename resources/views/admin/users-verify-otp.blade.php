@extends('layouts.admin')
@section('title', 'Verify OTP')
@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 class="page-title" style="margin-bottom: 0;">Verify OTP for Password Change</h2>
        <a href="{{ route('admin.users.edit', $user) }}" class="btn" style="background: #6c757d; color: white;">
            ← Back to Edit
        </a>
    </div>

    <div class="card" style="max-width: 500px;">
        <div class="card-header">🔐 OTP Verification</div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div style="background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%); padding: 20px; border-radius: 10px; margin-bottom: 20px; text-align: center;">
                <p style="margin: 0 0 10px; color: #0369a1; font-size: 14px;">
                    OTP has been sent to user's email:
                </p>
                <p style="margin: 0; font-weight: 600; color: #0c4a6e; font-size: 16px;">
                    {{ $user->email }}
                </p>
            </div>

            <p style="color: #64748b; font-size: 14px; margin-bottom: 20px; text-align: center;">
                Please ask <strong>{{ $user->name }}</strong> for the 6-digit OTP code sent to their email.
            </p>

            <form method="POST" action="{{ route('admin.users.verify-otp', $user) }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Enter OTP Code</label>
                    <input type="text" name="otp" class="form-control" 
                           placeholder="Enter 6-digit OTP" 
                           maxlength="6" 
                           pattern="[0-9]{6}"
                           style="text-align: center; font-size: 24px; letter-spacing: 8px; font-weight: bold;"
                           required autofocus>
                </div>

                <p style="color: #94a3b8; font-size: 12px; text-align: center; margin-bottom: 20px;">
                    OTP expires at: {{ $request->expires_at->format('h:i A') }}
                </p>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    ✓ Verify & Change Password
                </button>
            </form>

            <div style="text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid #e2e8f0;">
                <p style="color: #64748b; font-size: 13px; margin-bottom: 10px;">Didn't receive the code?</p>
                <form method="POST" action="{{ route('admin.users.resend-otp', $user) }}" style="display: inline;">
                    @csrf
                    <button type="submit" style="background: none; border: none; color: #667eea; cursor: pointer; font-size: 14px; font-weight: 500;">
                        📧 Resend OTP
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="card" style="max-width: 500px; border-left: 4px solid #f59e0b;">
        <div class="card-body">
            <p style="margin: 0; color: #92400e; font-size: 13px;">
                <strong>⚠️ Note:</strong> The password will only be changed after the user provides the correct OTP. 
                This ensures the user is aware of the password change.
            </p>
        </div>
    </div>
@endsection
