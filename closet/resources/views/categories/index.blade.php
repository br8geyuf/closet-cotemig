@extends('layouts.app')

@section('title', 'Meu Armário - Closet Fashion')

@push('styles')
<style>
  body {
    background: #f4f5f7;
  }

  /* Cabeçalho */
  .page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 2rem;
  }
  .page-header h2 {
    font-weight: 700;
    color: #343a40;
  }

  /* Botão nova categoria */
  .add-category-wrapper {
    display: flex;
    align-items: center;
    gap: .6rem;
  }
  .add-category-btn {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: none;
    background: linear-gradient(135deg, #ff85a2, #ffb347);
    color: #fff;
    font-size: 28px;
    box-shadow: 0 4px 12px rgba(0,0,0,.15);
    transition: all 0.25s ease;
  }
  .add-category-btn:hover {
    transform: translateY(-3px) scale(1.05);
    background: linear-gradient(135deg, #ff7696, #ffa43c);
    box-shadow: 0 6px 14px rgba(0,0,0,.2);
  }

  .add-category-wrapper span {
    font-weight: 500;
    color: #555;
  }

  /* Cards */
  .card {
    border: none;
    border-radius: 1rem;
    transition: all 0.25s ease;
    overflow: hidden;
  }

  .card:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 20px rgba(0,0,0,.1);
  }

  .card-body {
    padding: 1.25rem;
  }

  .card-thumb {
    height: 160px;
    border-radius: .75rem;
    background: linear-gradient(145deg, #f0f0f0, #fafafa);
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px dashed #ddd;
    transition: background 0.3s ease, border 0.3s ease;
  }

  .card:hover .card-thumb {
    background: linear-gradient(145deg, #ffe8ef, #fff3e6);
    border-color: #ffc2c2;
  }

  .card h6 {
    font-weight: 600;
    color: #333;
  }

  .card .text-muted {
    color: #777 !important;
  }

  /* Responsividade */
  @media (max-width: 576px) {
    .page-header {
      flex-direction: column;
      align-items: flex-start;
      gap: 1rem;
    }
    .add-category-wrapper {
      align-self: flex-start;
    }
  }
</style>
@endpush

@section('content')
<div class="container py-4">

  <!-- Cabeçalho -->
  <div class="page-header">
    <h2>Meu Armário</h2>
    <div class="add-category-wrapper">
      <a href="{{ route('categories.create') }}" class="add-category-btn" title="Nova Categoria">+</a>
      <span>Nova Categoria</span>
    </div>
  </div>

  <!-- Grid de categorias -->
  <div class="row g-4">
    @forelse ($categories as $category)
      <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <a href="{{ route('items.index', ['category' => $category->id]) }}" class="text-decoration-none">
          <div class="card shadow-sm">
            <div class="card-body text-center">
              <div class="card-thumb mb-3">
                <i class="fa {{ $category->icon ?? 'fa-image' }} fa-2x text-muted"></i>
              </div>
              <div class="small text-muted mb-2">{{ $category->items_count ?? 0 }} itens</div>
              <h6 class="mb-1">{{ $category->name }}</h6>
              <div class="text-muted small">{{ $category->description }}</div>
            </div>
          </div>
        </a>
      </div>
    @empty
      <div class="col-12 text-center py-5">
        <i class="fa fa-folder-open fa-3x text-muted mb-3"></i>
        <p class="text-muted">Nenhuma categoria criada ainda.</p>
      </div>
    @endforelse
  </div>
</div>
@endsection
