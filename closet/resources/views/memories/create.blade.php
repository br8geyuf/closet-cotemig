@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Criar Nova Memória</h1>

    {{-- Exibir erros de validação --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('memories.store') }}" method="POST">
        @csrf

        {{-- Item relacionado --}}
        <div class="mb-3">
            <label for="item_id" class="form-label">Item</label>
            <select name="item_id" id="item_id" class="form-select" required>
                <option value="">Selecione um item...</option>
                @foreach($items as $item)
                    <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                        {{ $item->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Título --}}
        <div class="mb-3">
            <label for="title" class="form-label">Título</label>
            <input type="text" name="title" id="title" class="form-control"
                   value="{{ old('title') }}" required>
        </div>

        {{-- Conteúdo --}}
        <div class="mb-3">
            <label for="content" class="form-label">Descrição</label>
            <textarea name="content" id="content" class="form-control" rows="4" required>{{ old('content') }}</textarea>
        </div>

        {{-- Data --}}
        <div class="mb-3">
            <label for="memory_date" class="form-label">Data</label>
            <input type="date" name="memory_date" id="memory_date" class="form-control"
                   value="{{ old('memory_date') }}" required>
        </div>

        {{-- Local (opcional) --}}
        <div class="mb-3">
            <label for="location" class="form-label">Local</label>
            <input type="text" name="location" id="location" class="form-control"
                   value="{{ old('location') }}">
        </div>

        {{-- Ocasião (opcional) --}}
        <div class="mb-3">
            <label for="occasion" class="form-label">Ocasião</label>
            <select name="occasion" id="occasion" class="form-select">
                <option value="">Selecione...</option>
                <option value="casual" {{ old('occasion') == 'casual' ? 'selected' : '' }}>Casual</option>
                <option value="trabalho" {{ old('occasion') == 'trabalho' ? 'selected' : '' }}>Trabalho</option>
                <option value="festa" {{ old('occasion') == 'festa' ? 'selected' : '' }}>Festa</option>
                <option value="viagem" {{ old('occasion') == 'viagem' ? 'selected' : '' }}>Viagem</option>
                <option value="especial" {{ old('occasion') == 'especial' ? 'selected' : '' }}>Especial</option>
                <option value="outro" {{ old('occasion') == 'outro' ? 'selected' : '' }}>Outro</option>
            </select>
        </div>

        {{-- Rating (opcional) --}}
        <div class="mb-3">
            <label for="rating" class="form-label">Avaliação</label>
            <input type="number" name="rating" id="rating" class="form-control" min="1" max="5"
                   value="{{ old('rating') }}">
        </div>

        {{-- Favorito --}}
        <div class="form-check mb-3">
            <input type="checkbox" name="is_favorite" id="is_favorite" class="form-check-input"
                   value="1" {{ old('is_favorite') ? 'checked' : '' }}>
            <label for="is_favorite" class="form-check-label">Marcar como favorita</label>
        </div>

        {{-- Botões --}}
        <button type="submit" class="btn btn-success">Salvar</button>
        <a href="{{ route('memories.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
