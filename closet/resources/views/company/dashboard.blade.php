@extends('layouts.app')

@section('title')
    {{ auth()->guard('company')->check() ? 'Dashboard da Empresa - Closet Fashion' : 'Dashboard - Closet Fashion' }}
@endsection

@section('content')
<div class="dashboard-container container py-4">

    {{-- Header --}}
    <header class="dashboard-header text-center mb-4">
        <h1>Olá, {{ auth()->user()->name ?? auth('company')->user()->name }} 👋</h1>
        <p>Bem-vindo ao painel {{ auth()->guard('company')->check() ? 'da sua empresa' : 'do usuário' }} no Closet Fashion</p>
    </header>

    {{-- Logout --}}
    <form 
        action="{{ auth()->guard('web')->check() ? route('logout') : route('company.logout') }}" 
        method="POST" 
        class="d-inline mb-4"
    >
        @csrf
        <button type="submit" class="btn btn-outline-danger">
            🚪 Sair
        </button>
    </form>

    {{-- Estatísticas principais --}}
    <div class="row g-3 mb-5">
        @if(auth()->guard('company')->check())
            @php
                $stats = [
                    ['icon' => 'fas fa-tshirt', 'color' => 'text-primary', 'count' => $itemsCount ?? 0, 'label' => 'Peças cadastradas'],
                    ['icon' => 'fas fa-tags', 'color' => 'text-success', 'count' => $promotionsCount ?? 0, 'label' => 'Promoções ativas'],
                    ['icon' => 'fas fa-store', 'color' => 'text-warning', 'count' => $salesCount ?? 0, 'label' => 'Vendas registradas'],
                ];
            @endphp

            @foreach($stats as $stat)
                <div class="col-md-4">
                    <div class="card stat-card shadow-sm">
                        <div class="card-body text-center">
                            <i class="{{ $stat['icon'] }} fa-2x mb-2 {{ $stat['color'] }}"></i>
                            <h5 class="fw-bold">{{ $stat['count'] }}</h5>
                            <p class="text-muted">{{ $stat['label'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="card stat-card shadow-sm text-center p-4">
                    <h5 class="fw-bold mb-2">Bem-vindo ao seu dashboard!</h5>
                    <p class="text-muted mb-0">Acompanhe seu armário e lista de compras.</p>
                </div>
            </div>
        @endif
    </div>

    {{-- Gráficos e relatórios --}}
    @if(auth()->guard('company')->check())
    <div class="row mb-5">
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">Top 5 peças mais vendidas</h6>
                </div>
                <div class="card-body">
                    <canvas id="topItemsChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">Promoções recentes</h6>
                </div>
                <div class="card-body">
                    @if(!empty($recentPromotions) && $recentPromotions->count())
                        <ul class="list-group list-group-flush">
                            @foreach($recentPromotions as $promo)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $promo->title }}
                                    <span class="badge {{ $promo->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $promo->is_active ? 'Ativa' : 'Inativa' }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted mb-0">Nenhuma promoção recente.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Ações rápidas --}}
    <section class="quick-actions mb-5">
        <h4 class="mb-3">Ações rápidas</h4>
        <div class="d-flex flex-wrap gap-3">
            @if(auth()->guard('company')->check())
                <a href="{{ route('items.create') }}" class="btn btn-outline-primary">
                    <i class="fas fa-plus-circle me-1"></i> Adicionar peça
                </a>
                <a href="{{ route('promotions.create') }}" class="btn btn-outline-success">
                    <i class="fas fa-bullhorn me-1"></i> Criar promoção
                </a>
                <a href="{{ route('stores.index') }}" class="btn btn-outline-warning">
                    <i class="fas fa-store me-1"></i> Gerenciar loja
                </a>
                <a href="{{route('company.profile.edit')}}" class="btn btn-outline-info">
                    <i class="fas fa-user-cog me-1"></i> Editar perfil
                </a>
            @else
                <a href="{{ route('wardrobe.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-tshirt me-1"></i> Meu Armário
                </a>
                <a href="{{ route('shopping-list.index') }}" class="btn btn-outline-success">
                    <i class="fas fa-list me-1"></i> Lista de Compras
                </a>
            @endif
        </div>
    </section>

    {{-- Notícias / dicas --}}
    <div class="card shadow-sm mb-5">
        <div class="card-header">
            <h6 class="mb-0">Dicas e novidades do Closet Fashion</h6>
        </div>
        <div class="card-body">
            <p class="text-muted mb-0">Aqui você poderá acompanhar novidades sobre gestão de produtos, promoções e vendas, além de dicas para aumentar seu faturamento.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @if(auth()->guard('company')->check())
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ctx = document.getElementById('topItemsChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($topItemsLabels ?? []),
                    datasets: [{
                        label: 'Vendas',
                        data: @json($topItemsData ?? []),
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        title: { display: true, text: 'Top 5 peças mais vendidas' }
                    },
                    scales: { y: { beginAZero: true, precision: 0 } }
                }
            });
        </script>
    @endif
@endsection
