@extends('layouts.app')

@section('title', 'Esqueci minha senha - Closet Fashion')

@section('content')
<div class="auth-card">
    <div class="auth-header">
        <img src="{{ asset('images/logo.png') }}" alt="Closet Fashion" width="150">
        <h2>Esqueceu sua senha?</h2>
        <p>Digite seu e-mail para redefinir a senha.</p>
    </div>

    {{-- Abas Cliente / Empresa --}}
    <div class="tabs">
        <div class="tab active" data-tab="cliente">Cliente</div>
        <div class="tab" data-tab="empresa">Empresa</div>
    </div>

    {{-- FORM RECUPERAÇÃO CLIENTE --}}
    <form action="{{ route('password.email') }}" method="POST" id="form-cliente" class="tab-content active">
        @csrf
        <input type="email" name="email" placeholder="E-mail do Cliente" value="{{ old('email') }}" required autofocus>
        <button type="submit" class="btn-primary">Enviar link de recuperação</button>
    </form>

    {{-- FORM RECUPERAÇÃO EMPRESA --}}
    <form action="{{ route('company.password.email') }}" method="POST" id="form-empresa" class="tab-content">
        @csrf
        <input type="email" name="email" placeholder="E-mail da Empresa" value="{{ old('email') }}" required>
        <button type="submit" class="btn-primary">Enviar link de recuperação</button>
    </form>

    {{-- ALERTAS --}}
    @if (session('status'))
        <div class="alert alert-success mt-3">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger mt-3">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="auth-footer">
        <a href="{{ route('login') }}">Voltar ao login</a>
    </div>
</div>

{{-- Script para alternar abas --}}
<script>
    document.querySelectorAll('.tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            document.querySelector('#form-' + tab.dataset.tab).classList.add('active');
        });
    });
</script>
@endsection
