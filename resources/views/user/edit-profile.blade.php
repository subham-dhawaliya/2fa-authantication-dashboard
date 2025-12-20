@extends('layouts.user')
@section('title', 'Edit Profile')
@section('content')
    @php
        $profileImg = auth()->user()->profile_picture ? asset('storage/' . auth()->user()->profile_picture) : (auth()->user()->avatar ?? null);
    @endphp

    <!-- Header -->
    <div class="welcome-card" style="padding: 25px 35px;">
        <h2 style="font-size: 20px; margin-bottom: 5px;"><i class="fas fa-user-edit" style="margin-right: 10px;"></i>Edit Profile</h2>
        <p style="font-size: 14px;">Update your personal information and profile picture.</p>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-id-card"></i> Profile Details
        </div>
        <div class="card-body">
            @if(session('success'))
                <div style="background: #f0fdf4; color: #166534; padding: 15px 20px; border-radius: 10px; margin-bottom: 25px; font-weight: 500; display: flex; align-items: center; gap: 10px; border: 1px solid #bbf7d0;">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div style="background: #fef2f2; color: #991b1b; padding: 15px 20px; border-radius: 10px; margin-bottom: 25px; font-weight: 500; border: 1px solid #fecaca;">
                    @foreach($errors->all() as $error)
                        <div style="display: flex; align-items: center; gap: 10px;"><i class="fas fa-exclamation-circle"></i> {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('user.profile.update') }}" enctype="multipart/form-data">
                @csrf
                
                <!-- Profile Picture Section -->
                <div style="margin-bottom: 30px; text-align: center; padding: 25px; background: #f8f9fa; border-radius: 12px;">
                    <label style="display: block; margin-bottom: 15px; font-weight: 600; color: #374151; font-size: 14px;">
                        <i class="fas fa-camera" style="margin-right: 5px; color: #5046e5;"></i> Profile Picture
                    </label>
                    
                    <div style="position: relative; display: inline-block;">
                        <div id="preview-container" style="width: 130px; height: 130px; border-radius: 50%; overflow: hidden; margin: 0 auto; border: 4px solid #e5e7eb; background: linear-gradient(135deg, #5046e5 0%, #7c3aed 100%); display: flex; align-items: center; justify-content: center;">
                            @if($profileImg)
                                <img id="preview-image" src="{{ $profileImg }}" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <span id="preview-initial" style="font-size: 50px; color: white; font-weight: bold;">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                                <img id="preview-image" src="" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                            @endif
                        </div>
                        
                        <label for="profile_picture" style="position: absolute; bottom: 5px; right: 5px; width: 38px; height: 38px; background: linear-gradient(135deg, #5046e5 0%, #7c3aed 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 4px 12px rgba(80,70,229,0.4); transition: all 0.3s; color: white;">
                            <i class="fas fa-camera"></i>
                        </label>
                        <input type="file" id="profile_picture" name="profile_picture" accept="image/*" style="display: none;" onchange="previewImage(this)">
                    </div>
                    
                    <p style="margin-top: 15px; font-size: 12px; color: #6b7280;">Click the camera icon to upload (Max: 2MB)</p>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 14px;">
                        <i class="fas fa-user" style="margin-right: 5px; color: #5046e5;"></i> Full Name
                    </label>
                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" 
                           style="width: 100%; padding: 14px 18px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 15px; transition: all 0.3s; outline: none;" 
                           onfocus="this.style.borderColor='#5046e5'; this.style.boxShadow='0 0 0 3px rgba(80,70,229,0.1)'"
                           onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"
                           required>
                </div>
                
                <div style="margin-bottom: 25px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 14px;">
                        <i class="fas fa-envelope" style="margin-right: 5px; color: #5046e5;"></i> Email Address
                    </label>
                    <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" 
                           style="width: 100%; padding: 14px 18px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 15px; transition: all 0.3s; outline: none;"
                           onfocus="this.style.borderColor='#5046e5'; this.style.boxShadow='0 0 0 3px rgba(80,70,229,0.1)'"
                           onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"
                           required>
                </div>

                <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                    <button type="submit" class="action-btn primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <a href="{{ route('user.dashboard') }}" class="action-btn secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewImage = document.getElementById('preview-image');
                    const previewInitial = document.getElementById('preview-initial');
                    
                    previewImage.src = e.target.result;
                    previewImage.style.display = 'block';
                    if (previewInitial) {
                        previewInitial.style.display = 'none';
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
