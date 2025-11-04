<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Closet Fashion')</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS da aplicação -->
    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">

    <style>
        body.light-mode { background: #ffffff; color: #111; }
        body.dark-mode { background: #121212; color: #f5f5f5; }

        .user-avatar-btn {
            width: 40px; height: 40px; padding: 0;
            display: inline-flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 16px; line-height: 1;
        }

        .avatar-circle {
            width: 36px; height: 36px; border-radius: 50%; background: #6c757d; color: #fff;
            display: flex; align-items: center; justify-content: center; font-weight: 700;
        }

        .btn-logout { background: none; border: none; color: #fff; padding: 0; cursor: pointer; }

        /* Barra de busca */
        .search-bar {
            max-width: 500px;
            width: 100%;
        }
        .search-bar input {
            border-radius: 20px 0 0 20px;
        }
        .search-bar button {
            border-radius: 0 20px 20px 0;
        }
    </style>

    @stack('styles')
    @vite(['resources/js/app.js'])
</head>
<body class="light-mode d-flex flex-column min-vh-100">

    <!-- Navbar -->
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            @php
                $dashboardRoute = auth()->guard('web')->check()
                    ? 'dashboard'
                    : (auth()->guard('company')->check() ? 'company.dashboard' : 'home');
            @endphp

            <!-- Logo -->
            <a class="navbar-brand fw-bold me-3" href="{{ route($dashboardRoute) }}">
                Closet Fashion
            </a>

            <!-- Barra de busca -->
            <form action="{{ route('search.items') }}" method="GET" class="d-flex mx-auto search-bar">
                <input
                    type="text"
                    name="query"
                    class="form-control"
                    placeholder="Buscar produtos, lojas, marcas..."
                    value="{{ request('query') }}"
                    required
                >
                <button class="btn btn-warning" type="submit">
                    <i class="fa fa-search"></i>
                </button>
            </form>

            <!-- Lado direito (tema + login/logout) -->
            <div class="d-flex align-items-center ms-auto">
                <!-- Botão de tema -->
                <button id="theme-toggle" class="btn btn-sm btn-outline-light me-3" title="Alternar tema">
                    <i class="fa fa-moon"></i>
                </button>

                @guest('web')
                    @guest('company')
                        <a href="{{ route('login') }}" class="btn btn-outline-light me-2">Entrar</a>
                        <a href="{{ route('register') }}" class="btn btn-warning">Cadastrar</a>
                    @endguest
                @else
                    <!-- Botão avatar -->
                    <button class="btn btn-outline-light rounded-circle user-avatar-btn me-2"
                        type="button"
                        data-bs-toggle="offcanvas"
                        data-bs-target="#userSidebar"
                        aria-controls="userSidebar"
                        title="Menu do usuário">
                        {{ strtoupper(substr(auth()->user()->name ?? auth('company')->user()->name, 0, 1)) }}
                    </button>

                    <!-- Logout -->
                    <form action="{{ auth()->guard('web')->check() ? route('logout') : route('company.logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="btn-logout">🚪 Sair</button>
                    </form>
                @endguest
            </div>
        </div>
    </nav>

    @auth
    <!-- Offcanvas (menu lateral do usuário e empresa) -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="userSidebar" aria-labelledby="userSidebarLabel">
        <div class="offcanvas-header">
            <div class="d-flex align-items-center">
                <div class="avatar-circle me-2">
                    {{ strtoupper(substr(auth()->user()->name ?? auth('company')->user()->name, 0, 1)) }}
                </div>
                <div>
                    <h5 class="offcanvas-title mb-0" id="userSidebarLabel">
                        {{ auth()->user()->name ?? auth('company')->user()->name }}
                    </h5>
                    <small class="text-muted">
                        {{ auth()->user()->email ?? auth('company')->user()->email }}
                    </small>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
        </div>

        <div class="offcanvas-body p-0">
            <div class="list-group list-group-flush">

                <!-- Menu Cliente -->
                @if(auth()->guard('web')->check())
                    <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action">
                        <i class="fa fa-user me-2"></i> Perfil
                    </a>
                    @if(Route::has('categories.index'))
                        <a href="{{ route('categories.index') }}" class="list-group-item list-group-item-action">
                            <i class="fa fa-shirt me-2"></i> Meu Armário
                        </a>
                    @endif
                    @if(Route::has('shopping-list.index'))
                        <a href="{{ route('shopping-list.index') }}" class="list-group-item list-group-item-action">
                            <i class="fa fa-list me-2"></i> Lista de Compras
                        </a>
                    @endif
                    @if(Route::has('filter.index'))
                        <a href="{{ route('filter.index') }}" class="list-group-item list-group-item-action">
                            <i class="fa fa-filter me-2"></i> Filtro de Busca
                        </a>
                    @endif
                @endif

                <!-- Menu Empresa -->
                @if(auth()->guard('company')->check())
                    <a href="{{ route('company.dashboard') }}" class="list-group-item list-group-item-action">
                        <i class="fa fa-user me-2"></i> Dashboard
                    </a>
                    @if(Route::has('company.profile'))
                        <a href="{{ route('company.profile.edit') }}" class="list-group-item list-group-item-action">
                            <i class="fa fa-user-cog me-2"></i> Editar Perfil
                        </a>
                    @endif
                    @if(Route::has('stores.index'))
                        <a href="{{ route('stores.index') }}" class="list-group-item list-group-item-action">
                            <i class="fa fa-store me-2"></i> Minhas Lojas
                        </a>
                    @endif
                    @if(Route::has('promotions.index'))
                        <a href="{{ route('promotions.index') }}" class="list-group-item list-group-item-action">
                            <i class="fa fa-bullhorn me-2"></i> Minhas Promoções
                        </a>
                    @endif
                @endif

                <!-- Comunicação -->
                <a href="{{ route('communication.index') }}" class="list-group-item list-group-item-action">
                    <i class="fa fa-comments me-2"></i> Comunicação
                </a>
            </div>
        </div>
    </div>
    @endauth

    <!-- Conteúdo principal -->
    <main class="container flex-grow-1 py-4">
        @yield('content')
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script Tema -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const body = document.body;
            const toggle = document.getElementById("theme-toggle");
            const icon = toggle.querySelector("i");

            if (localStorage.getItem("theme") === "dark") {
                body.classList.replace("light-mode", "dark-mode");
                icon.classList.replace("fa-moon", "fa-sun");
            } else {
                body.classList.replace("dark-mode", "light-mode");
                localStorage.setItem("theme", "light");
                icon.classList.replace("fa-sun", "fa-moon");
            }

            toggle.addEventListener("click", () => {
                if (body.classList.contains("light-mode")) {
                    body.classList.replace("light-mode", "dark-mode");
                    localStorage.setItem("theme", "dark");
                    icon.classList.replace("fa-moon", "fa-sun");
                } else {
                    body.classList.replace("dark-mode", "light-mode");
                    localStorage.setItem("theme", "light");
                    icon.classList.replace("fa-sun", "fa-moon");
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
