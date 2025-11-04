@extends('layouts.app')

@section('title', 'Esqueci minha senha - Empresa')

@section('content')
<div class="auth-card">
    <div class="auth-header">
        <img src="{{ asset('images/logo.png') }}" alt="Closet Fashion" width="150">
        <h2>Esqueceu sua senha?</h2>
        <p>Digite o e-mail da empresa para redefinir a senha.</p>
    </div>

    <form action="{{ route('company.password.email') }}" method="POST">
        @csrf
        <input type="email" name="email" placeholder="E-mail da Empresa"
               value="{{ old('email') }}" required autofocus>
        <button type="submit">Enviar link de recuperação</button>
    </form>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="auth-footer">
        <a href="{{ route('company.login') }}">Voltar ao login</a>
    </div>
</div>
@endsection
