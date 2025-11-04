@extends('layouts.app')

@section('title', 'Cadastro de Empresa - Closet Fashion')

@section('content')
<div class="auth-card">
    <div class="logo text-center mb-3">
        <img src="{{ asset('images/logo.png') }}" alt="Closet Fashion" width="150">
    </div>
    <div class="auth-header">
        <h2>Cadastre sua empresa</h2>
        <p>Preencha os dados abaixo para criar sua conta de lojista</p>
    </div>

    <form method="POST" action="{{ route('company.register.store') }}">
        @csrf

        <div class="form-group">
            <label for="name" class="form-label">
                <i class="fas fa-building me-1"></i>Nome da Empresa
            </label>
            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                   name="name" value="{{ old('name') }}" required autofocus>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="cnpj" class="form-label">
                <i class="fas fa-id-card me-1"></i>CNPJ
            </label>
            <input id="cnpj" type="text" class="form-control @error('cnpj') is-invalid @enderror" 
                   name="cnpj" value="{{ old('cnpj') }}" required>
            @error('cnpj')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="email" class="form-label">
                <i class="fas fa-envelope me-1"></i>E-mail
            </label>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                   name="email" value="{{ old('email') }}" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label">
                <i class="fas fa-lock me-1"></i>Senha
            </label>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                   name="password" required>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="form-label">
                <i class="fas fa-lock me-1"></i>Confirmar Senha
            </label>
            <input id="password_confirmation" type="password" class="form-control" 
                   name="password_confirmation" required>
        </div>

        <button type="submit" class="btn btn-primary btn-block">
            <i class="fas fa-user-plus me-2"></i>Cadastrar
        </button>
    </form>

   {{-- Botão para cadastro Cliente --}}
   <div class="mt-4 text-center">
    <p class="small text-muted mb-2">Não é Empresa? Cadastre como cliente aqui:</p>
    <a href="{{ route('register') }}" class="btn btn-outline-secondary rounded-pill px-4">
        Cadastro Cliente
    </a>
</div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
    $(document).ready(function(){
        $('#cnpj').mask('00.000.000/0000-00'); // Máscara CNPJ
    });
</script>
@endsection
