<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light dark">
    <title>{{ config('app.name', 'HRIS Online Leave') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Bootstrap CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        /* ============================================
           SHADCN UI THEME VARIABLES
        ============================================ */
        :root {
            --radius: 0.625rem;
            
            /* Light mode colors */
            --background: oklch(1 0 0);
            --foreground: oklch(0.145 0 0);
            --card: oklch(1 0 0);
            --card-foreground: oklch(0.145 0 0);
            --primary: oklch(0.205 0 0);
            --primary-foreground: oklch(0.985 0 0);
            --secondary: oklch(0.97 0 0);
            --secondary-foreground: oklch(0.205 0 0);
            --muted: oklch(0.97 0 0);
            --muted-foreground: oklch(0.556 0 0);
            --accent: oklch(0.97 0 0);
            --accent-foreground: oklch(0.205 0 0);
            --destructive: oklch(0.577 0.245 27.325);
            --border: oklch(0.922 0 0);
            --input: oklch(0.922 0 0);
            --ring: oklch(0.708 0 0);
            --success: oklch(0.627 0.194 149.214);
        }

        .dark {
            --background: oklch(0.145 0 0);
            --foreground: oklch(0.985 0 0);
            --card: oklch(0.205 0 0);
            --card-foreground: oklch(0.985 0 0);
            --primary: oklch(0.922 0 0);
            --primary-foreground: oklch(0.205 0 0);
            --secondary: oklch(0.269 0 0);
            --secondary-foreground: oklch(0.985 0 0);
            --muted: oklch(0.269 0 0);
            --muted-foreground: oklch(0.708 0 0);
            --accent: oklch(0.269 0 0);
            --accent-foreground: oklch(0.985 0 0);
            --destructive: oklch(0.704 0.191 22.216);
            --border: oklch(1 0 0 / 10%);
            --input: oklch(1 0 0 / 15%);
            --ring: oklch(0.556 0 0);
            --success: oklch(0.696 0.17 162.48);
        }

        /* ============================================
           BASE STYLES
        ============================================ */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background-color: var(--background);
            min-height: 100vh;
            line-height: 1.6;
            color: var(--foreground);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        /* ============================================
           THEME TOGGLE BUTTON
        ============================================ */
        .theme-toggle {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.5rem;
            border: 1px solid var(--border);
            background-color: var(--background);
            color: var(--foreground);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
            z-index: 1000;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }

        .theme-toggle:hover {
            background-color: var(--muted);
            transform: scale(1.05);
        }

        .theme-toggle svg {
            width: 1.25rem;
            height: 1.25rem;
        }

        .theme-toggle .sun-icon {
            display: none;
        }

        .theme-toggle .moon-icon {
            display: block;
        }

        html.dark .theme-toggle .sun-icon {
            display: block;
        }

        html.dark .theme-toggle .moon-icon {
            display: none;
        }

        /* ============================================
           NAVIGATION
        ============================================ */
        .navbar {
            background: var(--card);
            border-bottom: 1px solid var(--border);
            padding: 1rem 0;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }

        .navbar-brand {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--foreground) !important;
            letter-spacing: -0.025em;
            transition: color 0.15s ease;
        }

        .navbar-brand:hover {
            color: var(--muted-foreground) !important;
        }

        .navbar-toggler {
            border: 1px solid var(--border);
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
            transition: all 0.15s ease;
        }

        .navbar-toggler:hover {
            background-color: var(--muted);
        }

        .navbar-toggler-icon {
            background-image: none;
            width: 1.5rem;
            height: 1.5rem;
            position: relative;
        }

        .navbar-toggler-icon::before,
        .navbar-toggler-icon::after,
        .navbar-toggler-icon {
            background-color: var(--foreground);
        }

        .navbar-toggler-icon::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 2px;
            background-color: var(--foreground);
            top: 0;
            left: 0;
        }

        .navbar-toggler-icon::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 2px;
            background-color: var(--foreground);
            bottom: 0;
            left: 0;
        }

        .nav-link {
            color: var(--muted-foreground) !important;
            font-size: 0.9375rem;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            border-radius: 0.5rem;
            transition: all 0.15s ease;
            margin: 0 0.25rem;
        }

        .nav-link:hover {
            color: var(--foreground) !important;
            background-color: var(--muted);
        }

        .nav-link.active {
            color: var(--foreground) !important;
            background-color: var(--accent);
        }

        /* ============================================
           CONTAINER
        ============================================ */
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
        }

        /* ============================================
           RESPONSIVE
        ============================================ */
        @media (max-width: 768px) {
            .theme-toggle {
                top: 1rem;
                right: 1rem;
            }

            .navbar {
                padding: 0.75rem 0;
            }

            .main-container {
                padding: 1.5rem 1rem;
            }

            .navbar-collapse {
                margin-top: 1rem;
                padding: 1rem;
                background-color: var(--card);
                border-radius: 0.5rem;
                border: 1px solid var(--border);
            }

            .nav-link {
                margin: 0.25rem 0;
            }
        }

        @media (max-width: 480px) {
            .theme-toggle {
                width: 2.25rem;
                height: 2.25rem;
            }

            .navbar-brand {
                font-size: 1.125rem;
            }
        }

        /* ============================================
           BOOTSTRAP COMPONENT COMPATIBILITY
        ============================================ */
        
        /* Modal */
        .modal-content {
            background-color: var(--card);
            color: var(--card-foreground);
            border: 1px solid var(--border);
        }

        .modal-header {
            border-bottom-color: var(--border);
        }

        .modal-footer {
            border-top-color: var(--border);
        }

        .btn-close {
            filter: var(--bs-btn-close-filter, none);
        }

        html.dark .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        /* Tables */
        .table {
            color: var(--foreground);
            border-color: var(--border);
        }

        .table thead th {
            border-bottom-color: var(--border);
            background-color: var(--muted);
            color: var(--foreground);
        }

        .table tbody tr {
            border-bottom-color: var(--border);
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: var(--muted);
        }

        .table-hover tbody tr:hover {
            background-color: var(--accent);
            color: var(--accent-foreground);
        }

        /* Forms */
        .form-control,
        .form-select {
            background-color: var(--background);
            border-color: var(--input);
            color: var(--foreground);
        }

        .form-control:focus,
        .form-select:focus {
            background-color: var(--background);
            border-color: var(--ring);
            color: var(--foreground);
            box-shadow: 0 0 0 0.25rem rgba(var(--ring), 0.25);
        }

        .form-control::placeholder {
            color: var(--muted-foreground);
        }

        /* Buttons */
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            color: var(--primary-foreground);
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background-color: var(--primary);
            border-color: var(--primary);
            opacity: 0.9;
        }

        .btn-secondary {
            background-color: var(--secondary);
            border-color: var(--secondary);
            color: var(--secondary-foreground);
        }

        .btn-secondary:hover,
        .btn-secondary:focus {
            background-color: var(--secondary);
            border-color: var(--secondary);
            opacity: 0.9;
        }

        /* Cards */
        .card {
            background-color: var(--card);
            border-color: var(--border);
            color: var(--card-foreground);
        }

        .card-header {
            background-color: var(--muted);
            border-bottom-color: var(--border);
        }

        .card-footer {
            background-color: var(--muted);
            border-top-color: var(--border);
        }

        /* Alerts */
        .alert {
            border-color: var(--border);
        }

        /* Badges */
        .badge {
            font-weight: 500;
        }

        /* Dropdown */
        .dropdown-menu {
            background-color: var(--card);
            border-color: var(--border);
        }

        .dropdown-item {
            color: var(--foreground);
        }

        .dropdown-item:hover,
        .dropdown-item:focus {
            background-color: var(--muted);
            color: var(--foreground);
        }

        .dropdown-divider {
            border-top-color: var(--border);
        }
    </style>
</head>
<body>
    <!-- Theme Toggle Button -->
    <button type="button" class="theme-toggle" onclick="toggleTheme()" aria-label="Toggle theme">
        <svg class="sun-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        <svg class="moon-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
        </svg>
    </button>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="/">HRIS System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto" id="auth-nav">
                    <!-- Nav items will be injected here based on auth state -->
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-container">
        @yield('content')
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // ============================================
        // THEME MANAGEMENT
        // ============================================
        function getTheme() {
            if (localStorage.getItem('hris-theme')) {
                return localStorage.getItem('hris-theme');
            }
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }

        function setTheme(theme) {
            localStorage.setItem('hris-theme', theme);
            document.documentElement.classList.remove('light', 'dark');
            document.documentElement.classList.add(theme);
        }

        function toggleTheme() {
            const currentTheme = getTheme();
            setTheme(currentTheme === 'dark' ? 'light' : 'dark');
        }

        // Apply theme on load
        setTheme(getTheme());

        // Listen for system theme changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!localStorage.getItem('hris-theme')) {
                setTheme(e.matches ? 'dark' : 'light');
            }
        });

        // ============================================
        // AUTH STATE MANAGEMENT
        // ============================================
        function updateNav() {
            const token = localStorage.getItem('access_token');
            const nav = document.getElementById('auth-nav');
            if (token) {
                nav.innerHTML = `
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/personnel">Personnel</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/roles">Roles & Permissions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/users">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/profile">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="logout(event)">Logout</a>
                    </li>
                `;
            } else {
                nav.innerHTML = `
                    <li class="nav-item">
                        <a class="nav-link" href="/login">Login</a>
                    </li>
                `;
            }
            
            // Highlight active link
            highlightActiveLink();
        }

        function highlightActiveLink() {
            const currentPath = window.location.pathname;
            const links = document.querySelectorAll('.nav-link');
            links.forEach(link => {
                const href = link.getAttribute('href');
                if (href && href !== '#' && currentPath.startsWith(href)) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
        }

        async function logout(e) {
            e.preventDefault();
            const token = localStorage.getItem('access_token');
            try {
                await fetch('/api/logout', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });
            } catch (error) {
                console.error('Logout error', error);
            } finally {
                localStorage.removeItem('access_token');
                localStorage.removeItem('user');
                window.location.href = '/login';
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateNav();
        });
    </script>
    
    @yield('scripts')
</body>
</html>
