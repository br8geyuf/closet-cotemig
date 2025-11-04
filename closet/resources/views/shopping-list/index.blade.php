@extends('layouts.app')

@section('content')
<div class="shopping-container" style="background:#F4F4F4; min-height:100vh; padding:30px;">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <!-- Avatar + Usuário -->
        <div class="d-flex align-items-center">
            <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center me-3 shadow"
                style="width:60px; height:60px; font-size:24px; font-weight:bold;">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <span style="font-size:22px; font-weight:600; color:#333;">Olá, {{ Auth::user()->name }}</span>
        </div>

        <!-- Título -->
        <h3 class="m-0 text-center text-dark fw-bold flex-grow-1">🛍️ Sua Lista de Compras</h3>

        <!-- Placeholder para centralização -->
        <div style="width:60px;"></div>
    </div>

    <!-- Campo de pesquisa -->
    <div class="mb-4">
        <input type="text" id="searchInput" class="form-control shadow-sm" placeholder="Pesquisar produto..."
               style="border-radius:25px; padding:12px 20px;">
    </div>

    <!-- Quantidade e preço total -->
    <div class="d-flex justify-content-between align-items-center mb-4 px-2">
        <span class="badge bg-light text-dark fs-6" id="itemCount">🧾 Quantidade de itens: 0</span>
        <span class="badge bg-success text-white fs-6" id="totalPrice">💰 Total: R$ 0,00</span>
    </div>

    <!-- Grid de produtos -->
    <div class="row" id="productGrid">
        @php
            $products = [
                ['name' => 'Camisa Polo', 'price' => 49.90, 'img' => 'https://via.placeholder.com/300x300?text=Camisa+Polo'],
                ['name' => 'Calça Jeans', 'price' => 89.90, 'img' => 'https://via.placeholder.com/300x300?text=Calça+Jeans'],
                ['name' => 'Jaqueta de Couro', 'price' => 199.90, 'img' => 'https://via.placeholder.com/300x300?text=Jaqueta+Couro'],
                ['name' => 'Vestido Floral', 'price' => 129.90, 'img' => 'https://via.placeholder.com/300x300?text=Vestido+Floral'],
                ['name' => 'Tênis Esportivo', 'price' => 159.90, 'img' => 'https://via.placeholder.com/300x300?text=Tênis+Esportivo'],
                ['name' => 'Boné Casual', 'price' => 39.90, 'img' => 'https://via.placeholder.com/300x300?text=Boné+Casual'],
            ];
        @endphp

        @foreach($products as $product)
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0" style="border-radius:15px; overflow:hidden;">
                <img src="{{ $product['img'] }}" class="card-img-top" alt="{{ $product['name'] }}">
                <div class="card-body">
                    <p class="card-text mb-1 fw-semibold text-dark">{{ $product['name'] }}</p>
                    <p class="card-text fw-bold text-success fs-5">R$ {{ number_format($product['price'], 2, ',', '.') }}</p>
                    <button class="btn btn-primary w-100 add-to-cart"
                            data-name="{{ $product['name'] }}"
                            data-price="{{ $product['price'] }}"
                            style="border-radius:25px;">
                        ➕ Adicionar ao Carrinho
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Botão de finalizar compra -->
    <div class="text-center mt-5">
        <form id="checkoutForm" action="{{ route('checkout.pix') }}" method="POST">
            @csrf
            <input type="hidden" name="items" id="itemsInput">
            <input type="hidden" name="total" id="totalInput">

            <button type="submit" id="checkoutBtn" class="btn btn-success btn-lg shadow"
                    style="border-radius:30px; padding:12px 40px; font-size:18px;">
                💳 Finalizar Compra via Pix
            </button>
        </form>
    </div>

</div>

<!-- Script -->
<script>
    let cart = [];
    let itemCount = 0;
    let totalPrice = 0;

    const updateDisplay = () => {
        document.getElementById('itemCount').innerText = `🧾 Quantidade de itens: ${itemCount}`;
        document.getElementById('totalPrice').innerText = `💰 Total: R$ ${totalPrice.toFixed(2).replace('.', ',')}`;
        document.getElementById('checkoutBtn').disabled = itemCount === 0;
    };

    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', () => {
            const name = button.getAttribute('data-name');
            const price = parseFloat(button.getAttribute('data-price'));

            cart.push({ name, price });
            itemCount++;
            totalPrice += price;

            updateDisplay();

            // Feedback visual
            button.classList.add('btn-success');
            button.innerText = '✅ Adicionado!';
            setTimeout(() => {
                button.classList.remove('btn-success');
                button.innerText = '➕ Adicionar ao Carrinho';
            }, 1200);
        });
    });

    // Pesquisa dinâmica
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const term = this.value.toLowerCase();
        document.querySelectorAll('#productGrid .card').forEach(card => {
            const name = card.querySelector('.card-text').innerText.toLowerCase();
            card.parentElement.style.display = name.includes(term) ? '' : 'none';
        });
    });

    // Envio do carrinho
    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        document.getElementById('itemsInput').value = JSON.stringify(cart);
        document.getElementById('totalInput').value = totalPrice.toFixed(2);
    });

    // Inicializa
    updateDisplay();
</script>
@endsection
