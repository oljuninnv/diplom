@extends('layouts.app')

@section('content')
    <main class="pt-4 md:pt-8 min-h-screen pb-4">
        <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
            <!-- Заголовок чата -->
            <div class="mb-4 md:mb-6">
                <h1 class="text-xl md:text-2xl font-bold text-gray-900">Чаты</h1>
            </div>

            <!-- Контейнер чата -->
            <div class="bg-white shadow rounded-lg overflow-hidden flex flex-col md:flex-row h-[calc(100vh-150px)]">
                <!-- Список собеседников -->
                <div id="user-list-container"
                    class="w-full md:w-1/3 border-r border-gray-200 bg-gray-50 overflow-y-auto h-full">
                    <!-- Поиск -->
                    <div class="p-3 md:p-4 border-b border-gray-200">
                        <div class="relative">
                            <input type="text" id="user-search" placeholder="Поиск..."
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3 pl-10 border text-sm md:text-base">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 md:h-5 md:w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Список собеседников -->
                    <div id="user-list">
                        @foreach($interlocutors as $interlocutor)
                        @php
                            $lastMessage = \App\Models\Message::where(function($query) use ($interlocutor) {
                                    $query->where('sender_id', Auth::id())
                                        ->where('receiver_id', $interlocutor->id);
                                })
                                ->orWhere(function($query) use ($interlocutor) {
                                    $query->where('sender_id', $interlocutor->id)
                                        ->where('receiver_id', Auth::id());
                                })
                                ->latest()
                                ->first();
                            
                            $lastMessageText = $lastMessage ? 
                                ($lastMessage->sender_id == Auth::id() ? 'Вы: ' . Str::limit($lastMessage->message, 20) : Str::limit($lastMessage->message, 20)) : 
                                'Нет сообщений';
                            
                            $lastMessageTime = $lastMessage ? $lastMessage->created_at->diffForHumans() : '';
                            
                            $unreadCount = \App\Models\Message::where('receiver_id', Auth::id())
                                ->where('sender_id', $interlocutor->id)
                                ->whereNull('read_at')
                                ->count();
                        @endphp
                        <div class="user-item p-3 md:p-4 border-b border-gray-200 hover:bg-gray-100 cursor-pointer flex items-center relative {{ $currentInterlocutor && $currentInterlocutor->id == $interlocutor->id ? 'bg-indigo-50' : '' }}"
                            data-user-id="{{ $interlocutor->id }}" data-unread="{{ $unreadCount }}" data-name="{{ strtolower($interlocutor->name) }}" data-position="{{ strtolower($interlocutor->position) }}">
                            <div class="flex-shrink-0 h-8 w-8 md:h-10 md:w-10 rounded-full overflow-hidden">
                                <img src="{{ $interlocutor->avatar_url ?? 'https://randomuser.me/api/portraits/women/44.jpg' }}" alt="{{ $interlocutor->name }}"
                                    class="h-full w-full object-cover">
                            </div>
                            <div class="ml-2 md:ml-3 flex-1 min-w-0">
                                <p class="text-xs md:text-sm font-medium text-gray-900">{{ $interlocutor->name }}</p>
                                <p class="text-2xs md:text-xs text-gray-500 mt-1">{{ $interlocutor->position }}</p>
                                <div class="flex justify-between items-center mt-1">
                                    <p class="text-2xs md:text-xs text-gray-500 truncate">{{ $lastMessageText }}</p>
                                    <span class="text-2xs md:text-xs text-gray-400">{{ $lastMessageTime }}</span>
                                </div>
                            </div>
                            @if($unreadCount > 0)
                            <div class="absolute right-2 md:right-4 top-3 md:top-4 bg-indigo-600 text-white text-2xs md:text-xs rounded-full h-4 w-4 md:h-5 md:w-5 flex items-center justify-center">
                                {{ $unreadCount }}
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Правая часть с чатом -->
                <div id="chat-container" class="hidden md:flex md:w-2/3 flex-col h-full relative">
                    @if($currentInterlocutor)
                    <!-- Шапка чата для мобильной версии -->
                    <div class="md:hidden flex items-center p-3 border-b border-gray-200 bg-gray-50 sticky top-0 z-10">
                        <!-- Кнопка "Назад" -->
                        <button id="back-to-list" class="p-1 text-gray-600 hover:text-gray-900 rounded-full hover:bg-gray-200 mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        
                        <!-- Аватар и имя пользователя -->
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-8 w-8 rounded-full overflow-hidden">
                                <img id="selected-user-avatar" src="{{ $currentInterlocutor->avatar_url ?? 'https://randomuser.me/api/portraits/women/44.jpg' }}"
                                    alt="Аватар" class="h-full w-full object-cover">
                            </div>
                            <div class="ml-2">
                                <p class="text-sm font-medium text-gray-900" id="selected-user-name">{{ $currentInterlocutor->name }}</p>
                                <p class="text-xs text-gray-500" id="selected-user-role">{{ $currentInterlocutor->position }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Информация о выбранном пользователе (десктоп) -->
                    <div class="hidden md:flex border-b border-gray-200 p-3 md:p-4 bg-gray-50 items-center" id="selected-user-info">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-8 w-8 md:h-10 md:w-10 rounded-full overflow-hidden">
                                <img id="selected-user-avatar-desktop" src="{{ $currentInterlocutor->avatar_url ?? 'https://randomuser.me/api/portraits/women/44.jpg' }}"
                                    alt="Аватар" class="h-full w-full object-cover">
                            </div>
                            <div class="ml-2 md:ml-3">
                                <p class="text-xs md:text-sm font-medium text-gray-900" id="selected-user-name-desktop">{{ $currentInterlocutor->name }}</p>
                                <p class="text-2xs md:text-xs text-gray-500" id="selected-user-role-desktop">{{ $currentInterlocutor->position }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- История сообщений -->
                    <div class="chat-messages overflow-y-auto p-3 md:p-4 flex-grow"
                        id="chat-messages">
                        @foreach($messages as $message)
                        <div class="flex {{ $message->sender_id == Auth::id() ? 'justify-end' : 'justify-start' }} mb-2 md:mb-3 group" data-id="{{ $message->id }}" id="message-{{ $message->id }}">
                            <div class="message-bubble user-message p-2 md:p-3 max-w-xs md:max-w-md lg:max-w-lg relative transition-all duration-200">
                                @if($message->answer_message_id)
                                    @php
                                        $answeredMessage = $message->answeredMessage;
                                    @endphp
                                    @if($answeredMessage)
                                    <div class="reply-container bg-indigo-700 text-white text-2xs md:text-xs p-1 md:p-2 rounded mb-1 md:mb-2 border-l-4 border-indigo-300">
                                        <p class="font-medium">{{ $answeredMessage->sender_id == Auth::id() ? 'Вы' : $answeredMessage->sender->name }}:</p>
                                        <button class="view-original-message text-xs text-white hover:text-blue-600 mt-1" 
                                            data-message-id="{{ $answeredMessage->id }}">
                                            {{ Str::limit($answeredMessage->message, 50)}}
                                        </button>
                                    </div>
                                    @endif
                                @endif
                                
                                <p class="text-xs md:text-sm">{{ $message->message }}</p>
                                
                                @if($message->document)
                                <div class="mt-1 md:mt-2">
                                    <a href="{{ Storage::url($message->document) }}" download="{{ $message->original_filename }}" class="inline-flex items-center text-xs md:text-sm text-indigo-200 hover:text-indigo-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 md:h-4 md:w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        {{ $message->document }}
                                    </a>
                                </div>
                                @endif
                                
                                <div class="flex justify-between items-center mt-1 md:mt-2">
                                    <p class="text-2xs md:text-xs text-gray-300">{{ $message->created_at->format('H:i') }}</p>
                                    <div class="flex space-x-2">
                                        <button class="reply-btn text-2xs md:text-xs text-gray-300 hover:underline" data-id="{{ $message->id }}" data-text="{{ $message->message }}">Ответить</button>
                                        @if($message->sender_id == Auth::id())
                                        <form action="{{ route('chat.delete', $message->id) }}" method="POST" class="delete-message-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="text-2xs md:text-xs text-red-500 hover:text-red-700 delete-message-btn">Удалить</button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Форма отправки сообщения -->
                    <div class="border-t border-gray-200 p-3 md:p-4 bg-gray-50 sticky bottom-0">
                        <form id="chat-form" action="{{ route('worker-chat.send') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="current-user-id" name="receiver_id" value="{{ $currentInterlocutor->id }}">
                            <input type="hidden" id="answer-message-id" name="answer_message_id" value="">
                            
                            <!-- Превью прикрепленного файла -->
                            <div id="file-preview" class="hidden mb-2 bg-gray-100 p-2 rounded-md text-xs text-gray-700 border-l-4 border-indigo-500">
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span id="file-preview-name"></span>
                                    </div>
                                    <button type="button" id="cancel-file" class="text-gray-400 hover:text-gray-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Превью ответа -->
                            <div id="reply-preview"
                                class="hidden mb-2 bg-gray-100 p-2 rounded-md text-xs text-gray-700 border-l-4 border-indigo-500">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium text-gray-500 mb-1">Ответ на сообщение:</p>
                                        <p id="reply-content" class="text-sm"></p>
                                    </div>
                                    <button type="button" id="cancel-reply" class="text-gray-400 hover:text-gray-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="flex items-center space-x-2 md:space-x-3">
                                <!-- Поле ввода сообщения -->
                                <div class="flex-1">
                                    <input type="text" id="message" name="message"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3 border text-sm md:text-base"
                                        placeholder="Введите сообщение...">
                                </div>

                                <!-- Кнопка прикрепления файла -->
                                <div class="relative">
                                    <input type="file" id="attachment" name="attachment" class="hidden">
                                    <button type="button" id="attach-btn"
                                        class="p-1 md:p-2 text-gray-500 hover:text-gray-700 rounded-full hover:bg-gray-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Кнопка отправки -->
                                <button type="submit"
                                    class="p-1 md:p-2 text-white bg-indigo-600 hover:bg-indigo-700 rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                    @else
                    <div class="flex items-center justify-center h-full">
                        <p class="text-gray-500">Выберите собеседника для начала общения</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userList = document.getElementById('user-list');
            const chatMessages = document.getElementById('chat-messages');
            const chatForm = document.getElementById('chat-form');
            const attachBtn = document.getElementById('attach-btn');
            const fileInput = document.getElementById('attachment');
            const answerMessageIdInput = document.getElementById('answer-message-id');
            const replyPreview = document.getElementById('reply-preview');
            const replyContent = document.getElementById('reply-content');
            const cancelReplyBtn = document.getElementById('cancel-reply');
            const currentUserIdInput = document.getElementById('current-user-id');
            const backToListBtn = document.getElementById('back-to-list');
            const userListContainer = document.getElementById('user-list-container');
            const chatContainer = document.getElementById('chat-container');
            const filePreview = document.getElementById('file-preview');
            const filePreviewName = document.getElementById('file-preview-name');
            const cancelFileBtn = document.getElementById('cancel-file');
            const userSearch = document.getElementById('user-search');
            
            // Функция для определения мобильного устройства
            function isMobileDevice() {
                return window.innerWidth < 768;
            }
            
            const isMobile = isMobileDevice();

            // Обработчик поиска пользователей
            if (userSearch) {
                userSearch.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const userItems = document.querySelectorAll('.user-item');
                    
                    userItems.forEach(item => {
                        const name = item.dataset.name;
                        const position = item.dataset.position;
                        
                        if (name.includes(searchTerm) || position.includes(searchTerm)) {
                            item.style.display = 'flex';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            }

            // Обработчик выбора пользователя
            if (userList) {
                userList.addEventListener('click', function(e) {
                    const userItem = e.target.closest('.user-item');
                    if (userItem) {
                        const userId = userItem.dataset.userId;
                        
                        if (isMobile) {
                            // В мобильной версии просто переходим на страницу чата
                            window.location.href = `/worker-chat/${userId}`;
                        } else {
                            window.location.href = `/worker-chat/${userId}`;
                        }
                    }
                });
            }

            // Обработчик кнопки "Назад" для мобильных
            if (backToListBtn) {
                backToListBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.location.href = '/worker-chat';
                });
            }

            // Функция для инициализации обработчиков чата
            function initChatHandlers() {
                // Обработчик кнопки "Ответить"
                const messagesContainer = document.getElementById('chat-messages');
                if (messagesContainer) {
                    messagesContainer.addEventListener('click', function(e) {
                        // Обработка кнопки "Ответить"
                        if (e.target.classList.contains('reply-btn')) {
                            const messageId = e.target.dataset.id;
                            const messageText = e.target.dataset.text;
                            const messageElement = e.target.closest('.message-bubble');

                            answerMessageIdInput.value = messageId;
                            replyContent.textContent = messageText;
                            replyPreview.classList.remove('hidden');
                            document.getElementById('message').focus();

                            // Прокрутка к сообщению с плавной анимацией
                            if (messageElement) {
                                messageElement.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'center'
                                });

                                // Временная подсветка сообщения
                                messageElement.classList.add('message-highlight');
                                setTimeout(() => {
                                    messageElement.classList.remove('message-highlight');
                                }, 2000);
                            }
                        }

                        // Обработка кнопки "Просмотреть сообщение"
                        if (e.target.classList.contains('view-original-message')) {
                            e.preventDefault();
                            const messageId = e.target.dataset.messageId;
                            const originalMessage = document.getElementById(`message-${messageId}`);
                            
                            if (originalMessage) {
                                originalMessage.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'center'
                                });
                                
                                originalMessage.classList.add('message-highlight');
                                setTimeout(() => {
                                    originalMessage.classList.remove('message-highlight');
                                }, 2000);
                            }
                        }
                    });
                }

                // Отмена ответа
                if (cancelReplyBtn) {
                    cancelReplyBtn.addEventListener('click', function() {
                        answerMessageIdInput.value = '';
                        replyPreview.classList.add('hidden');
                    });
                }

                // Обработчик прикрепления файла
                if (attachBtn) {
                    attachBtn.addEventListener('click', function() {
                        fileInput.click();
                    });
                }

                if (fileInput) {
                    fileInput.addEventListener('change', function() {
                        if (fileInput.files.length > 0) {
                            filePreviewName.textContent = fileInput.files[0].name;
                            filePreview.classList.remove('hidden');
                        }
                    });
                }

                // Отмена прикрепления файла
                if (cancelFileBtn) {
                    cancelFileBtn.addEventListener('click', function() {
                        fileInput.value = '';
                        filePreview.classList.add('hidden');
                    });
                }

                // Обработчик удаления сообщения
                document.querySelectorAll('.delete-message-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        if (confirm('Вы уверены, что хотите удалить это сообщение?')) {
                            this.closest('form').submit();
                        }
                    });
                });
            }

            // Инициализация обработчиков при первой загрузке
            initChatHandlers();

            // Автопрокрутка чата вниз
            if (chatMessages) {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            // Инициализация мобильного вида
            if (isMobile && window.location.pathname.split('/').length > 2) {
                userListContainer.classList.add('hidden');
                chatContainer.classList.remove('hidden');
                chatContainer.classList.add('fixed', 'inset-0', 'z-10', 'bg-white');
                
                // Добавляем отступ снизу для формы ввода в мобильной версии
                const formContainer = document.querySelector('#chat-container .border-t');
                if (formContainer) {
                    formContainer.style.paddingBottom = 'calc(env(safe-area-inset-bottom) + 12px)';
                }
            }
        });
    </script>

    <style>
        .chat-messages {
            height: calc(100% - 180px);
            overflow-y: auto;
            padding-right: 8px;
        }

        @media (max-width: 767px) {
            #chat-container.fixed {
                height: 100vh;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                overflow: hidden;
                display: flex;
                flex-direction: column;
            }
            
            .chat-messages {
                flex-grow: 1;
                height: auto;
                padding-bottom: 0;
                margin-bottom: 0;
            }
            
            #chat-container.fixed .border-t {
                padding-bottom: calc(env(safe-area-inset-bottom) + 12px);
                background-color: #f9fafb;
            }
            
            .mobile-header {
                position: sticky;
                top: 0;
                z-index: 10;
                display: flex;
                align-items: center;
                padding: 8px 12px;
                border-bottom: 1px solid #e5e7eb;
                background-color: #f9fafb;
            }
            
            #back-to-list {
                margin-right: 12px;
            }
        }

        .message-highlight {
            animation: highlight 2s ease-out;
            background-color: rgba(59, 130, 246, 0.1);
            border-radius: 0.5rem;
        }

        @keyframes highlight {
            0% { background-color: rgba(59, 130, 246, 0.2); }
            100% { background-color: transparent; }
        }

        .message-bubble {
            transition: all 0.2s ease;
        }

        .message-bubble:hover {
            transform: translateY(-2px);
        }

        .user-message {
        background-color: #6366f1; /* Синий цвет как у кандидата */
        color: white;
        border-radius: 14px 14px 0 14px;
    }

        .reply-container {
            max-width: 100%;
            overflow: hidden;
            position: relative;
        }

        .view-original-message {
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
            text-align: left;
            display: block;
        }

        .delete-message-btn {
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
        }

        /* Стили для скроллбара */
        .chat-messages::-webkit-scrollbar {
            width: 6px;
        }

        .chat-messages::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        .chat-messages::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Плавная прокрутка для всего чата */
        html {
            scroll-behavior: smooth;
        }
    </style>
@endsection