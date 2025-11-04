@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Memórias</h1>

    <a href="{{ route('memories.create') }}" class="btn btn-primary mb-3">+ Nova Memória</a>

    {{-- Mensagem de sucesso --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Tabela --}}
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Título</th>
                <th>Data</th>
                <th>Favorita</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($memories as $memory)
                <tr>
                    <td>{{ $memory->title }}</td>
                    <td>{{ \Carbon\Carbon::parse($memory->memory_date)->format('d/m/Y') }}</td>
                    <td>
                        @if($memory->is_favorite)
                            <span class="badge bg-warning text-dark">⭐ Sim</span>
                        @else
                            <span class="badge bg-secondary">Não</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('memories.show', $memory->id) }}" class="btn btn-info btn-sm">Ver</a>
                        <a href="{{ route('memories.edit', $memory->id) }}" class="btn btn-warning btn-sm">Editar</a>
                        <form action="{{ route('memories.destroy', $memory->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('Tem certeza que deseja excluir esta memória?')">
                                Excluir
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Nenhuma memória cadastrada.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
