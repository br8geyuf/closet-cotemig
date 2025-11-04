@extends('layouts.app')

@section('title', 'Redefinir Senha - Empresa')

@section('content')
<div class="auth-card">
    <div class="auth-header">
        <img src="{{ asset('images/logo.png') }}" alt="Closet Fashion" width="150">
        <h2>Redefinir Senha</h2>
    </div>

    <form method="POST" action="{{ route('company.password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <input type="email" name="email" value="{{ old('email') }}"
               placeholder="E-mail da Empresa" required autofocus>

        <input type="password" name="password" placeholder="Nova senha" required>
        <input type="password" name="password_confirmation" placeholder="Confirme a senha" required>

        <button type="submit">Redefinir Senha</button>
    </form>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
@endsection
