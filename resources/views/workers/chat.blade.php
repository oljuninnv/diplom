@extends('layouts.app')

@section('content')
<main class="pt-4 md:pt-8 min-h-screen pb-4">
    <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
        <!-- Заголовок чата -->
        <div class="mb-4 md:mb-6">
            <h1 class="text-xl md:text-2xl font-bold text-gray-900">Чаты с кандидатами</h1>
        </div>

        <!-- Контейнер чата -->
        <div class="bg-white shadow rounded-lg overflow-hidden flex flex-col md:flex-row">
            <!-- Список кандидатов - скрыт на мобильных при открытом чате -->
            <div id="candidate-list-container"
                class="w-full md:w-1/3 border-r border-gray-200 bg-gray-50 overflow-y-auto md:block"
                style="height: calc(100vh - 150px);">
                <!-- Поиск -->
                <div class="p-3 md:p-4 border-b border-gray-200">
                    <div class="relative">
                        <input type="text" id="candidate-search" placeholder="Поиск кандидатов..."
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

                <!-- Список кандидатов -->
                <div id="candidate-list">
                    <!-- Кандидат 1 -->
                    <div class="candidate-item p-3 md:p-4 border-b border-gray-200 hover:bg-gray-100 cursor-pointer flex items-center relative bg-indigo-50"
                        data-user-id="1" data-unread="2">
                        <div class="flex-shrink-0 h-8 w-8 md:h-10 md:w-10 rounded-full overflow-hidden">
                            <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Кандидат"
                                class="h-full w-full object-cover">
                        </div>
                        <div class="ml-2 md:ml-3 flex-1 min-w-0">
                            <p class="text-xs md:text-sm font-medium text-gray-900">Иван Петров</p>
                            <p class="text-2xs md:text-xs text-gray-500 mt-1">Frontend разработчик</p>
                            <div class="flex justify-between items-center mt-1">
                                <p class="text-2xs md:text-xs text-gray-500 truncate">Вы: Проверим ваше тестовое задание...</p>
                                <span class="text-2xs md:text-xs text-gray-400">12:45</span>
                            </div>
                        </div>
                        <!-- Индикатор непрочитанных сообщений -->
                        <div
                            class="absolute right-2 md:right-4 top-3 md:top-4 bg-indigo-600 text-white text-2xs md:text-xs rounded-full h-4 w-4 md:h-5 md:w-5 flex items-center justify-center">
                            2
                        </div>
                    </div>

                    <!-- Кандидат 2 -->
                    <div class="candidate-item p-3 md:p-4 border-b border-gray-200 hover:bg-gray-100 cursor-pointer flex items-center"
                        data-user-id="2" data-unread="0">
                        <div class="flex-shrink-0 h-8 w-8 md:h-10 md:w-10 rounded-full overflow-hidden">
                            <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Кандидат"
                                class="h-full w-full object-cover">
                        </div>
                        <div class="ml-2 md:ml-3 flex-1 min-w-0">
                            <p class="text-xs md:text-sm font-medium text-gray-900">Анна Сидорова</p>
                            <p class="text-2xs md:text-xs text-gray-500 mt-1">UX/UI дизайнер</p>
                            <div class="flex justify-between items-center mt-1">
                                <p class="text-2xs md:text-xs text-gray-500 truncate">Анна: Спасибо за обратную связь!</p>
                                <span class="text-2xs md:text-xs text-gray-400">Вчера</span>
                            </div>
                        </div>
                    </div>

                    <!-- Кандидат 3 -->
                    <div class="candidate-item p-3 md:p-4 border-b border-gray-200 hover:bg-gray-100 cursor-pointer flex items-center relative"
                        data-user-id="3" data-unread="1">
                        <div class="flex-shrink-0 h-8 w-8 md:h-10 md:w-10 rounded-full overflow-hidden">
                            <img src="https://randomuser.me/api/portraits/men/75.jpg" alt="Кандидат"
                                class="h-full w-full object-cover">
                        </div>
                        <div class="ml-2 md:ml-3 flex-1 min-w-0">
                            <p class="text-xs md:text-sm font-medium text-gray-900">Дмитрий Волков</p>
                            <p class="text-2xs md:text-xs text-gray-500 mt-1">Backend разработчик</p>
                            <div class="flex justify-between items-center mt-1">
                                <p class="text-2xs md:text-xs text-gray-500 truncate">Дмитрий: Здравствуйте, у меня вопрос...</p>
                                <span class="text-2xs md:text-xs text-gray-400">Пн</span>
                            </div>
                        </div>
                        <!-- Индикатор непрочитанных сообщений -->
                        <div
                            class="absolute right-2 md:right-4 top-3 md:top-4 bg-indigo-600 text-white text-2xs md:text-xs rounded-full h-4 w-4 md:h-5 md:w-5 flex items-center justify-center">
                            1
                        </div>
                    </div>

                    <!-- Кандидат 4 -->
                    <div class="candidate-item p-3 md:p-4 border-b border-gray-200 hover:bg-gray-100 cursor-pointer flex items-center"
                        data-user-id="4" data-unread="0">
                        <div class="flex-shrink-0 h-8 w-8 md:h-10 md:w-10 rounded-full overflow-hidden">
                            <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Кандидат"
                                class="h-full w-full object-cover">
                        </div>
                        <div class="ml-2 md:ml-3 flex-1 min-w-0">
                            <p class="text-xs md:text-sm font-medium text-gray-900">Елена Ковалева</p>
                            <p class="text-2xs md:text-xs text-gray-500 mt-1">Product Manager</p>
                            <div class="flex justify-between items-center mt-1">
                                <p class="text-2xs md:text-xs text-gray-500 truncate">Вы: Отправьте исправленный вариант...</p>
                                <span class="text-2xs md:text-xs text-gray-400">2 нед.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Правая часть с чатом - скрыта на мобильных по умолчанию -->
            <div id="chat-container" class="hidden md:flex md:w-2/3 flex-col">
                <!-- Кнопка "Назад" для мобильных -->
                <div class="md:hidden p-2 border-b border-gray-200 bg-gray-50">
                    <button id="back-to-list" class="p-1 text-gray-600 hover:text-gray-900 rounded-full hover:bg-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                </div>

                <!-- Информация о выбранном кандидате -->
                <div class="border-b border-gray-200 p-3 md:p-4 bg-gray-50 flex items-center" id="selected-candidate-info">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-8 w-8 md:h-10 md:w-10 rounded-full overflow-hidden">
                            <img id="selected-candidate-avatar" src="https://randomuser.me/api/portraits/men/32.jpg"
                                alt="Аватар" class="h-full w-full object-cover">
                        </div>
                        <div class="ml-2 md:ml-3">
                            <p class="text-xs md:text-sm font-medium text-gray-900" id="selected-candidate-name">Иван Петров</p>
                            <p class="text-2xs md:text-xs text-gray-500" id="selected-candidate-role">Frontend разработчик</p>
                            <p class="text-2xs md:text-xs text-gray-400 mt-1" id="selected-candidate-status">На проверке тестового задания</p>
                        </div>
                    </div>
                </div>

                <!-- История сообщений -->
                <div class="chat-container overflow-y-auto p-3 md:p-4 space-y-3 md:space-y-4 flex-grow"
                    id="chat-messages">
                    <!-- Сообщения будут загружаться динамически -->
                </div>

                <!-- Форма отправки сообщения -->
                <div class="border-t border-gray-200 p-3 md:p-4 bg-gray-50">
                    <form id="chat-form" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="current-user-id" name="user_id" value="1">
                        
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
                        <input type="hidden" id="reply-to" name="reply_to" value="">

                        <div class="flex items-center space-x-2 md:space-x-3">
                            <!-- Поле ввода сообщения -->
                            <div class="flex-1">
                                <input type="text" id="message" name="message"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3 border text-sm md:text-base"
                                    placeholder="Введите сообщение..." required>
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
            </div>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const candidateList = document.getElementById('candidate-list');
        const chatMessages = document.getElementById('chat-messages');
        const chatForm = document.getElementById('chat-form');
        const attachBtn = document.getElementById('attach-btn');
        const fileInput = document.getElementById('attachment');
        const replyToInput = document.getElementById('reply-to');
        const replyPreview = document.getElementById('reply-preview');
        const replyContent = document.getElementById('reply-content');
        const cancelReplyBtn = document.getElementById('cancel-reply');
        const currentUserIdInput = document.getElementById('current-user-id');
        const selectedCandidateInfo = document.getElementById('selected-candidate-info');
        const selectedCandidateName = document.getElementById('selected-candidate-name');
        const selectedCandidateRole = document.getElementById('selected-candidate-role');
        const selectedCandidateStatus = document.getElementById('selected-candidate-status');
        const selectedCandidateAvatar = document.getElementById('selected-candidate-avatar');
        const candidateSearch = document.getElementById('candidate-search');
        const backToListBtn = document.getElementById('back-to-list');
        const candidateListContainer = document.getElementById('candidate-list-container');
        const chatContainer = document.getElementById('chat-container');
        const filePreview = document.getElementById('file-preview');
        const filePreviewName = document.getElementById('file-preview-name');
        const cancelFileBtn = document.getElementById('cancel-file');
        const isMobile = window.innerWidth < 768;

        // Данные кандидатов
        const candidates = {
            1: {
                id: 1,
                name: 'Иван Петров',
                role: 'Frontend разработчик',
                status: 'На проверке тестового задания',
                avatar: 'https://randomuser.me/api/portraits/men/32.jpg',
                unread: 2,
                lastMessage: {
                    text: 'Проверим ваше тестовое задание',
                    sender: 'hr',
                    time: '12:45'
                }
            },
            2: {
                id: 2,
                name: 'Анна Сидорова',
                role: 'UX/UI дизайнер',
                status: 'Ожидает собеседования',
                avatar: 'https://randomuser.me/api/portraits/women/44.jpg',
                unread: 0,
                lastMessage: {
                    text: 'Спасибо за обратную связь!',
                    sender: 'candidate',
                    time: 'Вчера'
                }
            },
            3: {
                id: 3,
                name: 'Дмитрий Волков',
                role: 'Backend разработчик',
                status: 'Отправил тестовое задание',
                avatar: 'https://randomuser.me/api/portraits/men/75.jpg',
                unread: 1,
                lastMessage: {
                    text: 'Здравствуйте, у меня вопрос...',
                    sender: 'candidate',
                    time: 'Пн'
                }
            },
            4: {
                id: 4,
                name: 'Елена Ковалева',
                role: 'Product Manager',
                status: 'На доработке тестового',
                avatar: 'https://randomuser.me/api/portraits/women/68.jpg',
                unread: 0,
                lastMessage: {
                    text: 'Отправьте исправленный вариант',
                    sender: 'hr',
                    time: '2 нед.'
                }
            }
        };

        // История чатов
        const chatHistories = {
            1: [{
                    id: 1,
                    sender: 'candidate',
                    text: 'Здравствуйте! Я отправил тестовое задание',
                    time: '10:30',
                    file: null,
                    fileUrl: null
                },
                {
                    id: 2,
                    sender: 'hr',
                    text: 'Спасибо, мы получили ваше задание. Проверим в течение 2 дней.',
                    time: '10:40',
                    file: null,
                    fileUrl: null
                },
                {
                    id: 3,
                    sender: 'candidate',
                    text: 'Да, я отправил тестовое задание. Есть вопросы?',
                    time: '12:45',
                    file: null,
                    fileUrl: null,
                    replyTo: 2,
                    originalMessage: 'Спасибо, мы получили ваше задание. Проверим в течение 2 дней.'
                }
            ],
            2: [{
                    id: 1,
                    sender: 'hr',
                    text: 'Ваше тестовое задание одобрено!',
                    time: '11:15',
                    file: 'review.pdf',
                    fileUrl: '#',
                    replyTo: null
                },
                {
                    id: 2,
                    sender: 'candidate',
                    text: 'Отлично! Что дальше?',
                    time: '11:20',
                    file: null,
                    fileUrl: null
                },
                {
                    id: 3,
                    sender: 'hr',
                    text: 'Назначим собеседование на следующей неделе',
                    time: '11:25',
                    file: null,
                    fileUrl: null
                },
                {
                    id: 4,
                    sender: 'candidate',
                    text: 'Спасибо за обратную связь!',
                    time: 'Вчера',
                    file: null,
                    fileUrl: null
                }
            ],
            3: [{
                    id: 1,
                    sender: 'candidate',
                    text: 'Здравствуйте, у меня вопрос по тестовому заданию',
                    time: 'Пн',
                    file: null,
                    fileUrl: null
                }
            ],
            4: [{
                    id: 1,
                    sender: 'hr',
                    text: 'К сожалению, ваше тестовое задание требует доработки',
                    time: '14:30',
                    file: 'feedback.pdf',
                    fileUrl: '#',
                    replyTo: null
                },
                {
                    id: 2,
                    sender: 'candidate',
                    text: 'Понял, исправлю и отправлю заново',
                    time: '2 нед.',
                    file: null,
                    fileUrl: null
                },
                {
                    id: 3,
                    sender: 'hr',
                    text: 'Отправьте исправленный вариант',
                    time: '2 нед.',
                    file: null,
                    fileUrl: null
                }
            ]
        };

        // Форматирование времени
        function formatTime(date) {
            const now = new Date();
            const diffDays = Math.floor((now - date) / (1000 * 60 * 60 * 24));

            if (diffDays === 0) {
                return date.toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } else if (diffDays === 1) {
                return 'Вчера';
            } else if (diffDays < 7) {
                return ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'][date.getDay()];
            } else {
                return `${Math.floor(diffDays / 7)} нед.`;
            }
        }

        // Подсветка и прокрутка к сообщению
        function highlightAndScrollToMessage(messageId) {
            // Удаляем предыдущую подсветку
            document.querySelectorAll('.highlighted-message').forEach(el => {
                el.classList.remove('highlighted-message');
            });

            const messageElement = document.querySelector(`[data-id="${messageId}"] .message-bubble`);
            if (messageElement) {
                // Прокручиваем к сообщению
                messageElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

                // Добавляем подсветку
                messageElement.classList.add('highlighted-message');

                // Убираем подсветку через 3 секунды
                setTimeout(() => {
                    messageElement.classList.remove('highlighted-message');
                }, 3000);
            }
        }

        // Обновление последнего сообщения в списке
        function updateLastMessage(userId, message, sender) {
            const candidate = candidates[userId];
            if (!candidate) return;

            const now = new Date();
            candidate.lastMessage = {
                text: message,
                sender: sender,
                time: formatTime(now)
            };

            const candidateItem = document.querySelector(`.candidate-item[data-user-id="${userId}"]`);
            if (candidateItem) {
                const lastMessageEl = candidateItem.querySelectorAll(
                    '.text-xs.text-gray-500, .text-2xs.text-gray-500')[1];
                const lastTimeEl = candidateItem.querySelector('.text-xs.text-gray-400, .text-2xs.text-gray-400');

                if (lastMessageEl) {
                    const prefix = sender === 'hr' ? 'Вы: ' : `${candidate.name.split(' ')[0]}: `;
                    lastMessageEl.textContent = prefix + message;
                }

                if (lastTimeEl) {
                    lastTimeEl.textContent = candidate.lastMessage.time;
                }
            }
        }

        // Загрузка истории чата
        function loadChatHistory(userId) {
            chatMessages.innerHTML = '';
            currentUserIdInput.value = userId;

            // Обновляем информацию о выбранном кандидате
            const candidate = candidates[userId];
            selectedCandidateName.textContent = candidate.name;
            selectedCandidateRole.textContent = candidate.role;
            selectedCandidateStatus.textContent = candidate.status;
            selectedCandidateAvatar.src = candidate.avatar;

            // Загружаем сообщения
            if (chatHistories[userId]) {
                chatHistories[userId].forEach(msg => {
                    addMessageToChat(msg);
                });
            }

            // Помечаем выбранного кандидата
            document.querySelectorAll('.candidate-item').forEach(item => {
                if (item.dataset.userId === userId.toString()) {
                    item.classList.add('bg-indigo-50');

                    // Сбрасываем счетчик непрочитанных
                    if (item.dataset.unread !== '0') {
                        item.dataset.unread = '0';
                        const unreadBadge = item.querySelector('.absolute');
                        if (unreadBadge) unreadBadge.remove();

                        // Обновляем данные в объекте candidates
                        candidates[userId].unread = 0;
                    }
                } else {
                    item.classList.remove('bg-indigo-50');
                }
            });

            // На мобильных устройствах переключаем вид на чат
            if (window.innerWidth < 768) {
                candidateListContainer.classList.add('hidden');
                chatContainer.classList.remove('hidden');
            }
        }

        // Обработчик выбора кандидата
        candidateList.addEventListener('click', function(e) {
            const candidateItem = e.target.closest('.candidate-item');
            if (candidateItem) {
                const userId = candidateItem.dataset.userId;
                loadChatHistory(userId);
            }
        });

        // Обработчик кнопки "Назад" на мобильных
        backToListBtn.addEventListener('click', function(e) {
            e.preventDefault();
            candidateListContainer.classList.remove('hidden');
            chatContainer.classList.add('hidden');
        });

        // Инициализация - на мобильных показываем список, на десктопах - первый чат
        if (window.innerWidth >= 768) {
            loadChatHistory(1);
        } else {
            candidateListContainer.classList.remove('hidden');
            chatContainer.classList.add('hidden');
        }

        // Добавление сообщения в чат
        function addMessageToChat(msg) {
            const isHr = msg.sender === 'hr';
            const messageClass = isHr ? 'hr-message' : 'candidate-message';
            const alignClass = isHr ? 'justify-end' : 'justify-start';

            let fileElement = '';
            if (msg.file) {
                fileElement = `
                <div class="mt-1 md:mt-2">
                    <a href="${msg.fileUrl}" download class="inline-flex items-center text-xs md:text-sm ${isHr ? 'text-indigo-200 hover:text-indigo-100' : 'text-indigo-600 hover:text-indigo-500'}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 md:h-4 md:w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                <div class="reply-container bg-${isHr ? 'indigo-700' : 'gray-200'} text-${isHr ? 'white' : 'gray-800'} text-2xs md:text-xs p-1 md:p-2 rounded mb-1 md:mb-2 border-l-4 border-${isHr ? 'indigo-300' : 'gray-500'} cursor-pointer" 
                     onclick="highlightAndScrollToMessage(${msg.replyTo})">
                    <p class="font-medium">${msg.sender === 'hr' ? 'Вы' : candidates[currentUserIdInput.value].name}:</p>
                    <p>${msg.originalMessage}</p>
                </div>
            `;
            }

            const messageElement = `
            <div class="flex ${alignClass} mb-2 md:mb-3" data-id="${msg.id}">
                <div class="message-bubble ${messageClass} p-2 md:p-3 max-w-xs md:max-w-md lg:max-w-lg">
                    ${replyElement}
                    <p class="text-xs md:text-sm">${msg.text}</p>
                    ${fileElement}
                    <div class="flex justify-between items-center mt-1 md:mt-2">
                        <p class="text-2xs md:text-xs ${isHr ? 'text-gray-300' : 'text-gray-500'}">${msg.time}</p>
                        <button class="reply-btn text-2xs md:text-xs ${isHr ? 'text-gray-300' : 'text-indigo-600'} hover:underline" data-id="${msg.id}" data-text="${msg.text}">Ответить</button>
                    </div>
                </div>
            </div>
        `;

            chatMessages.insertAdjacentHTML('beforeend', messageElement);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Обработчик поиска
        candidateSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            document.querySelectorAll('.candidate-item').forEach(item => {
                const name = item.querySelector('.text-sm.font-medium, .text-xs.font-medium')
                    .textContent
                    .toLowerCase();
                const role = item.querySelector(
                        '.text-xs.text-gray-500, .text-2xs.text-gray-500').textContent
                    .toLowerCase();
                const lastMessage = item.querySelectorAll(
                        '.text-xs.text-gray-500, .text-2xs.text-gray-500')[1]
                    .textContent.toLowerCase();

                if (name.includes(searchTerm) || role.includes(searchTerm) || lastMessage
                    .includes(searchTerm)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });

        // Обработчик кнопки "Ответить"
        chatMessages.addEventListener('click', function(e) {
            if (e.target.classList.contains('reply-btn')) {
                const messageId = e.target.dataset.id;
                const messageText = e.target.dataset.text;

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
            if (fileInput.files.length > 0) {
                filePreviewName.textContent = fileInput.files[0].name;
                filePreview.classList.remove('hidden');
            }
        });

        // Отмена прикрепления файла
        cancelFileBtn.addEventListener('click', function() {
            fileInput.value = '';
            filePreview.classList.add('hidden');
        });

        // Обработчик отправки формы
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const messageInput = document.getElementById('message');
            const message = messageInput.value.trim();
            const userId = currentUserIdInput.value;

            if (message || fileInput.files.length > 0) {
                const now = new Date();
                const timeString = formatTime(now);
                const fileName = fileInput.files.length > 0 ? fileInput.files[0].name : null;

                // Получаем текст оригинального сообщения для ответа
                let originalMessage = '';
                if (replyToInput.value) {
                    const repliedMsg = chatMessages.querySelector(
                        `[data-id="${replyToInput.value}"] .message-bubble`);
                    if (repliedMsg) {
                        originalMessage = repliedMsg.querySelector('p:not(.font-medium)').textContent;
                    }
                }

                // Создаем новое сообщение
                const newMessage = {
                    id: Date.now(),
                    sender: 'hr',
                    text: message,
                    time: timeString,
                    file: fileName,
                    fileUrl: '#',
                    replyTo: replyToInput.value ? parseInt(replyToInput.value) : null,
                    originalMessage: originalMessage
                };

                // Добавляем сообщение в историю
                if (!chatHistories[userId]) {
                    chatHistories[userId] = [];
                }
                chatHistories[userId].push(newMessage);

                // Добавляем сообщение в чат
                addMessageToChat(newMessage);

                // Обновляем последнее сообщение в списке кандидатов
                updateLastMessage(userId, message || 'Файл', 'hr');

                // Очищаем форму
                messageInput.value = '';
                fileInput.value = '';
                filePreview.classList.add('hidden');
                replyToInput.value = '';
                replyPreview.classList.add('hidden');
            }
        });

        // Обработчик кнопки "Назад" на мобильных
        if (backToListBtn) {
            backToListBtn.addEventListener('click', function() {
                candidateListContainer.classList.remove('hidden');
                chatContainer.classList.add('hidden');
            });
        }

        // Инициализация чата с первым кандидатом
        if (!isMobile) {
            loadChatHistory(1);
        }

        // Делаем функцию доступной глобально для обработчиков в HTML
        window.highlightAndScrollToMessage = highlightAndScrollToMessage;
    });
</script>

<style>
    .chat-container {
        height: calc(100vh - 250px);
        overflow-y: auto;
        padding-right: 8px;
    }

    @media (min-width: 768px) {
        .chat-container {
            height: calc(100vh - 320px);
        }
    }

    .message-bubble {
        max-width: 80%;
        word-break: break-word;
        transition: all 0.3s ease;
    }

    .hr-message {
        background-color: #6366f1;
        color: white;
        border-radius: 14px 14px 0 14px;
    }

    .candidate-message {
        background-color: #e5e7eb;
        color: #111827;
        border-radius: 14px 14px 14px 0;
    }

    .reply-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
        transition: opacity 0.2s;
    }

    .reply-btn:hover {
        opacity: 0.8;
    }

    .reply-container {
        max-width: 100%;
        overflow: hidden;
        transition: all 0.2s ease;
    }

    .reply-container:hover {
        opacity: 0.9;
        background-color: rgba(0, 0, 0, 0.05);
        transform: translateX(3px);
    }

    .highlighted-message {
        animation: highlightPulse 3s ease-out;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.5);
        position: relative;
    }

    @keyframes highlightPulse {
        0% {
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.7);
        }

        70% {
            box-shadow: 0 0 0 12px rgba(99, 102, 241, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(99, 102, 241, 0);
        }
    }

    #attach-btn:hover {
        background-color: #f3f4f6;
    }

    .candidate-item {
        transition: background-color 0.2s;
    }

    .candidate-item:hover {
        background-color: #f9fafb;
    }

    .bg-indigo-50 {
        background-color: #eef2ff;
    }

    /* Стили для скроллбара */
    .chat-container::-webkit-scrollbar {
        width: 6px;
    }

    .chat-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .chat-container::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }

    .chat-container::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    /* Дополнительные классы для очень маленького текста */
    .text-2xs {
        font-size: 0.65rem;
        line-height: 0.9rem;
    }

    /* Стили для превью файла */
    #file-preview {
        transition: all 0.2s ease;
    }

    #file-preview:hover {
        background-color: #e5e7eb;
    }

    #cancel-file {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
    }

    #cancel-file:hover {
        color: #dc2626;
    }

    /* Стили для превью ответа */
    #reply-preview {
        transition: all 0.2s ease;
    }

    #reply-preview:hover {
        background-color: #e5e7eb;
    }

    #cancel-reply {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
    }

    #cancel-reply:hover {
        color: #dc2626;
    }
</style>
@endsection