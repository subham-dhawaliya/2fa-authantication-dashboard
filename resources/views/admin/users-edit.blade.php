@extends('layouts.admin')
@section('title', 'Edit User')
@section('content')
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h1>Edit User</h1>
            <p>Update information for {{ $user->name }}</p>
        </div>
        <a href="{{ route('admin.users') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-user-edit"></i> User Information
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <div><i class="fas fa-exclamation-circle"></i> {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                    <div class="form-group">
                        <label class="form-label"><i class="fas fa-user" style="color: #f59e0b; margin-right: 5px;"></i> Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><i class="fas fa-envelope" style="color: #f59e0b; margin-right: 5px;"></i> Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><i class="fas fa-lock" style="color: #f59e0b; margin-right: 5px;"></i> New Password <small style="color: #94a3b8;">(leave blank to keep)</small></label>
                        <input type="password" name="password" class="form-control" placeholder="Enter new password">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><i class="fas fa-shield-alt" style="color: #f59e0b; margin-right: 5px;"></i> Role</label>
                        <select name="role" class="form-control" required>
                            <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>User</option>
                            <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                </div>

                <div style="background: #f8fafc; padding: 15px; border-radius: 8px; margin-top: 20px; border: 1px solid #e2e8f0;">
                    <p style="margin: 0; color: #64748b; font-size: 12px;">
                        <i class="fas fa-info-circle" style="margin-right: 5px;"></i>
                        <strong>ID:</strong> #{{ $user->id }} | 
                        <strong>Created:</strong> {{ $user->created_at->format('M d, Y H:i') }} | 
                        <strong>Updated:</strong> {{ $user->updated_at->format('M d, Y H:i') }}
                    </p>
                </div>

                <div style="margin-top: 25px; display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update User
                    </button>
                    <a href="{{ route('admin.users') }}" class="btn btn-secondary">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    @if($user->id !== auth()->id())
    <div class="card" style="border-color: #fecaca;">
        <div class="card-header" style="background: #fef2f2; color: #dc2626;">
            <i class="fas fa-exclamation-triangle"></i> Danger Zone
        </div>
        <div class="card-body">
            <p style="margin-bottom: 15px; color: #64748b; font-size: 13px;">Once you delete a user, there is no going back. Please be certain.</p>
            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" 
                  onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone!');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Delete User
                </button>
            </form>
        </div>
    </div>
    @endif
@endsection
