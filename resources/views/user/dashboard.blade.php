@extends('layouts.user')
@section('title', 'Dashboard')
@section('content')
    @php
        $profileImg = auth()->user()->profile_picture ? asset('storage/' . auth()->user()->profile_picture) : (auth()->user()->avatar ?? null);
    @endphp

    <!-- Welcome Card -->
    <div class="welcome-card">
        <div class="welcome-content">
            <div class="welcome-avatar">
                @if($profileImg)
                    <img src="{{ $profileImg }}" alt="Profile">
                @else
                    <span>{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                @endif
            </div>
            <div>
                <h2>Welcome back, {{ auth()->user()->name }}!</h2>
                <p>Here's what's happening with your account today.</p>
            </div>
        </div>
    </div>

    <!-- Profile Card -->
    <div class="card">
        <div class="card-header">
            <i class="fas fa-user-circle"></i> Profile Information
        </div>
        <div class="card-body">
            <div style="display: flex; gap: 30px; align-items: flex-start; flex-wrap: wrap;">
                <!-- Profile Picture -->
                <div style="text-align: center;">
                    <div style="width: 110px; height: 110px; border-radius: 50%; overflow: hidden; border: 4px solid #e8e8e8; margin: 0 auto 15px; background: linear-gradient(135deg, #5046e5 0%, #7c3aed 100%); display: flex; align-items: center; justify-content: center;">
                        @if($profileImg)
                            <img src="{{ $profileImg }}" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <span style="font-size: 42px; color: white; font-weight: bold;">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        @endif
                    </div>
                    <a href="{{ route('user.profile') }}" class="action-btn primary" style="font-size: 12px; padding: 8px 16px;">
                        <i class="fas fa-camera"></i> Change Photo
                    </a>
                </div>
                
                <!-- Profile Info -->
                <div style="flex: 1; min-width: 280px;">
                    <div class="profile-grid">
                        <div class="profile-item">
                            <label><i class="fas fa-user"></i> Full Name</label>
                            <span>{{ auth()->user()->name }}</span>
                        </div>
                        <div class="profile-item">
                            <label><i class="fas fa-envelope"></i> Email</label>
                            <span>{{ auth()->user()->email }}</span>
                        </div>
                        <div class="profile-item">
                            <label><i class="fas fa-shield-alt"></i> Role</label>
                            <span>{{ ucfirst(auth()->user()->role) }}</span>
                        </div>
                        <div class="profile-item">
                            <label><i class="fas fa-calendar"></i> Joined</label>
                            <span>{{ auth()->user()->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header">
            <i class="fas fa-bolt"></i> Quick Actions
        </div>
        <div class="card-body">
            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                <a href="{{ route('user.profile') }}" class="action-btn primary">
                    <i class="fas fa-user-edit"></i> Edit Profile
                </a>
                <a href="{{ route('user.password') }}" class="action-btn secondary">
                    <i class="fas fa-key"></i> Change Password
                </a>
                <a href="#" class="action-btn secondary">
                    <i class="fas fa-bell"></i> Notifications
                </a>
                <a href="#" class="action-btn secondary">
                    <i class="fas fa-life-ring"></i> Get Help
                </a>
            </div>
        </div>
    </div>

    <!-- Account Stats -->
    <div class="card">
        <div class="card-header">
            <i class="fas fa-chart-bar"></i> Account Overview
        </div>
        <div class="card-body">
            <div class="stats-grid">
                <div class="stat-item blue">
                    <i class="fas fa-shield-alt"></i>
                    <div class="stat-value">2FA</div>
                    <div class="stat-label">Enabled</div>
                </div>
                <div class="stat-item green">
                    <i class="fas fa-check-circle"></i>
                    <div class="stat-value">Active</div>
                    <div class="stat-label">Status</div>
                </div>
                <div class="stat-item amber">
                    <i class="fas fa-clock"></i>
                    <div class="stat-value">{{ auth()->user()->created_at->diffInDays(now()) }}</div>
                    <div class="stat-label">Days Active</div>
                </div>
            </div>
        </div>
    </div>
@endsection
