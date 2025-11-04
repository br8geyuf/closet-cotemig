@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Criar Nova Categoria</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('categories.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Nome da Categoria</label>
            <input type="text" name="name" id="name" class="form-control" required value="{{ old('name') }}">
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Descrição</label>
            <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
        </div>

        <div class="mb-3">
            <label for="color" class="form-label">Cor (opcional)</label>
            <input type="color" name="color" id="color" class="form-control form-control-color" value="{{ old('color', '#cccccc') }}">
        </div>

        <div class="mb-3">
            <label for="icon" class="form-label">Ícone (classe FontAwesome)</label>
            <input type="text" name="icon" id="icon" class="form-control" placeholder="ex: fas fa-tshirt" value="{{ old('icon') }}">
        </div>

        <button type="submit" class="btn btn-primary">Salvar</button>
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
