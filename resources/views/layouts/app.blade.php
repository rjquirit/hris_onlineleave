<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRIS Online Leave</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">HRIS System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto" id="auth-nav">
                    <!-- Nav items will be injected here based on auth state -->
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Global Auth State Management
        function updateNav() {
            const token = localStorage.getItem('access_token');
            const nav = document.getElementById('auth-nav');
            if (token) {
                nav.innerHTML = `
                    <li class="nav-item">
                        <a class="nav-link" href="/personnel">Personnel</a>
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
                window.location.href = '/login';
            }
        }

        document.addEventListener('DOMContentLoaded', updateNav);
    </script>
    @yield('scripts')
</body>
</html>
