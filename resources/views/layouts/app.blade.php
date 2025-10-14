<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'P2DF Email Forensic System')</title>
    
    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        body {
            background: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Simple Navbar */
        .navbar {
            background: #343a40 !important;
            border-bottom: 1px solid #dee2e6;
        }

        .navbar-brand {
            font-weight: 600;
            font-size: 1.2rem;
        }

        .nav-link {
            font-weight: 400;
            padding: 8px 16px !important;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.1);
        }

        /* Simple Cards */
        .card {
            border: 1px solid #dee2e6;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .card-header {
            background: #f8f9fa;
            color: #495057;
            font-weight: 500;
            border-bottom: 1px solid #dee2e6;
            padding: 1rem 1.5rem;
        }

        /* Simple Buttons */
        .btn {
            border-radius: 4px;
            padding: 8px 16px;
            font-weight: 400;
            border: 1px solid transparent;
        }

        /* Simple Badges */
        .badge {
            border-radius: 4px;
            padding: 4px 8px;
            font-weight: 400;
            font-size: 0.75rem;
        }

        .role-badge {
            font-size: 0.7rem;
            padding: 2px 6px;
            border-radius: 4px;
            margin-left: 8px;
        }

        /* Simple Alerts */
        .alert {
            border-radius: 4px;
            border: 1px solid transparent;
        }

        /* Simple Tables */
        .table {
            border-radius: 4px;
        }

        .table thead th {
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            background: #f8f9fa;
        }

        .table-hover tbody tr:hover {
            background: #f8f9fa;
        }

        /* Simple Forms */
        .form-control {
            border-radius: 4px;
            border: 1px solid #ced4da;
            padding: 8px 12px;
        }

        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        textarea.form-control {
            min-height: 100px;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 20px 0;
        }

        .page-header {
            margin-bottom: 20px;
        }

        .page-header h2 {
            font-weight: 600;
            color: #495057;
        }

        /* Simple Stats Cards */
        .stat-card {
            border-radius: 4px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
            background: white;
        }

        .stat-card h5 {
            font-weight: 500;
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 10px;
        }

        .stat-card h2 {
            font-weight: 600;
            font-size: 2rem;
            margin: 0;
            color: #495057;
        }

        /* Simple Footer */
        footer {
            background: #343a40;
            color: white;
            padding: 20px 0;
            margin-top: 40px;
            border-top: 1px solid #dee2e6;
        }

        /* Simple Encrypted Text */
        .encrypted-text {
            font-family: 'Courier New', monospace;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
            word-break: break-all;
            max-height: 200px;
            overflow-y: auto;
        }

        /* Simple Dropdown */
        .dropdown-menu {
            border-radius: 4px;
            border: 1px solid #dee2e6;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .dropdown-item {
            padding: 8px 16px;
        }

        .dropdown-item:hover {
            background: #f8f9fa;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Simple Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="fas fa-shield-alt"></i> P2DF Forensic
            </a>
            
            @auth
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mr-auto">
                        @if(auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.emails') }}">
                                    <i class="fas fa-envelope"></i> Emails
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.upload') }}">
                                    <i class="fas fa-cloud-upload-alt"></i> Upload
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.requests') }}">
                                    <i class="fas fa-key"></i> Requests
                                </a>
                            </li>
                             <li class="nav-item">
                                 <a class="nav-link" href="{{ route('admin.logs') }}">
                                     <i class="fas fa-history"></i> Logs
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a class="nav-link" href="{{ route('admin.workflow') }}">
                                     <i class="fas fa-sitemap"></i> Workflow
                                 </a>
                             </li>
                        @elseif(auth()->user()->isInvestigator())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('investigator.dashboard') }}">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('investigator.emails') }}">
                                    <i class="fas fa-envelope"></i> Emails
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('investigator.requests') }}">
                                    <i class="fas fa-key"></i> Requests
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('investigator.reports') }}">
                                    <i class="fas fa-file-alt"></i> Reports
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('investigator.logs') }}">
                                    <i class="fas fa-history"></i> Logs
                                </a>
                            </li>
                        @endif
                    </ul>
                    
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" data-toggle="dropdown">
                                <i class="fas fa-user-circle"></i> {{ auth()->user()->name }}
                                <span class="badge badge-{{ auth()->user()->isAdmin() ? 'danger' : 'info' }} role-badge">
                                    {{ strtoupper(auth()->user()->role) }}
                                </span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </li>
                    </ul>
                </div>
            @endauth
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <!-- Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Success!</strong> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Validation Error!</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- Simple Footer -->
    <footer>
        <div class="container text-center">
            <p class="mb-0">
                P2DF Email Forensic System &copy; {{ date('Y') }}
            </p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    </script>
    
    @stack('scripts')
</body>
</html>
