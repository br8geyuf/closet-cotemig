@extends('layouts.app')

@section('title', 'Login Empresa - Closet Fashion')

@section('content')
<div class="auth-card">
    <div class="auth-header">
        <img src="{{ asset('images/logo.png') }}" alt="Closet Fashion">
        <h2>Área da Empresa</h2>
        <p>Entre na sua conta empresarial</p>
    </div>

   <form method="POST" action="{{ route('company.login.store') }}">

        @csrf
        
        <div class="form-group">
            <label for="email" class="form-label">
                <i class="fas fa-envelope me-1"></i>E-mail da Empresa
            </label>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                   name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label">
                <i class="fas fa-lock me-1"></i>Senha
            </label>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                   name="password" required autocomplete="current-password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" 
                       {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember">
                    Lembrar de mim
                </label>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-block">
            <i class="fas fa-sign-in-alt me-2"></i>Entrar
        </button>

        <div class="text-center mt-3">
            <p>Ou entre com:</p>
            <a href="{{ route(\'auth.google\') }}" class="btn btn-danger btn-block">
                <i class="fab fa-google me-2"></i> Google
            </a>
        </div>
    </form>

    <div class="text-center mt-3">
        <p class="mb-0">Não tem conta empresarial? 
            <a href="{{ route('company.register') }}" class="auth-link">Cadastre sua empresa</a>
        </p>
        <p class="mt-2">
            É cliente? 
            <a href="{{ route('login') }}" class="auth-link">Acesse aqui</a>
        </p>
    </div>
</div>
@endsection
