@extends('layouts.app')
@section('title', 'Verify Your Login')
@section('content')
<div class="container">
    <div class="card">
        <div style="text-align: center; margin-bottom: 25px;">
            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #EA4335 0%, #FBBC05 25%, #34A853 50%, #4285F4 75%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 36px; color: white;">
                ✉
            </div>
            <h2 style="margin-bottom: 10px; font-size: 22px;">Connect to Gmail</h2>
            <p style="color: #666; font-size: 15px; line-height: 1.6;">
                We've sent a verification link to<br>
                <strong style="color: #333; font-size: 16px;">{{ auth()->user()->email }}</strong>
            </p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <div style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 20px; border-radius: 12px; margin-bottom: 20px;">
            <p style="margin: 0 0 15px; color: #495057; font-size: 14px; text-align: center;">
                Click the button below to open Gmail and verify your login
            </p>
            <a href="https://mail.google.com" target="_blank" 
               style="display: flex; align-items: center; justify-content: center; gap: 12px; background: white; border: 2px solid #dadce0; padding: 14px 24px; border-radius: 8px; text-decoration: none; color: #3c4043; font-weight: 500; font-size: 15px; transition: all 0.3s ease; box-shadow: 0 1px 3px rgba(0,0,0,0.08);"
               onmouseover="this.style.background='#f8f9fa'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.12)';"
               onmouseout="this.style.background='white'; this.style.boxShadow='0 1px 3px rgba(0,0,0,0.08)';">
                <svg width="24" height="24" viewBox="0 0 24 24">
                    <path fill="#EA4335" d="M24 5.457v13.909c0 .904-.732 1.636-1.636 1.636h-3.819V11.73L12 16.64l-6.545-4.91v9.273H1.636A1.636 1.636 0 0 1 0 19.366V5.457c0-2.023 2.309-3.178 3.927-1.964L5.455 4.64 12 9.548l6.545-4.91 1.528-1.145C21.69 2.28 24 3.434 24 5.457z"/>
                </svg>
                Open Gmail
            </a>
        </div>

        <div style="text-align: center; margin-bottom: 20px;">
            <p style="color: #888; font-size: 13px; margin-bottom: 8px;">Using a different email provider?</p>
            <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                <a href="https://outlook.live.com" target="_blank" style="padding: 8px 16px; background: #0078d4; color: white; border-radius: 6px; text-decoration: none; font-size: 13px;">Outlook</a>
                <a href="https://mail.yahoo.com" target="_blank" style="padding: 8px 16px; background: #6001d2; color: white; border-radius: 6px; text-decoration: none; font-size: 13px;">Yahoo</a>
            </div>
        </div>

        <div style="border-top: 1px solid #eee; padding-top: 20px;">
            <p style="text-align: center; color: #888; font-size: 13px; margin-bottom: 15px;">
                Or enter the 6-digit code from the email
            </p>
            <form method="POST" action="{{ route('2fa.verify') }}">
                @csrf
                <div class="form-group">
                    <input type="text" name="code" maxlength="6" placeholder="000000" required 
                           style="text-align: center; font-size: 24px; letter-spacing: 10px; font-weight: bold;">
                </div>
                <button type="submit" class="btn">Verify Code</button>
            </form>
        </div>
        
        <div style="display: flex; gap: 10px; margin-top: 15px;">
            <form method="POST" action="{{ route('2fa.resend') }}" style="flex: 1;">
                @csrf
                <button type="submit" class="btn btn-secondary" style="width: 100%;">Resend Email</button>
            </form>

            <form method="POST" action="{{ route('logout') }}" style="flex: 1;">
                @csrf
                <button type="submit" class="btn btn-secondary" style="width: 100%;">Cancel</button>
            </form>
        </div>

        <p style="text-align: center; color: #999; font-size: 12px; margin-top: 20px;">
            ⏱ Link expires in 10 minutes
        </p>
    </div>
</div>
@endsection
