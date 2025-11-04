@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Sidebar com lista de conversas -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Mensagens</h2>
                    <a href="{{ route('users.search') }}" class="text-blue-500 hover:text-blue-700" title="Iniciar novo chat">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </a>
                </div>

                <!-- Lista de conversas -->
                <div class="space-y-2 max-h-96 overflow-y-auto">
                    @forelse($conversation->users as $user)
                        <div class="p-3 rounded-lg bg-blue-100">
                            <div class="flex items-center">
                                <img src="{{ $user->getAvatarUrl() }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full mr-3">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-800">{{ $user->name }}</h3>
                                    <p class="text-sm text-gray-600">@{{ $user->email }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500">Nenhuma conversa encontrada.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Área de chat -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-lg shadow-md flex flex-col h-screen">
                <!-- Header -->
                <div class="border-b border-gray-200 p-6 flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800">{{ $conversation->getDisplayName() }}</h2>
                    <button onclick="deleteConversation()" class="text-red-500 hover:text-red-700" title="Deletar conversa">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>

                <!-- Mensagens -->
                <div id="messages-container" class="flex-1 overflow-y-auto p-6 space-y-4">
                    @forelse($messages as $message)
                        <div class="flex @if($message->sender_id === auth()->id() && $message->sender_type === \App\Models\User::class) justify-end @else justify-start @endif">
                            <div class="max-w-xs lg:max-w-md @if($message->sender_id === auth()->id() && $message->sender_type === \App\Models\User::class) bg-blue-500 text-white @else bg-gray-200 text-gray-800 @endif rounded-lg p-3">
                                @if($message->sender_id !== auth()->id() || $message->sender_type !== \App\Models\User::class)
                                    <p class="text-sm font-semibold mb-1">{{ $message->sender->name ?? 'Usuário Desconhecido' }}</p>
                                @endif
                                <p class="break-words">{{ $message->content }}</p>
                                <p class="text-xs @if($message->sender_id === auth()->id() && $message->sender_type === \App\Models\User::class) text-blue-100 @else text-gray-500 @endif mt-1">
                                    {{ $message->created_at->format('H:i') }}
                                    {{-- A lógica de lido/não lido é complexa e será tratada via JavaScript --}}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <p class="text-gray-500">Nenhuma mensagem ainda. Comece a conversa!</p>
                        </div>
                    @endforelse
                </div>

                <!-- Input de mensagem -->
                <div class="border-t border-gray-200 p-6">
                    <form id="message-form" class="flex gap-2">
                        @csrf
                        <input 
                            type="text" 
                            id="message-input" 
                            name="content" 
                            placeholder="Digite sua mensagem..." 
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required
                        >
                        <button 
                            type="submit" 
                            class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition"
                        >
                            Enviar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="module">
    import { createApp } from 'vue';

    const conversationId = {{ $conversation->id }};
    const userId = {{ auth()->id() }};
    const messagesContainer = document.getElementById('messages-container');
    const messageForm = document.getElementById('message-form');
    const messageInput = document.getElementById('message-input');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Função para rolar para a última mensagem
    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Enviar mensagem
    messageForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const content = messageInput.value.trim();
        if (!content) return;

        try {
            const response = await fetch('{{ route("communication.send", $conversation->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ content }),
            });

            if (response.ok) {
                const data = await response.json();
                addMessageToUI(data.message);
                messageInput.value = '';
                messageInput.focus();
            } else {
                alert('Erro ao enviar mensagem');
            }
        } catch (error) {
            console.error('Erro:', error);
            alert('Erro ao enviar mensagem');
        }
    });

    // Adicionar mensagem à interface
    function addMessageToUI(message) {
        // Verifica se a mensagem já existe para evitar duplicidade (pode ser enviada via AJAX e Echo)
        if (document.getElementById(`message-${message.id}`)) {
            return;
        }

        const isSender = message.sender_id === userId && message.sender_type === 'App\\Models\\User';
        const messageDiv = document.createElement('div');
        messageDiv.id = `message-${message.id}`;
        messageDiv.className = 'flex ' + (isSender ? 'justify-end' : 'justify-start');

        const contentDiv = document.createElement('div');
        contentDiv.className = 'max-w-xs lg:max-w-md ' + 
            (isSender ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800') + 
            ' rounded-lg p-3';

        if (!isSender) {
            const nameP = document.createElement('p');
            nameP.className = 'text-sm font-semibold mb-1';
            nameP.textContent = message.sender.name ?? 'Usuário Desconhecido';
            contentDiv.appendChild(nameP);
        }

        const textP = document.createElement('p');
        textP.className = 'break-words';
        textP.textContent = message.content;
        contentDiv.appendChild(textP);

        const timeP = document.createElement('p');
        timeP.className = 'text-xs ' + (isSender ? 'text-blue-100' : 'text-gray-500') + ' mt-1';
        timeP.textContent = new Date(message.created_at).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
        contentDiv.appendChild(timeP);

        messageDiv.appendChild(contentDiv);
        messagesContainer.appendChild(messageDiv);

        scrollToBottom();
    }

    // Deletar conversa
    function deleteConversation() {
        if (confirm('Tem certeza que deseja deletar esta conversa?')) {
            fetch('{{ route("communication.delete", $conversation->id) }}', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '{{ route("communication.index") }}';
                } else {
                    alert('Erro ao deletar conversa');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao deletar conversa');
            });
        }
    }

    // Scroll para a última mensagem ao carregar
    window.addEventListener('load', scrollToBottom);
    
    // Ouvir por novas mensagens com Laravel Echo
    window.Echo.private(`chat.{{ $conversation->id }}`)
        .listen(".message.sent", (e) => {
            // Evitar duplicar a mensagem para o remetente
            if (e.message.sender_id !== userId || e.message.sender_type !== 'App\\Models\\User') {
                addMessageToUI(e.message);
                
                // Marcar como lida
                fetch('{{ url('communication') }}/' + e.message.id + '/read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });
            }
        });
</script>
@endsection

