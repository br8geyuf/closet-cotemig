@extends('layouts.app')

@section('content')
<div class="container mt-5" style="max-width: 500px;">
    <h2 class="mb-4">Esqueci minha senha</h2>

    <!-- Mensagem de sucesso -->
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <!-- Erros -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $erro)
                    <li>{{ $erro }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Formulário -->
    <form method="POST" action="{{ route('senha.enviar') }}">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">Digite seu e-mail cadastrado:</label>
            <input type="email" class="form-control" name="email" required autofocus>
        </div>
        <button type="submit" class="btn btn-primary w-100">Enviar link de redefinição</button>
    </form>
</div>
@endsection
