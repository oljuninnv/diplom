@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">Кандидаты на тестовое задание</h1>

        <!-- Фильтры и поиск -->
        <div class="mb-6 bg-white rounded-lg shadow p-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <!-- Поиск -->
                <div class="w-full md:w-1/3">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Поиск</label>
                    <input type="text" id="search" placeholder="Имя или задание..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Фильтр по статусу -->
                <div class="w-full md:w-1/3">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Статус</label>
                    <select id="status"
                        class="w-full py-2 px-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Все статусы</option>
                        <option value="в процессе">В процессе</option>
                        <option value="на проверке">На проверке</option>
                        <option value="одобрено">Одобрено</option>
                        <option value="доработка">Доработка</option>
                        <option value="выполнено">Выполнено</option>
                        <option value="провалено">Провалено</option>
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
            </div>
        </div>

        <!-- Таблица -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Кандидат</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Задание</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Дедлайн</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ссылка на задание</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Статус</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Действия</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="candidates-table">
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

    <!-- Модальное окно информации о кандидате -->
    @include('candidates.modals.candidate-info')

    <!-- Модальное окно информации о задании -->
    @include('candidates.modals.task-info')

    <!-- Модальное окно смены статуса -->
    @include('candidates.modals.change-status')

    <!-- Модальное окно создания отчета -->
    @include('candidates.modals.create-report')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Фиктивные данные
            const candidates = [{
                    id: 1,
                    name: "Иван Иванов",
                    avatar: "https://i.pravatar.cc/150?img=1",
                    email: "ivan@example.com",
                    phone: "+7 (123) 456-7890",
                    telegram: "@ivanov",
                    task: {
                        id: 101,
                        title: "Разработка SPA на Vue.js",
                        difficulty: "Средняя",
                        description: "Создать одностраничное приложение с использованием Vue 3 и Vuex",
                        document: "/tasks/vue-test.pdf",
                        github: "https://github.com/ivanov/vue-test-task",
                        status: "в процессе",
                        deadline: "15.06.2023",
                        created_at: "2023-05-10"
                    }
                },
                {
                    id: 2,
                    name: "Мария Петрова",
                    avatar: "https://i.pravatar.cc/150?img=2",
                    email: "maria@example.com",
                    phone: "+7 (123) 456-7891",
                    telegram: "@petrova",
                    task: {
                        id: 102,
                        title: "Создание API на Node.js",
                        difficulty: "Высокая",
                        description: "Разработать RESTful API с использованием Express.js",
                        document: "/tasks/node-api.pdf",
                        status: "завершено",
                        deadline: "01.07.2023",
                        created_at: "2023-06-01"
                    }
                },
                {
                    id: 3,
                    name: "Алексей Смирнов",
                    avatar: "https://i.pravatar.cc/150?img=3",
                    email: "alexey@example.com",
                    phone: "+7 (123) 456-7892",
                    telegram: "@smirnov",
                    task: {
                        id: 103,
                        title: "Разработка мобильного приложения",
                        difficulty: "Средняя",
                        description: "Создать кроссплатформенное приложение на React Native",
                        document: "/tasks/react-native-app.pdf",
                        status: "в процессе",
                        deadline: "20.08.2023",
                        created_at: "2023-07-15"
                    }
                },
                {
                    id: 4,
                    name: "Ольга Васильева",
                    avatar: "https://i.pravatar.cc/150?img=4",
                    email: "olga@example.com",
                    phone: "+7 (123) 456-7893",
                    telegram: "@vasilieva",
                    task: {
                        id: 104,
                        title: "Дизайн веб-сайта",
                        difficulty: "Низкая",
                        description: "Создать макет для нового корпоративного сайта",
                        document: "/tasks/web-design.pdf",
                        status: "в процессе",
                        deadline: "10.09.2023",
                        created_at: "2023-08-01"
                    }
                },
                {
                    id: 5,
                    name: "Дмитрий Кузнецов",
                    avatar: "https://i.pravatar.cc/150?img=5",
                    email: "dmitry@example.com",
                    phone: "+7 (123) 456-7894",
                    telegram: "@kuznetsov",
                    task: {
                        id: 105,
                        title: "Тестирование программного обеспечения",
                        difficulty: "Средняя",
                        description: "Провести тестирование нового программного обеспечения",
                        document: "/tasks/software-testing.pdf",
                        status: "завершено",
                        deadline: "30.06.2023",
                        created_at: "2023-05-20"
                    }
                },
                {
                    id: 6,
                    name: "Елена Федорова",
                    avatar: "https://i.pravatar.cc/150?img=6",
                    email: "elena@example.com",
                    phone: "+7 (123) 456-7895",
                    telegram: "@fedorova",
                    task: {
                        id: 106,
                        title: "Анализ данных",
                        difficulty: "Высокая",
                        description: "Провести анализ данных для бизнес-отчета",
                        document: "/tasks/data-analysis.pdf",
                        status: "в процессе",
                        deadline: "15.10.2023",
                        created_at: "2023-09-01"
                    }
                },
                {
                    id: 7,
                    name: "Сергей Николаев",
                    avatar: "https://i.pravatar.cc/150?img=7",
                    email: "sergey@example.com",
                    phone: "+7 (123) 456-7896",
                    telegram: "@nikolaev",
                    task: {
                        id: 107,
                        title: "Оптимизация базы данных",
                        difficulty: "Средняя",
                        description: "Оптимизировать существующую базу данных",
                        document: "/tasks/db-optimization.pdf",
                        status: "в процессе",
                        deadline: "25.11.2023",
                        created_at: "2023-10-01"
                    }
                },
                {
                    id: 8,
                    name: "Анна Сергеева",
                    avatar: "https://i.pravatar.cc/150?img=8",
                    email: "anna@example.com",
                    phone: "+7 (123) 456-7897",
                    telegram: "@sergeeva",
                    task: {
                        id: 108,
                        title: "Разработка чат-бота",
                        difficulty: "Высокая",
                        description: "Создать чат-бота для поддержки клиентов",
                        document: "/tasks/chatbot-development.pdf",
                        status: "в процессе",
                        deadline: "05.12.2023",
                        created_at: "2023-11-01"
                    }
                },
                {
                    id: 9,
                    name: "Виктория Лебедева",
                    avatar: "https://i.pravatar.cc/150?img=9",
                    email: "victoria@example.com",
                    phone: "+7 (123) 456-7898",
                    telegram: "@lebedeva",
                    task: {
                        id: 109,
                        title: "Создание презентации",
                        difficulty: "Низкая",
                        description: "Подготовить презентацию для конференции",
                        document: "/tasks/presentation.pdf",
                        status: "завершено",
                        deadline: "15.06.2023",
                        created_at: "2023-05-15"
                    }
                },
                {
                    id: 10,
                    name: "Артем Григорьев",
                    avatar: "https://i.pravatar.cc/150?img=10",
                    email: "artem@example.com",
                    phone: "+7 (123) 456-7899",
                    telegram: "@grigorev",
                    task: {
                        id: 110,
                        title: "Разработка игры",
                        difficulty: "Высокая",
                        description: "Создать простую 2D-игру на Unity",
                        document: "/tasks/game-development.pdf",
                        status: "в процессе",
                        deadline: "01.01.2024",
                        created_at: "2023-12-01"
                    }
                },
                {
                    id: 11,
                    name: "Ксения Соколова",
                    avatar: "https://i.pravatar.cc/150?img=11",
                    email: "kseniya@example.com",
                    phone: "+7 (123) 456-7800",
                    telegram: "@sokolova",
                    task: {
                        id: 111,
                        title: "Создание сайта-визитки",
                        difficulty: "Низкая",
                        description: "Разработать сайт-визитку для личного бренда",
                        document: "/tasks/landing-page.pdf",
                        status: "в процессе",
                        deadline: "20.02.2024",
                        created_at: "2024-01-01"
                    }
                }
            ];

            // DOM элементы
            const candidatesTable = document.getElementById('candidates-table');
            const searchInput = document.getElementById('search');
            const statusFilter = document.getElementById('status');
            const perPageSelect = document.getElementById('perPage');
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
            let filteredCandidates = [...candidates];

            // Инициализация
            function init() {
                renderTable();
                setupEventListeners();
            }

            // Настройка обработчиков событий
            function setupEventListeners() {
                searchInput.addEventListener('input', filterCandidates);
                statusFilter.addEventListener('change', filterCandidates);
                perPageSelect.addEventListener('change', updateItemsPerPage);

                // Пагинация
                paginationElements.prev.addEventListener('click', goToPrevPage);
                paginationElements.next.addEventListener('click', goToNextPage);
                paginationElements.prevMobile.addEventListener('click', goToPrevPage);
                paginationElements.nextMobile.addEventListener('click', goToNextPage);
            }

            // Рендер таблицы
            function renderTable() {
                const startIndex = (currentPage - 1) * itemsPerPage;
                const paginatedItems = filteredCandidates.slice(startIndex, startIndex + itemsPerPage);

                candidatesTable.innerHTML = paginatedItems.length > 0 ?
                    paginatedItems.map(candidate => `
    <tr class="hover:bg-gray-50">
        <td class="px-6 py-4 whitespace-nowrap">
            <div class="flex items-center cursor-pointer" onclick="showCandidateModal(${candidate.id})">
                <img class="h-10 w-10 rounded-full" src="${candidate.avatar}" alt="${candidate.name}">
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">${candidate.name}</div>
                </div>
            </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <div class="text-sm text-gray-900 cursor-pointer" onclick="showTaskModal(${candidate.id})">${candidate.task.title}</div>
            <div class="text-sm text-gray-500">${candidate.task.difficulty}</div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm ${isDeadlinePassed(candidate.task.deadline) ? 'text-red-600' : 'text-gray-500'}">
            ${candidate.task.deadline}
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
    ${candidate.task.github ? 
        `<a href="${candidate.task.github}" target="_blank" class="text-blue-600 hover:text-blue-900">
                ${candidate.task.github}
            </a>` 
        : ''
    }
</td>
        <td class="px-6 py-4 whitespace-nowrap">
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${getStatusClass(candidate.task.status)}">
                ${candidate.task.status}
            </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
            <button onclick="showStatusModal(${candidate.id})" class="text-blue-600 hover:text-blue-900 mr-3">Изменить статус</button>
            <button onclick="showReportModal(${candidate.id})" class="text-indigo-600 hover:text-indigo-900">Создать отчёт</button>
        </td>
    </tr>
`).join('') :
                    `<tr><td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Нет данных для отображения</td></tr>`;

                updatePagination();
            }

            // Функции для модальных окон (должны быть глобальными)
            window.showCandidateModal = function(candidateId) {
                const candidate = candidates.find(c => c.id === candidateId);
                // Заполняем модальное окно данными кандидата
                document.getElementById('modal-candidate-name').textContent = candidate.name;
                document.getElementById('modal-candidate-email').textContent = candidate.email;
                document.getElementById('modal-candidate-phone').textContent = candidate.phone;
                document.getElementById('modal-candidate-telegram').textContent = candidate.telegram;
                // Показываем модальное окно
                document.getElementById('candidate-modal').classList.remove('hidden');
            };

            window.showTaskModal = function(candidateId) {
                const candidate = candidates.find(c => c.id === candidateId);
                // Заполняем модальное окно данными задания
                document.getElementById('modal-task-title').textContent = candidate.task.title;
                document.getElementById('modal-task-difficulty').textContent = candidate.task.difficulty;
                document.getElementById('modal-task-description').textContent = candidate.task.description;
                document.getElementById('modal-task-document-link').href = candidate.task.document;
                // Показываем модальное окно
                document.getElementById('task-modal').classList.remove('hidden');
            };

            window.showStatusModal = function(candidateId) {
                const candidate = candidates.find(c => c.id === candidateId);
                // Заполняем модальное окно
                document.getElementById('status-candidate-name').textContent = candidate.name;
                // Сбрасываем выбор статуса
                document.querySelectorAll('input[name="status"]').forEach(radio => radio.checked = false);
                // Показываем модальное окно
                document.getElementById('status-modal').classList.remove('hidden');
            };

            window.showReportModal = function(candidateId) {
                const candidate = candidates.find(c => c.id === candidateId);
                // Заполняем модальное окно
                document.getElementById('report-candidate-name').textContent = candidate.name;
                // Сбрасываем форму
                document.getElementById('report-form').reset();
                // Показываем модальное окно
                document.getElementById('report-modal').classList.remove('hidden');
            };

            // Вспомогательные функции
            function isDeadlinePassed(deadline) {
                const deadlineDate = new Date(deadline.split('.').reverse().join('-'));
                return deadlineDate < new Date();
            }

            function getStatusClass(status) {
                const classes = {
                    'в процессе': 'bg-blue-100 text-blue-800',
                    'на проверке': 'bg-purple-100 text-purple-800',
                    'одобрено': 'bg-green-100 text-green-800',
                    'доработка': 'bg-yellow-100 text-yellow-800',
                    'выполнено': 'bg-indigo-100 text-indigo-800',
                    'провалено': 'bg-red-100 text-red-800'
                };
                return classes[status] || 'bg-gray-100 text-gray-800';
            }

            // Фильтрация и сортировка
            function filterCandidates() {
                const searchTerm = searchInput.value.toLowerCase();
                const status = statusFilter.value;

                filteredCandidates = candidates.filter(candidate => {
                    const matchesSearch = candidate.name.toLowerCase().includes(searchTerm) ||
                        candidate.task.title.toLowerCase().includes(searchTerm);
                    const matchesStatus = status ? candidate.task.status === status : true;
                    return matchesSearch && matchesStatus;
                });

                currentPage = 1;
                renderTable();
            }

            // Пагинация
            function updateItemsPerPage() {
                itemsPerPage = parseInt(perPageSelect.value);
                currentPage = 1;
                renderTable();
            }

            function updatePagination() {
                const totalItems = filteredCandidates.length;
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
                        renderTable();
                    });
                    paginationElements.numbers.appendChild(pageBtn);
                }
            }

            function goToPrevPage(e) {
                e.preventDefault();
                if (currentPage > 1) {
                    currentPage--;
                    renderTable();
                }
            }

            function goToNextPage(e) {
                e.preventDefault();
                const totalPages = Math.ceil(filteredCandidates.length / itemsPerPage);
                if (currentPage < totalPages) {
                    currentPage++;
                    renderTable();
                }
            }

            // Закрытие модальных окон
            document.querySelectorAll('.modal-close').forEach(btn => {
                btn.addEventListener('click', function() {
                    this.closest('.modal').classList.add('hidden');
                });
            });

            // Запускаем приложение
            init();
        });
    </script>
@endsection
