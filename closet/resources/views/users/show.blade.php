@extends('layouts.app')

@section('title', 'Perfil - ' . $user->name)

@section('content')
<div class="user-profile-container">
    <div class="user-profile-card">
        <!-- Header com imagem de capa -->
        <div class="profile-cover">
            <div class="cover-gradient"></div>
        </div>

        <!-- Conteúdo do perfil -->
        <div class="profile-content">
            <!-- Avatar -->
            <div class="profile-avatar-wrapper">
                <img src="{{ $user->avatar_url }}" 
                     class="profile-avatar" 
                     alt="Foto de {{ $user->name }}">
                @if($user->badge)
                    <span class="profile-badge">{{ $user->badge }}</span>
                @endif
            </div>

            <!-- Informações do usuário -->
            <div class="profile-info">
                <h1 class="profile-name">{{ $user->name }}</h1>
                
                @if($user->profile && $user->profile->full_name)
                    <p class="profile-fullname">{{ $user->profile->full_name }}</p>
                @endif

                <p class="profile-email">
                    <span class="email-icon">📧</span>
                    {{ $user->email }}
                </p>

                @if($user->profile && $user->profile->bio)
                    <div class="profile-bio">
                        <p>{{ $user->profile->bio }}</p>
                    </div>
                @endif

                <!-- Informações adicionais -->
                <div class="profile-details">
                    @if($user->profile && $user->profile->phone)
                        <div class="detail-item">
                            <span class="detail-icon">📱</span>
                            <span>{{ $user->profile->phone }}</span>
                        </div>
                    @endif

                    @if($user->profile && $user->profile->birth_date)
                        <div class="detail-item">
                            <span class="detail-icon">🎂</span>
                            <span>{{ $user->profile->birth_date->format('d/m/Y') }}</span>
                        </div>
                    @endif

                    @if($user->profile && $user->profile->gender)
                        <div class="detail-item">
                            <span class="detail-icon">⚧️</span>
                            <span>
                                @switch($user->profile->gender)
                                    @case('male') Masculino @break
                                    @case('female') Feminino @break
                                    @case('other') Outro @break
                                    @case('prefer_not_to_say') Prefiro não dizer @break
                                @endswitch
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Estatísticas -->
                <div class="profile-stats">
                    <div class="stat-item">
                        <span class="stat-value">{{ $user->followers_count ?? 0 }}</span>
                        <span class="stat-label">Seguidores</span>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-item">
                        <span class="stat-value">{{ $user->following()->count() }}</span>
                        <span class="stat-label">Seguindo</span>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-item">
                        <span class="stat-value">{{ $user->items()->count() }}</span>
                        <span class="stat-label">Itens</span>
                    </div>
                </div>

                <!-- Botões de ação -->
                <div class="profile-actions">
                    @if(auth()->id() !== $user->id)
                        @if($isFollowing)
                            <form action="{{ route('users.unfollow', $user->id) }}" method="POST" class="action-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-unfollow">
                                    <span class="btn-icon">➖</span>
                                    Deixar de seguir
                                </button>
                            </form>
                        @else
                            <form action="{{ route('users.follow', $user->id) }}" method="POST" class="action-form">
                                @csrf
                                <button type="submit" class="btn btn-follow">
                                    <span class="btn-icon">➕</span>
                                    Seguir
                                </button>
                            </form>
                        @endif
                    @else
                        <a href="{{ route('profile.edit') }}" class="btn btn-edit">
                            <span class="btn-icon">✏️</span>
                            Editar Perfil
                        </a>
                    @endif

                    <a href="{{ url()->previous() }}" class="btn btn-back">
                        <span class="btn-icon">←</span>
                        Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Container principal */
.user-profile-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 2rem 1rem;
    display: flex;
    justify-content: center;
    align-items: center;
}

.user-profile-card {
    max-width: 700px;
    width: 100%;
    background: white;
    border-radius: 24px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    overflow: hidden;
    animation: slideUp 0.5s ease-out;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Capa do perfil */
.profile-cover {
    height: 200px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    position: relative;
}

.cover-gradient {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 100px;
    background: linear-gradient(to bottom, transparent, rgba(0, 0, 0, 0.3));
}

/* Conteúdo do perfil */
.profile-content {
    padding: 0 2rem 2rem 2rem;
    position: relative;
}

/* Avatar */
.profile-avatar-wrapper {
    position: relative;
    width: 160px;
    height: 160px;
    margin: -80px auto 1.5rem auto;
}

.profile-avatar {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    border: 6px solid white;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
}

.profile-badge {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 0.375rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

/* Informações */
.profile-info {
    text-align: center;
}

.profile-name {
    font-size: 2rem;
    font-weight: 800;
    color: #1f2937;
    margin: 0 0 0.5rem 0;
}

.profile-fullname {
    font-size: 1.125rem;
    color: #6b7280;
    margin: 0 0 0.75rem 0;
}

.profile-email {
    font-size: 1rem;
    color: #6b7280;
    margin: 0 0 1.5rem 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.email-icon {
    font-size: 1.125rem;
}

.profile-bio {
    background: #f9fafb;
    border-radius: 12px;
    padding: 1.25rem;
    margin: 0 0 1.5rem 0;
    border-left: 4px solid #667eea;
}

.profile-bio p {
    margin: 0;
    color: #374151;
    line-height: 1.6;
    font-size: 1rem;
}

/* Detalhes adicionais */
.profile-details {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.detail-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #6b7280;
    font-size: 0.95rem;
}

.detail-icon {
    font-size: 1.25rem;
}

/* Estatísticas */
.profile-stats {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 2rem;
    margin: 2rem 0;
    padding: 1.5rem;
    background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
    border-radius: 16px;
}

.stat-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.25rem;
}

.stat-value {
    font-size: 1.75rem;
    font-weight: 800;
    color: #667eea;
}

.stat-label {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 600;
}

.stat-divider {
    width: 2px;
    height: 40px;
    background: #d1d5db;
}

/* Ações */
.profile-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.action-form {
    display: inline-block;
}

.btn {
    padding: 0.875rem 1.75rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    transition: all 0.2s ease;
    border: none;
    text-decoration: none;
}

.btn-icon {
    font-size: 1.125rem;
}

.btn-follow {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.btn-follow:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
}

.btn-unfollow {
    background: #ef4444;
    color: white;
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
}

.btn-unfollow:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(239, 68, 68, 0.5);
    background: #dc2626;
}

.btn-edit {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
}

.btn-edit:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.5);
}

.btn-back {
    background: #f3f4f6;
    color: #374151;
}

.btn-back:hover {
    background: #e5e7eb;
}

.btn:active {
    transform: translateY(0);
}

/* Responsividade */
@media (max-width: 768px) {
    .user-profile-container {
        padding: 1rem;
    }

    .profile-content {
        padding: 0 1.5rem 1.5rem 1.5rem;
    }

    .profile-name {
        font-size: 1.75rem;
    }

    .profile-avatar-wrapper {
        width: 140px;
        height: 140px;
        margin: -70px auto 1.5rem auto;
    }

    .profile-stats {
        gap: 1rem;
        padding: 1rem;
    }

    .stat-value {
        font-size: 1.5rem;
    }

    .profile-actions {
        flex-direction: column;
    }

    .btn {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .profile-name {
        font-size: 1.5rem;
    }

    .profile-stats {
        flex-direction: column;
        gap: 1.5rem;
    }

    .stat-divider {
        width: 60px;
        height: 2px;
    }

    .profile-details {
        flex-direction: column;
        gap: 1rem;
    }
}
</style>
@endsection
