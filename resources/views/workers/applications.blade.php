@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">Список заявок</h1>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Фильтры -->
        <form method="GET" action="{{ route('applications.index') }}" id="filter-form" class="mb-6 bg-white p-4 rounded-lg shadow">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Имя кандидата</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="w-full mt-1 p-2 border rounded">
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Статус</label>
                    <select name="status" id="status" class="w-full mt-1 p-2 border rounded">
                        <option value="">Все статусы</option>
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="department" class="block text-sm font-medium text-gray-700">Отдел</label>
                    <select name="department" id="department" class="w-full mt-1 p-2 border rounded">
                        <option value="">Все отделы</option>
                        @foreach ($departments as $id => $name)
                            <option value="{{ $id }}" {{ request('department') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="per_page" class="block text-sm font-medium text-gray-700">Элементов на странице</label>
                    <select name="per_page" id="per_page" class="w-full mt-1 p-2 border rounded" onchange="document.getElementById('filter-form').submit()">
                        @foreach([2, 5, 10, 20, 50] as $perPage)
                            <option value="{{ $perPage }}" {{ request('per_page', session('per_page', 10)) == $perPage ? 'selected' : '' }}>
                                {{ $perPage }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-4 flex space-x-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Применить фильтры</button>
                <a href="{{ route('applications.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg">Сбросить</a>
            </div>
        </form>

        <!-- Таблица заявок -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto overflow-y-auto max-h-[600px]">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Кандидат</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Статус</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Резюме</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Отдел</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Дата создания</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Действия</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($applications as $application)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <img src="{{ $application->user->avatar_url }}" class="h-10 w-10 rounded-full" alt="{{ $application->user->name }}">
                                        <span class="ml-2">{{ $application->user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'ожидание' => 'bg-yellow-100 text-yellow-800',
                                            'на рассмотрении' => 'bg-blue-100 text-blue-800',
                                            'одобрено' => 'bg-green-100 text-green-800',
                                            'отклонено' => 'bg-red-100 text-red-800',
                                            'созвон назначен' => 'bg-purple-100 text-purple-800',
                                        ];
                                        $color = $statusColors[$application->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $color }}">
                                        {{ $application->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($application->resume)
                                        <a href="{{ route('applications.download', $application) }}" class="text-blue-600 hover:underline">Скачать</a>
                                    @else
                                        Нет резюме
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $application->department->name ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $application->created_at->format('d.m.Y H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap space-x-2">
                                    @if ($application->status === 'ожидание')
                                        <form action="{{ route('applications.under_consideration', $application) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">Взять на рассмотрение</button>
                                        </form>
                                        <form action="{{ route('applications.decline', $application) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">Отклонить</button>
                                        </form>
                                    @elseif ($application->status === 'на рассмотрении')
                                        <button onclick="openAssignCallModal({{ $application->id }})" class="px-3 py-1 bg-purple-500 text-white rounded hover:bg-purple-600">Назначить созвон</button>
                                        <button onclick="openApproveModal({{ $application->id }}, {{ $application->department_id }})" class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600">Одобрить</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center">Заявки не найдены</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Пагинация -->
        @if($applications->hasPages())
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
                @if ($applications->onFirstPage())
                    <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-white">Назад</span>
                @else
                    <a href="{{ $applications->appends(request()->except('page'))->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Назад</a>
                @endif
                
                @if ($applications->hasMorePages())
                    <a href="{{ $applications->appends(request()->except('page'))->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Вперед</a>
                @else
                    <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-white">Вперед</span>
                @endif
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Показано <span>{{ $applications->firstItem() }}</span> - <span>{{ $applications->lastItem() }}</span> из <span>{{ $applications->total() }}</span>
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        @if ($applications->onFirstPage())
                            <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-300">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        @else
                            <a href="{{ $applications->appends(request()->except('page'))->previousPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        @endif

                        @foreach ($applications->getUrlRange(1, $applications->lastPage()) as $page => $url)
                            @if ($page == $applications->currentPage())
                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-blue-500 text-white text-sm font-medium">{{ $page }}</span>
                            @else
                                <a href="{{ $applications->appends(request()->except('page'))->url($page) }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if ($applications->hasMorePages())
                            <a href="{{ $applications->appends(request()->except('page'))->nextPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        @else
                            <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-300">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        @endif
                    </nav>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Модальное окно для назначения созвона -->
    <div id="assign-call-modal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Назначить созвон</h3>
                            <form id="assign-call-form" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label for="date" class="block text-sm font-medium text-gray-700">Дата</label>
                                    <input type="date" name="date" id="date" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" required min="{{ date('Y-m-d') }}">
                                </div>
                                <div class="mb-4">
                                    <label for="time" class="block text-sm font-medium text-gray-700">Время</label>
                                    <input type="time" name="time" id="time" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                                </div>
                                <div class="mb-4">
                                    <label for="meeting_link" class="block text-sm font-medium text-gray-700">Ссылка на созвон</label>
                                    <input type="url" name="meeting_link" id="meeting_link" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                                </div>
                                <div class="mb-4">
                                    <label for="hr-manager" class="block text-sm font-medium text-gray-700">HR-менеджер</label>
                                    <select name="hr-manager" id="hr-manager" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                                        <option value="">Выберите HR-менеджера</option>
                                        @foreach ($hrManagers as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                        Назначить
                                    </button>
                                    <button type="button" onclick="closeAssignCallModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                        Отмена
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно для одобрения заявки -->
    <div id="approve-modal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Одобрить заявку</h3>
                            <form id="approve-form" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label for="approve-hr-manager" class="block text-sm font-medium text-gray-700">HR-менеджер</label>
                                    <select name="hr_manager_id" id="approve-hr-manager" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                                        <option value="">Выберите HR-менеджера</option>
                                        @foreach ($hrManagers as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label for="approve-tutor" class="block text-sm font-medium text-gray-700">Тьютор</label>
                                    <select name="tutor_id" id="approve-tutor" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                                        <option value="">Выберите тьютора</option>
                                        @foreach ($tutors as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label for="approve-task" class="block text-sm font-medium text-gray-700">Задание</label>
                                    <select name="task_id" id="approve-task" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                                        <option value="">Выберите задание</option>
                                    </select>
                                </div>
                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                                        Одобрить
                                    </button>
                                    <button type="button" onclick="closeApproveModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                        Отмена
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openAssignCallModal(applicationId) {
            const form = document.getElementById('assign-call-form');
            form.action = `/applications/${applicationId}/assign-call`;
            
            // Установим минимальную дату - сегодня
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('date').min = today;
            
            document.getElementById('assign-call-modal').classList.remove('hidden');
        }

        function closeAssignCallModal() {
            document.getElementById('assign-call-modal').classList.add('hidden');
        }

        function openApproveModal(applicationId, departmentId) {
            const form = document.getElementById('approve-form');
            form.action = `/applications/${applicationId}/approve`;
            
            // Загружаем задания для отдела
            if (departmentId) {
                fetch(`/departments/${departmentId}/tasks`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Не удалось загрузить задания');
                        }
                        return response.json();
                    })
                    .then(tasks => {
                        const taskSelect = document.getElementById('approve-task');
                        taskSelect.innerHTML = '<option value="">Выберите задание</option>';
                        
                        if (tasks.length === 0) {
                            taskSelect.innerHTML += '<option value="" disabled>Нет доступных заданий</option>';
                        } else {
                            tasks.forEach(task => {
                                taskSelect.innerHTML += `<option value="${task.id}">${task.title}</option>`;
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Ошибка загрузки заданий:', error);
                        const taskSelect = document.getElementById('approve-task');
                        taskSelect.innerHTML = '<option value="" disabled>Ошибка загрузки заданий</option>';
                    });
            }

            document.getElementById('approve-modal').classList.remove('hidden');
        }

        function closeApproveModal() {
            document.getElementById('approve-modal').classList.add('hidden');
        }

        // Закрытие модальных окон при клике вне их
        document.getElementById('assign-call-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAssignCallModal();
            }
        });

        document.getElementById('approve-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeApproveModal();
            }
        });
    </script>
@endsection