<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'User Panel')</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; min-height: 100vh; }
        
        /* Header */
        .header { background: #fff; color: #1a1a2e; padding: 0 25px; display: flex; justify-content: space-between; align-items: center; position: fixed; top: 0; left: 0; right: 0; z-index: 100; height: 65px; box-shadow: 0 2px 15px rgba(0,0,0,0.08); border-bottom: 1px solid #e8e8e8; }
        .header-left { display: flex; align-items: center; gap: 20px; }
        .header .logo { font-size: 20px; font-weight: 700; color: #5046e5; display: flex; align-items: center; gap: 10px; }
        .header .logo i { font-size: 24px; }
        .header .user-menu { display: flex; align-items: center; gap: 20px; }
        .header .user-info { display: flex; align-items: center; gap: 12px; padding: 8px 15px; background: #f8f9fa; border-radius: 50px; }
        .header .user-info img { width: 35px; height: 35px; border-radius: 50%; object-fit: cover; }
        .header .user-info .avatar-placeholder { width: 35px; height: 35px; border-radius: 50%; background: linear-gradient(135deg, #5046e5, #7c3aed); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 14px; }
        .header .user-info span { font-size: 14px; font-weight: 500; color: #333; }
        .header .logout-btn { background: #fee2e2; color: #dc2626; border: none; padding: 10px 18px; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 600; transition: all 0.3s ease; display: flex; align-items: center; gap: 8px; }
        .header .logout-btn:hover { background: #dc2626; color: white; }
        
        /* Hamburger */
        .hamburger { background: #f3f4f6; border: none; cursor: pointer; padding: 12px; display: flex; flex-direction: column; gap: 5px; border-radius: 6px; transition: all 0.3s ease; }
        .hamburger:hover { background: #e5e7eb; }
        .hamburger span { display: block; width: 20px; height: 2px; background: #374151; border-radius: 2px; transition: all 0.3s; }
        
        /* Sidebar */
        .sidebar { width: 270px; background: #fff; color: #333; position: fixed; top: 65px; left: 0; bottom: 0; overflow-y: auto; box-shadow: 2px 0 20px rgba(0,0,0,0.05); transition: width 0.3s ease; z-index: 99; border-right: 1px solid #e8e8e8; }
        .sidebar.collapsed { width: 0; overflow: hidden; }
        .sidebar .user-panel { padding: 20px 15px; text-align: center; background: linear-gradient(135deg, #f8f9ff 0%, #f0f1ff 100%); border-bottom: 1px solid #e8e8e8; }
        .sidebar .user-avatar { width: 85px; height: 85px; background: linear-gradient(135deg, #5046e5 0%, #7c3aed 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; font-size: 32px; font-weight: bold; color: white; box-shadow: 0 8px 25px rgba(80, 70, 229, 0.3); border: 4px solid white; overflow: hidden; }
        .sidebar .user-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .sidebar .user-panel h4 { font-size: 16px; margin-bottom: 8px; color: #1f2937; font-weight: 600; }
        .sidebar .user-panel .status { font-size: 12px; color: #10b981; background: #d1fae5; padding: 4px 12px; border-radius: 20px; font-weight: 500; display: inline-flex; align-items: center; gap: 5px; }
        .sidebar .user-panel .status i { font-size: 10px; }
        
        .sidebar .nav-section { padding: 8px 0; }
        .sidebar .nav-title { padding: 8px 15px; font-size: 11px; text-transform: uppercase; color: #9ca3af; letter-spacing: 1px; font-weight: 600; }
        .sidebar .nav-item { display: flex; align-items: center; gap: 10px; padding: 12px 20px; color: #6b7280; text-decoration: none; transition: all 0.2s ease; margin: 3px 10px; border-radius: 6px; font-weight: 500; font-size: 14px; }
        .sidebar .nav-item i { width: 20px; text-align: center; font-size: 16px; }
        .sidebar .nav-item:hover { background: #f3f4f6; color: #5046e5; }
        .sidebar .nav-item.active { background: linear-gradient(135deg, #5046e5 0%, #7c3aed 100%); color: white; box-shadow: 0 4px 15px rgba(80, 70, 229, 0.3); }
        
        /* Main Content */
        .main-content { margin-left: 270px; margin-top: 65px; padding: 25px; min-height: calc(100vh - 65px); transition: margin-left 0.3s ease; }
        .main-content.expanded { margin-left: 0; }
        
        /* Cards */
        .card { background: white; border-radius: 8px; box-shadow: 0 2px 15px rgba(0,0,0,0.04); margin-bottom: 25px; overflow: hidden; border: 1px solid #e8e8e8; }
        .card-header { padding: 18px 22px; border-bottom: 1px solid #f0f0f0; font-weight: 600; font-size: 15px; color: #1f2937; display: flex; align-items: center; gap: 10px; }
        .card-header i { color: #5046e5; }
        .card-body { padding: 22px; }   
        
        /* Welcome Card */
        .welcome-card { background: linear-gradient(135deg, #5c50ffff 0%, #0b001fff 100%); color: white; border-radius: 10px; padding: 35px; margin-bottom: 25px; position: relative; overflow: hidden; }
        .welcome-card::before { content: ''; position: absolute; top: -50%; right: -10%; width: 300px; height: 300px; background: rgba(255,255,255,0.1); border-radius: 50%; }
        .welcome-card .welcome-content { display: flex; align-items: center; gap: 20px; position: relative; z-index: 1; }
        .welcome-card .welcome-avatar { width: 70px; height: 70px; border-radius: 50%; border: 3px solid rgba(255,255,255,0.3); overflow: hidden; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .welcome-card .welcome-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .welcome-card .welcome-avatar span { font-size: 28px; font-weight: bold; }
        .welcome-card h2 { font-size: 24px; font-weight: 600; margin-bottom: 5px; }
        .welcome-card p { opacity: 0.9; font-size: 14px; }
        
        /* Profile Grid */
        .profile-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; }
        .profile-item { padding: 18px; background: #f8f9fa; border-radius: 6px; border: 1px solid #e8e8e8; }
        .profile-item label { font-size: 11px; color: #6b7280; display: flex; align-items: center; gap: 6px; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; }
        .profile-item label i { color: #5046e5; }
        .profile-item span { font-size: 15px; color: #1f2937; font-weight: 500; }

        /* Action Buttons */
        .action-btn { display: inline-flex; align-items: center; gap: 8px; padding: 12px 20px; border-radius: 6px; text-decoration: none; font-weight: 500; font-size: 14px; transition: all 0.2s ease; border: none; cursor: pointer; }
        .action-btn.primary { background: linear-gradient(135deg, #5046e5 0%, #7c3aed 100%); color: white; }
        .action-btn.primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(80, 70, 229, 0.3); }
        .action-btn.secondary { background: #f3f4f6; color: #374151; }
        .action-btn.secondary:hover { background: #e5e7eb; }

        /* Stats Grid */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 15px; }
        .stat-item { padding: 20px; border-radius: 6px; text-align: center; }
        .stat-item i { font-size: 24px; margin-bottom: 10px; }
        .stat-item .stat-value { font-size: 22px; font-weight: 700; margin-bottom: 3px; }
        .stat-item .stat-label { font-size: 12px; font-weight: 500; }
        .stat-item.blue { background: #eff6ff; }
        .stat-item.blue i, .stat-item.blue .stat-value { color: #3b82f6; }
        .stat-item.blue .stat-label { color: #60a5fa; }
        .stat-item.green { background: #f0fdf4; }
        .stat-item.green i, .stat-item.green .stat-value { color: #22c55e; }
        .stat-item.green .stat-label { color: #4ade80; }
        .stat-item.amber { background: #fffbeb; }
        .stat-item.amber i, .stat-item.amber .stat-value { color: #f59e0b; }
        .stat-item.amber .stat-label { color: #fbbf24; }

        /* Scrollbar */
        .sidebar::-webkit-scrollbar { width: 5px; }
        .sidebar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 3px; }

        @media (max-width: 768px) {
            .sidebar { width: 0; overflow: hidden; }
            .sidebar.collapsed { width: 270px; overflow: auto; }
            .main-content { margin-left: 0; }
            .profile-grid { grid-template-columns: 1fr; }
            .header .user-info span { display: none; }
        }
    </style>
</head>
<body>
    @php
        $profileImg = auth()->user()->profile_picture ? asset('storage/' . auth()->user()->profile_picture) : (auth()->user()->avatar ?? null);
    @endphp

    <!-- Header -->
    <header class="header">
        <div class="header-left">
            <button class="hamburger" onclick="toggleSidebar()">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <div class="logo">
                <i class="fas fa-cube"></i>
                Dashboard
            </div>
        </div>
        <div class="user-menu">
            <div class="user-info">
                @if($profileImg)
                    <img src="{{ $profileImg }}" alt="Profile">
                @else
                    <div class="avatar-placeholder">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                @endif
                <span>{{ auth()->user()->name }}</span>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </header>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        
        <nav class="nav-section">
            <div class="nav-title">Main</div>
            <a href="{{ route('user.dashboard') }}" class="nav-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </nav>

        <nav class="nav-section">
            <div class="nav-title">Account</div>
            <a href="{{ route('user.profile') }}" class="nav-item {{ request()->routeIs('user.profile') ? 'active' : '' }}">
                <i class="fas fa-user"></i> My Profile
            </a>
            <a href="{{ route('user.password') }}" class="nav-item {{ request()->routeIs('user.password') ? 'active' : '' }}">
                <i class="fas fa-lock"></i> Change Password
            </a>
        </nav>

        <nav class="nav-section">
            <div class="nav-title">Communication</div>
            <a href="{{ route('chat.index') }}" class="nav-item {{ request()->routeIs('chat.*') ? 'active' : '' }}">
                <i class="fas fa-comments"></i> Chat
            </a>
        </nav>

        <nav class="nav-section">
            <div class="nav-title">More</div>
            <a href="#" class="nav-item">
                <i class="fas fa-cog"></i> Settings
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-bell"></i> Notifications
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-question-circle"></i> Help
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content" id="main-content">
        @yield('content')
    </main>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('collapsed');
            document.getElementById('main-content').classList.toggle('expanded');
        }
    </script>
</body>