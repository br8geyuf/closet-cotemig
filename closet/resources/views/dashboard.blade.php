@extends('layouts.app')

@section('title', 'Dashboard - Closet Fashion (Cliente)')

@section('content')
<div class="dashboard">
    <main class="main-content">

        <!-- PERFIL -->
        <div class="profile-header">
            <div class="profile-info">

                <!-- Avatar -->
                <div class="avatar-container">
                    <div class="avatar">
                        @if(Auth::user()->avatar_url)
                            <img src="{{ asset('storage/' . Auth::user()->avatar_url) }}" alt="{{ Auth::user()->name }}" class="avatar-img">
                        @else
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        @endif
                    </div>
                </div>

                <!-- Nome e @usuario -->
                <div class="user-data">
                    <h3>{{ Auth::user()->name }}</h3>

                    @php
                        $username = Str::slug(Auth::user()->name, '_');
                        $maxLength = 15;
                        $displayUsername = strlen($username) > $maxLength 
                            ? substr($username, 0, $maxLength) . '...' 
                            : $username;
                    @endphp

                    <p>{{ '@' . $displayUsername }}</p>

                    <div class="actions">
                        @if(Auth::id() !== Auth::user()->id)
                            <form action="{{ route('users.follow', Auth::user()->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="btn-follow">➕ Seguir</button>
                            </form>

                            <form action="{{ route('users.unfollow', Auth::user()->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-unfollow">➖ Deixar de seguir</button>
                            </form>
                        @else
                            <a href="{{ route('profile.edit') }}" class="btn-edit">✏️ Editar Perfil</a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- BOTÃO ⚙ COM DROPDOWN -->
<div class="settings-dropdown">
    <button class="btn-settings">⚙</button>
    <div class="dropdown-menu">
        <a href="{{ route('settings.notifications') }}">🔔 Notificações <span id="notification-count" class="badge">5</span></a>
        <a href="{{ route('settings.activities') }}">📋 Suas Atividades</a>
        <a href="{{ route('settings.privacy') }}">🔒 Privacidade</a>
        <a href="{{ route('settings.blocked') }}">🚫 Bloqueados</a>
        <a href="{{ route('settings.permissions') }}">📱 Permissões</a>
        <a href="{{ route('settings.accessibility') }}">♿ Acessibilidade</a>

        <!-- Logout -->
        <form action="{{ route('logout') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="btn-logout">🚪 Sair</button>
        </form>
    </div>
</div>
        </div>

        <!-- ESTATÍSTICAS -->
        <div class="stats">
            <div class="stat"><i class="fas fa-image"></i><p>Posts</p></div>
            <div class="stat"><i class="fas fa-users"></i><p>Seguidores</p><strong>{{ $followersCount }}</strong></div>
            <div class="stat"><i class="fas fa-user-check"></i><p>Seguindo</p><strong>{{ $followingCount }}</strong></div>
            <div class="stat"><i class="fas fa-tshirt"></i><p>Peças</p><strong>{{ $itemsCount }}</strong></div>
            <div class="stat"><i class="fas fa-layer-group"></i><p>Categorias</p><strong>{{ $categoriesCount }}</strong></div>
            <div class="stat"><i class="fas fa-heart"></i><p>Favoritos</p><strong>{{ $favoritesCount }}</strong></div>
            <div class="stat"><i class="fas fa-clock"></i><p>Memórias</p><strong>{{ $memoriesCount }}</strong></div>
            <div class="stat"><i class="fas fa-bullhorn"></i><p>Promoções</p><strong>{{ $promotionsCount }}</strong></div>
        </div>

        <!-- FEED COM ABAS -->
        <div class="tabs">
            <button class="tab active" data-tab="posts">📸 Posts</button>
            <button class="tab" data-tab="items">👕 Peças</button>
            <button class="tab" data-tab="favorites">❤️ Favoritos</button>
        </div>

        <!-- POSTS -->
        <div class="tab-content active" id="posts">
            <div class="feed-card">📸 {{ $post->title ?? 'Post sem título' }}</div>
            <div class="feed-card">🙃 Nenhum post ainda</div>
        </div>

        <!-- PEÇAS -->
        <div class="tab-content" id="items">
            <div class="feed-card">👕 Nenhuma peça cadastrada</div>
        </div>

        <!-- FAVORITOS -->
        <div class="tab-content" id="favorites">
            <div class="feed-card">❤️ Nenhum favorito ainda</div>
        </div>

        <!-- MENU INFERIOR -->
        <div class="bottom-menu">
            <a href="{{ route('items.create') }}" class="btn">➕ Adicionar Roupas</a>
            <a href="#" class="btn">📅 Calendário</a>
            <a href="{{ route('items.create', ['for_sale' => true]) }}" class="btn">🛒 Adicionar à Venda</a>
        </div>
    </main>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.querySelector('.btn-settings');
    const menu = document.querySelector('.settings-dropdown .dropdown-menu');
    const tabs = document.querySelectorAll('.tab');
    const contents = document.querySelectorAll('.tab-content');

    if (btn && menu) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            menu.classList.toggle('show');
        });

        window.addEventListener('click', function(e) {
            if (!e.target.closest('.settings-dropdown')) {
                menu.classList.remove('show');
            }
        });
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            contents.forEach(c => c.classList.remove('active'));

            tab.classList.add('active');
            document.getElementById(tab.dataset.tab).classList.add('active');
        });
    });

    setInterval(() => {
        const notificationCount = document.getElementById('notification-count');
        let count = parseInt(notificationCount.textContent);
        if (count > 0) {
            count--;
            notificationCount.textContent = count;
        }
    }, 5000);
});
</script>
@endpush

@push('styles')
<style>
.avatar-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}
.avatar-img {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
}
.user-data h3 {
    margin: 0;
    font-size: 18px;
    font-weight: bold;
}
.user-data p {
    margin: 2px 0 10px;
    color: #666;
}
.actions .btn-edit, .actions .btn-follow, .actions .btn-unfollow {
    margin-right: 5px;
    padding: 5px 10px;
    border-radius: 5px;
    border: none;
    cursor: pointer;
}
.actions .btn-edit { background-color: #007bff; color: #fff; }
.actions .btn-follow { background-color: #28a745; color: #fff; }
.actions .btn-unfollow { background-color: #dc3545; color: #fff; }
.stats {
    display: flex;
    gap: 15px;
    margin: 20px 0;
    flex-wrap: wrap;
}
.stat {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 10px;
    background-color: #f8f8f8;
    border-radius: 8px;
    width: 100px;
}
.tab-content {
    display: none;
    margin-top: 15px;
}
.tab-content.active {
    display: block;
}
.tabs button.tab {
    margin-right: 5px;
    padding: 8px 12px;
    border-radius: 5px;
    border: none;
    cursor: pointer;
    background-color: #eee;
}
.tabs button.tab.active {
    background-color: #007bff;
    color: #fff;
}
.bottom-menu {
    margin-top: 20px;
    display: flex;
    justify-content: space-around;
}
.bottom-menu .btn {
    padding: 10px 15px;
    border-radius: 5px;
    background-color: #007bff;
    color: #fff;
    text-decoration: none;
}
.settings-dropdown {
    position: relative;
}
.settings-dropdown .dropdown-menu {
    display: none;
    position: absolute;
    right: 0;
    background: #fff;
    border: 1px solid #ccc;
    border-radius: 5px;
    padding: 10px;
    z-index: 10;
}
.settings-dropdown .dropdown-menu.show {
    display: block;
}
.settings-dropdown .dropdown-menu a, 
.settings-dropdown .dropdown-menu form button {
    display: block;
    margin-bottom: 5px;
    color: #333;
    text-decoration: none;
    background: none;
    border: none;
    cursor: pointer;
    text-align: left;
}
</style>
@endpush
