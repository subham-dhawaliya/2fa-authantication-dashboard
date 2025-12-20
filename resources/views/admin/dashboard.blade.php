@extends('layouts.admin')
@section('title', 'Admin Dashboard')
@section('content')
    <div class="page-header">
        <h1>Dashboard</h1>
        <p>Welcome back! Here's what's happening with your platform.</p>
    </div>
    
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['total_users'] }}</h3>
                <p>Total Users</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon amber">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['admins'] }}</h3>
                <p>Administrators</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fas fa-user"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['users'] }}</h3>
                <p>Regular Users</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon red">
                <i class="fas fa-calendar"></i>
            </div>
            <div class="stat-info">
                <h3>{{ now()->format('d M') }}</h3>
                <p>Today's Date</p>
            </div>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="card">
        <div class="card-header">
            <i class="fas fa-clock"></i> Recent Users
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recent_users as $user)
                        <tr>
                            <td style="font-weight: 500;">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge {{ $user->role === 'admin' ? 'badge-admin' : 'badge-user' }}">
                                    <i class="fas {{ $user->role === 'admin' ? 'fa-shield-alt' : 'fa-user' }}"></i>
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
