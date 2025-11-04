@extends('layouts.app')

@section('title', 'Comunicação - Closet Fashion')

@push('styles')
<style>
    .communication-container {
        display: flex;
        gap: 1rem;
        height: 75vh;
    }

    .conversations-list {
        width: 30%;
        border-right: 1px solid #ddd;
        overflow-y: auto;
        border-radius: 0.5rem;
        background: #f8f9fa;
        padding: 1rem;
    }

    .conversation-card {
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        margin-bottom: 0.5rem;
        cursor: pointer;
        transition: background 0.2s;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .conversation-card:hover {
        background: #e9ecef;
    }

    .conversation-card.active {
        background: #dee2e6;
        font-weight: 600;
    }

    .chat-window {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: #fff;
        border-radius: 0.5rem;
        border: 1px solid #ddd;
        overflow: hidden;
    }

    .chat-messages {
        flex: 1;
        padding: 1rem;
        overflow-y: auto;
    }

    .message {
        margin-bottom: 1rem;
        max-width: 70%;
        padding: 0.5rem 0.75rem;
        border-radius: 0.75rem;
        position: relative;
    }

    .message.user {
        background: #e0f7fa;
        align-self: flex-end;
        border-bottom-right-radius: 0.25rem;
    }

    .message.partner {
        background: #f1f3f5;
        align-self: flex-start;
        border-bottom-left-radius: 0.25rem;
    }

    .message small {
        display: block;
        font-size: 0.7rem;
        color: #666;
        margin-top: 0.25rem;
    }

    .chat-input {
        border-top: 1px solid #ddd;
        display: flex;
        padding: 0.5rem;
        gap: 0.5rem;
    }

    .chat-input input {
        flex: 1;
        border-radius: 1rem;
        border: 1px solid #ccc;
        padding: 0.5rem 1rem;
    }

    .chat-input button {
        border-radius: 1rem;
        padding: 0.5rem 1rem;
    }

    @media (max-width: 768px) {
        .communication-container {
            flex-direction: column;
            height: auto;
        }
        .conversations-list {
            width: 100%;
            border-right: none;
            margin-bottom: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <h2 class="fw-bold mb-3">Comunicação (Empresa)</h2>
    <div class="communication-container">
        
        <!-- Lista de conversas -->
        <div class="conversations-list">
            @foreach($conversations as $conversation)
            <div class="conversation-card {{ isset($selectedConversation) && $selectedConversation->id == $conversation->id ? 'active' : '' }}" 
                 data-conversation="{{ $conversation->id }}">
                <span>{{ $conversation->partner_name }}</span>
                @if($conversation->unread_count > 0)
                    <span class="badge bg-danger rounded-pill">{{ $conversation->unread_count }}</span>
                @endif
            </div>
            @endforeach
        </div>

        <!-- Janela de chat -->
        <div class="chat-window">
            <div class="chat-messages" id="chat-messages">
                @if(isset($selectedConversation))
                    @foreach($selectedConversation->messages as $message)
                        <div class="message {{ $message->sender_id == auth('company')->id() ? 'user' : 'partner' }}">
                            {{ $message->content }}
                            <small>{{ $message->created_at->format('H:i') }}</small>
                        </div>
                    @endforeach
                @else
                    <p class="text-center text-muted mt-3">Selecione uma conversa para começar a conversar.</p>
                @endif
            </div>
            @if(isset($selectedConversation))
            <form id="chat-form" class="chat-input" method="POST" action="{{ route('company.communication.send', $selectedConversation->id) }}">
                @csrf
                <input type="text" name="message" placeholder="Digite sua mensagem..." required>
                <button type="submit" class="btn btn-primary">Enviar</button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", () => {
    const conversationCards = document.querySelectorAll(".conversation-card");

    conversationCards.forEach(card => {
        card.addEventListener('click', () => {
            const conversationId = card.dataset.conversation;
            window.location.href = "{{ url('/company/communication') }}/" + conversationId;
        });
    });

    const chatMessages = document.getElementById('chat-messages');
    if(chatMessages){
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
});
</script>
@endpush
