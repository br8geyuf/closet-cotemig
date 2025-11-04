@extends('layouts.app')

@section('title', 'Lojas Parceiras - Closet Fashion')
@section('page-title', 'Lojas Parceiras')

@section('content')
<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Lojas Parceiras</h1>
        <a href="{{ route('stores.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nova Loja
        </a>
    </div>

    @if($stores->count() > 0)
        <div class="items-grid">
            @foreach($stores as $store)
                <div class="item-card">
                    <div class="item-image">
                        <i class="fas fa-store fa-3x"></i>
                    </div>
                    
                    <h5 class="mb-2">{{ $store->name }}</h5>
                    
                    @if($store->description)
                        <p class="text-muted small mb-2">{{ Str::limit($store->description, 80) }}</p>
                    @endif
                    
                    <div class="mb-2">
                        <span class="badge bg-secondary">{{ ucfirst($store->type) }}</span>
                        @if($store->city)
                            <span class="badge bg-info">{{ $store->city }}</span>
                        @endif
                    </div>
                    
                    <div class="d-flex gap-2">
                        @if($store->website)
                            <a href="{{ $store->website }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-globe"></i>
                            </a>
                        @endif
                        
                        @if($store->phone)
                            <a href="tel:{{ $store->phone }}" class="btn btn-sm btn-outline-success">
                                <i class="fas fa-phone"></i>
                            </a>
                        @endif
                        
                        <a href="{{ route('stores.show', $store) }}" class="btn btn-sm btn-outline-dark">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $stores->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-store fa-4x text-muted mb-3"></i>
            <h4>Nenhuma loja cadastrada</h4>
            <p class="text-muted">Cadastre sua primeira loja parceira para começar.</p>
            <a href="{{ route('stores.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Cadastrar Primeira Loja
            </a>
        </div>
    @endif
</div>
@endsection

