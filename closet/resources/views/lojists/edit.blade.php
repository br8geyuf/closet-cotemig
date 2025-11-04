@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Lojista</h1>

    <form action="{{ route('lojists.update', $lojist->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Nome *</label>
            <input type="text" name="name" id="name" class="form-control" required value="{{ old('name', $lojist->name) }}">
            @error('name') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $lojist->email) }}">
            @error('email') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Telefone</label>
            <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $lojist->phone) }}">
            @error('phone') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-primary">Atualizar</button>
        <a href="{{ route('lojists.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
