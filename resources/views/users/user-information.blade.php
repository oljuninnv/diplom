<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Профиль пользователя</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-2xl w-full bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Аватар сверху (увеличен в 2 раза) -->
            <div class="bg-gray-50 p-6 flex flex-col items-center">
                <img src="{{ auth()->user()?->avatar ? asset('storage/' . auth()->user()->avatar) : asset('deafault-avatar.jpg') }}"
                alt="Аватар пользователя" class="rounded-full h-11 w-11 object-cover">
                <h1 class="mt-4 text-xl font-semibold text-gray-800">{{ auth()->user()->name }}</h1>
            </div>
            
            <!-- Вкладки -->
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px">
                    <button id="personal-tab" class="tab-button active w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm border-blue-500 text-blue-600">
                        Личная информация
                    </button>
                    <button id="password-tab" class="tab-button w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Смена пароля
                    </button>
                </nav>
            </div>
            
            <!-- Содержимое вкладок -->
            <div class="p-6">
                <!-- Вкладка личной информации -->
                <div id="personal-content" class="tab-content">
                    <div class="max-w-md mx-auto">
                        <form action="{{ route('home') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                    
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700">Имя</label>
                                <input type="text" id="name" name="name" value="{{ auth()->user()->name }}" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring focus:ring-blue-500 focus:border-blue-500" required>
                            </div>
                    
                            <div class="mb-4">
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" id="email" name="email" value="{{ auth()->user()->email }}" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring focus:ring-blue-500 focus:border-blue-500" required>
                            </div>
                    
                            <div class="mb-4">
                                <label for="phone" class="block text-sm font-medium text-gray-700">Номер телефона</label>
                                <input type="tel" id="phone" name="phone" value="{{ auth()->user()->phone }}" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring focus:ring-blue-500 focus:border-blue-500">
                            </div>
                    
                            <div class="mb-4">
                                <label for="avatar" class="block text-sm font-medium text-gray-700">Обновить аватар</label>
                                <input type="file" id="avatar" name="avatar" 
                                       class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                    
                            <div class="mb-6">
                                <label for="telegram" class="block text-sm font-medium text-gray-700">Ссылка на Telegram аккаунт</label>
                                <input type="url" id="telegram" name="telegram" value="{{ auth()->user()->telegram }}" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring focus:ring-blue-500 focus:border-blue-500">
                            </div>
                    
                            <div class="flex items-center justify-center">
                                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                    Сохранить изменения
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Вкладка смены пароля -->
                <div id="password-content" class="tab-content hidden">
                    <div class="max-w-md mx-auto">
                        <form action="{{ route('home') }}" method="POST">
                            @csrf
                            @method('PUT')
                    
                            <div class="mb-4">
                                <label for="new_password" class="block text-sm font-medium text-gray-700">Новый пароль</label>
                                <input type="password" id="new_password" name="new_password" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring focus:ring-blue-500 focus:border-blue-500" required>
                            </div>
                    
                            <div class="mb-6">
                                <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">Подтвердите новый пароль</label>
                                <input type="password" id="new_password_confirmation" name="new_password_confirmation" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring focus:ring-blue-500 focus:border-blue-500" required>
                            </div>
                    
                            <div class="flex items-center justify-center">
                                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
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
        // Переключение между вкладками
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', () => {
                // Убираем активные классы у всех кнопок
                document.querySelectorAll('.tab-button').forEach(btn => {
                    btn.classList.remove('active', 'border-blue-500', 'text-blue-600');
                    btn.classList.add('border-transparent', 'text-gray-500');
                });
                
                // Добавляем активные классы текущей кнопке
                button.classList.add('active', 'border-blue-500', 'text-blue-600');
                button.classList.remove('border-transparent', 'text-gray-500');
                
                // Скрываем все содержимое вкладок
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.add('hidden');
                });
                
                // Показываем соответствующее содержимое
                const tabId = button.id.replace('-tab', '-content');
                document.getElementById(tabId).classList.remove('hidden');
            });
        });
    </script>
</body>
</html>