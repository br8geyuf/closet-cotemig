@extends('layouts.app')

@section('title', 'Editar Perfil')

@section('content')
<div class="edit-profile-container">
    <div class="edit-profile-card">
        <div class="card-header">
            <h1 class="card-title">✏️ Editar Perfil</h1>
            <p class="card-subtitle">Personalize suas informações e mantenha seu perfil atualizado</p>
        </div>

        <!-- Mensagens de sucesso -->
        @if(session('success'))
            <div class="alert alert-success">
                <span class="alert-icon">✅</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Mensagens de erro -->
        @if($errors->any())
            <div class="alert alert-error">
                <span class="alert-icon">⚠️</span>
                <div>
                    <strong>Ops! Encontramos alguns problemas:</strong>
                    <ul class="error-list">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="profile-form">
            @csrf
            @method('PUT')

            <!-- Seção de Avatar -->
            <div class="form-section">
                <h2 class="section-title">
                    <span class="section-icon">📸</span>
                    Foto de Perfil
                </h2>
                
                <div class="avatar-upload-container">
                    <div class="avatar-preview-wrapper">
                        <img id="avatarPreview" 
                             src="{{ $user->avatar_url }}" 
                             alt="Avatar de {{ $user->name }}" 
                             class="avatar-preview">
                        <div class="avatar-overlay">
                            <label for="avatar" class="avatar-edit-btn">
                                <span class="edit-icon">📷</span>
                                <span class="edit-text">Alterar foto</span>
                            </label>
                        </div>
                    </div>
                    <input type="file" 
                           id="avatar" 
                           name="avatar" 
                           accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" 
                           class="avatar-input" 
                           onchange="previewAvatar(event)">
                    <p class="avatar-hint">JPG, PNG, GIF ou WEBP. Máximo 2MB.</p>
                </div>
            </div>

            <!-- Seção de Informações Básicas -->
            <div class="form-section">
                <h2 class="section-title">
                    <span class="section-icon">👤</span>
                    Informações Básicas
                </h2>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name" class="form-label">
                            Nome Completo <span class="required">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $user->name) }}" 
                               class="form-input"
                               required
                               placeholder="Digite seu nome completo">
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">
                            E-mail <span class="required">*</span>
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', $user->email) }}" 
                               class="form-input"
                               required
                               placeholder="seu@email.com">
                    </div>

                    <div class="form-group">
                        <label for="first_name" class="form-label">Primeiro Nome</label>
                        <input type="text" 
                               id="first_name" 
                               name="first_name" 
                               value="{{ old('first_name', $profile->first_name ?? '') }}" 
                               class="form-input"
                               placeholder="João">
                    </div>

                    <div class="form-group">
                        <label for="last_name" class="form-label">Sobrenome</label>
                        <input type="text" 
                               id="last_name" 
                               name="last_name" 
                               value="{{ old('last_name', $profile->last_name ?? '') }}" 
                               class="form-input"
                               placeholder="Silva">
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label">
                            <span class="label-icon">📱</span>
                            Telefone
                        </label>
                        <input type="tel" 
                               id="phone" 
                               name="phone" 
                               value="{{ old('phone', $profile->phone ?? '') }}" 
                               class="form-input"
                               placeholder="(11) 98765-4321">
                    </div>

                    <div class="form-group">
                        <label for="birth_date" class="form-label">
                            <span class="label-icon">🎂</span>
                            Data de Nascimento
                        </label>
                        <input type="date" 
                               id="birth_date" 
                               name="birth_date" 
                               value="{{ old('birth_date', $profile->birth_date ? $profile->birth_date->format('Y-m-d') : '') }}" 
                               class="form-input">
                    </div>

                    <div class="form-group">
                        <label for="gender" class="form-label">
                            <span class="label-icon">⚧️</span>
                            Gênero
                        </label>
                        <select id="gender" name="gender" class="form-select">
                            <option value="">Selecione...</option>
                            <option value="male" {{ old('gender', $profile->gender ?? '') == 'male' ? 'selected' : '' }}>Masculino</option>
                            <option value="female" {{ old('gender', $profile->gender ?? '') == 'female' ? 'selected' : '' }}>Feminino</option>
                            <option value="other" {{ old('gender', $profile->gender ?? '') == 'other' ? 'selected' : '' }}>Outro</option>
                            <option value="prefer_not_to_say" {{ old('gender', $profile->gender ?? '') == 'prefer_not_to_say' ? 'selected' : '' }}>Prefiro não dizer</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="bio" class="form-label">
                        <span class="label-icon">✍️</span>
                        Biografia
                    </label>
                    <textarea id="bio" 
                              name="bio" 
                              rows="4" 
                              class="form-textarea"
                              placeholder="Conte um pouco sobre você... (máximo 500 caracteres)"
                              maxlength="500">{{ old('bio', $profile->bio ?? '') }}</textarea>
                    <div class="char-counter">
                        <span id="charCount">{{ strlen(old('bio', $profile->bio ?? '')) }}</span>/500 caracteres
                    </div>
                </div>
            </div>

            <!-- Seção de Segurança -->
            <div class="form-section">
                <h2 class="section-title">
                    <span class="section-icon">🔒</span>
                    Alterar Senha
                </h2>
                
                <div class="security-notice">
                    <span class="notice-icon">ℹ️</span>
                    <p>Deixe em branco se não deseja alterar sua senha</p>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="current_password" class="form-label">Senha Atual</label>
                        <input type="password" 
                               id="current_password" 
                               name="current_password" 
                               class="form-input"
                               placeholder="Digite sua senha atual">
                    </div>

                    <div class="form-group">
                        <label for="new_password" class="form-label">Nova Senha</label>
                        <input type="password" 
                               id="new_password" 
                               name="new_password" 
                               class="form-input"
                               placeholder="Digite a nova senha (mínimo 8 caracteres)">
                    </div>

                    <div class="form-group form-group-full">
                        <label for="new_password_confirmation" class="form-label">Confirmar Nova Senha</label>
                        <input type="password" 
                               id="new_password_confirmation" 
                               name="new_password_confirmation" 
                               class="form-input"
                               placeholder="Digite a nova senha novamente">
                    </div>
                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="form-actions">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                    <span class="btn-icon">←</span>
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <span class="btn-icon">💾</span>
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Scripts -->
<script>
// Preview do avatar
function previewAvatar(event) {
    const file = event.target.files[0];
    if (file) {
        // Validar tamanho (2MB)
        if (file.size > 2048 * 1024) {
            alert('⚠️ O arquivo é muito grande! O tamanho máximo é 2MB.');
            event.target.value = '';
            return;
        }

        // Validar tipo
        const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
        if (!validTypes.includes(file.type)) {
            alert('⚠️ Formato inválido! Use JPG, PNG, GIF ou WEBP.');
            event.target.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatarPreview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}

// Contador de caracteres da bio
const bioTextarea = document.getElementById('bio');
const charCount = document.getElementById('charCount');

if (bioTextarea && charCount) {
    bioTextarea.addEventListener('input', function() {
        charCount.textContent = this.value.length;
    });
}

// Validação de senha
const newPasswordInput = document.getElementById('new_password');
const currentPasswordInput = document.getElementById('current_password');

if (newPasswordInput && currentPasswordInput) {
    newPasswordInput.addEventListener('input', function() {
        if (this.value && !currentPasswordInput.value) {
            currentPasswordInput.required = true;
        } else {
            currentPasswordInput.required = false;
        }
    });
}

// Máscara de telefone
const phoneInput = document.getElementById('phone');
if (phoneInput) {
    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 11) value = value.slice(0, 11);
        
        if (value.length > 10) {
            value = value.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
        } else if (value.length > 6) {
            value = value.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
        } else if (value.length > 2) {
            value = value.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
        } else if (value.length > 0) {
            value = value.replace(/^(\d*)/, '($1');
        }
        
        e.target.value = value;
    });
}
</script>

<!-- Estilos -->
<style>
/* Container principal */
.edit-profile-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 2rem 1rem;
    display: flex;
    justify-content: center;
    align-items: center;
}

.edit-profile-card {
    max-width: 900px;
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

/* Header do card */
.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2.5rem 2rem;
    text-align: center;
}

.card-title {
    font-size: 2.5rem;
    font-weight: 800;
    margin: 0 0 0.5rem 0;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.card-subtitle {
    font-size: 1rem;
    opacity: 0.95;
    margin: 0;
}

/* Alertas */
.alert {
    padding: 1rem 1.5rem;
    border-radius: 12px;
    margin: 1.5rem 2rem;
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
    border: 2px solid #10b981;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    border: 2px solid #ef4444;
}

.alert-icon {
    font-size: 1.5rem;
    flex-shrink: 0;
}

.error-list {
    margin: 0.5rem 0 0 1.25rem;
    padding: 0;
}

.error-list li {
    margin: 0.25rem 0;
}

/* Formulário */
.profile-form {
    padding: 2rem;
}

.form-section {
    margin-bottom: 2.5rem;
    padding-bottom: 2.5rem;
    border-bottom: 2px solid #f3f4f6;
}

.form-section:last-of-type {
    border-bottom: none;
}

.section-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0 0 1.5rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.section-icon {
    font-size: 1.75rem;
}

/* Avatar Upload */
.avatar-upload-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
}

.avatar-preview-wrapper {
    position: relative;
    width: 180px;
    height: 180px;
    border-radius: 50%;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.avatar-preview-wrapper:hover {
    transform: scale(1.05);
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.25);
}

.avatar-preview {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.avatar-preview-wrapper:hover .avatar-overlay {
    opacity: 1;
}

.avatar-edit-btn {
    cursor: pointer;
    color: white;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
}

.edit-icon {
    font-size: 2.5rem;
}

.edit-text {
    font-size: 0.875rem;
    font-weight: 600;
}

.avatar-input {
    display: none;
}

.avatar-hint {
    font-size: 0.875rem;
    color: #6b7280;
    text-align: center;
    margin: 0;
}

/* Grid de formulário */
.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group-full {
    grid-column: 1 / -1;
}

.form-label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.375rem;
    font-size: 0.95rem;
}

.label-icon {
    font-size: 1.1rem;
}

.required {
    color: #ef4444;
    font-weight: 700;
}

.form-input,
.form-select,
.form-textarea {
    width: 100%;
    padding: 0.875rem 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.2s ease;
    background: white;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.form-input:hover,
.form-select:hover,
.form-textarea:hover {
    border-color: #d1d5db;
}

.form-textarea {
    resize: vertical;
    min-height: 100px;
    font-family: inherit;
}

.char-counter {
    font-size: 0.875rem;
    color: #6b7280;
    text-align: right;
    margin-top: 0.5rem;
}

/* Aviso de segurança */
.security-notice {
    background: #eff6ff;
    border: 2px solid #3b82f6;
    border-radius: 12px;
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
}

.notice-icon {
    font-size: 1.5rem;
    flex-shrink: 0;
}

.security-notice p {
    margin: 0;
    color: #1e40af;
    font-weight: 500;
}

/* Botões de ação */
.form-actions {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 2px solid #f3f4f6;
}

.btn {
    padding: 1rem 2rem;
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
    font-size: 1.25rem;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
}

.btn-primary:active {
    transform: translateY(0);
}

.btn-secondary {
    background: #f3f4f6;
    color: #374151;
}

.btn-secondary:hover {
    background: #e5e7eb;
}

/* Responsividade */
@media (max-width: 768px) {
    .edit-profile-container {
        padding: 1rem;
    }

    .card-header {
        padding: 2rem 1.5rem;
    }

    .card-title {
        font-size: 2rem;
    }

    .profile-form {
        padding: 1.5rem;
    }

    .form-grid {
        grid-template-columns: 1fr;
    }

    .form-actions {
        flex-direction: column-reverse;
    }

    .btn {
        width: 100%;
        justify-content: center;
    }

    .avatar-preview-wrapper {
        width: 150px;
        height: 150px;
    }
}

@media (max-width: 480px) {
    .card-title {
        font-size: 1.75rem;
    }

    .section-title {
        font-size: 1.25rem;
    }
}
</style>
@endsection
