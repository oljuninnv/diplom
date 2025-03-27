@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">Управление созвонами</h1>

        <!-- Фильтры и поиск -->
        <div class="mb-6 bg-white rounded-lg shadow p-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <!-- Поиск -->
                <div class="w-full md:w-1/3">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Поиск</label>
                    <input type="text" id="search" placeholder="Имя пользователя..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Фильтр по типу -->
                <div class="w-full md:w-1/3">
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Тип созвона</label>
                    <select id="type"
                        class="w-full py-2 px-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Все типы</option>
                        <option value="primary">Первичный</option>
                        <option value="technical">Технический</option>
                        <option value="final">Финальный</option>
                    </select>
                </div>

                <!-- Фильтр по дате -->
                <div class="w-full md:w-1/3">
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Дата созвона</label>
                    <input type="date" id="date"
                        class="w-full py-2 px-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mt-4">
                <!-- Сортировка -->
                <div class="w-full md:w-1/3">
                    <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Сортировка</label>
                    <select id="sort"
                        class="w-full py-2 px-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="datetime_asc">Дата и время (по возрастанию)</option>
                        <option value="datetime_desc">Дата и время (по убыванию)</option>
                    </select>
                </div>

                <!-- Элементов на странице -->
                <div class="w-full md:w-1/3">
                    <label for="perPage" class="block text-sm font-medium text-gray-700 mb-1">На странице</label>
                    <select id="perPage"
                        class="w-full py-2 px-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                </div>
                
                <!-- Кнопка добавления -->
                <div class="w-full md:w-1/3 flex items-end">
                    <button onclick="openModal('add-modal')" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md flex items-center justify-center gap-2">
                        <i class="fas fa-plus"></i> Назначить созвон
                    </button>
                </div>
            </div>
        </div>

        <!-- Таблица -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Пользователь</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата и время</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ссылка</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Тип</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="calls-table">
                        <!-- Данные будут загружены через JavaScript -->
                    </tbody>
                </table>
            </div>

            <!-- Пагинация -->
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    <button id="prev-page-mobile"
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Назад</button>
                    <button id="next-page-mobile"
                        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Вперед</button>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Показано <span id="pagination-from">1</span> - <span id="pagination-to">10</span> из <span
                                id="pagination-total">0</span>
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <button id="prev-page"
                                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div id="pagination-numbers" class="flex"></div>
                            <button id="next-page"
                                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно просмотра пользователя -->
    <div id="user-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold" id="modal-user-name">Иван Иванов</h3>
                <button onclick="closeModal('user-modal')" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="space-y-4">
                <div class="flex items-center gap-4">
                    <img id="modal-user-avatar" src="https://via.placeholder.com/80" alt="Аватар" class="w-20 h-20 rounded-full">
                </div>
                <div>
                    <p class="text-gray-600">Телефон:</p>
                    <p class="font-medium" id="modal-user-phone">+7 (999) 123-45-67</p>
                </div>
                <div>
                    <p class="text-gray-600">Email:</p>
                    <p class="font-medium" id="modal-user-email">ivan@example.com</p>
                </div>
                <div>
                    <p class="text-gray-600">Telegram:</p>
                    <p class="font-medium" id="modal-user-telegram">@ivanov</p>
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button onclick="closeModal('user-modal')" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-md">Закрыть</button>
            </div>
        </div>
    </div>
    
    <!-- Модальное окно добавления/редактирования -->
    <div id="add-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold" id="modal-title">Назначить созвон</h3>
                <button onclick="closeModal('add-modal')" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="call-form" class="space-y-4">
                <input type="hidden" id="call-id">
                <div>
                    <label for="user-select" class="block text-sm font-medium text-gray-700 mb-1">Пользователь *</label>
                    <select id="user-select" required
                        class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Выберите пользователя</option>
                        <!-- Опции будут заполнены через JS -->
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="call-date" class="block text-sm font-medium text-gray-700 mb-1">Дата *</label>
                        <input type="date" id="call-date" required
                            class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="call-time" class="block text-sm font-medium text-gray-700 mb-1">Время *</label>
                        <input type="time" id="call-time" required
                            class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div>
                    <label for="call-link" class="block text-sm font-medium text-gray-700 mb-1">Ссылка на конференцию *</label>
                    <input type="url" id="call-link" required
                        class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                        placeholder="https://meet.example.com/room123">
                </div>
                <div>
                    <label for="call-type" class="block text-sm font-medium text-gray-700 mb-1">Тип созвона *</label>
                    <select id="call-type" required
                        class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="primary">Первичный</option>
                        <option value="technical">Технический</option>
                        <option value="final">Финальный</option>
                    </select>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="closeModal('add-modal')" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-md">Отмена</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">Сохранить</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Фиктивные данные пользователей
        const users = [
            { id: 1, name: "Иван Иванов", avatar: "https://randomuser.me/api/portraits/men/1.jpg", phone: "+7 (999) 123-45-67", email: "ivan@example.com", telegram: "@ivanov" },
            { id: 2, name: "Петр Петров", avatar: "https://randomuser.me/api/portraits/men/2.jpg", phone: "+7 (999) 234-56-78", email: "petr@example.com", telegram: "@petrov" },
            { id: 3, name: "Мария Сидорова", avatar: "https://randomuser.me/api/portraits/women/1.jpg", phone: "+7 (999) 345-67-89", email: "maria@example.com", telegram: "@sidorova" },
            { id: 4, name: "Алексей Алексеев", avatar: "https://randomuser.me/api/portraits/men/3.jpg", phone: "+7 (999) 456-78-90", email: "alex@example.com", telegram: "@alexeev" },
            { id: 5, name: "Елена Еленова", avatar: "https://randomuser.me/api/portraits/women/2.jpg", phone: "+7 (999) 567-89-01", email: "elena@example.com", telegram: "@elenova" }
        ];

        // Фиктивные данные созвонов
        let calls = [
            { id: 1, userId: 1, date: "2023-06-15", time: "10:00", link: "https://meet.example.com/room1", type: "primary" },
            { id: 2, userId: 2, date: "2023-06-16", time: "14:30", link: "https://meet.example.com/room2", type: "technical" },
            { id: 3, userId: 3, date: "2023-06-17", time: "11:15", link: "https://meet.example.com/room3", type: "final" },
            { id: 4, userId: 4, date: "2023-06-18", time: "16:45", link: "https://meet.example.com/room4", type: "primary" },
            { id: 5, userId: 5, date: "2023-06-19", time: "09:30", link: "https://meet.example.com/room5", type: "technical" },
            { id: 6, userId: 1, date: "2023-06-20", time: "13:00", link: "https://meet.example.com/room6", type: "final" },
            { id: 7, userId: 2, date: "2023-06-21", time: "15:20", link: "https://meet.example.com/room7", type: "primary" },
            { id: 8, userId: 3, date: "2023-06-22", time: "10:45", link: "https://meet.example.com/room8", type: "technical" },
            { id: 9, userId: 4, date: "2023-06-23", time: "12:30", link: "https://meet.example.com/room9", type: "final" },
            { id: 10, userId: 5, date: "2023-06-24", time: "17:00", link: "https://meet.example.com/room10", type: "primary" },
            { id: 11, userId: 1, date: "2023-06-25", time: "10:30", link: "https://meet.example.com/room11", type: "technical" },
            { id: 12, userId: 2, date: "2023-06-26", time: "14:00", link: "https://meet.example.com/room12", type: "final" }
        ];

        // DOM элементы
        const callsTable = document.getElementById('calls-table');
        const searchInput = document.getElementById('search');
        const typeFilter = document.getElementById('type');
        const dateFilter = document.getElementById('date');
        const sortSelect = document.getElementById('sort');
        const perPageSelect = document.getElementById('perPage');
        const userSelect = document.getElementById('user-select');
        const callForm = document.getElementById('call-form');
        const paginationElements = {
            prev: document.getElementById('prev-page'),
            next: document.getElementById('next-page'),
            prevMobile: document.getElementById('prev-page-mobile'),
            nextMobile: document.getElementById('next-page-mobile'),
            numbers: document.getElementById('pagination-numbers'),
            from: document.getElementById('pagination-from'),
            to: document.getElementById('pagination-to'),
            total: document.getElementById('pagination-total')
        };

        // Настройки пагинации
        let currentPage = 1;
        let itemsPerPage = parseInt(perPageSelect.value);
        let filteredCalls = [...calls];

        // Инициализация страницы
        document.addEventListener('DOMContentLoaded', function() {
            // Заполняем select пользователей
            populateUserSelect();
            
            // Первоначальная загрузка данных
            filterCalls();
            
            // Настройка обработчиков событий для фильтров
            setupFilterEventListeners();
            
            // Настройка обработчиков для пагинации
            setupPaginationEventListeners();
            
            // Настройка обработчика формы
            setupFormEventListener();
        });

        // Настройка обработчиков событий для фильтров
        function setupFilterEventListeners() {
            // Обработчики для автоматического обновления при изменении фильтров
            [searchInput, typeFilter, dateFilter, sortSelect, perPageSelect].forEach(element => {
                element.addEventListener('change', function() {
                    // Для perPage обновляем только пагинацию
                    if (element === perPageSelect) {
                        updateItemsPerPage();
                    } else {
                        filterCalls();
                    }
                });
                
                // Для текстового поля и select'ов добавляем input событие
                if (element === searchInput || element === typeFilter || element === dateFilter) {
                    element.addEventListener('input', function() {
                        filterCalls();
                    });
                }
            });
        }

        // Настройка обработчиков для пагинации
        function setupPaginationEventListeners() {
            paginationElements.prev.addEventListener('click', goToPrevPage);
            paginationElements.next.addEventListener('click', goToNextPage);
            paginationElements.prevMobile.addEventListener('click', goToPrevPage);
            paginationElements.nextMobile.addEventListener('click', goToNextPage);
        }

        // Настройка обработчика формы
        function setupFormEventListener() {
            callForm.addEventListener('submit', function(e) {
                e.preventDefault();
                saveCall();
            });
        }

        // Заполнение select пользователей
        function populateUserSelect() {
            userSelect.innerHTML = '<option value="">Выберите пользователя</option>';
            
            users.forEach(user => {
                const option = document.createElement('option');
                option.value = user.id;
                option.textContent = user.name;
                userSelect.appendChild(option);
            });
        }

        // Отображение таблицы созвонов
        function renderCallsTable() {
            const startIndex = (currentPage - 1) * itemsPerPage;
            const paginatedItems = filteredCalls.slice(startIndex, startIndex + itemsPerPage);

            callsTable.innerHTML = paginatedItems.length > 0 ?
                paginatedItems.map(call => {
                    const user = users.find(u => u.id === call.userId);
                    const typeText = {
                        'primary': 'Первичный',
                        'technical': 'Технический',
                        'final': 'Финальный'
                    }[call.type] || call.type;
                    
                    const typeColor = {
                        'primary': 'bg-blue-100 text-blue-800',
                        'technical': 'bg-yellow-100 text-yellow-800',
                        'final': 'bg-green-100 text-green-800'
                    }[call.type] || 'bg-gray-100 text-gray-800';
                    
                    const datetime = new Date(`${call.date}T${call.time}`);
                    const formattedDate = datetime.toLocaleDateString('ru-RU', { 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric',
                        weekday: 'short'
                    });
                    const formattedTime = datetime.toLocaleTimeString('ru-RU', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    return `
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center cursor-pointer" onclick="showUserModal(${user.id})">
                                    <img class="h-10 w-10 rounded-full" src="${user.avatar}" alt="${user.name}">
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">${user.name}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">${formattedDate}</div>
                                <div class="text-sm text-gray-500">${formattedTime}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="${call.link}" target="_blank" class="text-blue-600 hover:text-blue-900 truncate max-w-xs inline-block">
                                    ${call.link.replace(/^https?:\/\//, '')}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${typeColor}">
                                    ${typeText}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button onclick="editCall(${call.id})" class="text-blue-600 hover:text-blue-900 mr-3">Редактировать</button>
                                <button onclick="deleteCall(${call.id})" class="text-red-600 hover:text-red-900">Удалить</button>
                            </td>
                        </tr>
                    `;
                }).join('') :
                `<tr><td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Нет данных для отображения</td></tr>`;

            updatePagination();
        }

        // Фильтрация и сортировка созвонов
        function filterCalls() {
            const searchTerm = searchInput.value.toLowerCase();
            const type = typeFilter.value;
            const date = dateFilter.value;
            const sortValue = sortSelect.value;

            filteredCalls = calls.filter(call => {
                const user = users.find(u => u.id === call.userId);
                const matchesSearch = user.name.toLowerCase().includes(searchTerm);
                const matchesType = type ? call.type === type : true;
                const matchesDate = date ? call.date === date : true;
                
                return matchesSearch && matchesType && matchesDate;
            });

            // Сортировка
            filteredCalls.sort((a, b) => {
                const dateTimeA = new Date(`${a.date}T${a.time}`).getTime();
                const dateTimeB = new Date(`${b.date}T${b.time}`).getTime();
                
                return sortValue === 'datetime_asc' ? 
                    dateTimeA - dateTimeB : 
                    dateTimeB - dateTimeA;
            });

            currentPage = 1;
            renderCallsTable();
        }

        // Пагинация
        function updateItemsPerPage() {
            itemsPerPage = parseInt(perPageSelect.value);
            currentPage = 1;
            renderCallsTable();
        }

        function updatePagination() {
            const totalItems = filteredCalls.length;
            const totalPages = Math.ceil(totalItems / itemsPerPage);

            paginationElements.from.textContent = (currentPage - 1) * itemsPerPage + 1;
            paginationElements.to.textContent = Math.min(currentPage * itemsPerPage, totalItems);
            paginationElements.total.textContent = totalItems;

            // Обновляем кнопки
            paginationElements.prev.disabled = currentPage === 1;
            paginationElements.next.disabled = currentPage === totalPages;
            paginationElements.prevMobile.disabled = currentPage === 1;
            paginationElements.nextMobile.disabled = currentPage === totalPages;

            // Обновляем номера страниц
            paginationElements.numbers.innerHTML = '';

            const maxVisiblePages = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
            let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

            if (endPage - startPage + 1 < maxVisiblePages) {
                startPage = Math.max(1, endPage - maxVisiblePages + 1);
            }

            // Добавляем кнопки страниц
            for (let i = startPage; i <= endPage; i++) {
                const pageBtn = document.createElement('button');
                pageBtn.className =
                    `relative inline-flex items-center px-4 py-2 border text-sm font-medium ${i === currentPage ? 'bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50'}`;
                pageBtn.textContent = i;
                pageBtn.addEventListener('click', () => {
                    currentPage = i;
                    renderCallsTable();
                });
                paginationElements.numbers.appendChild(pageBtn);
            }
        }

        function goToPrevPage(e) {
            e.preventDefault();
            if (currentPage > 1) {
                currentPage--;
                renderCallsTable();
            }
        }

        function goToNextPage(e) {
            e.preventDefault();
            const totalPages = Math.ceil(filteredCalls.length / itemsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                renderCallsTable();
            }
        }

        // Модальные окна
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        // Показать модальное окно пользователя
        window.showUserModal = function(userId) {
            const user = users.find(u => u.id === userId);
            if (!user) return;
            
            document.getElementById('modal-user-name').textContent = user.name;
            document.getElementById('modal-user-avatar').src = user.avatar;
            document.getElementById('modal-user-phone').textContent = user.phone;
            document.getElementById('modal-user-email').textContent = user.email;
            document.getElementById('modal-user-telegram').textContent = user.telegram;
            
            openModal('user-modal');
        };

        // Редактирование созвона
        window.editCall = function(callId) {
            const call = calls.find(c => c.id === callId);
            if (!call) return;
            
            document.getElementById('modal-title').textContent = 'Редактировать созвон';
            document.getElementById('call-id').value = call.id;
            document.getElementById('user-select').value = call.userId;
            document.getElementById('call-date').value = call.date;
            document.getElementById('call-time').value = call.time;
            document.getElementById('call-link').value = call.link;
            document.getElementById('call-type').value = call.type;
            
            openModal('add-modal');
        };

        // Удаление созвона
        window.deleteCall = function(callId) {
            if (confirm('Вы уверены, что хотите удалить этот созвон?')) {
                calls = calls.filter(c => c.id !== callId);
                filterCalls();
            }
        };

        // Сохранение созвона
        function saveCall() {
            const callId = document.getElementById('call-id').value;
            const userId = parseInt(document.getElementById('user-select').value);
            const date = document.getElementById('call-date').value;
            const time = document.getElementById('call-time').value;
            const link = document.getElementById('call-link').value;
            const type = document.getElementById('call-type').value;
            
            // Валидация формы
            if (!userId || !date || !time || !link || !type) {
                alert('Пожалуйста, заполните все обязательные поля (помечены *)');
                return;
            }
            
            if (callId) {
                // Редактирование существующего созвона
                const index = calls.findIndex(c => c.id === parseInt(callId));
                if (index !== -1) {
                    calls[index] = { ...calls[index], userId, date, time, link, type };
                }
            } else {
                // Добавление нового созвона
                const newId = calls.length > 0 ? Math.max(...calls.map(c => c.id)) + 1 : 1;
                calls.push({ id: newId, userId, date, time, link, type });
            }
            
            closeModal('add-modal');
            filterCalls();
            resetForm();
        }

        // Сброс формы
        function resetForm() {
            document.getElementById('call-form').reset();
            document.getElementById('call-id').value = '';
            document.getElementById('modal-title').textContent = 'Назначить созвон';
        }
    </script>
@endsection