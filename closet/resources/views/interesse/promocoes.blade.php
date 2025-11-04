{{-- resources/views/interesse/promocoes.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container py-5">
    {{-- Título --}}
    <h2 class="mb-4 text-center">👕 Contas com Promoções</h2>

    {{-- Barra de pesquisa --}}
    <div class="row mb-4">
        <div class="col-md-8 mx-auto">
            <input type="text" id="searchInput" class="form-control form-control-lg"
                placeholder="🔎 Buscar por nome ou email...">
        </div>
    </div>

    {{-- Grid de usuários --}}
    <div class="row g-4" id="usersGrid">
        @forelse ($users as $user)
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card shadow-sm h-100 border-0 rounded-3">
                    <div class="card-body d-flex flex-column text-center">
                        {{-- Avatar com inicial do nome --}}
                        <div class="avatar-circle mx-auto mb-3">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                        <p class="text-muted small mb-0">{{ $user->email }}</p>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted text-center">Nenhuma conta com promoções encontrada.</p>
        @endforelse
    </div>
</div>

{{-- Script AJAX --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function loadPromotionUsers(query = '') {
        $.ajax({
            url: "{{ route('interesse.promocoes.search') }}",
            type: "GET",
            data: { q: query },
            success: function(data) {
                $('#usersGrid').empty();

                if (data.length > 0) {
                    data.forEach(user => {
                        $('#usersGrid').append(`
                            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                <div class="card shadow-sm h-100 border-0 rounded-3">
                                    <div class="card-body d-flex flex-column text-center">
                                        <div class="avatar-circle mx-auto mb-3">
                                            ${user.name.charAt(0).toUpperCase()}
                                        </div>
                                        <h5 class="fw-bold mb-1">${user.name}</h5>
                                        <p class="text-muted small mb-0">${user.email}</p>
                                    </div>
                                </div>
                            </div>
                        `);
                    });
                } else {
                    $('#usersGrid').append(`
                        <p class="text-muted text-center">Nenhuma conta encontrada.</p>
                    `);
                }
            }
        });
    }

    // Atualiza conforme digita
    $(document).ready(function() {
        $('#searchInput').on('keyup', function() {
            let query = $(this).val();
            loadPromotionUsers(query);
        });
    });
</script>

{{-- Estilo avatar --}}
<style>
    .avatar-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #253d57ff;
        color: #858585ff;
        font-weight: bold;
        font-size: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endsection
