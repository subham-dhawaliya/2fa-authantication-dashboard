@extends('layouts.admin')
@section('title', 'Create User')
@section('content')
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h1>Create User</h1>
            <p>Add a new user to the system</p>
        </div>
        <a href="{{ route('admin.users') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-user-plus"></i> User Information
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <div><i class="fas fa-exclamation-circle"></i> {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                    <div class="form-group">
                        <label class="form-label"><i class="fas fa-user" style="color: #f59e0b; margin-right: 5px;"></i> Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="Enter full name" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><i class="fas fa-envelope" style="color: #f59e0b; margin-right: 5px;"></i> Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="Enter email address" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><i class="fas fa-lock" style="color: #f59e0b; margin-right: 5px;"></i> Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><i class="fas fa-shield-alt" style="color: #f59e0b; margin-right: 5px;"></i> Role</label>
                        <select name="role" class="form-control" required>
                            <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                </div>

                <div style="margin-top: 25px; display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check"></i> Create User
                    </button>
                    <a href="{{ route('admin.users') }}" class="btn btn-secondary">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
