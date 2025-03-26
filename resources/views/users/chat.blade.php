@extends('layouts.app')

@section('content')

<main class="pt-8 min-h-screen pb-4">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Заголовок чата -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Чат поддержки</h1>
            
            <!-- Предупреждающее сообщение -->
            <div class="mt-4 p-3 bg-yellow-50 border-l-4 border-yellow-400 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            Пожалуйста, обращайтесь в чат только в крайнем случае. Все ваши обращения будут учитываться при оценке задания. Перед обращением убедитесь, что ответа нет в документации.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Контейнер чата -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <!-- Выбор получателя -->
            <div class="border-b border-gray-200 p-4 bg-gray-50">
                <select id="recipient" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3 border">
                    <option value="tutor">Тьютор (технические вопросы)</option>
                    <option value="hr">HR-менеджер (организационные вопросы)</option>
                </select>
            </div>

            <!-- История сообщений -->
            <div class="chat-container overflow-y-auto p-4 space-y-4" id="chat-messages">
                <!-- Сообщения будут загружаться динамически -->
            </div>

            <!-- Форма отправки сообщения -->
            <div class="border-t border-gray-200 p-4 bg-gray-50">
                <form id="chat-form" enctype="multipart/form-data">
                    @csrf
                    <div class="flex items-center space-x-3">
                        <!-- Поле ввода сообщения -->
                        <div class="flex-1">
                            <input type="text" id="message" name="message" 
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3 border" 
                                   placeholder="Введите сообщение..." required>
                        </div>
                        
                        <!-- Кнопка прикрепления файла -->
                        <div class="relative">
                            <input type="file" id="attachment" name="attachment" class="hidden">
                            <button type="button" id="attach-btn" class="p-2 text-gray-500 hover:text-gray-700 rounded-full hover:bg-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                            </button>
                            <span id="file-name" class="absolute top-full left-0 mt-1 text-xs text-gray-500 whitespace-nowrap"></span>
                        </div>
                        
                        <!-- Кнопка отправки -->
                        <button type="submit" 
                                class="p-2 text-white bg-indigo-600 hover:bg-indigo-700 rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Превы ответа -->
                    <input type="hidden" id="reply-to" name="reply_to" value="">
                    <div id="reply-preview" class="hidden mt-3 bg-gray-100 p-3 rounded-md text-sm text-gray-700 border-l-4 border-indigo-500">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-medium text-xs text-gray-500 mb-1">Ответ на сообщение:</p>
                                <p id="reply-content" class="text-sm"></p>
                            </div>
                            <button type="button" id="cancel-reply" class="text-gray-400 hover:text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const recipientSelect = document.getElementById('recipient');
        const chatMessages = document.getElementById('chat-messages');
        const chatForm = document.getElementById('chat-form');
        const attachBtn = document.getElementById('attach-btn');
        const fileInput = document.getElementById('attachment');
        const fileName = document.getElementById('file-name');
        const replyToInput = document.getElementById('reply-to');
        const replyPreview = document.getElementById('reply-preview');
        const replyContent = document.getElementById('reply-content');
        const cancelReplyBtn = document.getElementById('cancel-reply');
        
        // Загрузка истории чата
        function loadChatHistory(recipient) {
            chatMessages.innerHTML = '';
            
            const messages = {
                tutor: [
                    {id: 1, sender: 'support', text: 'Чем могу помочь по техническим вопросам?', time: '10:30', file: null, fileUrl: null},
                    {id: 2, sender: 'user', text: 'Проблема с выполнением задания', time: '10:32', file: 'task.pdf', fileUrl: '#', replyTo: null},
                    {id: 3, sender: 'support', text: 'Какая именно проблема? Вот инструкция:', time: '10:35', file: 'manual.pdf', fileUrl: '#', replyTo: 2, originalMessage: 'Проблема с выполнением задания'},
                    {id: 4, sender: 'user', text: 'Не понимаю как реализовать API', time: '10:36', file: null, fileUrl: null, replyTo: 3, originalMessage: 'Какая именно проблема? Вот инструкция:'}
                ],
                hr: [
                    {id: 1, sender: 'support', text: 'Чем могу помочь по организационным вопросам?', time: '11:15', file: null, fileUrl: null},
                    {id: 2, sender: 'user', text: 'Нужно изменить данные в профиле', time: '11:20', file: 'data.docx', fileUrl: '#', replyTo: 1, originalMessage: 'Чем могу помочь по организационным вопросам?'}
                ]
            };
            
            messages[recipient].forEach(msg => {
                addMessageToChat(msg);
            });
        }
        
        // Добавление сообщения в чат
        function addMessageToChat(msg) {
            const isUser = msg.sender === 'user';
            const messageClass = isUser ? 'user-message' : 'support-message';
            const alignClass = isUser ? 'justify-end' : 'justify-start';
            
            let fileElement = '';
            if (msg.file) {
                fileElement = `
                    <div class="mt-2">
                        <a href="${msg.fileUrl}" download class="inline-flex items-center text-sm ${isUser ? 'text-indigo-200 hover:text-indigo-100' : 'text-indigo-600 hover:text-indigo-500'}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            ${msg.file}
                        </a>
                    </div>
                `;
            }
            
            let replyElement = '';
            if (msg.replyTo !== null && msg.originalMessage) {
                replyElement = `
                    <div class="reply-container bg-${isUser ? 'indigo-700' : 'gray-200'} text-${isUser ? 'white' : 'gray-800'} text-xs p-2 rounded mb-2 border-l-4 border-${isUser ? 'indigo-300' : 'gray-500'}">
                        <p class="font-medium">${msg.sender === 'user' ? 'Поддержка' : 'Вы'}:</p>
                        <p class="truncate">${msg.originalMessage}</p>
                    </div>
                `;
            }
            
            const messageElement = `
                <div class="flex ${alignClass} mb-3" data-id="${msg.id}">
                    <div class="message-bubble ${messageClass} p-3 max-w-xs md:max-w-md lg:max-w-lg">
                        ${replyElement}
                        <p class="text-sm">${msg.text}</p>
                        ${fileElement}
                        <div class="flex justify-between items-center mt-2">
                            <p class="text-xs ${isUser ? 'text-gray-300' : 'text-gray-500'}">${msg.time}</p>
                            ${!isUser ? `<button class="reply-btn text-xs ${isUser ? 'text-gray-300' : 'text-indigo-600'} hover:underline" data-id="${msg.id}" data-text="${msg.text}">Ответить</button>` : ''}
                        </div>
                    </div>
                </div>
            `;
            
            chatMessages.insertAdjacentHTML('beforeend', messageElement);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
        
        // Обработчик кнопки "Ответить"
        chatMessages.addEventListener('click', function(e) {
            if (e.target.classList.contains('reply-btn')) {
                const messageId = e.target.dataset.id;
                const messageText = e.target.dataset.text;
                const isUserMessage = e.target.closest('.message-bubble').classList.contains('user-message');
                
                replyToInput.value = messageId;
                replyContent.textContent = messageText;
                replyPreview.classList.remove('hidden');
                document.getElementById('message').focus();
            }
        });
        
        // Отмена ответа
        cancelReplyBtn.addEventListener('click', function() {
            replyToInput.value = '';
            replyPreview.classList.add('hidden');
        });
        
        // Обработчик прикрепления файла
        attachBtn.addEventListener('click', function() {
            fileInput.click();
        });
        
        fileInput.addEventListener('change', function() {
            fileName.textContent = fileInput.files.length > 0 ? fileInput.files[0].name : '';
        });
        
        // Обработчик отправки формы
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const messageInput = document.getElementById('message');
            const message = messageInput.value.trim();
            
            if (message || fileInput.files.length > 0) {
                const now = new Date();
                const timeString = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                const fileName = fileInput.files.length > 0 ? fileInput.files[0].name : null;
                
                // Получаем текст оригинального сообщения для ответа
                let originalMessage = '';
                if (replyToInput.value) {
                    const repliedMsg = chatMessages.querySelector(`[data-id="${replyToInput.value}"] .message-bubble`);
                    if (repliedMsg) {
                        originalMessage = repliedMsg.querySelector('p:not(.font-medium)').textContent;
                    }
                }
                
                // Здесь будет код отправки сообщения на сервер
                // В демо-версии просто добавляем сообщение в чат
                
                addMessageToChat({
                    id: Date.now(),
                    sender: 'user',
                    text: message,
                    time: timeString,
                    file: fileName,
                    fileUrl: '#', // В реальном приложении будет URL файла
                    replyTo: replyToInput.value ? parseInt(replyToInput.value) : null,
                    originalMessage: originalMessage
                });
                
                // Очищаем форму
                messageInput.value = '';
                fileInput.value = '';
                document.getElementById('file-name').textContent = '';
                replyToInput.value = '';
                replyPreview.classList.add('hidden');
            }
        });
        
        // Инициализация чата
        loadChatHistory(recipientSelect.value);
        
        // Обновление чата при изменении получателя
        recipientSelect.addEventListener('change', function() {
            loadChatHistory(this.value);
        });
    });
</script>
<style>
    .chat-container {
        height: calc(100vh - 320px);
    }
    .message-bubble {
        max-width: 80%;
        word-break: break-word;
    }
    .user-message {
        background-color: #6366f1;
        color: white;
        border-radius: 14px 14px 0 14px;
    }
    .support-message {
        background-color: #e5e7eb;
        color: #111827;
        border-radius: 14px 14px 14px 0;
    }
    .reply-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
    }
    .reply-container {
        max-width: 100%;
        overflow: hidden;
    }
    #attach-btn:hover {
        background-color: #f3f4f6;
    }
</style>
@endsection