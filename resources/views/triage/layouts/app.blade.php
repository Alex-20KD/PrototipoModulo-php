<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Triaje Médico y Agendamiento de Citas - MSP Ecuador">
    <title>@yield('title', 'Sistema de Triaje Médico')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #0f766e;
            --primary-light: #14b8a6;
            --primary-dark: #0d5c56;
            --accent: #f59e0b;
            --bg-gradient-start: #0f172a;
            --bg-gradient-end: #1e293b;
            --card-bg: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.08);
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
        }

        * { font-family: 'Inter', sans-serif; }

        body {
            background: linear-gradient(135deg, var(--bg-gradient-start) 0%, var(--bg-gradient-end) 100%);
            min-height: 100vh;
            color: var(--text-primary);
        }

        .navbar {
            background: rgba(15, 23, 42, 0.85) !important;
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--glass-border);
            padding: 0.75rem 0;
        }

        .navbar-brand {
            font-weight: 700; font-size: 1.25rem;
            color: var(--primary-light) !important;
            display: flex; align-items: center; gap: 0.5rem;
        }
        .navbar-brand i { font-size: 1.5rem; }

        .nav-link {
            color: var(--text-secondary) !important; font-weight: 500;
            padding: 0.5rem 1rem !important; border-radius: 8px;
            transition: all 0.3s ease; margin: 0 0.15rem;
        }
        .nav-link:hover, .nav-link.active {
            color: var(--text-primary) !important;
            background: rgba(20, 184, 166, 0.15);
        }
        .nav-link.active {
            background: rgba(20, 184, 166, 0.2);
            color: var(--primary-light) !important;
        }
        .nav-link i { margin-right: 0.35rem; }

        .glass-card {
            background: var(--card-bg); backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border); border-radius: 16px;
            padding: 2rem; transition: all 0.3s ease;
        }
        .glass-card:hover {
            border-color: rgba(20, 184, 166, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .section-title {
            font-size: 1.75rem; font-weight: 700; margin-bottom: 0.25rem;
            background: linear-gradient(135deg, var(--primary-light), var(--accent));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .section-subtitle { color: var(--text-secondary); font-size: 0.95rem; margin-bottom: 1.5rem; }

        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.05); border: 1px solid var(--glass-border);
            color: var(--text-primary); border-radius: 10px;
            padding: 0.65rem 1rem; transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            background: rgba(255, 255, 255, 0.08); border-color: var(--primary-light);
            color: var(--text-primary); box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.15);
        }
        .form-control::placeholder { color: var(--text-secondary); }
        .form-label { color: var(--text-secondary); font-weight: 500; font-size: 0.875rem; margin-bottom: 0.35rem; }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border: none; color: white; font-weight: 600;
            padding: 0.65rem 1.5rem; border-radius: 10px;
            transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(15, 118, 110, 0.3);
        }
        .btn-primary-custom:hover {
            transform: translateY(-2px); box-shadow: 0 6px 20px rgba(15, 118, 110, 0.5); color: white;
        }

        .btn-accent {
            background: linear-gradient(135deg, var(--accent), #d97706);
            border: none; color: #1e293b; font-weight: 600;
            padding: 0.65rem 1.5rem; border-radius: 10px;
            transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
        }
        .btn-accent:hover {
            transform: translateY(-2px); box-shadow: 0 6px 20px rgba(245, 158, 11, 0.5); color: #1e293b;
        }

        .btn-outline-glass {
            background: transparent; border: 1px solid var(--glass-border);
            color: var(--text-secondary); font-weight: 500;
            padding: 0.5rem 1rem; border-radius: 10px; transition: all 0.3s ease;
        }
        .btn-outline-glass:hover {
            border-color: var(--primary-light); color: var(--primary-light);
            background: rgba(20, 184, 166, 0.1);
        }

        .alert-success-custom {
            background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3);
            color: #6ee7b7; border-radius: 12px; padding: 1rem 1.25rem;
        }
        .alert-warning-custom {
            background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3);
            color: #fcd34d; border-radius: 12px; padding: 1rem 1.25rem;
            animation: pulse-border 2s infinite;
        }
        @keyframes pulse-border {
            0%, 100% { border-color: rgba(245, 158, 11, 0.3); }
            50% { border-color: rgba(245, 158, 11, 0.7); }
        }
        .alert-danger-custom {
            background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5; border-radius: 12px; padding: 1rem 1.25rem;
        }

        .patient-card {
            background: rgba(20, 184, 166, 0.08); border: 1px solid rgba(20, 184, 166, 0.2);
            border-radius: 12px; padding: 1.25rem; margin-bottom: 1.5rem;
        }
        .patient-card .patient-name { font-size: 1.1rem; font-weight: 600; color: var(--primary-light); }
        .patient-card .patient-detail { color: var(--text-secondary); font-size: 0.875rem; }

        .table-glass { color: var(--text-primary); }
        .table-glass thead th {
            background: rgba(255, 255, 255, 0.05); border-bottom: 1px solid var(--glass-border);
            color: var(--text-secondary); font-weight: 600; font-size: 0.8rem;
            text-transform: uppercase; letter-spacing: 0.05em; padding: 0.85rem 1rem;
        }
        .table-glass tbody td {
            border-bottom: 1px solid rgba(255, 255, 255, 0.03);
            padding: 0.85rem 1rem; vertical-align: middle;
        }
        .table-glass tbody tr:hover { background: rgba(255, 255, 255, 0.03); }

        .badge-status { padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; letter-spacing: 0.03em; }
        .badge-pending { background: rgba(245, 158, 11, 0.2); color: #fcd34d; border: 1px solid rgba(245, 158, 11, 0.3); }
        .badge-assigned { background: rgba(16, 185, 129, 0.2); color: #6ee7b7; border: 1px solid rgba(16, 185, 129, 0.3); }

        .time-slot-btn {
            background: rgba(255, 255, 255, 0.05); border: 1px solid var(--glass-border);
            color: var(--text-primary); padding: 0.6rem 1.25rem; border-radius: 10px;
            font-weight: 500; transition: all 0.3s ease; cursor: pointer;
        }
        .time-slot-btn:hover { border-color: var(--primary-light); background: rgba(20, 184, 166, 0.15); color: var(--primary-light); }
        .time-slot-btn.active {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-color: transparent; color: white; box-shadow: 0 4px 12px rgba(15, 118, 110, 0.3);
        }

        .container-main { max-width: 900px; margin: 0 auto; padding: 2rem 1rem; }
        .fade-in { animation: fadeIn 0.5s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .form-select option { background: #1e293b; color: var(--text-primary); }

        .icon-circle { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
        .icon-circle-teal { background: rgba(20, 184, 166, 0.15); color: var(--primary-light); }
        .icon-circle-amber { background: rgba(245, 158, 11, 0.15); color: var(--accent); }
        .icon-circle-rose { background: rgba(239, 68, 68, 0.15); color: #f87171; }
    </style>
    @yield('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="{{ route('triage.nursing.index') }}">
                <i class="bi bi-hospital"></i>
                MedTriaje
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('triage.nursing.*') ? 'active' : '' }}" href="{{ route('triage.nursing.index') }}">
                            <i class="bi bi-heart-pulse"></i> Enfermería
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('triage.reception.*') ? 'active' : '' }}" href="{{ route('triage.reception.index') }}">
                            <i class="bi bi-calendar-check"></i> Recepción
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('triage.doctor.*') ? 'active' : '' }}" href="{{ route('triage.doctor.index') }}">
                            <i class="bi bi-clipboard2-pulse"></i> Doctor
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-main fade-in">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
