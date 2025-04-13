@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">Управление созвонами</h1>

        <!-- Фильтры и поиск -->
        <div class="mb-6 bg-white rounded-lg shadow p-4">
            <form id="filter-form" method="GET" action="{{ route('meetings.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Фильтр по типу -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Тип созвона</label>
                        <select id="type" name="type" onchange="this.form.submit()"
                            class="w-full py-2 px-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Все типы</option>
                            <option value="primary" @selected(request('type') == 'primary')>Первичный</option>
                            <option value="technical" @selected(request('type') == 'technical')>Технический</option>
                            <option value="final" @selected(request('type') == 'final')>Финальный</option>
                        </select>
                    </div>

                    <!-- Сортировка -->
                    <div>
                        <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Сортировка</label>
                        <select id="sort" name="sort" onchange="this.form.submit()"
                            class="w-full py-2 px-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="datetime_asc" @selected(request('sort') == 'datetime_asc')>Дата и время (по возрастанию)</option>
                            <option value="datetime_desc" @selected(request('sort') == 'datetime_desc')>Дата и время (по убыванию)</option>
                        </select>
                    </div>

                    <!-- Элементов на странице -->
                    <div>
                        <label for="perPage" class="block text-sm font-medium text-gray-700 mb-1">На странице</label>
                        <select id="perPage" name="perPage" onchange="this.form.submit()"
                            class="w-full py-2 px-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="2" @selected(request('perPage') == 2)>2</option>
                            <option value="10" @selected(request('perPage', 10) == 10)>10</option>
                            <option value="20" @selected(request('perPage') == 20)>20</option>
                            <option value="50" @selected(request('perPage') == 50)>50</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <!-- Поиск -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Поиск</label>
                        <input type="text" id="search" name="search" placeholder="Имя пользователя..."
                            value="{{ request('search') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Фильтр по дате -->
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Дата созвона</label>
                        <input type="date" id="date" name="date" value="{{ request('date') }}"
                            class="w-full py-2 px-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div class="mt-4 flex justify-end gap-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                        Поиск
                    </button>
                    @if (auth()->user()->isTutorWorker() || auth()->user()->isAdmin())
                        <button type="button" onclick="openModal('add-modal')"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">
                            Назначить
                        </button>
                    @endif
                </div>
            </form>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                <p class="font-bold">Ошибка</p>
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
                <p class="font-bold">Успешно</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <!-- Таблица -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Пользователь</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата
                                и время</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ссылка</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Тип
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Тьютор</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                HR-менеджер</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Действия</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($calls as $call)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center cursor-pointer"
                                        onclick="showUserModal({{ $call->candidate_id }})">
                                        <img class="h-10 w-10 rounded-full" src="{{ $call->candidate->avatar_url }}"
                                            alt="{{ $call->candidate->name }}">
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $call->candidate->name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($call->date . ' ' . $call->time)->format('d.m.Y H:i') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ $call->meeting_link }}" target="_blank"
                                        class="text-blue-600 hover:text-blue-900">{{ Str::limit($call->meeting_link, 20) }}</a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $typeClasses = [
                                            'primary' => 'bg-blue-100 text-blue-800',
                                            'technical' => 'bg-yellow-100 text-yellow-800',
                                            'final' => 'bg-green-100 text-green-800',
                                        ];
                                        $typeLabels = [
                                            'primary' => 'Первичный',
                                            'technical' => 'Технический',
                                            'final' => 'Финальный',
                                        ];
                                    @endphp
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $typeClasses[$call->type] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $typeLabels[$call->type] ?? $call->type }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($call->tutor)
                                        <div class="flex items-center cursor-pointer"
                                            onclick="showUserModal({{ $call->tutor_id }})">
                                            <img class="h-8 w-8 rounded-full" src="{{ $call->tutor->avatar_url }}"
                                                alt="{{ $call->tutor->name }}">
                                            <div class="ml-2 text-sm text-gray-900">{{ $call->tutor->name }}</div>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500">Не назначен</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($call->hr_manager)
                                        <div class="flex items-center cursor-pointer"
                                            onclick="showUserModal({{ $call->hr_manager_id }})">
                                            <img class="h-8 w-8 rounded-full" src="{{ $call->hr_manager->avatar_url }}"
                                                alt="{{ $call->hr_manager->name }}">
                                            <div class="ml-2 text-sm text-gray-900">{{ $call->hr_manager->name }}</div>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500">Не назначен</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if (app('App\Http\Controllers\MeetingController')->canUpdate(auth()->user(), $call))
                                        <button onclick="editCall({{ $call->id }})"
                                            class="text-blue-600 hover:text-blue-900 mr-3">Редактировать</button>
                                    @endif
                                    @if (app('App\Http\Controllers\MeetingController')->canDelete(auth()->user(), $call))
                                        <form action="{{ route('meetings.destroy', $call->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="perPage" value="{{ request('perPage', 10) }}">

                                            <button type="submit" class="text-red-600 hover:text-red-900"
                                                onclick="return confirm('Вы уверены, что хотите удалить этот созвон?')">
                                                Удалить
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">Нет данных для
                                    отображения</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($calls->hasPages())
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    <div class="flex flex-col sm:flex-row items-center justify-between">
                        <div class="mb-2 sm:mb-0">
                            <p class="text-sm text-gray-700">
                                Показано с <span class="font-medium">{{ $calls->firstItem() }}</span>
                                по <span class="font-medium">{{ $calls->lastItem() }}</span>
                                из <span class="font-medium">{{ $calls->total() }}</span> результатов
                            </p>
                        </div>

                        <div class="flex space-x-1">
                            @php
                                // Получаем все текущие параметры запроса
                                $queryParams = request()->query();
                                // Удаляем page из параметров для предыдущей/следующей страницы
                                unset($queryParams['page']);
                            @endphp

                            {{-- Кнопка "Назад" --}}
                            <a href="{{ $calls->previousPageUrl() ? $calls->previousPageUrl() . '&' . http_build_query($queryParams) : '#' }}"
                                class="px-3 py-1 border rounded {{ $calls->onFirstPage() ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                                &larr; Назад
                            </a>

                            {{-- Номера страниц --}}
                            @foreach ($calls->getUrlRange(max(1, $calls->currentPage() - 2), min($calls->lastPage(), $calls->currentPage() + 2)) as $page => $url)
                                @php
                                    $queryParams['page'] = $page;
                                @endphp
                                <a href="{{ $url . '&' . http_build_query($queryParams) }}"
                                    class="px-3 py-1 border rounded {{ $page == $calls->currentPage() ? 'bg-blue-50 text-blue-600 border-blue-500' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                                    {{ $page }}
                                </a>
                            @endforeach

                            {{-- Кнопка "Вперед" --}}
                            <a href="{{ $calls->nextPageUrl() ? $calls->nextPageUrl() . '&' . http_build_query($queryParams) : '#' }}"
                                class="px-3 py-1 border rounded {{ $calls->hasMorePages() ? 'bg-white text-gray-700 hover:bg-gray-50' : 'bg-gray-100 text-gray-400 cursor-not-allowed' }}">
                                Вперед &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Модальное окно просмотра пользователя -->
    <div id="user-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold" id="modal-user-name"></h3>
                <button onclick="closeModal('user-modal')" class="text-gray-500 hover:text-gray-700">
                    ✕
                </button>
            </div>
            <div class="space-y-4">
                <div class="flex items-center gap-4">
                    <img id="modal-user-avatar" src="" alt="Аватар" class="w-20 h-20 rounded-full">
                </div>
                <div>
                    <p class="text-gray-600">Роль:</p>
                    <p class="font-medium" id="modal-user-role"></p>
                </div>
                <div>
                    <p class="text-gray-600">Телефон:</p>
                    <p class="font-medium" id="modal-user-phone"></p>
                </div>
                <div>
                    <p class="text-gray-600">Email:</p>
                    <p class="font-medium" id="modal-user-email"></p>
                </div>
                <div>
                    <p class="text-gray-600">Telegram:</p>
                    <p class="font-medium" id="modal-user-telegram"></p>
                </div>
                <div id="worker-info" class="hidden space-y-4 pt-4 border-t border-gray-200">
                    <div>
                        <p class="text-gray-600">Отдел:</p>
                        <p class="font-medium" id="modal-worker-department"></p>
                    </div>
                    <div>
                        <p class="text-gray-600">Должность:</p>
                        <p class="font-medium" id="modal-worker-post"></p>
                    </div>
                    <div>
                        <p class="text-gray-600">Дата приема:</p>
                        <p class="font-medium" id="modal-worker-hire-date"></p>
                    </div>
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button onclick="closeModal('user-modal')"
                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-md">Закрыть</button>
            </div>
        </div>
    </div>

    <!-- Модальное окно добавления/редактирования -->
    <div id="add-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold" id="modal-title">Назначить созвон</h3>
                <button onclick="closeModal('add-modal')" class="text-gray-500 hover:text-gray-700">
                    ✕
                </button>
            </div>
            <form id="call-form" action="{{ route('meetings.store') }}" method="POST">
                @csrf
                <input type="hidden" id="call-id" name="id">

                <input type="hidden" name="perPage" value="{{ request('perPage', 10) }}">

                <div class="space-y-4">
                    <div>
                        <label for="user-select" class="block text-sm font-medium text-gray-700 mb-1">Кандидат *</label>
                        <select id="user-select" name="user_id" required
                            class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Выберите кандидата</option>
                            @foreach ($candidates as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="call-date" class="block text-sm font-medium text-gray-700 mb-1">Дата *</label>
                            <input type="date" id="call-date" name="date" required
                                class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="call-time" class="block text-sm font-medium text-gray-700 mb-1">Время *</label>
                            <input type="time" id="call-time" name="time" required
                                class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div>
                        <label for="call-link" class="block text-sm font-medium text-gray-700 mb-1">Ссылка на конференцию
                            *</label>
                        <input type="url" id="call-link" name="link" required
                            class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="https://meet.example.com/room123">
                    </div>

                    <div>
                        <label for="call-type" class="block text-sm font-medium text-gray-700 mb-1">Тип созвона *</label>
                        <select id="call-type" name="type" required
                            class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @if (auth()->user()->isTutorWorker())
                                <option value="technical">Технический</option>
                            @else
                                <option value="primary">Первичный</option>
                                <option value="technical">Технический</option>
                                <option value="final">Финальный</option>
                            @endif
                        </select>
                    </div>

                    @unless (auth()->user()->isTutorWorker())
                        <div>
                            <label for="tutor-select" class="block text-sm font-medium text-gray-700 mb-1">Тьютор *</label>
                            <select id="tutor-select" name="tutor_id" required
                                class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Выберите тьютора</option>
                                @foreach ($tutors as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endunless

                    @if (auth()->user()->isAdmin())
                        <input type="hidden" name="hr_manager_id" value="{{ auth()->id() }}">
                    @elseif (!auth()->user()->isTutorWorker())
                        <div>
                            <label for="hr-manager-select"
                                class="block text-sm font-medium text-gray-700 mb-1">HR-менеджер *</label>
                            <select id="hr-manager-select" name="hr_manager_id" required
                                class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Выберите HR-менеджера</option>
                                @foreach ($hrManagers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="closeModal('add-modal')"
                            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-md">Отмена</button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">Сохранить</button>
                    </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr("#date", {
                dateFormat: "Y-m-d",
                allowInput: true,
                locale: "ru"
            });
            flatpickr("#call-date", {
                dateFormat: "Y-m-d",
                allowInput: true,
                locale: "ru",
                minDate: "today"
            });
            flatpickr("#call-time", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                allowInput: true,
                locale: "ru",
                minuteIncrement: 15
            });

            document.getElementById('perPage').addEventListener('change', function() {
                document.getElementById('filter-form').submit();
            });

            document.getElementById('date').addEventListener('change', function() {
                document.getElementById('filter-form').submit();
            });

            let searchTimeout;
            document.getElementById('search').addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    document.getElementById('filter-form').submit();
                }, 500);
            });
        });

        window.showUserModal = function(userId) {
            fetch(`/users/${userId}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(user => {
                    document.getElementById('modal-user-name').textContent = user.name;
                    document.getElementById('modal-user-avatar').src = user.avatar_url;
                    document.getElementById('modal-user-role').textContent = user.role || 'Не указана';
                    document.getElementById('modal-user-phone').textContent = user.phone || 'Не указан';
                    document.getElementById('modal-user-email').textContent = user.email || 'Не указан';

                    const telegramText = user.telegram_user?.username ? `@${user.telegram_user.username}` :
                        'Не указан';
                    document.getElementById('modal-user-telegram').textContent = telegramText;

                    const workerInfo = document.getElementById('worker-info');
                    if (user.worker) {
                        workerInfo.classList.remove('hidden');
                        document.getElementById('modal-worker-department').textContent = user.worker.department ||
                            'Не указан';
                        document.getElementById('modal-worker-post').textContent = user.worker.post || 'Не указан';
                        document.getElementById('modal-worker-hire-date').textContent = user.worker.hire_date ||
                            'Не указана';
                    } else {
                        workerInfo.classList.add('hidden');
                    }

                    document.getElementById('user-modal').classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Произошла ошибка при загрузке данных пользователя');
                });
        };

        window.editCall = function(callId) {
            fetch(`/meetings/${callId}/edit`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(call => {
                    // Устанавливаем заголовок модального окна
                    document.getElementById('modal-title').textContent = 'Редактировать созвон';

                    // Заполняем поля формы данными созвона
                    document.getElementById('call-id').value = call.id;
                    document.getElementById('user-select').value = call.candidate_id;
                    document.getElementById('call-date').value = call.date;
                    document.getElementById('call-time').value = call.time;
                    document.getElementById('call-link').value = call.meeting_link;
                    document.getElementById('call-type').value = call.type;

                    // Заполняем поля для тьютора и HR-менеджера (если они есть)
                    @unless (auth()->user()->isTutorWorker())
                        if (document.getElementById('tutor-select')) {
                            document.getElementById('tutor-select').value = call.tutor_id;
                        }
                    @endunless

                    @unless (auth()->user()->isAdmin())
                        if (document.getElementById('hr-manager-select')) {
                            document.getElementById('hr-manager-select').value = call.hr_manager_id;
                        }
                    @endunless

                    // Получаем текущие параметры URL
                    const urlParams = new URLSearchParams(window.location.search);

                    // Заполняем скрытые поля параметрами фильтрации
                    document.querySelector('input[name="perPage"]').value = urlParams.get('perPage') || '10';

                    // Обновляем action формы и метод
                    const form = document.getElementById('call-form');
                    form.action = `/meetings/${callId}`;

                    // Удаляем старый метод, если есть
                    form.querySelector('input[name="_method"]')?.remove();

                    // Добавляем метод PUT
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'PUT';
                    form.appendChild(methodInput);

                    // Показываем модальное окно
                    document.getElementById('add-modal').classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Произошла ошибка при загрузке данных созвона');
                });
        };

        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            document.body.style.overflow = 'auto';
            if (modalId === 'add-modal') {
                document.getElementById('call-form').reset();
                document.getElementById('call-id').value = '';
                document.getElementById('modal-title').textContent = 'Назначить созвон';
                const form = document.getElementById('call-form');
                form.action = "{{ route('meetings.store') }}";
                form.querySelector('input[name="_method"]')?.remove();
            }
        }

        window.addEventListener('click', function(event) {
            if (event.target.id === 'user-modal' || event.target.id === 'add-modal') {
                closeModal(event.target.id);
            }
        });
    </script>
@endsection
