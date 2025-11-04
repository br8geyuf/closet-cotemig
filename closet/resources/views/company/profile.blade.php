@extends('layouts.app')

@section('title', 'Editar Perfil - Empresa')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Editar Perfil da Empresa</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Informações da Empresa</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('company.profile.update') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Nome da Empresa</label>
                    <input type="text" name="name" id="name" class="form-control" 
                        value="{{ old('name', $company->name) }}" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" name="email" id="email" class="form-control" 
                        value="{{ old('email', $company->email) }}" required>
                </div>

                <div class="mb-3">
                    <label for="cnpj" class="form-label">CNPJ</label>
                    <input type="text" name="cnpj" id="cnpj" class="form-control" 
                        value="{{ old('cnpj', $company->cnpj) }}" required>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Salvar Alterações
                </button>
                <a href="{{ route('company.dashboard') }}" class="btn btn-secondary ms-2">
                    <i class="fas fa-arrow-left me-1"></i> Voltar ao Dashboard
                </a>
            </form>
        </div>
    </div>
</div>
@endsection
