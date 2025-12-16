<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Product Manager')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #F54E1F;
            --primary-dark: #D93D0A;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --light-bg: #f8f9fa;
            --card-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            transition: var(--transition);
        }

        html, body {
            height: 100%;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: #2c3e50;
        }

        /* Navigation */
        .navbar {
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            box-shadow: var(--card-shadow);
            padding: 1rem 0;
            border-bottom: 3px solid var(--primary-dark);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            letter-spacing: -0.5px;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            margin: 0 0.5rem;
            padding: 0.5rem 1rem !important;
            border-radius: 4px;
            position: relative;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white !important;
        }

        .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            color: white !important;
        }

        /* Main container */
        .main-container {
            flex: 1;
            padding: 2rem 0;
        }

        .page-header {
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid rgba(0, 102, 204, 0.1);
        }

        .page-header h1 {
            color: var(--primary-color);
            font-weight: 700;
            font-size: 2rem;
            margin: 0;
        }

        .page-header p {
            color: var(--secondary-color);
            margin: 0.5rem 0 0 0;
        }

        /* Cards */
        .card {
            border: none;
            box-shadow: var(--card-shadow);
            border-radius: 8px;
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .card:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        .card-header {
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            font-weight: 600;
            border: none;
            padding: 1.5rem;
        }

        .card-body {
            padding: 2rem;
        }

        /* Buttons */
        .btn {
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            border: none;
            transition: var(--transition);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        .btn-icon {
            width: 36px;
            height: 36px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        /* Forms */
        .form-label {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.75rem;
        }

        .form-control,
        .form-select {
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: var(--transition);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 102, 204, 0.15);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        /* Tables */
        .table {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
        }

        .table thead th {
            background-color: var(--light-bg);
            color: var(--primary-color);
            font-weight: 700;
            border-color: #e0e0e0;
            padding: 1.25rem;
        }

        .table tbody td {
            padding: 1.25rem;
            border-color: #e0e0e0;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background-color: rgba(0, 102, 204, 0.02);
        }

        /* Alerts */
        .alert {
            border: none;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            font-weight: 500;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border-left: 4px solid #17a2b8;
        }

        /* Badges */
        .badge {
            padding: 0.5rem 1rem;
            font-weight: 600;
            border-radius: 20px;
            font-size: 0.85rem;
        }

        /* Action buttons group */
        .btn-group-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--secondary-color);
        }

        .empty-state i {
            font-size: 3rem;
            color: #ddd;
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        /* Breadcrumb */
        .breadcrumb {
            background-color: transparent;
            padding: 0;
            margin-bottom: 2rem;
        }

        .breadcrumb-item.active {
            color: var(--primary-color);
            font-weight: 600;
        }

        /* Footer */
        footer {
            background-color: rgba(44, 62, 80, 0.1);
            padding: 2rem 0;
            text-align: center;
            color: var(--secondary-color);
            margin-top: auto;
            border-top: 1px solid rgba(0, 102, 204, 0.1);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 1.5rem;
            }

            .btn-group-actions {
                flex-direction: column;
            }

            .btn-group-actions .btn {
                width: 100%;
            }

            .table {
                font-size: 0.875rem;
            }

            .table thead th,
            .table tbody td {
                padding: 0.75rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('products.index') }}">
                <i class="bi bi-box-seam"></i> Product Manager
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link @if(request()->routeIs('products.*')) active @endif" href="{{ route('products.index') }}">
                            <i class="bi bi-bag"></i> Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if(request()->routeIs('categories.*')) active @endif" href="{{ route('categories.index') }}">
                            <i class="bi bi-tags"></i> Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('products.create') }}">
                            <i class="bi bi-plus-circle"></i> Create Product
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main content -->
    <div class="main-container">
        <div class="container-fluid">
            <!-- Alerts -->
            @if ($message = session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> {{ $message }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($message = session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle"></i> {{ $message }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Page content -->
            @yield('content')
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container-fluid">
            <p>&copy; 2025 Product Manager. Built with <i class="bi bi-heart-fill text-danger"></i> using Laravel</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
