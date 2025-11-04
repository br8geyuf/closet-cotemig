@extends('layouts.app')

@section('title', 'Closet Fashion - Início')

@section('content')
<div class="container py-4">

    <!-- Carrossel com imagens reais de moda (celebridades e street style) -->
    <div id="fashionCarousel" class="carousel slide mb-5 shadow rounded" data-bs-ride="carousel" data-bs-interval="4000">
        <div class="carousel-inner rounded">
            @php
    $slides = [
        [
            'img' => 'https://hips.hearstapps.com/hmg-prod/images/jenna-ortega-dating-jpg-68b6bd80b4bab.jpg?crop=1xw:0.3750881730543146xh;0,0.0748xh&resize=1200:*', // imagem via web da Cardi B com vestido
            'title' => 'Looks Inspiradores das Celebridades',
            'text' => 'Veja o que as estrelas estão vestindo nas ruas e eventos.',
            'button' => route('wardrobe.index'),
            'btnText' => 'Acesse seu Armário'
        ],
        [
            'img' => 'https://people.com/thmb/L8V9pWHj-drRinmIyDccI8JObls=/1500x0/filters:no_upscale():max_bytes(150000):strip_icc():focal(704x449:706x451)/kylie-jenner-met-gala-1-09db267945014e89bb3d6e2a6eca5474.jpg',
            'title' => 'Elegância para Ocasiões Especiais',
            'text' => 'Encontre vestidos e ternos perfeitos para brilhar.',
        ],
        [
            'img' => 'https://fly.metroimg.com/upload/q_85,w_700/https://uploads.metroimg.com/wp-content/uploads/2024/09/24161251/GettyImages-113523528-1.jpg',
            'title' => 'Acessórios que Fazem a Diferença',
            'text' => 'Complete seu visual com toques sofisticados.',
        ],
    ];
@endphp


            @foreach($slides as $index => $slide)
                <div class="carousel-item @if($index==0) active @endif">
                    <img src="{{ $slide['img'] }}" class="d-block w-100" alt="Slide {{ $index+1 }}" style="height: 400px; object-fit: cover;">
                    <div class="carousel-caption bg-dark bg-opacity-50 rounded p-3">
                        <h3>{{ $slide['title'] }}</h3>
                        <p>{{ $slide['text'] }}</p>
                        @if(isset($slide['button']))
                            <a href="{{ $slide['button'] }}" class="btn btn-primary">{{ $slide['btnText'] }}</a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#fashionCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#fashionCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>

    <!-- (continuação do seu código sem alterações) -->

    <!-- Busca com sugestões inteligentes -->
    <div class="mb-5 text-center position-relative">
        <h4 class="mb-3 fw-bold">Busque por roupas, looks e categorias</h4>
        <div class="input-group mx-auto" style="max-width:600px;">
            <input type="text" id="searchInput" class="form-control" placeholder="Buscar roupas, looks, categorias...">
            <button class="btn btn-primary" id="searchBtn">Buscar</button>
        </div>
        <div id="searchSuggestions" class="list-group mx-auto position-absolute shadow rounded d-none" style="width: 600px; z-index: 1050; top: 58px;"></div>
    </div>

    <!-- Categorias em destaque -->
    <h4 class="mb-4 fw-bold">Categorias em Destaque</h4>
    <div class="row g-3 mb-5">
        @php
            $categories = [
                ['name' => 'Camisetas', 'icon' => 'tshirt', 'count' => 54],
                ['name' => 'Calças', 'icon' => 'pants', 'count' => 82],
                ['name' => 'Vestidos', 'icon' => 'dress', 'count' => 13],
                ['name' => 'Sapatos', 'icon' => 'shoe-prints', 'count' => 40],
                ['name' => 'Acessórios', 'icon' => 'hat-cowboy', 'count' => 31],
                ['name' => 'Casacos', 'icon' => 'user-astronaut', 'count' => 89],
            ];
        @endphp

        @foreach($categories as $cat)
            <div class="col-6 col-sm-4 col-md-2">
                <div class="card shadow-sm text-center p-3 h-100 border-0 category-card">
                    <i class="fas fa-{{ $cat['icon'] }} fa-2x mb-3 text-primary"></i>
                    <h6 class="fw-bold">{{ $cat['name'] }}</h6>
                    <small class="text-muted">{{ $cat['count'] }} itens disponíveis</small>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Novidades -->
    <div class="bg-light rounded p-4 mb-5 shadow-sm">
        <h4 class="mb-4 fw-bold">Novidades</h4>
        <div class="row g-3">
            @php
                $novidades = [
                    ['name' => 'Vestido Vermelho', 'price' => 280, 'img' => 'https://img.ltwebstatic.com/images3_pi/2024/10/25/a5/1729844084e964f7508812fd664ed255d2fc913cf2_thumbnail_405x.webp'],
                    ['name' => 'Jaqueta Jeans', 'price' => 220, 'img' => 'https://images.tcdn.com.br/img/img_prod/1289039/jaqueta_jeans_masculina_ouzzare_735_1_8f1068eaf2aa9a506ae2af1b69664012.jpg'],
                    ['name' => 'Tênis Branco', 'price' => 130, 'img' => 'https://sistema.sistemawbuy.com.br/arquivos/0cf59bd3b2c14ed16aaf7331fb7f5b9d/produtos/FAE1FOU4/img-20220718-wa0043-62d5b42d9b7aa.jpg'],
                    ['name' => 'Bolsa de Couro', 'price' => 450, 'img' => 'https://couribi.com.br/wp-content/uploads/2024/04/IMG-1256-scaled.jpg'],
                    ['name' => 'Camisa Branca', 'price' => 120, 'img' => 'https://cdn.shoppub.io/cdn-cgi/image/w=1000,h=1000,q=80,f=auto/outlet360/media/uploads/produtos/foto/kuibftik/946cb11f-60d9-426e-a5ac-b2c418fafd24.jpeg'],
                    ['name' => 'Calça Social', 'price' => 260, 'img' => 'https://www.homemsa.com.br/wp-content/uploads/2023/03/Danithais-11.0715921.png'],
                ];
            @endphp

            @foreach($novidades as $item)
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card shadow-sm border-0 h-100 hover-scale">
                        <img src="{{ $item['img'] }}" class="card-img-top" alt="{{ $item['name'] }}" style="height:180px; object-fit:cover;">
                        <div class="card-body text-center">
                            <h6 class="fw-bold">{{ $item['name'] }}</h6>
                            <p class="text-muted mb-0">$ {{ number_format($item['price'], 2, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Tendências da Semana -->
    <h4 class="mb-4 fw-bold">Tendências da Semana</h4>
    <div class="row g-4 mb-5">
        @php
            $trends = [
                ['title' => 'Look Casual Moderno', 'desc' => 'Combine conforto e estilo nas ruas.', 'img' => 'https://www.fashionbubbles.com/wp-content/uploads/2025/07/looks-para-inverno-7.jpg'],
                ['title' => 'Elegância Urbana', 'desc' => 'Roupas que destacam sua personalidade.', 'img' => 'https://i.pinimg.com/236x/c5/9e/13/c59e132cf870a78efd41f2d732b3fb89.jpg'],
                ['title' => 'Acessórios Impactantes', 'desc' => 'Detalhes que fazem toda a diferença.', 'img' => 'https://viladasjoias.com.br/wp-content/uploads/2024/03/joias-empoderadoras-vila-das-joias.jpg'],
                ['title' => 'Estilo para Todas as Ocasiões', 'desc' => 'Looks versáteis para o dia e a noite.', 'img' => 'https://i.pinimg.com/originals/a3/ea/94/a3ea94812db99e1a778d9288f0bff68e.jpg'],
            ];
        @endphp

        @foreach($trends as $trend)
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card text-white trend-card border-0 rounded" style="overflow:hidden; position:relative;">
                    <img src="{{ $trend['img'] }}" alt="{{ $trend['title'] }}" class="card-img" style="height: 300px; object-fit: cover;">
                    <div class="card-img-overlay d-flex flex-column justify-content-end p-3 bg-gradient" style="background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);">
                        <h5 class="fw-bold">{{ $trend['title'] }}</h5>
                        <p>{{ $trend['desc'] }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Newsletter com contagem regressiva e validação -->
    <div class="bg-primary text-white rounded-4 text-center p-5 shadow">
        <h4 class="fw-bold mb-3">Receba nossas novidades e promoções</h4>
        <p class="mb-4 fs-5">Assine nossa newsletter e fique por dentro do que há de novo no mundo fashion.</p>

        <div class="countdown mb-4 fs-5" id="countdownTimer">Promoção acaba em: <span id="countdown"></span></div>

        <form id="newsletterForm" class="mx-auto" style="max-width: 400px;">
            <div class="input-group">
                <input type="email" id="emailInput" class="form-control" placeholder="Seu e-mail" required>
                <button type="submit" class="btn btn-dark">Assinar</button>
            </div>
            <div id="newsletterMsg" class="mt-2"></div>
        </form>
    </div>

</div>

@push('styles')
<style>
    .category-card, .hover-scale, .trend-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
    }
    .category-card:hover, .hover-scale:hover, .trend-card:hover {
        transform: translateY(-6px) scale(1.03);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }
    .trend-card .card-img-overlay p {
        font-size: 0.9rem;
    }
    #searchSuggestions {
        max-height: 200px;
        overflow-y: auto;
    }
</style>
@endpush

@push('scripts')
<script>
    // Sugestões inteligentes para busca
    const searchInput = document.getElementById('searchInput');
    const suggestionsBox = document.getElementById('searchSuggestions');

    const itemsToSearch = [
        'Camisetas', 'Calças', 'Vestidos', 'Sapatos', 'Acessórios', 'Casacos',
        'Jaqueta Jeans', 'Tênis Branco', 'Bolsa de Couro', 'Camisa Branca',
        'Calça Social', 'Vestido Vermelho'
    ];

    searchInput.addEventListener('input', () => {
        const query = searchInput.value.trim().toLowerCase();
        if(query.length > 1){
            const filtered = itemsToSearch.filter(item => item.toLowerCase().includes(query));
            suggestionsBox.innerHTML = '';
            if(filtered.length > 0) {
                filtered.forEach(item => {
                    const div = document.createElement('div');
                    div.classList.add('list-group-item', 'list-group-item-action');
                    div.textContent = item;
                    div.style.cursor = 'pointer';
                    div.addEventListener('click', () => {
                        searchInput.value = item;
                        suggestionsBox.classList.add('d-none');
                    });
                    suggestionsBox.appendChild(div);
                });
                suggestionsBox.classList.remove('d-none');
            } else {
                suggestionsBox.classList.add('d-none');
            }
        } else {
            suggestionsBox.classList.add('d-none');
        }
    });

    // Newsletter - Validação e mensagem
    const newsletterForm = document.getElementById('newsletterForm');
    const emailInput = document.getElementById('emailInput');
    const newsletterMsg = document.getElementById('newsletterMsg');

    newsletterForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const email = emailInput.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if(emailRegex.test(email)){
            newsletterMsg.textContent = 'Obrigado por assinar!';
            newsletterMsg.classList.remove('text-danger');
            newsletterMsg.classList.add('text-success');
            emailInput.value = '';
        } else {
            newsletterMsg.textContent = 'Por favor, insira um e-mail válido.';
            newsletterMsg.classList.remove('text-success');
            newsletterMsg.classList.add('text-danger');
        }
    });

    // Countdown para promoções (3 dias a partir de agora)
    function startCountdown(durationInSeconds) {
        const countdownElement = document.getElementById('countdown');
        let timeLeft = durationInSeconds;

        const interval = setInterval(() => {
            if(timeLeft <= 0) {
                clearInterval(interval);
                countdownElement.textContent = 'Promoção encerrada!';
                return;
            }
            const days = Math.floor(timeLeft / 86400);
            const hours = Math.floor((timeLeft % 86400) / 3600);
            const minutes = Math.floor((timeLeft % 3600) / 60);
            const seconds = timeLeft % 60;

            countdownElement.textContent = `${days}d ${hours}h ${minutes}m ${seconds}s`;
            timeLeft--;
        }, 1000);
    }

    // Inicializa o countdown com 3 dias (259200 segundos)
    startCountdown(259200);
</script>
@endpush

@endsection
