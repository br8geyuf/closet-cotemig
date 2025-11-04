@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detalhes da Memória</h1>

    <div class="card mt-3">
        <div class="card-header">
            <strong>{{ $memory->title }}</strong>
        </div>
        <div class="card-body">
            {{-- Item relacionado --}}
            <p><strong>Item:</strong> {{ $memory->item->name ?? 'Não informado' }}</p>

            {{-- Usuário --}}
            <p><strong>Cadastrada por:</strong> {{ $memory->user->name ?? 'Usuário desconhecido' }}</p>

            {{-- Conteúdo --}}
            <p><strong>Descrição:</strong></p>
            <p>{{ $memory->content }}</p>

            {{-- Data --}}
            <p><strong>Data da memória:</strong> {{ $memory->memory_date->format('d/m/Y') }}</p>

            {{-- Local --}}
            @if($memory->location)
                <p><strong>Local:</strong> {{ $memory->location }}</p>
            @endif

            {{-- Ocasião --}}
            @if($memory->occasion)
                <p><strong>Ocasião:</strong> {{ ucfirst($memory->occasion) }}</p>
            @endif

            {{-- Avaliação --}}
            @if($memory->rating)
                <p><strong>Avaliação:</strong> ⭐ {{ $memory->rating }}/5</p>
            @endif

            {{-- Favorito --}}
            <p>
                <strong>Favorita:</strong>
                @if($memory->is_favorite)
                    <span class="badge bg-success">Sim</span>
                @else
                    <span class="badge bg-secondary">Não</span>
                @endif
            </p>

            {{-- Tags --}}
            @if(!empty($memory->tags))
                <p><strong>Tags:</strong>
                    @foreach($memory->tags as $tag)
                        <span class="badge bg-info text-dark">{{ $tag }}</span>
                    @endforeach
                </p>
            @endif

            {{-- Fotos --}}
            @if(!empty($memory->photos))
                <div class="mt-3">
                    <strong>Fotos:</strong>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        @foreach($memory->photos as $photo)
                            <img src="{{ asset('storage/'.$photo) }}" alt="Foto da memória" class="img-thumbnail" style="max-width: 150px;">
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
        <div class="card-footer">
            <a href="{{ route('memories.edit', $memory->id) }}" class="btn btn-warning">Editar</a>
            <form action="{{ route('memories.destroy', $memory->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger"
                        onclick="return confirm('Tem certeza que deseja excluir esta memória?')">Excluir</button>
            </form>
            <a href="{{ route('memories.index') }}" class="btn btn-secondary">Voltar</a>
        </div>
    </div>
</div>
@endsection
