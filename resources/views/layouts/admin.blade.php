<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; min-height: 100vh; }
        
        /* Header */
        .header { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; padding: 0 25px; display: flex; justify-content: space-between; align-items: center; position: fixed; top: 0; left: 0; right: 0; z-index: 100; height: 60px; }
        .header-left { display: flex; align-items: center; gap: 20px; }
        .header .logo { font-size: 18px; font-weight: 700; display: flex; align-items: center; gap: 10px; }
        .header .logo i { color: #f59e0b; }
        .header .user-menu { display: flex; align-items: center; gap: 15px; }
        .header .user-info { display: flex; align-items: center; gap: 10px; padding: 6px 12px; background: rgba(255,255,255,0.1); border-radius: 6px; }
        .header .user-info .avatar { width: 32px; height: 32px; border-radius: 6px; background: linear-gradient(135deg, #f59e0b, #d97706); display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 13px; }
        .header .user-info span { font-size: 13px; font-weight: 500; }
        .header .logout-btn { background: rgba(239,68,68,0.2); color: #fca5a5; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: 600; transition: all 0.3s ease; display: flex; align-items: center; gap: 6px; }
        .header .logout-btn:hover { background: #ef4444; color: white; }
        
        /* Hamburger */
        .hamburger { background: rgba(255,255,255,0.1); border: none; cursor: pointer; padding: 10px; display: flex; flex-direction: column; gap: 4px; border-radius: 6px; transition: all 0.3s ease; }
        .hamburger:hover { background: rgba(255,255,255,0.2); }
        .hamburger span { display: block; width: 18px; height: 2px; background: white; border-radius: 2px; }
        
        /* Sidebar */
        .sidebar { width: 250px; background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%); color: white; position: fixed; top: 60px; left: 0; bottom: 0; overflow-y: auto; transition: width 0.3s ease; z-index: 99; }
        .sidebar.collapsed { width: 0; overflow: hidden; }
        .sidebar::-webkit-scrollbar { width: 5px; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 3px; }
        
        .sidebar .user-panel { padding: 25px 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar .user-avatar { width: 70px; height: 70px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; font-size: 26px; font-weight: bold; box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3); }
        .sidebar .user-panel h4 { font-size: 15px; margin-bottom: 5px; font-weight: 600; }
        .sidebar .user-panel .role { font-size: 11px; color: #f59e0b; background: rgba(245,158,11,0.15); padding: 4px 12px; border-radius: 20px; font-weight: 500; }
        
        .sidebar .nav-section { padding: 10px 0; }
        .sidebar .nav-title { padding: 5px 15px; font-size: 10px; text-transform: uppercase; color: #64748b; letter-spacing: 1px; font-weight: 600; }
        .sidebar .nav-item { display: flex; align-items: center; gap: 12px; padding: 11px 20px; color: #94a3b8; text-decoration: none; transition: all 0.2s ease; margin: 2px 8px; border-radius: 6px; font-size: 13px; font-weight: 500; }
        .sidebar .nav-item i { width: 18px; text-align: center; font-size: 14px; }
        .sidebar .nav-item:hover { background: rgba(255,255,255,0.08); color: white; }
        .sidebar .nav-item.active { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3); }
        
        /* Main Content */
        .main-content { margin-left: 250px; margin-top: 60px; padding: 25px; min-height: calc(100vh - 60px); transition: margin-left 0.3s ease; }
        .main-content.expanded { margin-left: 0; }
        
        /* Page Header */
        .page-header { margin-bottom: 25px; }
        .page-header h1 { font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 5px; }
        .page-header p { color: #64748b; font-size: 14px; }
        
        /* Cards */
        .card { background: white; border-radius: 6px; box-shadow: 0 1px 10px rgba(0,0,0,0.04); margin-bottom: 25px; border: 1px solid #e2e8f0; }
        .card-header { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; font-weight: 600; font-size: 14px; color: #1e293b; display: flex; align-items: center; gap: 10px; }
        .card-header i { color: #f59e0b; }
        .card-body { padding: 20px; }
        
        /* Stats Grid */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 25px; }
        .stat-card { background: white; border-radius: 6px; padding: 20px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 15px; }
        .stat-card .stat-icon { width: 50px; height: 50px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
        .stat-card .stat-icon.blue { background: #eff6ff; color: #3b82f6; }
        .stat-card .stat-icon.amber { background: #fffbeb; color: #f59e0b; }
        .stat-card .stat-icon.green { background: #f0fdf4; color: #22c55e; }
        .stat-card .stat-icon.red { background: #fef2f2; color: #ef4444; }
        .stat-card .stat-info h3 { font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 2px; }
        .stat-card .stat-info p { font-size: 13px; color: #64748b; }
        
        /* Table */
        .table-responsive { overflow-x: auto; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #f1f5f9; font-size: 13px; }
        .table th { background: #f8fafc; font-weight: 600; color: #475569; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; }
        .table tbody tr:hover { background: #f8fafc; }
        
        /* Badge */
        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; }
        .badge-admin { background: #fef2f2; color: #dc2626; }
        .badge-user { background: #f0fdf4; color: #16a34a; }
        
        /* Buttons */
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.2s ease; border: none; text-decoration: none; }
        .btn-primary { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; }
        .btn-primary:hover { box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3); transform: translateY(-1px); }
        .btn-secondary { background: #f1f5f9; color: #475569; }
        .btn-secondary:hover { background: #e2e8f0; }
        .btn-danger { background: #fef2f2; color: #dc2626; }
        .btn-danger:hover { background: #dc2626; color: white; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        
        /* Action Buttons */
        .action-btns { display: flex; gap: 6px; }
        .action-btn { width: 30px; height: 30px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; border: none; cursor: pointer; transition: all 0.2s ease; font-size: 12px; }
        .action-btn.edit { background: #eff6ff; color: #3b82f6; }
        .action-btn.edit:hover { background: #3b82f6; color: white; }
        .action-btn.delete { background: #fef2f2; color: #dc2626; }
        .action-btn.delete:hover { background: #dc2626; color: white; }
        
        /* Form */
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; margin-bottom: 6px; font-weight: 600; color: #374151; font-size: 13px; }
        .form-control { width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px; transition: all 0.2s ease; }
        .form-control:focus { outline: none; border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1); }
        
        /* Alerts */
        .alert { padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; font-size: 13px; display: flex; align-items: center; gap: 10px; }
        .alert-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .alert-danger { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

        @media (max-width: 768px) {
            .sidebar { width: 0; overflow: hidden; }
            .sidebar.open { width: 250px; }
            .main-content { margin-left: 0; padding: 15px; }
            .stats-grid { grid-template-columns: 1fr; }
            .header .user-info span { display: none; }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-left">
            <button class="hamburger" onclick="toggleSidebar()">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <div class="logo">
                <i class="fas fa-shield-halved"></i>
                Admin Panel
            </div>
        </div>
        <div class="user-menu">
            <div class="user-info">
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
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
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </nav>

        <nav class="nav-section">
            <div class="nav-title">Management</div>
            <a href="{{ route('admin.users') }}" class="nav-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Users
            </a>
        </nav>

        <nav class="nav-section">
            <div class="nav-title">Communication</div>
            <a href="{{ route('chat.index') }}" class="nav-item {{ request()->routeIs('chat.*') ? 'active' : '' }}">
                <i class="fas fa-comments"></i> Chat
            </a>
        </nav>

        <nav class="nav-section">
            <div class="nav-title">Settings</div>
            <a href="#" class="nav-item">
                <i class="fas fa-cog"></i> General
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-shield-alt"></i> Security
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content" id="main-content">
        @yield('content')
    </main>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('open');
            } else {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            }
        }
    </script>
</body>
</html>
