<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    @vite('resources/css/app.css')
</head>

<body>
    <header>
        <nav class="bg-white shadow-lg fixed w-full z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="#" class="flex-shrink-0 flex items-center">
                            <h2>ATWINTA</h2>
                        </a>
                        <div class="hidden md:ml-6 md:flex md:space-x-8">
                            <a href="#"
                                class="border-indigo-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Home
                            </a>
                            <a href="#"
                                class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                About
                            </a>
                            <a href="#"
                                class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Services
                            </a>
                            <a href="#"
                                class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Contact
                            </a>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="hidden md:ml-4 md:flex gap-2 md:items-center">
                            @auth
                                <div class="relative group">
                                    <a href="#" class="flex items-center space-x-2">
                                        <img src="{{ auth()->user()?->avatar ? asset('storage/' . auth()->user()->avatar) : asset('images/default-avatar.png') }}"
                                            alt="Аватар пользователя" class="rounded-full h-11 w-11 object-cover">
                                    </a>
                                    <!-- Выпадающее меню -->
                                    <div
                                        class="absolute hidden group-hover:block bg-white shadow-lg rounded-lg mt-2 w-48 right-0 z-20 border border-gray-200">
                                        <a href="/admin"
                                            class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Админ-панель</a>
                                        <a href="/profile"
                                            class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Профиль</a>
                                        <a href="logout" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Выйти</a>
                                    </div>
                                </div>
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
                    <a href="#"
                        class="bg-indigo-50 border-indigo-500 text-indigo-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                        Home
                    </a>
                    <a href="#"
                        class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                        About
                    </a>
                    <a href="#"
                        class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                        Services
                    </a>
                    <a href="#"
                        class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                        Contact
                    </a>
                </div>
                <div class="pt-4 pb-3 border-t border-gray-200">
                    <div class="flex items-center px-4">
                        @auth
                            <x-moonshine::layout.profile route="/profile" :log-out-route="route('logout')" :avatar="auth()->user()?->avatar
                                ? asset('storage/' . auth()->user()->avatar)
                                : asset('images/default-avatar.png')"
                                :name-of-user="auth()->user()?->name ?? 'Гость'" />
                            @elseguest
                            <x-moonshine::link-button :href="route('login')" class="btn-primary">
                                Войти
                            </x-moonshine::link-button>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <main class="bg-white min-h-screen flex flex-col">
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
