@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Buscar Usuários</h2>

    <!-- Campo de busca -->
    <input type="text" id="search" placeholder="Digite um nome..." class="form-control mb-3">
    <button id="searchBtn" class="btn btn-primary">Buscar</button>

    <!-- Área onde os usuários aparecerão -->
    <div id="results" class="mt-4"></div>
</div>

<script>
document.getElementById('searchBtn').addEventListener('click', function() {
    const query = document.getElementById('search').value;

    fetch(`/search-users?name=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('results');
            container.innerHTML = ''; // Limpa resultados anteriores

            if (data.data.length === 0) {
                container.innerHTML = '<p>Nenhum usuário encontrado.</p>';
                return;
            }

            // Cria um card pra cada usuário
            data.data.forEach(user => {
                const div = document.createElement('div');
                div.classList.add('card', 'mb-2');
                div.innerHTML = `
                    <div class="card-body d-flex align-items-center">
                        <img src="${user.avatar_url}" alt="Avatar" class="rounded-circle me-3" width="50" height="50">
                        <div>
                            <h5 class="mb-0">${user.name}</h5>
                            <small>${user.email}</small>
                        </div>
                        <button class="btn btn-success ms-auto" onclick="iniciarConversa(${user.id})">Iniciar conversa</button>
                    </div>
                `;
                container.appendChild(div);
            });
        })
        .catch(error => {
            console.error('Erro ao buscar usuários:', error);
        });
});

// Função pra iniciar conversa (você pode conectar depois com sua lógica)
function iniciarConversa(userId) {
    alert(`Iniciando conversa com o usuário ID: ${userId}`);
}
</script>
@endsection
