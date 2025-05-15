@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">Выполняемые задания</h1>

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
                        <option value="2">2</option>
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
                                Тьютор</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                HR-менеджер</th>
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

    <!-- Модальное окно информации о тьюторе -->
    @include('workers.modals.tutor-info')

    <!-- Модальное окно информации о HR-менеджере -->
    @include('workers.modals.hr-manager-info')

    <!-- Модальное окно информации о задании -->
    @include('candidates.modals.task-info')

    <!-- Модальное окно смены статуса -->
    @include('candidates.modals.change-status')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
            let totalItems = 0;

            // Инициализация
            function init() {
                loadData();
                setupEventListeners();
            }

            // Настройка обработчиков событий
            function setupEventListeners() {
                searchInput.addEventListener('input', debounce(filterCandidates, 300));
                statusFilter.addEventListener('change', filterCandidates);
                perPageSelect.addEventListener('change', updateItemsPerPage);

                // Пагинация
                paginationElements.prev.addEventListener('click', goToPrevPage);
                paginationElements.next.addEventListener('click', goToNextPage);
                paginationElements.prevMobile.addEventListener('click', goToPrevPage);
                paginationElements.nextMobile.addEventListener('click', goToNextPage);
            }

            // Загрузка данных с сервера
            function loadData() {
                const params = new URLSearchParams({
                    page: currentPage,
                    perPage: itemsPerPage,
                    search: searchInput.value,
                    status: statusFilter.value
                });

                fetch(`/tasks/get_tasks?${params}`)
                    .then(response => response.json())
                    .then(data => {
                        totalItems = data.total;
                        renderTable(data.data);
                        updatePagination(data);
                    })
                    .catch(error => console.error('Error:', error));
            }

            // Рендер таблицы
            function renderTable(candidates) {
                candidatesTable.innerHTML = candidates.length > 0 ?
                    candidates.map(candidate => {
                        const status = candidate.task.status.toLowerCase();
                        const isFinalStatus = ['одобрено', 'провалено', 'в процессе'].includes(status);
                        const isInProgress = status === 'в процессе' || status === 'доработка';
                        const isAdopted = status === 'принят';
                        const showChangeStatus = !isFinalStatus && !isInProgress;

                        return `
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
                        <div class="text-sm text-gray-900 cursor-pointer" onclick="showTaskModal(${candidate.task_status_id})">${candidate.task.title}</div>
                        <div class="text-sm text-gray-500">${candidate.task.difficulty}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        ${candidate.tutor ? `
                                <div class="flex items-center cursor-pointer" onclick="showTutorModal(${candidate.tutor.id})">
                                    <img class="h-10 w-10 rounded-full" src="${candidate.tutor.avatar}" alt="${candidate.tutor.name}">
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">${candidate.tutor.name}</div>
                                    </div>
                                </div>
                            ` : '<div class="text-sm text-gray-500">Не назначен</div>'}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        ${candidate.hr_manager ? `
                                <div class="flex items-center cursor-pointer" onclick="showHrManagerModal(${candidate.hr_manager.id})">
                                    <img class="h-10 w-10 rounded-full" src="${candidate.hr_manager.avatar}" alt="${candidate.hr_manager.name}">
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">${candidate.hr_manager.name}</div>
                                    </div>
                                </div>
                            ` : '<div class="text-sm text-gray-500">Не назначен</div>'}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm ${isDeadlinePassed(candidate.task.deadline) ? 'text-red-600' : 'text-gray-500'}">
                        ${candidate.task.deadline || 'Не указан'}
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
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex justify-center space-x-2">
                            ${showChangeStatus && !isAdopted ? 
                                `<button onclick="showStatusModal(${candidate.task_status_id})" 
                                     class="text-blue-600 hover:text-blue-900 mr-3">
                                        Изменить статус
                                    </button>` 
                                : ''
                            }
                        </div>
                    </td>
                </tr>
            `;
                    }).join('') :
                    `<tr>
            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                Нет данных для отображения
            </td>
        </tr>`;
            }

            // Функции для модальных окон
            window.showCandidateModal = function(candidateId) {
                fetch(`/tasks/candidate/${candidateId}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('modal-candidate-name').textContent = data.name;
                        document.getElementById('modal-candidate-email').textContent = data.email;
                        document.getElementById('modal-candidate-phone').textContent = data.phone;
                        document.getElementById('modal-candidate-telegram').textContent = data.telegram ||
                            'Не указан';
                        document.getElementById('candidate-modal').classList.remove('hidden');
                    });
            };

            window.showTutorModal = function(tutorId) {
                fetch(`/tasks/tutor/${tutorId}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('modal-tutor-name').textContent = data.name;
                        document.getElementById('modal-tutor-email').textContent = data.email;
                        document.getElementById('modal-tutor-phone').textContent = data.phone;
                        document.getElementById('modal-tutor-telegram').textContent = data.telegram ||
                            'Не указан';
                        document.getElementById('modal-tutor-post').textContent = data.post || 'Не указана';
                        document.getElementById('modal-tutor-department').textContent = data.department ||
                            'Не указан';
                        document.getElementById('modal-tutor-level').textContent = data.level ||
                        'Не указан';
                        document.getElementById('modal-tutor-hire-date').textContent = data.hire_date ||
                            'Не указана';
                        document.getElementById('tutor-modal').classList.remove('hidden');
                    });
            };

            window.showHrManagerModal = function(hrManagerId) {
                fetch(`/tasks/hr-manager/${hrManagerId}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('modal-hr-manager-name').textContent = data.name;
                        document.getElementById('modal-hr-manager-email').textContent = data.email;
                        document.getElementById('modal-hr-manager-phone').textContent = data.phone;
                        document.getElementById('modal-hr-manager-telegram').textContent = data.telegram ||
                            'Не указан';
                        document.getElementById('modal-hr-manager-post').textContent = data.post ||
                            'Не указана';
                        document.getElementById('modal-hr-manager-department').textContent = data
                            .department || 'Не указан';
                        document.getElementById('modal-hr-manager-level').textContent = data.level ||
                            'Не указан';
                        document.getElementById('modal-hr-manager-hire-date').textContent = data
                            .hire_date || 'Не указана';
                        document.getElementById('hr-manager-modal').classList.remove('hidden');
                    });
            };

            window.showTaskModal = function(taskStatusId) {
                fetch(`/tasks/task/${taskStatusId}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('modal-task-title').textContent = data.title;
                        document.getElementById('modal-task-difficulty').textContent = data.difficulty;
                        if (data.document) {
                            document.getElementById('modal-task-document-link').href = data.document;
                            document.getElementById('modal-task-document-link').classList.remove('hidden');
                        } else {
                            document.getElementById('modal-task-document-link').classList.add('hidden');
                        }
                        document.getElementById('task-modal').classList.remove('hidden');
                    });
            };

            window.showStatusModal = function(taskStatusId) {
                fetch(`/tasks/task-status/${taskStatusId}`)
                    .then(response => response.json())
                    .then(taskStatusData => {
                        fetch(`/tasks/candidate/${taskStatusData.user_id}`)
                            .then(response => response.json())
                            .then(candidateData => {
                                fetch('/tasks/statuses')
                                    .then(response => response.json())
                                    .then(statuses => {
                                        const statusContainer = document.getElementById(
                                            'status-options');
                                        statusContainer.innerHTML = '';

                                        for (const [value, label] of Object.entries(statuses)) {
                                            const div = document.createElement('div');
                                            div.className = 'flex items-center';

                                            const input = document.createElement('input');
                                            input.type = 'radio';
                                            input.id = `status-${value}`;
                                            input.name = 'status';
                                            input.value = value;
                                            input.className =
                                                'h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300';
                                            if (value === taskStatusData.status) {
                                                input.checked = true;
                                            }

                                            const labelEl = document.createElement('label');
                                            labelEl.htmlFor = `status-${value}`;
                                            labelEl.className =
                                                'ml-3 block text-sm font-medium text-gray-700';
                                            labelEl.textContent = label;

                                            div.appendChild(input);
                                            div.appendChild(labelEl);
                                            statusContainer.appendChild(div);
                                        }

                                        document.getElementById('status-candidate-name')
                                            .textContent = `Кандидат: ${candidateData.name}`;
                                        document.getElementById('status-form').dataset
                                            .taskStatusId = taskStatusId;
                                        document.getElementById('status-modal').classList.remove(
                                            'hidden');
                                    });
                            });
                    });
            };

            window.submitStatusForm = async function(event) {
                event.preventDefault();

                const form = document.getElementById('status-form');
                const taskStatusId = form.dataset.taskStatusId;
                const formData = new FormData(form);

                // Добавляем метод PUT в FormData
                formData.append('_method', 'PUT');

                try {
                    const response = await fetch(`/tasks/status/${taskStatusId}`, {
                        method: 'POST', // Оставляем POST, но имитируем PUT через _method
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .content,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    if (!response.ok) {
                        const errorData = await response.json().catch(() => ({}));
                        throw new Error(errorData.message || 'Ошибка сервера');
                    }

                    const data = await response.json();
                    alert(data.message || 'Статус успешно обновлен');
                    document.getElementById('status-modal').classList.add('hidden');
                    form.reset();
                    loadData();

                } catch (error) {
                    console.error('Error:', error);
                    alert(error.message.includes('Failed to fetch') ?
                        'Ошибка сети: проверьте соединение' :
                        error.message
                    );
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Сохранить';
                }
            };

            // Вспомогательные функции
            function isDeadlinePassed(deadline) {
                if (!deadline) return false;
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

            function debounce(func, wait) {
                let timeout;
                return function() {
                    const context = this,
                        args = arguments;
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(context, args), wait);
                };
            }

            // Фильтрация
            function filterCandidates() {
                currentPage = 1;
                loadData();
            }

            // Пагинация
            function updateItemsPerPage() {
                itemsPerPage = parseInt(perPageSelect.value);
                currentPage = 1;
                loadData();
            }

            function updatePagination(data) {
                paginationElements.from.textContent = (data.current_page - 1) * data.per_page + 1;
                paginationElements.to.textContent = Math.min(data.current_page * data.per_page, data.total);
                paginationElements.total.textContent = data.total;

                // Обновляем кнопки
                paginationElements.prev.disabled = data.current_page === 1;
                paginationElements.next.disabled = data.current_page === data.last_page;
                paginationElements.prevMobile.disabled = data.current_page === 1;
                paginationElements.nextMobile.disabled = data.current_page === data.last_page;

                // Обновляем номера страниц
                paginationElements.numbers.innerHTML = '';

                const maxVisiblePages = 5;
                let startPage = Math.max(1, data.current_page - Math.floor(maxVisiblePages / 2));
                let endPage = Math.min(data.last_page, startPage + maxVisiblePages - 1);

                if (endPage - startPage + 1 < maxVisiblePages) {
                    startPage = Math.max(1, endPage - maxVisiblePages + 1);
                }

                // Добавляем кнопки страниц
                for (let i = startPage; i <= endPage; i++) {
                    const pageBtn = document.createElement('button');
                    pageBtn.className =
                        `relative inline-flex items-center px-4 py-2 border text-sm font-medium ${i === data.current_page ? 'bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50'}`;
                    pageBtn.textContent = i;
                    pageBtn.addEventListener('click', () => {
                        currentPage = i;
                        loadData();
                    });
                    paginationElements.numbers.appendChild(pageBtn);
                }
            }

            function goToPrevPage(e) {
                e.preventDefault();
                if (currentPage > 1) {
                    currentPage--;
                    loadData();
                }
            }

            function goToNextPage(e) {
                e.preventDefault();
                if (currentPage < Math.ceil(totalItems / itemsPerPage)) {
                    currentPage++;
                    loadData();
                }
            }

            // Закрытие модальных окон
            document.querySelectorAll('.modal-close').forEach(btn => {
                btn.addEventListener('click', function() {
                    this.closest('.modal').classList.add('hidden');
                });
            });

            window.closeModal = function(modalId) {
                document.getElementById(modalId).classList.add('hidden');
            };

            // Запускаем приложение
            init();
        });
    </script>
@endsection
