@extends('layouts.user')
@section('title', 'Change Password')
@section('content')
    <div class="welcome-card" style="padding: 30px;">
        <h2>Change Password 🔐</h2>
        <p>Keep your account secure by updating your password regularly.</p>
    </div>

    <div class="card">
        <div class="card-header">🔒 Update Your Password</div>
        <div class="card-body">
            @if(session('success'))
                <div style="background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); color: #166534; padding: 15px 20px; border-radius: 12px; margin-bottom: 25px; font-weight: 500; display: flex; align-items: center; gap: 10px;">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #991b1b; padding: 15px 20px; border-radius: 12px; margin-bottom: 25px; font-weight: 500;">
                    @foreach($errors->all() as $error)
                        <div style="display: flex; align-items: center; gap: 10px;">❌ {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('user.password.update') }}">
                @csrf
                <div style="margin-bottom: 25px;">
                    <label style="display: block; margin-bottom: 10px; font-weight: 600; color: #374151; font-size: 14px;">🔑 Current Password</label>
                    <input type="password" name="current_password" 
                           style="width: 100%; padding: 15px 20px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 15px; transition: all 0.3s; outline: none;"
                           onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 4px rgba(99,102,241,0.1)'"
                           onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"
                           required>
                </div>
                
                <div style="margin-bottom: 25px;">
                    <label style="display: block; margin-bottom: 10px; font-weight: 600; color: #374151; font-size: 14px;">🔐 New Password</label>
                    <input type="password" name="password" 
                           style="width: 100%; padding: 15px 20px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 15px; transition: all 0.3s; outline: none;"
                           onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 4px rgba(99,102,241,0.1)'"
                           onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"
                           required>
                </div>

                <div style="margin-bottom: 25px;">
                    <label style="display: block; margin-bottom: 10px; font-weight: 600; color: #374151; font-size: 14px;">🔐 Confirm New Password</label>
                    <input type="password" name="password_confirmation" 
                           style="width: 100%; padding: 15px 20px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 15px; transition: all 0.3s; outline: none;"
                           onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 4px rgba(99,102,241,0.1)'"
                           onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"
                           required>
                </div>

                <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                    <button type="submit" class="action-btn primary">
                        🔄 Update Password
                    </button>
                    <a href="{{ route('user.dashboard') }}" class="action-btn secondary">
                        ← Back to Dashboard
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">💡 Password Tips</div>
        <div class="card-body">
            <ul style="list-style: none; padding: 0; margin: 0;">
                <li style="padding: 12px 0; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; gap: 12px;">
                    <span style="background: #dbeafe; padding: 8px; border-radius: 8px;">✓</span>
                    <span>Use at least 8 characters</span>
                </li>
                <li style="padding: 12px 0; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; gap: 12px;">
                    <span style="background: #dcfce7; padding: 8px; border-radius: 8px;">✓</span>
                    <span>Include uppercase and lowercase letters</span>
                </li>
                <li style="padding: 12px 0; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; gap: 12px;">
                    <span style="background: #fef3c7; padding: 8px; border-radius: 8px;">✓</span>
                    <span>Add numbers and special characters</span>
                </li>
                <li style="padding: 12px 0; display: flex; align-items: center; gap: 12px;">
                    <span style="background: #fce7f3; padding: 8px; border-radius: 8px;">✓</span>
                    <span>Avoid using personal information</span>
                </li>
            </ul>
        </div>
    </div>
@endsection
