<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetCare Admin — @yield('title', 'Dashboard')</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #7C6FF7;
            --primary-light: #EEF0FF;
            --primary-dark: #5A52D5;
            --accent: #F7A87C;
            --accent-light: #FFF4EE;
            --success: #4CAF8C;
            --success-light: #E8F7F2;
            --danger: #F76F6F;
            --danger-light: #FFF0F0;
            --warning: #F7C76F;
            --warning-light: #FFFAEE;
            --bg: #F5F6FA;
            --sidebar-bg: #ffffff;
            --card-bg: #ffffff;
            --text-dark: #2D3250;
            --text-muted: #8A95A5;
            --border: #EAECF0;
            --sidebar-width: 260px;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg);
            color: var(--text-dark);
            min-height: 100vh;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            z-index: 100;
            transition: all 0.3s ease;
            overflow-y: auto;
        }
        .sidebar-brand {
            padding: 24px 20px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--border);
        }
        .brand-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            flex-shrink: 0;
        }
        .brand-text { font-size: 18px; font-weight: 700; color: var(--text-dark); }
        .brand-sub  { font-size: 11px; color: var(--text-muted); font-weight: 400; }

        .sidebar-nav { padding: 16px 12px; flex: 1; }
        .nav-section-label {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
            padding: 12px 10px 6px;
        }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 2px;
            transition: all 0.2s ease;
        }
        .nav-link i { font-size: 17px; width: 22px; text-align: center; }
        .nav-link:hover { background: var(--primary-light); color: var(--primary); }
        .nav-link.active { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: white; box-shadow: 0 4px 12px rgba(124,111,247,0.35); }
        .nav-link.active i { color: white; }

        .sidebar-footer {
            padding: 16px 12px;
            border-top: 1px solid var(--border);
        }
        .user-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            background: var(--bg);
            border-radius: 12px;
            margin-bottom: 10px;
        }
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--accent), #F7876F);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 14px;
            flex-shrink: 0;
        }
        .user-info-name { font-size: 13px; font-weight: 600; color: var(--text-dark); }
        .user-info-role { font-size: 11px; color: var(--text-muted); }
        .btn-logout {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 9px;
            background: var(--danger-light);
            color: var(--danger);
            border: none;
            border-radius: 10px;
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-logout:hover { background: var(--danger); color: white; }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            padding: 28px;
        }

        /* ===== TOPBAR ===== */
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }
        .page-title { font-size: 24px; font-weight: 700; color: var(--text-dark); }
        .page-subtitle { font-size: 13px; color: var(--text-muted); margin-top: 2px; }

        /* ===== CARDS ===== */
        .card {
            background: var(--card-bg);
            border-radius: 16px;
            border: 1px solid var(--border);
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            overflow: hidden;
        }
        .card-header {
            padding: 18px 22px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: white;
        }
        .card-title { font-size: 15px; font-weight: 600; color: var(--text-dark); }
        .card-body { padding: 22px; }

        /* ===== STAT CARDS ===== */
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 22px;
            border: 1px solid var(--border);
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }
        .stat-value { font-size: 24px; font-weight: 700; color: var(--text-dark); line-height: 1; }
        .stat-label { font-size: 13px; color: var(--text-muted); margin-top: 4px; }

        /* ===== TABLE ===== */
        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            background: var(--bg);
            padding: 12px 16px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            text-align: left;
            border-bottom: 1px solid var(--border);
        }
        tbody td {
            padding: 14px 16px;
            font-size: 14px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover { background: #FAFBFF; }

        /* ===== BADGES ===== */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-success  { background: var(--success-light); color: var(--success); }
        .badge-warning  { background: var(--warning-light); color: #C8860A; }
        .badge-danger   { background: var(--danger-light); color: var(--danger); }
        .badge-primary  { background: var(--primary-light); color: var(--primary); }
        .badge-muted    { background: var(--bg); color: var(--text-muted); }

        /* ===== BUTTONS ===== */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 18px;
            border-radius: 10px;
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .btn-primary { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: white; box-shadow: 0 4px 12px rgba(124,111,247,0.3); }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(124,111,247,0.4); color: white; }
        .btn-success { background: var(--success); color: white; }
        .btn-success:hover { background: #3D9E7A; color: white; }
        .btn-danger  { background: var(--danger-light); color: var(--danger); }
        .btn-danger:hover  { background: var(--danger); color: white; }
        .btn-warning { background: var(--warning-light); color: #C8860A; }
        .btn-warning:hover { background: var(--warning); color: white; }
        .btn-outline { background: white; color: var(--text-dark); border: 1px solid var(--border); }
        .btn-outline:hover { background: var(--bg); color: var(--text-dark); }
        .btn-sm { padding: 6px 12px; font-size: 12px; border-radius: 8px; }
        .btn-whatsapp { background: #25D366; color: white; }
        .btn-whatsapp:hover { background: #20b858; color: white; }

        /* ===== FORMS ===== */
        .form-label { font-size: 13px; font-weight: 600; color: var(--text-dark); margin-bottom: 6px; display: block; }
        .form-control, .form-select {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            color: var(--text-dark);
            background: white;
            transition: border-color 0.2s;
            outline: none;
        }
        .form-control:focus, .form-select:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(124,111,247,0.12); }
        .form-check-input { accent-color: var(--primary); }

        /* ===== MODAL ===== */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(45,50,80,0.5);
            backdrop-filter: blur(4px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        .modal-overlay.active { display: flex; }
        .modal-box {
            background: white;
            border-radius: 20px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            max-height: 90vh;
            overflow-y: auto;
            animation: slideUp 0.3s ease;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .modal-title { font-size: 16px; font-weight: 700; }
        .modal-close {
            width: 32px; height: 32px;
            border-radius: 8px;
            border: none;
            background: var(--bg);
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; color: var(--text-muted);
        }
        .modal-close:hover { background: var(--danger-light); color: var(--danger); }
        .modal-body { padding: 24px; }
        .modal-footer { padding: 16px 24px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 10px; }

        /* ===== ALERTS ===== */
        .alert {
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-success { background: var(--success-light); color: var(--success); border: 1px solid #c3e6d8; }
        .alert-danger  { background: var(--danger-light); color: var(--danger); border: 1px solid #fdc0c0; }

        /* ===== GRID ===== */
        .grid { display: grid; gap: 20px; }
        .grid-2 { grid-template-columns: repeat(2, 1fr); }
        .grid-3 { grid-template-columns: repeat(3, 1fr); }
        .grid-4 { grid-template-columns: repeat(4, 1fr); }

        /* ===== AVATAR ===== */
        .pet-avatar {
            width: 38px; height: 38px;
            border-radius: 10px;
            background: var(--primary-light);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: var(--text-muted);
        }
        .empty-state i { font-size: 48px; opacity: 0.3; display: block; margin-bottom: 12px; }
        .empty-state p { font-size: 14px; }

        /* ===== SCROLLBAR ===== */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--text-muted); }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main-content { margin-left: 0; padding: 16px; }
            .grid-4, .grid-3 { grid-template-columns: repeat(2, 1fr); }
            .grid-2 { grid-template-columns: 1fr; }
        }
    </style>
    @stack('styles')
</head>
<body>
@auth
<div class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="bi bi-heart-fill"></i></div>
        <div>
            <div class="brand-text">PetCare</div>
            <div class="brand-sub">Admin Panel</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        @if(auth()->user()->isOwner())
            <div class="nav-section-label">Utama</div>
            <a href="{{ route('owner.dashboard') }}" class="nav-link {{ request()->routeIs('owner.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i> Dashboard
            </a>

            <div class="nav-section-label">Manajemen</div>
            <a href="{{ route('owner.admins') }}" class="nav-link {{ request()->routeIs('owner.admins*') ? 'active' : '' }}">
                <i class="bi bi-person-badge-fill"></i> Data Admin
            </a>
            <a href="{{ route('owner.customers') }}" class="nav-link {{ request()->routeIs('owner.customers*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Data Customer
            </a>
            <a href="{{ route('owner.services') }}" class="nav-link {{ request()->routeIs('owner.services*') ? 'active' : '' }}">
                <i class="bi bi-stars"></i> Layanan & Harga
            </a>
            <a href="{{ route('owner.transactions') }}" class="nav-link {{ request()->routeIs('owner.transactions*') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i> Transaksi
            </a>

            <div class="nav-section-label">Export</div>
            <a href="{{ route('owner.export.customers') }}" target="_blank" class="nav-link">
                <i class="bi bi-file-earmark-pdf"></i> Export Customer
            </a>
            <a href="{{ route('owner.export.transactions') }}" target="_blank" class="nav-link">
                <i class="bi bi-file-earmark-pdf"></i> Export Transaksi
            </a>
        @endif

        @if(auth()->user()->isAdmin())
            <div class="nav-section-label">Utama</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i> Dashboard
            </a>

            <div class="nav-section-label">Operasional</div>
            <a href="{{ route('admin.customers') }}" class="nav-link {{ request()->routeIs('admin.customers*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Data Customer
            </a>
            <a href="{{ route('admin.transactions') }}" class="nav-link {{ request()->routeIs('admin.transactions*') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i> Transaksi
            </a>
        @endif
    </nav>

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
            <div>
                <div class="user-info-name">{{ auth()->user()->name }}</div>
                <div class="user-info-role">{{ ucfirst(auth()->user()->role) }}</div>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-logout"><i class="bi bi-box-arrow-right"></i> Keluar</button>
        </form>
    </div>
</div>

<div class="main-content">
    <div class="topbar">
        <div>
            <div class="page-title">@yield('title', 'Dashboard')</div>
            <div class="page-subtitle">@yield('subtitle', 'Selamat datang di panel admin PetCare')</div>
        </div>
        <div>@yield('topbar-action')</div>
    </div>

    @if(session('success'))
        <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger"><i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-circle-fill"></i>
            <div>@foreach($errors->all() as $e) <div>{{ $e }}</div> @endforeach</div>
        </div>
    @endif

    @yield('content')
</div>
@else
@yield('content')
@endauth

<script>
function openModal(id) { document.getElementById(id).classList.add('active'); }
function closeModal(id) { document.getElementById(id).classList.remove('active'); }

document.querySelectorAll('.modal-overlay').forEach(m => {
    m.addEventListener('click', function(e) { if (e.target === this) this.classList.remove('active'); });
});
</script>
@stack('scripts')
</body>
</html>
