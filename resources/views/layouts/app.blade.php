<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Добавьте эти строки в секцию head -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ru.js"></script>

    <!-- Для иконок Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    @vite('resources/css/app.css')
</head>

<body>
    <header>
        <nav class="bg-white shadow-lg fixed w-full z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="/" class="flex-shrink-0 flex items-center">
                            <h2>ATWINTA</h2>
                        </a>
                        <div class="hidden md:ml-6 md:flex md:space-x-8">
                            @if (auth()->user())
                                <a href="/profile"
                                    class="{{ request()->is('profile') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Профиль
                                </a>
                                @if (auth()->user()?->role->name === 'User')
                                    <a href="/task"
                                        class="{{ request()->is('task*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                        Задание
                                    </a>
                                    <a href="/chat"
                                        class="{{ request()->is('chat*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                        Чат
                                    </a>
                                @elseif (auth()->user()?->role->name !== 'User' && auth()->user()?->role->name !== 'Worker')
                                    <a href="/tasks"
                                        class="{{ request()->is('tasks*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                        Задание
                                    </a>
                                    <a href="/meetings"
                                        class="{{ request()->is('meetings*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                        Созвон
                                    </a>
                                    <a href="/worker-chat"
                                        class="{{ request()->is('worker-chat*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                        Чат
                                    </a>
                                @endif
                                @if (auth()->user()?->role->name === 'Admin')
                                    <a href="/application"
                                        class="{{ request()->is('application*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                        Заявки
                                    </a>
                                    <a href="/admin"
                                        class="{{ request()->is('admin*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                        Админ-панель
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="hidden md:ml-4 md:flex gap-2 md:items-center">
                            @auth

                                <a href="/profile" class="flex items-center space-x-2">
                                    <img src="{{ auth()->user()?->avatar ? asset('storage/' . auth()->user()->avatar) : asset('deafault-avatar.jpg') }}"
                                        alt="Аватар пользователя" class="rounded-full h-11 w-11 object-cover">
                                </a>
                                <a href="/logout" class="text-m text-gray-700 hover:text-gray-900">
                                    Выйти
                                </a>
                                @elseguest
                                <a href="login" class="btn-primary">
                                    Войти
                                </a>
                            @endauth
                        </div>
                        <div class="flex items-center md:hidden">
                            <button type="button"
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                                aria-expanded="false">
                                <span class="sr-only">Open main menu</span>
                                <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="md:hidden hidden" id="mobile-menu">
                <div class="pt-2 pb-3 space-y-1">
                    @if (auth()->user())
                        <a href="/profile"
                            class="{{ request()->is('profile') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700' }} block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                            Профиль
                        </a>
                        @if (auth()->user()?->role->name === 'User')
                            <a href="/task"
                                class="{{ request()->is('task') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700' }} block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                                Задание
                            </a>
                            <a href="/chat"
                                class="{{ request()->is('chat') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700' }} block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                                Чат
                            </a>
                        @elseif (auth()->user()?->role->name !== 'User' && auth()->user()?->role->name !== 'Worker')
                            <a href="/tasks"
                                class="{{ request()->is('tasks') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700' }} block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                                Задание
                            </a>
                            <a href="/meetings"
                                class="{{ request()->is('meetings') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700' }} block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                                Созвон
                            </a>
                            <a href="/worker-chat"
                                class="{{ request()->is('worker-chat') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700' }} block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                                Чат
                            </a>
                        @endif
                        @if (auth()->user()?->role->name === 'Admin')
                            <a href="/application"
                                class="{{ request()->is('application*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Заявки
                            </a>
                            <a href="/admin"
                                class="{{ request()->is('admin*') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700' }} block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                                Админ-панель
                            </a>
                        @endif
                    @endif
                </div>

                <div class="pt-4 pb-3 border-t border-gray-200">
                    @auth
                        <div class="flex items-center px-4">
                            <!-- Аватар пользователя -->
                            <div class="flex-shrink-0 mr-3">
                                <img src="{{ auth()->user()?->avatar ? asset('storage/' . auth()->user()->avatar) : asset('deafault-avatar.jpg') }}"
                                    alt="Аватар" class="h-10 w-10 rounded-full object-cover">
                            </div>
                            <!-- Имя пользователя -->
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-700">
                                    {{ auth()->user()->name ?? 'Гость' }}
                                </p>
                            </div>
                        </div>
                        <!-- Кнопка выхода -->
                        <div class="mt-3 px-4">
                            <a href="{{ route('logout') }}"
                                class="w-full flex justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                                Выйти
                            </a>
                        </div>
                    @else
                        <!-- Кнопка входа для гостей -->
                        <div class="px-4">
                            <a href="{{ route('login') }}"
                                class="w-full flex justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                Войти
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </nav>
    </header>
    <main class="bg-white min-h-screen flex flex-col pt-16">
        @yield('content')
    </main>
</body>

</html>
<script>
    document.querySelector('button[aria-expanded]').addEventListener('click', function() {
        const mobileMenu = document.getElementById('mobile-menu');
        const isExpanded = this.getAttribute('aria-expanded') === 'true';
        this.setAttribute('aria-expanded', !isExpanded);
        mobileMenu.classList.toggle('hidden');
    });
</script>
