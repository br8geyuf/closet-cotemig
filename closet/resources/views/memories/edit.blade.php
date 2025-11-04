@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Memória</h1>

    <form action="{{ route('memories.update', $memory->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Item --}}
        <div class="form-group">
            <label for="item_id">Item</label>
            <select name="item_id" id="item_id" class="form-control" required>
                <option value="">Selecione um item</option>
                @foreach ($items as $item)
                    <option value="{{ $item->id }}" {{ $memory->item_id == $item->id ? 'selected' : '' }}>
                        {{ $item->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Título --}}
        <div class="form-group mt-2">
            <label for="title">Título</label>
            <input type="text" name="title" id="title" class="form-control"
                   value="{{ old('title', $memory->title) }}" required>
        </div>

        {{-- Conteúdo --}}
        <div class="form-group mt-2">
            <label for="content">Descrição</label>
            <textarea name="content" id="content" class="form-control" rows="4" required>{{ old('content', $memory->content) }}</textarea>
        </div>

        {{-- Data --}}
        <div class="form-group mt-2">
            <label for="memory_date">Data</label>
            <input type="date" name="memory_date" id="memory_date" class="form-control"
                   value="{{ old('memory_date', $memory->memory_date->format('Y-m-d')) }}" required>
        </div>

        {{-- Local --}}
        <div class="form-group mt-2">
            <label for="location">Local</label>
            <input type="text" name="location" id="location" class="form-control"
                   value="{{ old('location', $memory->location) }}">
        </div>

        {{-- Ocasião --}}
        <div class="form-group mt-2">
            <label for="occasion">Ocasião</label>
            <select name="occasion" id="occasion" class="form-control">
                <option value="">Selecione...</option>
                @foreach (['casual','trabalho','festa','viagem','especial','outro'] as $option)
                    <option value="{{ $option }}" {{ $memory->occasion == $option ? 'selected' : '' }}>
                        {{ ucfirst($option) }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Tags --}}
        <div class="form-group mt-2">
            <label for="tags">Tags (separadas por vírgula)</label>
            <input type="text" name="tags" id="tags" class="form-control"
                   value="{{ old('tags', implode(',', $memory->tags ?? [])) }}">
        </div>

        {{-- Rating --}}
        <div class="form-group mt-2">
            <label for="rating">Avaliação (1 a 5)</label>
            <input type="number" name="rating" id="rating" class="form-control"
                   value="{{ old('rating', $memory->rating) }}" min="1" max="5">
        </div>

        {{-- Favorito --}}
        <div class="form-check mt-2">
            <input type="checkbox" name="is_favorite" id="is_favorite" class="form-check-input"
                   {{ old('is_favorite', $memory->is_favorite) ? 'checked' : '' }}>
            <label for="is_favorite" class="form-check-label">Favorito</label>
        </div>

        {{-- Fotos --}}
        <div class="form-group mt-3">
            <label for="photos">Fotos</label>
            <input type="file" name="photos[]" id="photos" class="form-control" multiple>
        </div>

        {{-- Mostrar fotos existentes --}}
        @if ($memory->photos && count($memory->photos) > 0)
            <div class="mt-2">
                <p>Fotos atuais:</p>
                <div class="d-flex flex-wrap">
                    @foreach ($memory->photos as $photo)
                        <img src="{{ asset('storage/' . $photo) }}" alt="Foto" class="img-thumbnail m-1" width="120">
                    @endforeach
                </div>
            </div>
        @endif

        <button type="submit" class="btn btn-success mt-3">Salvar Alterações</button>
        <a href="{{ route('memories.index') }}" class="btn btn-secondary mt-3">Cancelar</a>
    </form>
</div>
@endsection
