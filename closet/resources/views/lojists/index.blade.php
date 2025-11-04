@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Lojistas</h1>

    <a href="{{ route('lojists.create') }}" class="btn btn-primary mb-3">
        + Novo Lojista
    </a>

    @if($lojists->isEmpty())
        <div class="alert alert-info">
            Nenhum lojista cadastrado ainda.
        </div>
    @else
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Telefone</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lojists as $lojist)
                    <tr>
                        <td>{{ $lojist->id }}</td>
                        <td>{{ $lojist->name }}</td>
                        <td>{{ $lojist->email ?? '-' }}</td>
                        <td>{{ $lojist->phone ?? '-' }}</td>
                        <td class="text-center">
                            <a href="{{ route('lojists.show', $lojist->id) }}" class="btn btn-info btn-sm">Ver</a>
                            <a href="{{ route('lojists.edit', $lojist->id) }}" class="btn btn-warning btn-sm">Editar</a>
                            <form action="{{ route('lojists.destroy', $lojist->id) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Tem certeza que deseja excluir este lojista?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
