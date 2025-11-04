@extends('layouts.app')

@section('content')
<div class="wrapper">

  <!-- Top Bar: busca + tema + conta de resultados -->
  <div class="top-bar">
    <div class="search-container">
      <input
        type="text"
        id="searchInput"
        placeholder="🔍 Buscar usuários..."
        aria-label="Buscar usuários"
      >
    </div>
    <div class="controls">
      <button id="themeToggle" class="theme-btn">🌙</button>
      <div id="resultCount" class="result-count">—</div>
    </div>
  </div>

  <!-- Filtros / Ordenação -->
  <div class="filters-bar">
    <button class="filter-btn" data-filter="seguindo">Seguindo</button>
    <button class="filter-btn" data-filter="promocoes">Promoções</button>
    <button class="filter-btn" data-filter="recentes">Recentes</button>

    <select id="sortSelect" class="sort-select">
      <option value="">Ordenar por</option>
      <option value="name_asc">Nome ↑</option>
      <option value="name_desc">Nome ↓</option>
      <option value="followers_desc">Seguidores ↓</option>
      <option value="followers_asc">Seguidores ↑</option>
    </select>
  </div>

  <!-- Conteúdo -->
  <div class="content-area">

    <section class="interest-section">
      <h2 class="section-title">Seu Interesse</h2>
      <div class="interest-items">
        <article class="item clickable">
          <div class="avatar small"></div>
          <div class="info">
            <div class="label">Contas que você segue</div>
            <div class="badge">Seguindo</div>
          </div>
        </article>
        <article class="item clickable">
          <div class="avatar small"></div>
          <div class="info">
            <div class="label">Com quem teve mais interação</div>
            <div class="badge">Interativo</div>
          </div>
        </article>
        <a href="#" class="item clickable">
          <div class="avatar small"></div>
          <div class="info">
            <div class="label">Contas com Promoções</div>
            <div class="badge">Promoção</div>
          </div>
        </a>
      </div>
    </section>

    <section class="explore-section">
      <h2 class="section-title">Explorar</h2>
      <div id="userList" class="user-list">
        <div class="info-message">Digite algo para buscar usuários</div>
      </div>
      <button id="loadMore" class="load-btn" style="display: none;">Carregar mais</button>
    </section>

  </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  let page = 1;
  let currentQuery = '';
  let currentFilter = '';
  let currentSort = '';
  let isLoading = false;

  function updateResultCount(count) {
    $('#resultCount').text(`${count} usuário${count !== 1 ? 's' : ''} encontrado${count !== 1 ? 's' : ''}`);
  }

  function loadUsers(query = '', append = false, filter = '', sort = '') {
    if (isLoading) return;
    isLoading = true;

    if (!append) {
      $('#userList').html('<div class="info-message">Carregando...</div>');
      $('#loadMore').hide();
    }

    $.ajax({
      url: "{{ route('users.search') }}",
      type: "GET",
      data: { q: query, page: page, filter: filter, sort: sort },
      success: function(response) {
        const data = response.data;
        const total = response.total;

        if (!append) $('#userList').empty();

        if (data && data.length > 0) {
          data.forEach(user => {
            $('#userList').append(`
              <article class="item user-item">
                <img src="${user.avatar_url || '/images/avatar-default.png'}" alt="${user.name}" class="avatar">
                <div class="info">
                  <div class="name">${user.name}</div>
                  <div class="subinfo">${user.username ? '@'+user.username : ''}</div>
                  <div class="badge">${user.badge || ''}</div>
                </div>
                <a href="{{ url('/users') }}/${user.id}" class="profile-btn">Perfil</a>
              </article>
            `);
          });

          updateResultCount(total);
          $('#loadMore').toggle(response.has_more);

        } else {
          if (!append) {
            $('#userList').html('<div class="info-message">Nenhum usuário encontrado</div>');
            updateResultCount(0);
          }
          $('#loadMore').hide();
        }
      },
      error: function() {
        if (!append) $('#userList').html('<div class="info-message">Ocorreu um erro carregando usuários.</div>');
        $('#loadMore').hide();
      },
      complete: function() { isLoading = false; }
    });
  }

  // Eventos
  $('#searchInput').on('input', function() {
    page = 1;
    currentQuery = $(this).val().trim();
    loadUsers(currentQuery, false, currentFilter, currentSort);
  });

  $('#sortSelect').on('change', function() {
    currentSort = $(this).val();
    page = 1;
    loadUsers(currentQuery, false, currentFilter, currentSort);
  });

  $('.filter-btn').on('click', function() {
    $('.filter-btn.active').removeClass('active');
    $(this).addClass('active');

    currentFilter = $(this).data('filter');
    page = 1;
    loadUsers(currentQuery, false, currentFilter, currentSort);
  });

  $('#loadMore').on('click', function() {
    page++;
    loadUsers(currentQuery, true, currentFilter, currentSort);
  });

  $('#themeToggle').on('click', function() {
    $('body').toggleClass('dark-mode');
    $(this).text($('body').hasClass('dark-mode') ? '☀️' : '🌙');
  });
</script>

<style>
  :root {
    --color-bg: #ffffff;
    --color-bg-alt: #f9f9f9;
    --color-text: #333333;
    --color-primary: #007bff;
    --color-primary-hover: #0056b3;
    --color-secondary: #6c757d;
    --color-border: #e0e0e0;
    --color-shadow: rgba(0,0,0,0.1);
    --avatar-size: 50px;
    --avatar-small-size: 40px;
  }

  body.dark-mode {
    --color-bg: #1e1e1e;
    --color-bg-alt: #2a2a2a;
    --color-text: #f4f4f4;
    --color-border: #444444;
    --color-shadow: rgba(0,0,0,0.5);
  }

  body {
    background-color: var(--color-bg-alt);
    color: var(--color-text);
    transition: background-color 0.3s, color 0.3s;
  }

  .wrapper {
    max-width: 960px;
    margin: 40px auto;
    background: var(--color-bg);
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 4px 15px var(--color-shadow);
  }

  .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
  .search-container input { width: 100%; max-width: 500px; padding: 12px 20px; font-size: 16px; border: 1px solid var(--color-border); border-radius: 25px; outline: none; transition: border-color 0.2s; }
  .search-container input:focus { border-color: var(--color-primary); }
  .controls { display: flex; align-items: center; gap: 15px; }
  .theme-btn { background: none; border: none; font-size: 20px; cursor: pointer; }
  .result-count { font-size: 14px; color: var(--color-secondary); }
  .filters-bar { display: flex; align-items: center; gap: 10px; margin-bottom: 30px; }
  .filter-btn { padding: 8px 16px; border: none; border-radius: 8px; background: var(--color-bg-alt); cursor: pointer; font-size: 14px; transition: background 0.2s, color 0.2s; }
  .filter-btn.active, .filter-btn:hover { background: var(--color-primary); color: white; }
  .sort-select { margin-left: auto; padding: 8px; font-size: 14px; border: 1px solid var(--color-border); border-radius: 8px; background-color: var(--color-bg); color: var(--color-text); }
  .section-title { font-size: 22px; font-weight: 600; margin-bottom: 20px; }
  .interest-section .interest-items { display: flex; flex-wrap: wrap; gap: 20px; }
  .item.clickable { cursor: pointer; }
  .item { display: flex; align-items: center; background: var(--color-bg-alt); padding: 12px 16px; border-radius: 12px; transition: background 0.2s, transform 0.1s; text-decoration: none; color: inherit; }
  .item:hover { background: var(--color-bg-alt); transform: scale(1.02); }
  .avatar { width: var(--avatar-size); height: var(--avatar-size); border-radius: 50%; object-fit: cover; margin-right: 16px; }
  .avatar.small { width: var(--avatar-small-size); height: var(--avatar-small-size); }
  .info { display: flex; flex-direction: column; }
  .info .name { font-size: 16px; font-weight: 500; }
  .info .subinfo { font-size: 13px; color: var(--color-secondary); }
  .badge { margin-top: 4px; font-size: 12px; color: white; background-color: var(--color-primary); padding: 2px 6px; border-radius: 8px; align-self: flex-start; }
  .user-list .item.user-item { justify-content: space-between; }
  .profile-btn { background-color: var(--color-primary); color: white; text-decoration: none; padding: 6px 14px; border-radius: 8px; transition: background-color 0.2s; }
  .profile-btn:hover { background-color: var(--color-primary-hover); }
  .load-btn { margin: 30px auto; display: block; padding: 10px 24px; background-color: var(--color-primary); color: white; border: none; border-radius: 8px; font-size: 15px; cursor: pointer; transition: background-color 0.2s; }
  .load-btn:hover { background-color: var(--color-primary-hover); }
  .info-message { text-align: center; font-size: 15px; color: var(--color-secondary); margin: 20px 0; }
</style>
@endsection
