@extends('layouts.app')

@section('content')
<div class="container py-5 text-center">
    <h2 class="mb-4">Pagamento via Pix</h2>

    <h4>Total: <span class="text-success">R${{ number_format($total, 2, ',', '.') }}</span></h4>

    <p class="mt-3">Itens:</p>
    <ul class="list-group w-50 mx-auto mb-4">
        @foreach ($items as $item)
            <li class="list-group-item d-flex justify-content-between">
                <span>{{ $item['name'] }}</span>
                <span>R${{ number_format($item['price'], 2, ',', '.') }}</span>
            </li>
        @endforeach
    </ul>

    <p>Escaneie o QR Code abaixo para realizar o pagamento:</p>
    <img src="https://api.qrserver.com/v1/create-qr-code/?data=Pagamento%20Pix%20R${{ $total }}&size=200x200" alt="QR Code Pix" class="mt-3">

    <div class="mt-4">
        <a href="{{ url('/') }}" class="btn btn-secondary">Voltar à Loja</a>
    </div>
</div>
@endsection
