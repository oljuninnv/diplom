<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Карьера в ATWINTA | Digital-агентство</title>
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        }
        .benefit-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        .vacancy-card:hover {
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.2);
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800">
    <!-- Hero Section -->
    <div class="gradient-bg text-white">
        <div class="container mx-auto px-4 py-16 md:py-24">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-6">ATWINTA</h1>
                <p class="text-xl md:text-2xl font-light mb-8">Создаём digital-будущее с 2011 года</p>
                <a href="#vacancies" class="inline-block bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-300">
                    Смотреть вакансии
                </a>
            </div>
        </div>
    </div>

    <!-- About Section -->
    <div class="container mx-auto px-4 py-16">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-3xl font-bold mb-8 text-center">Работа для лучших в Digital!</h2>
            <div class="space-y-6 text-lg text-gray-700">
                <p class="leading-relaxed">Digital-агентство «Атвинта» объединяет сильных специалистов, чтобы делать сложные и значимые веб-проекты.</p>
                <p class="leading-relaxed">Наши сотрудники увлечены своей профессией и умеют работать в команде. Благодаря этому мы создаём проекты, которые собирают награды и признание профессионального сообщества.</p>
                <p class="leading-relaxed font-medium">Агентство входит в ТОП-50 digital-агентств России по версии Рейтинга Рунета и Tagline.</p>
                <p class="leading-relaxed">Мы постоянно развиваемся, проектов становится больше, команда растёт. Присоединяйся и ты!</p>
            </div>
        </div>
    </div>

    <!-- Benefits Section -->
    <div class="bg-gray-100 py-16">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold mb-12 text-center">Наши преимущества</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white p-8 rounded-xl shadow-sm transition duration-300 benefit-card">
                    <div class="text-blue-600 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Официальное трудоустройство</h3>
                    <p class="text-gray-600">Полный соцпакет, белая зарплата и все положенные гарантии</p>
                </div>
                
                <div class="bg-white p-8 rounded-xl shadow-sm transition duration-300 benefit-card">
                    <div class="text-blue-600 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Интересные задачи</h3>
                    <p class="text-gray-600">Работа над проектами, которые бросают вызов и развивают</p>
                </div>
                
                <div class="bg-white p-8 rounded-xl shadow-sm transition duration-300 benefit-card">
                    <div class="text-blue-600 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Профессиональный рост</h3>
                    <p class="text-gray-600">Чёткий карьерный путь и возможности для развития</p>
                </div>
                
                <div class="bg-white p-8 rounded-xl shadow-sm transition duration-300 benefit-card">
                    <div class="text-blue-600 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Обучение за счёт компании</h3>
                    <p class="text-gray-600">Оплачиваем курсы, конференции и профессиональную литературу</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Vacancies Section -->
    <div id="vacancies" class="container mx-auto px-4 py-16">
        <h2 class="text-3xl font-bold mb-12 text-center">Текущие вакансии</h2>
        <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach ($vacancies as $vacancy)
                <div class="bg-white border border-gray-200 rounded-xl p-6 transition duration-300 vacancy-card">
                    <h3 class="text-xl font-semibold mb-3 text-blue-600">{{ $vacancy->post->name }}</h3>
                    <p class="text-gray-700 mb-4">{{ $vacancy->description }}</p>
                    @if ($vacancy->salary)
                        <p class="text-lg font-semibold text-gray-900">Зарплата: {{ $vacancy->salary }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Application Form Section -->
    <div class="bg-gray-100 py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-sm p-8 md:p-10">
                <h2 class="text-3xl font-bold mb-6 text-center">Хотите с нами работать?</h2>
                
                @if (session('success'))
                    <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-lg border border-green-200">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-lg border border-red-200">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="text-center mb-8">
                    <p class="text-lg text-gray-600 mb-3">Заполните форму или свяжитесь с нами:</p>
                    <a href="mailto:info@atwinta.ru" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        info@atwinta.ru
                    </a>
                    <p class="mt-2 text-gray-500">Ответим в течение 2-х часов в рабочее время</p>
                </div>

                <form action="{{ route('career.submit') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-lg border border-red-200">
                            <h3 class="font-bold mb-2">Пожалуйста, исправьте ошибки:</h3>
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Имя *</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-300">
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Почта *</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-300">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Телефон *</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-300">
                        </div>
                        
                        <div>
                            <label for="position" class="block text-sm font-medium text-gray-700 mb-1">Тип работ *</label>
                            <select id="position" name="position" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-300">
                                <option value="" disabled selected>Выберите тип работ</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" 
                                        {{ old('position') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="resume" class="block text-sm font-medium text-gray-700 mb-1">Прикрепить резюме *</label>
                        <div class="mt-1 flex items-center">
                            <label for="resume" class="cursor-pointer bg-white py-2 px-4 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-300">
                                <span>Выберите файл</span>
                                <input id="resume" name="resume" type="file" class="sr-only" required accept=".doc,.docx,.pdf">
                            </label>
                            <span class="ml-3 text-sm text-gray-500" id="file-name">
                                @if (old('resume_filename')) {{ old('resume_filename') }} @else PDF, DOC или DOCX @endif
                            </span>
                        </div>
                        <script>
                            document.getElementById('resume').addEventListener('change', function(e) {
                                var fileName = e.target.files[0] ? e.target.files[0].name : '@if (old('resume_filename')) {{ old('resume_filename') }} @else PDF, DOC или DOCX @endif';
                                document.getElementById('file-name').textContent = fileName;
                            });
                        </script>
                    </div>

                    <div class="text-xs text-gray-500 mb-6">
                        * Поля обязательны для заполнения
                    </div>

                    <button type="submit"
                        class="w-full gradient-bg text-white px-6 py-4 rounded-lg hover:opacity-90 transition duration-300 font-semibold text-lg shadow-md">
                        Отправить заявку
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <h2 class="text-2xl font-bold mb-4">ATWINTA</h2>
                <p class="text-gray-400">Digital-агентство полного цикла</p>
                <p class="text-gray-400 mt-2">© 2011-{{ date('Y') }} Все права защищены</p>
            </div>
        </div>
    </footer>
</body>
</html>

<style>
    html {
        scroll-behavior: smooth;
    }
</style>