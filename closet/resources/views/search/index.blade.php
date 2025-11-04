@extends('layouts.app')

@section('title', 'Buscar Itens')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Filtros laterais -->
        <div class="col-md-3 col-lg-2 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <strong>Filtros</strong>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('search.items') }}">
                        <input type="hidden" name="query" value="{{ request('query') }}">

                        <!-- Categoria -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Categoria</label>
                            <select class="form-select" name="category">
                                <option value="">Todas</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Loja -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Loja</label>
                            <select class="form-select" name="store">
                                <option value="">Todas</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}" {{ request('store') == $store->id ? 'selected' : '' }}>
                                        {{ $store->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Faixa de Preço -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Preço</label>
                            <div class="input-group">
                                <input type="number" name="min_price" class="form-control" placeholder="Mín" value="{{ request('min_price') }}">
                                <input type="number" name="max_price" class="form-control" placeholder="Máx" value="{{ request('max_price') }}">
                            </div>
                        </div>

                        <!-- Frete grátis -->
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" name="free_shipping" id="free_shipping"
                                   {{ request('free_shipping') ? 'checked' : '' }}>
                            <label for="free_shipping" class="form-check-label">Frete Grátis</label>
                        </div>

                        <button type="submit" class="btn btn-dark w-100">
                            <i class="fa fa-filter me-1"></i> Aplicar Filtros
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Resultados -->
        <div class="col-md-9 col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">
                    Resultados {{ $query ? "para \"$query\"" : '' }}
                </h4>
                <span class="text-muted">{{ $items->total() }} itens encontrados</span>
            </div>

            @if($items->isEmpty())
                <div class="alert alert-secondary text-center">
                    Nenhum produto encontrado 😢
                </div>
            @else
                <div class="row">
                    @foreach($items as $item)
                        <div class="col-6 col-md-4 col-lg-3 mb-4">
                            <div class="card h-100 shadow-sm border-0">
                                <img src="{{ $item->image_url ?? asset('images/no-image.png') }}"
                                     class="card-img-top" alt="{{ $item->name }}"
                                     style="height:180px; object-fit:cover;">
                                <div class="card-body">
                                    <h6 class="card-title text-truncate">{{ $item->name }}</h6>
                                    <p class="text-muted small mb-1">{{ $item->store->name ?? 'Loja desconhecida' }}</p>
                                    <p class="fw-bold text-warning mb-1">R$ {{ number_format($item->price, 2, ',', '.') }}</p>
                                    @if($item->free_shipping)
                                        <span class="badge bg-success">Frete Grátis</span>
                                    @endif
                                </div>
                                <div class="card-footer bg-transparent border-0">
                                    <a href="{{ route('items.show', $item->id) }}" class="btn btn-outline-dark w-100 btn-sm">
                                        Ver Detalhes
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Paginação -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $items->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
