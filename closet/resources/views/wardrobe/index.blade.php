@extends('layouts.app')

@section('title', 'Meu Armário - Closet Fashion')

@push('styles')
<style>
  :root {
    --primary: #6366f1;
    --light-gray: #f8f9fa;
    --text-muted: #6c757d;
  }

  .add-category-fab {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 1000;
    width: 56px; height: 56px;
    background-color: var(--primary);
    border-radius: 50%;
    color: white;
    font-size: 28px;
    border: none;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    transition: 0.3s;
  }

  .add-category-fab:hover {
    background-color: #4f46e5;
    transform: scale(1.05);
  }

  .category-card {
    transition: 0.3s;
  }

  .category-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.1);
  }

  .card-thumb {
    height: 140px;
    background: var(--light-gray);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: .5rem;
    margin-bottom: 1rem;
  }

  .editable-name {
    border-bottom: 1px dashed transparent;
    padding: 2px 4px;
    border-radius: 4px;
  }

  .editable-name:hover {
    background: #f1f3f5;
    border-bottom-color: #ccc;
    cursor: text;
  }

  .stat-bar {
    background-color: #f1f3f5;
    border-radius: 8px;
    padding: .75rem 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
  }

  .stat-bar span {
    font-weight: 500;
  }
</style>
@endpush

@section('content')
<div class="container py-4">

  <!-- Título e Estatísticas -->
  <div class="mb-4">
    <h2 class="fw-bold text-dark">👗 Meu Armário</h2>
    <div class="stat-bar shadow-sm">
      <span><i class="fa fa-folder-open me-2 text-primary"></i>{{ $categories->count() }} categorias</span>
      <span><i class="fa fa-boxes me-2 text-success"></i>{{ $categories->sum('items_count') }} itens</span>
    </div>
  </div>

  <!-- Filtros -->
  <div class="row mb-4 g-3 align-items-center">
    <div class="col-md-8">
      <input type="text" id="searchInput" class="form-control" placeholder="🔍 Buscar categoria...">
    </div>
    <div class="col-md-4">
      <select id="filterItems" class="form-select">
        <option value="all">Todas</option>
        <option value="with">Com itens</option>
        <option value="without">Sem itens</option>
      </select>
    </div>
  </div>

  <!-- Cards de Categorias -->
  <div class="row g-4" id="categoriesGrid">
    @forelse($categories as $category)
      <div class="col-12 col-sm-6 col-md-4 col-lg-3 category-card"
           data-name="{{ strtolower($category->name) }}"
           data-count="{{ $category->items_count }}">
        <div class="card shadow-sm h-100 border-0 position-relative">

          <!-- Botão de ação -->
          <div class="dropdown position-absolute top-0 end-0 m-2">
            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
              <i class="fa fa-ellipsis-v"></i>
            </button>
            <ul class="dropdown-menu">
              <li><button class="dropdown-item" onclick="editCategory('{{ $category->id }}')">✏️ Editar</button></li>
              <li>
                <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir?')">
                  @csrf @method('DELETE')
                  <button class="dropdown-item text-danger">🗑 Excluir</button>
                </form>
              </li>
            </ul>
          </div>

          <!-- Conteúdo do card -->
          <div class="card-body text-center">
            <div class="card-thumb">
              <i class="fa {{ $category->icon ?? 'fa-tag' }} fa-2x text-muted"></i>
            </div>
            <h6 class="editable-name mb-1" contenteditable="true"
                onblur="saveName(this, '{{ $category->id }}')">
              {{ $category->name }}
            </h6>
            <span class="badge {{ $category->items_count > 0 ? 'bg-primary' : 'bg-secondary' }}">
              {{ $category->items_count }} {{ Str::plural('item', $category->items_count) }}
            </span>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12 text-center text-muted">Nenhuma categoria encontrada.</div>
    @endforelse
  </div>
</div>

<!-- Botão flutuante -->
<button class="add-category-fab" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
  <i class="fa fa-plus"></i>
</button>

<!-- Modal: Nova Categoria -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
  <div class="modal-dialog">
    <form action="{{ route('categories.store') }}" method="POST" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Nova Categoria</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Nome da categoria</label>
          <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Ícone (classe FontAwesome)</label>
          <input type="text" name="icon" class="form-control" placeholder="ex: fa-hat-cowboy">
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary">Salvar</button>
      </div>
    </form>
  </div>
</div>

<!-- Scripts -->
<script>
  const cards = document.querySelectorAll('.category-card');

  document.getElementById('searchInput').addEventListener('input', e => {
    const term = e.target.value.toLowerCase();
    cards.forEach(card => {
      card.style.display = card.dataset.name.includes(term) ? '' : 'none';
    });
  });

  document.getElementById('filterItems').addEventListener('change', e => {
    const val = e.target.value;
    cards.forEach(card => {
      const count = parseInt(card.dataset.count);
      if (val === 'all') card.style.display = '';
      else if (val === 'with') card.style.display = count > 0 ? '' : 'none';
      else if (val === 'without') card.style.display = count === 0 ? '' : 'none';
    });
  });

  function saveName(el, id) {
    const name = el.innerText.trim();
    fetch(`/categories/${id}`, {
      method: 'PUT',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ name })
    })
    .then(res => res.ok ? showToast('Nome atualizado') : Promise.reject())
    .catch(() => showToast('Erro ao atualizar', true));
  }

  function showToast(msg, error = false) {
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white ${error ? 'bg-danger' : 'bg-success'} position-fixed bottom-0 end-0 m-3`;
    toast.innerHTML = `
      <div class="d-flex">
        <div class="toast-body">${msg}</div>
        <button class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>`;
    document.body.appendChild(toast);
    new bootstrap.Toast(toast).show();
    setTimeout(() => toast.remove(), 4000);
  }
</script>
@endsection
