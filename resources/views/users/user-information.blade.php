@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8 ">
        <div class="max-w-7xl mx-auto">
            <!-- Вкладки -->
            <div class="bg-white shadow rounded-t-lg overflow-hidden">
                <nav class="flex border-b border-gray-200">
                    <a href="#" id="personal-tab"
                        class="tab-link w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm border-blue-500 text-blue-600 bg-gray-50">
                        Личная информация
                    </a>
                    <a href="#" id="password-tab"
                        class="tab-link w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Смена пароля
                    </a>
                </nav>
            </div>

            <!-- Содержимое вкладок -->
            <div class="bg-white shadow rounded-b-lg overflow-hidden">
                <!-- Вкладка личной информации -->
                <div id="personal-content" class="tab-content p-6">
                    <div class="max-w-3xl mx-auto">
                        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Нередактируемое поле - Имя -->
                            <div class="mb-6">
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Имя</label>
                                <div class="relative">
                                    <input type="text" id="name" name="name" value="{{ auth()->user()->name }}"
                                        class="block w-full bg-gray-100 border border-gray-300 rounded-md shadow-sm py-2 px-3 text-gray-600 cursor-not-allowed"
                                        disabled readonly>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Имя можно изменить только через администратора</p>
                            </div>

                            <div class="mb-6">
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" id="email" name="email" value="{{ auth()->user()->email }}"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring focus:ring-blue-500 focus:border-blue-500"
                                    required>
                                <p class="mt-1 text-xs text-gray-500">Ваш основной email для входа в систему</p>
                                <p id="email-error" class="mt-1 text-xs text-red-500 hidden">Введите корректный email
                                    (например, example@domain.com)</p>
                            </div>

                            <div class="mb-6">
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Номер
                                    телефона</label>
                                <input type="tel" id="phone" name="phone" value="{{ auth()->user()->phone }}"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="+7 (XXX) XXX-XX-XX">
                                <p class="mt-1 text-xs text-gray-500">Используется для связи с вами</p>
                                <p id="phone-error" class="mt-1 text-xs text-red-500 hidden">Введите корректный номер
                                    телефона (например, +7 (XXX) XXX-XX-XX)</p>
                            </div>

                            <!-- Редактируемое поле - Аватар -->
                            <div class="mb-6">
                                <label for="avatar" class="block text-sm font-medium text-gray-700 mb-1">Аватар
                                    профиля</label>
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12 rounded-full overflow-hidden bg-gray-200 mr-4">
                                        @if (auth()->user()->avatar)
                                            <img class="h-full w-full object-cover"
                                                src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Аватар">
                                        @else
                                            <svg class="h-full w-full text-gray-400" fill="currentColor"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112 15c3.183 0 6.235 1.264 8.485 3.515A9.975 9.975 0 0024 20.993zM12 4a4 4 0 100 8 4 4 0 000-8z" />
                                            </svg>
                                        @endif
                                    </div>
                                    <input type="file" id="avatar" name="avatar"
                                        accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                        class="flex-1 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Рекомендуемый размер: 200×200 пикселей</p>
                            </div>

                            <!-- Нередактируемое поле - Telegram -->
                            <!-- Нередактируемое поле - Telegram -->
                            <div class="mb-6">
                                <label for="telegram" class="block text-sm font-medium text-gray-700 mb-1">Ссылка на
                                    Telegram</label>
                                <div class="relative">
                                    @if (auth()->user()->telegramUser?->username)
                                        <div
                                            class="flex items-center bg-gray-100 border border-gray-300 rounded-md shadow-sm">
                                            <a href="https://t.me/{{ auth()->user()->telegramUser->username }}"
                                                target="_blank"
                                                class="flex-1 py-2 px-3 text-gray-600 hover:underline cursor-pointer">
                                                @<?= auth()->user()->telegramUser?->username ?>
                                            </a>
                                            <div class="px-3 flex items-center">
                                                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                                    <path
                                                        d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.14.14-.26.26-.534.26l.213-3.053 5.56-5.022c.24-.213-.054-.334-.373-.121l-6.87 4.326-2.96-.924c-.64-.203-.658-.64.135-.954l11.566-4.458c.538-.196 1.006.128.832.941z" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <button id="unlink-telegram-btn"
                                                class="text-xs text-red-600 hover:text-red-800 underline focus:outline-none">
                                                Отвязать Telegram аккаунт
                                            </button>
                                        </div>
                                    @else
                                        <div class="bg-gray-100 border border-gray-300 rounded-md p-4 text-center">
                                            <a href="https://t.me/Atwinta_Helper_bot?start=link_{{ auth()->id() }}_{{ sha1(auth()->id() . env('APP_KEY')) }}"
                                                class="telegram-btn inline-flex items-center justify-center w-full bg-[#0088cc] hover:bg-[#0077b3] text-white font-medium py-2 px-4 rounded-md transition-colors">
                                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                                    <path
                                                        d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.14.18-.357.295-.6.295-.002 0-.003 0-.005 0l.213-3.054 5.56-5.022c.24-.213-.054-.334-.373-.121l-6.869 4.326-2.96-.924c-.64-.203-.658-.64.135-.954l11.566-4.458c.538-.196 1.006.128.832.941z" />
                                                </svg>
                                                Привязать Telegram
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Информация о работнике -->
                            @if (auth()->user()->role->name !== 'User')
                                <div class="mb-6 border-t border-gray-200 pt-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Служебная информация</h3>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Отдел -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Отдел</label>
                                            <div class="relative">
                                                <input type="text"
                                                    value="{{ auth()->user()->worker->department->name ?? 'Не указано' }}"
                                                    class="block w-full bg-gray-100 border border-gray-300 rounded-md shadow-sm py-2 px-3 text-gray-600 cursor-not-allowed"
                                                    disabled readonly>
                                            </div>
                                        </div>

                                        <!-- Должность -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Должность</label>
                                            <div class="relative">
                                                <input type="text"
                                                    value="{{ auth()->user()->worker->post->name ?? 'Не указано' }}"
                                                    class="block w-full bg-gray-100 border border-gray-300 rounded-md shadow-sm py-2 px-3 text-gray-600 cursor-not-allowed"
                                                    disabled readonly>
                                            </div>
                                        </div>

                                        <!-- Уровень подготовки -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Уровень
                                                подготовки</label>
                                            <div class="relative">
                                                <input type="text"
                                                    value="{{ auth()->user()?->worker->level_of_experience }}"
                                                    class="block w-full bg-gray-100 border border-gray-300 rounded-md shadow-sm py-2 px-3 text-gray-600 cursor-not-allowed"
                                                    disabled readonly>
                                            </div>
                                        </div>

                                        <!-- Дата приема -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Дата приема на
                                                должность</label>
                                            <div class="relative">
                                                <input type="text" value="{{ auth()->user()->worker->hire_date }}"
                                                    class="block w-full bg-gray-100 border border-gray-300 rounded-md shadow-sm py-2 px-3 text-gray-600 cursor-not-allowed"
                                                    disabled readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="flex items-center justify-end pt-6 border-t border-gray-200">
                                <button type="submit"
                                    class="w-full px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Сохранить изменения
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Вкладка смены пароля -->
                <div id="password-content" class="tab-content hidden p-6">
                    <div class="max-w-3xl mx-auto">
                        <form action="{{ route('profile.password.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            @if ($errors->any())
                                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                                    <ul class="list-disc list-inside">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="mb-6">
                                <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">Новый
                                    пароль</label>
                                <input type="password" id="new_password" name="new_password"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring focus:ring-blue-500 focus:border-blue-500"
                                    required>
                                <p class="mt-1 text-xs text-gray-500">Минимум 8 символов, включая цифры и буквы</p>
                            </div>

                            <div class="mb-6">
                                <label for="new_password_confirmation"
                                    class="block text-sm font-medium text-gray-700 mb-1">Подтвердите новый пароль</label>
                                <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring focus:ring-blue-500 focus:border-blue-500"
                                    required>
                            </div>

                            <div class="flex items-center justify-end pt-6 border-t border-gray-200">
                                <button type="submit"
                                    class="w-full px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Изменить пароль
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Регулярные выражения для валидации
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const phoneRegex = /^\+7\s?\(\d{3}\)\s?\d{3}-\d{2}-\d{2}$/;

        // Элементы формы
        const emailInput = document.getElementById('email');
        const phoneInput = document.getElementById('phone');
        const emailError = document.getElementById('email-error');
        const phoneError = document.getElementById('phone-error');
        const submitButton = document.querySelector('button[type="submit"]');

        // Обработка отвязки Telegram
        document.getElementById('unlink-telegram-btn')?.addEventListener('click', async function() {
            if (!confirm('Вы уверены, что хотите отвязать Telegram аккаунт?')) {
                return;
            }

            try {
                const response = await fetch('{{ route('profile.telegram.unlink') }}', {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Ошибка сервера');
                }

                alert('Telegram аккаунт успешно отвязан');
                window.location.reload();
            } catch (error) {
                console.error('Error:', error);
                alert('Ошибка: ' + error.message);
            }
        });

        // Функция валидации email
        function validateEmail() {
            if (!emailRegex.test(emailInput.value)) {
                emailError.classList.remove('hidden');
                emailInput.classList.add('border-red-500');
                return false;
            } else {
                emailError.classList.add('hidden');
                emailInput.classList.remove('border-red-500');
                return true;
            }
        }

        // Функция валидации телефона
        function validatePhone() {
            if (phoneInput.value && !phoneRegex.test(phoneInput.value)) {
                phoneError.classList.remove('hidden');
                phoneInput.classList.add('border-red-500');
                return false;
            } else {
                phoneError.classList.add('hidden');
                phoneInput.classList.remove('border-red-500');
                return true;
            }
        }

        // Слушатели событий
        emailInput.addEventListener('input', validateEmail);
        emailInput.addEventListener('blur', validateEmail);
        phoneInput.addEventListener('input', validatePhone);
        phoneInput.addEventListener('blur', validatePhone);

        // Валидация перед отправкой формы
        document.querySelector('form').addEventListener('submit', function(e) {
            const isEmailValid = validateEmail();
            const isPhoneValid = validatePhone();

            if (!isEmailValid || !isPhoneValid) {
                e.preventDefault();
            }
        });

        // Маска для телефона (опционально)
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 1) {
                value = '+7 (' + value.substring(1, 4) + ') ' + value.substring(4, 7) + '-' + value.substring(7,
                    9) + '-' + value.substring(9, 11);
            } else if (value.length === 1) {
                value = '+7 (' + value;
            }
            e.target.value = value;
        });

        document.querySelectorAll('.tab-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();

                // Убираем активные классы у всех ссылок
                document.querySelectorAll('.tab-link').forEach(tab => {
                    tab.classList.remove('border-blue-500', 'text-blue-600', 'bg-gray-50');
                    tab.classList.add('border-transparent', 'text-gray-500');
                });

                // Добавляем активные классы текущей ссылке
                link.classList.add('border-blue-500', 'text-blue-600', 'bg-gray-50');
                link.classList.remove('border-transparent', 'text-gray-500');

                // Скрываем все содержимое вкладок
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.add('hidden');
                });

                // Показываем соответствующее содержимое
                const tabId = link.id.replace('-tab', '-content');
                document.getElementById(tabId).classList.remove('hidden');
            });
        });
    </script>
@endsection
