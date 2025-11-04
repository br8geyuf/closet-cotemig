@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detalhes do Lojista</h1>

    <p><strong>ID:</strong> {{ $lojist->id }}</p>
    <p><strong>Nome:</strong> {{ $lojist->name }}</p>
    <p><strong>Email:</strong> {{ $lojist->email ?? 'Não informado' }}</p>
    <p><strong>Telefone:</strong> {{ $lojist->phone ?? 'Não informado' }}</p>

    <a href="{{ route('lojists.index') }}" class="btn btn-secondary">Voltar</a>
</div>
@endsection
