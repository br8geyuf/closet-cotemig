@extends('layouts.app')

@section('title', 'Meu Closet - Closet Fashion (Categoria)')

@section('content')
<div class="container py-4">

    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <!-- Botão Voltar (aparece só quando filtrado por categoria) -->
        @if(request()->has('category_id'))
            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary me-3">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        @endif

        <h1 class="h4 mb-0 flex-grow-1 text-center">
            <i class="fas fa-tshirt text-primary me-2"></i>Meu Closet
            <span class="badge bg-secondary ms-2">{{ $items->count() }} itens</span>
        </h1>

        <a href="{{ route('items.create') }}" class="btn btn-primary ms-3">
            <i class="fas fa-plus me-2"></i>Adicionar Item
        </a>
    </div>

    <!-- Filtros -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('items.index') }}" id="filterForm">
                <div class="row g-3">
                    <!-- Busca -->
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="🔍 Buscar..." value="{{ $filters['search'] ?? '' }}">
                    </div>

                    <!-- Categoria -->
                    <div class="col-md-2">
                        <select name="category_id" class="form-select">
                            <option value="">Categoria</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ ($filters['category_id'] ?? '') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Temporada -->
                    <div class="col-md-2">
                        <select name="season" class="form-select">
                            <option value="">Temporada</option>
                            <option value="primavera" {{ ($filters['season'] ?? '') == 'primavera' ? 'selected' : '' }}>Primavera</option>
                            <option value="verao" {{ ($filters['season'] ?? '') == 'verao' ? 'selected' : '' }}>Verão</option>
                            <option value="outono" {{ ($filters['season'] ?? '') == 'outono' ? 'selected' : '' }}>Outono</option>
                            <option value="inverno" {{ ($filters['season'] ?? '') == 'inverno' ? 'selected' : '' }}>Inverno</option>
                        </select>
                    </div>

                    <!-- Ocasião -->
                    <div class="col-md-2">
                        <select name="occasion" class="form-select">
                            <option value="">Ocasião</option>
                            <option value="casual" {{ ($filters['occasion'] ?? '') == 'casual' ? 'selected' : '' }}>Casual</option>
                            <option value="trabalho" {{ ($filters['occasion'] ?? '') == 'trabalho' ? 'selected' : '' }}>Trabalho</option>
                            <option value="festa" {{ ($filters['occasion'] ?? '') == 'festa' ? 'selected' : '' }}>Festa</option>
                            <option value="esporte" {{ ($filters['occasion'] ?? '') == 'esporte' ? 'selected' : '' }}>Esporte</option>
                            <option value="formal" {{ ($filters['occasion'] ?? '') == 'formal' ? 'selected' : '' }}>Formal</option>
                        </select>
                    </div>

                    <!-- Condição -->
                    <div class="col-md-2">
                        <select name="condition" class="form-select">
                            <option value="">Condição</option>
                            <option value="novo" {{ ($filters['condition'] ?? '') == 'novo' ? 'selected' : '' }}>Novo</option>
                            <option value="usado_excelente" {{ ($filters['condition'] ?? '') == 'usado_excelente' ? 'selected' : '' }}>Usado - Excelente</option>
                            <option value="usado_bom" {{ ($filters['condition'] ?? '') == 'usado_bom' ? 'selected' : '' }}>Usado - Bom</option>
                            <option value="usado_regular" {{ ($filters['condition'] ?? '') == 'usado_regular' ? 'selected' : '' }}>Usado - Regular</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <!-- Marca -->
                    <div class="col-md-3">
                        <input type="text" name="brand" class="form-control" placeholder="Marca" value="{{ $filters['brand'] ?? '' }}">
                    </div>

                    <!-- Preço -->
                    <div class="col-md-2">
                        <input type="number" name="price_min" step="0.01" class="form-control" placeholder="Preço Mín." value="{{ $filters['price_min'] ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="price_max" step="0.01" class="form-control" placeholder="Preço Máx." value="{{ $filters['price_max'] ?? '' }}">
                    </div>

                    <!-- Botões -->
                    <div class="col-md-5 d-flex">
                        <button type="submit" class="btn btn-primary me-2 flex-fill">
                            <i class="fas fa-filter me-1"></i>Filtrar
                        </button>
                        <a href="{{ route('items.index') }}" class="btn btn-outline-secondary flex-fill">
                            <i class="fas fa-times me-1"></i>Limpar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Itens -->
    @if($items->count() > 0)
        <div class="row">
            @foreach($items as $item)
                <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                    <div class="card h-100 shadow-sm">
                        
                        <!-- Imagem -->
                        @if($item->images && count($item->images) > 0)
                            <img src="{{ asset('storage/' . $item->images[0]) }}" 
                                 alt="{{ $item->name }}" 
                                 class="card-img-top" 
                                 style="object-fit: cover; height: 200px;">
                        @else
                            <div class="d-flex align-items-center justify-content-center bg-light" style="height: 200px;">
                                <i class="fas fa-tshirt fa-3x text-muted"></i>
                            </div>
                        @endif

                        <!-- Detalhes -->
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title mb-1">{{ $item->name }}</h6>
                            <p class="text-muted small mb-1">{{ $item->category->name ?? 'Sem categoria' }}</p>
                            @if($item->brand)
                                <p class="text-muted small mb-1">{{ $item->brand }}</p>
                            @endif
                            @if($item->purchase_price)
                                <p class="text-success fw-bold mb-2">
                                    R$ {{ number_format($item->purchase_price, 2, ',', '.') }}
                                </p>
                            @endif
                            
                            <p class="text-muted small mb-2">
                                <i class="fas fa-calendar me-1"></i>{{ ucfirst($item->season) }} | 
                                <i class="fas fa-star me-1"></i>{{ ucfirst(str_replace('_', ' ', $item->condition)) }}
                            </p>

                            <!-- Botões -->
                            <div class="mt-auto d-flex justify-content-between">
                                <small class="text-muted">Usado {{ $item->usage_count }}x</small>
                                <form action="{{ route('items.increment-usage', $item->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Estado vazio -->
        <div class="text-center py-5">
            <i class="fas fa-tshirt fa-5x text-muted mb-4"></i>
            <h4 class="text-muted">Nenhum item encontrado</h4>
            <p class="text-muted">
                @if(!empty($filters))
                    Ajuste os filtros ou 
                    <a href="{{ route('items.index') }}" class="text-decoration-none">limpe a busca</a>.
                @else
                    Comece adicionando seu primeiro item ao closet.
                @endif
            </p>
            <a href="{{ route('items.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Adicionar Item
            </a>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
    // Submete o filtro automaticamente ao trocar selects
    document.querySelectorAll('#filterForm select').forEach(select => {
        select.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    });
</script>
@endpush
