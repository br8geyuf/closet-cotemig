@extends('layouts.app')

@section('title', 'Adicionar Item - Meu Closet')

@section('content')
<div class="container py-4">

    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0">
            <i class="fas fa-plus text-primary me-2"></i>Adicionar Item
        </h1>
        <a href="{{ route('items.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Voltar
        </a>
    </div>

    <!-- Formulário -->
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-3">
                    <!-- Nome -->
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nome *</label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name') }}" 
                               required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Categoria -->
                    <div class="col-md-6">
                        <label for="category_id" class="form-label">Categoria *</label>
                        <select name="category_id" 
                                id="category_id" 
                                class="form-select @error('category_id') is-invalid @enderror" 
                                required>
                            <option value="">Selecione...</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Marca -->
                    <div class="col-md-6">
                        <label for="brand" class="form-label">Marca</label>
                        <input type="text" name="brand" id="brand" class="form-control" value="{{ old('brand') }}">
                    </div>

                    <!-- Tamanho -->
                    <div class="col-md-6">
                        <label for="size" class="form-label">Tamanho</label>
                        <input type="text" name="size" id="size" class="form-control" value="{{ old('size') }}">
                    </div>

                    <!-- Condição -->
                    <div class="col-md-6">
                        <label for="condition" class="form-label">Condição *</label>
                        <select name="condition" id="condition" class="form-select" required>
                            <option value="">Selecione...</option>
                            <option value="novo">Novo</option>
                            <option value="usado_excelente">Usado - Excelente</option>
                            <option value="usado_bom">Usado - Bom</option>
                            <option value="usado_regular">Usado - Regular</option>
                            <option value="danificado">Danificado</option>
                        </select>
                    </div>

                    <!-- Temporada -->
                    <div class="col-md-6">
                        <label for="season" class="form-label">Temporada *</label>
                        <select name="season" id="season" class="form-select" required>
                            <option value="">Selecione...</option>
                            <option value="primavera">Primavera</option>
                            <option value="verao">Verão</option>
                            <option value="outono">Outono</option>
                            <option value="inverno">Inverno</option>
                            <option value="todas">Todas</option>
                        </select>
                    </div>

                    <!-- Ocasião -->
                    <div class="col-md-6">
                        <label for="occasion" class="form-label">Ocasião *</label>
                        <select name="occasion" id="occasion" class="form-select" required>
                            <option value="">Selecione...</option>
                            <option value="casual">Casual</option>
                            <option value="trabalho">Trabalho</option>
                            <option value="festa">Festa</option>
                            <option value="esporte">Esporte</option>
                            <option value="formal">Formal</option>
                            <option value="todas">Todas</option>
                        </select>
                    </div>

                    <!-- Preço -->
                    <div class="col-md-6">
                        <label for="purchase_price" class="form-label">Preço de Compra</label>
                        <input type="number" step="0.01" name="purchase_price" id="purchase_price" class="form-control" value="{{ old('purchase_price') }}">
                    </div>

                    <!-- Data da compra -->
                    <div class="col-md-6">
                        <label for="purchase_date" class="form-label">Data da Compra</label>
                        <input type="date" name="purchase_date" id="purchase_date" class="form-control" value="{{ old('purchase_date') }}">
                    </div>

                    <!-- Cores -->
                    <div class="col-md-6">
                        <label for="colors" class="form-label">Cores</label>
                        <input type="text" name="colors[]" class="form-control mb-2" placeholder="Ex: Azul">
                        <input type="text" name="colors[]" class="form-control mb-2" placeholder="Ex: Branco">
                        <small class="text-muted">Adicione até 2 cores (ou mais no futuro com JS)</small>
                    </div>

                    <!-- Tags -->
                    <div class="col-md-6">
                        <label for="tags" class="form-label">Tags</label>
                        <input type="text" name="tags[]" class="form-control mb-2" placeholder="Ex: Básico">
                        <input type="text" name="tags[]" class="form-control mb-2" placeholder="Ex: Festa">
                        <small class="text-muted">Palavras-chave para organizar os itens</small>
                    </div>

                    <!-- Imagens -->
                    <div class="col-md-12">
                        <label for="images" class="form-label">Imagens</label>
                        <input type="file" name="images[]" id="images" class="form-control" multiple>
                        <small class="text-muted">Pode enviar várias imagens (jpg, png, até 2MB cada)</small>
                    </div>

                    <!-- Botão -->
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Salvar Item
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
