@extends('layouts.app')

@section('title', 'Cadastro - Closet Fashion')

@section('content')
<div class="auth-card">

    {{-- Logo --}}
    <div class="logo text-center mb-3">
        <img src="{{ asset('images/logo.png') }}" alt="Closet Fashion" width="150">
    </div>

    {{-- ================= FORM CLIENTE ================= --}}
    <form method="POST" action="{{ route('register') }}" id="form-cliente" class="active">
        @csrf

        <h4 class="text-center mb-2">Cadastre-se como Cliente</h4>
        <p class="text-center text-muted mb-3 small">Preencha os dados abaixo para criar sua conta</p>

        {{-- Nome --}}
        <div class="mb-2 small fw-semibold">
            <i class="fa fa-user me-1"></i> Nome
        </div>
        <input type="text" name="first_name" value="{{ old('first_name') }}"
            class="form-control rounded-pill @error('first_name') is-invalid @enderror mb-3 auth-input" required>
        @error('first_name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

        {{-- Sobrenome --}}
        <div class="mb-2 small fw-semibold">
            <i class="fa fa-user me-1"></i> Sobrenome
        </div>
        <input type="text" name="last_name" value="{{ old('last_name') }}"
            class="form-control rounded-pill @error('last_name') is-invalid @enderror mb-3 auth-input" required>
        @error('last_name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

        {{-- Nome de Usuário --}}
        <div class="mb-2 small fw-semibold">
            <i class="fa fa-id-badge me-1"></i> Nome de usuário
        </div>
        <input type="text" name="name" value="{{ old('name') }}"
            class="form-control rounded-pill @error('name') is-invalid @enderror mb-3 auth-input" required>
        @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

        {{-- Email --}}
        <div class="mb-2 small fw-semibold">
            <i class="fa fa-envelope me-1"></i> E-mail
        </div>
        <input type="email" name="email" value="{{ old('email') }}"
            class="form-control rounded-pill @error('email') is-invalid @enderror mb-3 auth-input" required>
        @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

        {{-- Senha --}}
        <div class="mb-2 small fw-semibold">
            <i class="fa fa-lock me-1"></i> Senha
        </div>
        <input type="password" name="password"
            class="form-control rounded-pill @error('password') is-invalid @enderror mb-3 auth-input" required>
        @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

        {{-- Confirmar senha --}}
        <div class="mb-2 small fw-semibold">
            <i class="fa fa-lock me-1"></i> Confirmar Senha
        </div>
        <input type="password" name="password_confirmation"
            class="form-control rounded-pill mb-3 auth-input" required>

        {{-- Botão --}}
        <button type="submit" class="btn btn-primary rounded-pill w-100">
            <i class="fa fa-user-plus me-1"></i> Cadastrar
        </button>

        <p class="mt-3 text-center small">Já tem uma conta? 
            <a href="{{ route('login') }}" class="auth-link">Fazer login</a>
        </p>
    </form>

    {{-- Botão para cadastro empresa --}}
    <div class="mt-4 text-center">
        <p class="small text-muted mb-2">Não é cliente? Cadastre sua empresa aqui:</p>
        <a href="{{ route('company.register') }}" class="btn btn-outline-secondary rounded-pill px-4">
            Cadastro Empresa
        </a>
    </div>

</div>
@endsection
