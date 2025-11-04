@extends('layouts.app')

@section('content')
<h1>Conversa (Empresa): {{ $conversation->id }}</h1>

<ul>
    @foreach($messages as $message)
        <li>
            <strong>{{ $message->sender->name ?? 'Remetente' }}:</strong>
            {{ $message->content }}
        </li>
    @endforeach
</ul>

<form action="{{ route('company.communication.send', $conversation->id) }}" method="POST">
    @csrf
    <input type="text" name="message" placeholder="Digite sua mensagem">
    <button type="submit">Enviar</button>
</form>
@endsection
