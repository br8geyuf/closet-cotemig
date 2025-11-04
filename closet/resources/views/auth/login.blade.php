@extends('layouts.app')

@section('title', 'Login Cliente - Closet Fashion')

@section('content')
<div class="d-flex justify-content-center align-items-center" style="min-height: 100vh; background:#c2bcbc;">
    <div class="auth-card p-4 shadow" style="max-width: 380px; width: 100%; border-radius: 15px; background: #fff;">
        
        {{-- Logo --}}
        <div class="logo text-center mb-3">
            <img src="{{ asset('images/logo.png') }}" alt="Closet Fashion" width="150">
        </div>

        {{-- Abas Cliente / Empresa --}}
        <div class="tabs d-flex justify-content-center mb-4">
            <div class="tab active px-3 py-2" data-tab="cliente">Cliente</div>
            <div class="tab px-3 py-2" data-tab="empresa">Empresa</div>
        </div>

        {{-- FORM LOGIN CLIENTE --}}
        <form method="POST" action="{{ route('login') }}" id="form-cliente" class="tab-content active">
            @csrf
            <input id="email" type="email" name="email" placeholder="E-mail" required autofocus>
            <input id="password" type="password" name="password" placeholder="Senha" required>

            <div class="d-flex justify-content-between mb-2">
                <a href="{{ route('senha.form') }}">Esqueci minha senha</a>
            </div>

            <button type="submit" class="btn btn-primary w-100">Entrar</button>

            <div class="mt-3 text-center">
                <p>Ou entre com:</p>
                <a href="{{ route('auth.google') }}" class="btn btn-danger w-100">
                    <i class="fab fa-google me-2"></i> Google
                </a>
            </div>

            <div class="auth-footer text-center mt-3">
                <a href="{{ route('register') }}">Ir para cadastro de Cliente</a>
            </div>
        </form>

        {{-- FORM LOGIN EMPRESA --}}
        <form method="POST" action="{{ route('company.login.store') }}" id="form-empresa" class="tab-content">
            @csrf
            <input id="email_empresa" type="email" name="email" placeholder="E-mail da empresa" required>
            <input id="password_empresa" type="password" name="password" placeholder="Senha da empresa" required>

            <div class="d-flex justify-content-between mb-2">
                <a href="{{ route('senha.form') }}">Esqueci minha senha</a>
            </div>

            <button type="submit" class="btn btn-primary w-100">Entrar</button>

            <div class="mt-3 text-center">
                <p>Ou entre com:</p>
                <a href="{{ route('auth.google') }}" class="btn btn-danger w-100">
                    <i class="fab fa-google me-2"></i> Google
                </a>
            </div>

            <div class="auth-footer text-center mt-3">
                <a href="{{ route('company.register') }}">Ir para cadastro de Empresa</a>
            </div>
        </form>
    </div>
</div>

{{-- CSS extra --}}
<style>
    .auth-card input {
        width: 100%;
        padding: 10px 15px;
        margin-bottom: 12px;
        border-radius: 30px;
        border: 1px solid #ccc;
        background: #f1f5ff;
        outline: none;
    }

    .auth-card input:focus {
        border-color: #006bff;
        box-shadow: 0 0 4px rgba(0, 107, 255, 0.5);
    }

    .btn-primary {
        background-color: #006bff;
        border: none;
        border-radius: 30px;
        padding: 10px;
        font-weight: 500;
    }

    .btn-primary:hover {
        background-color: #0056cc;
    }

    .tabs .tab {
        cursor: pointer;
        flex: 1;
        text-align: center;
        border-radius: 20px 20px 0 0;
        background: #e0e0e0;
        font-weight: 500;
    }

    .tabs .tab.active {
        background: #006bff;
        color: white;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }
</style>

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
