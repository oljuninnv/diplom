<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Карьера в нашей компании</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-white text-gray-800">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold text-center mb-8">ATWINTA</h1>

        <section class="mb-12">
            <h2 class="text-3xl font-bold mb-4">Работа для лучших в Digital!</h2>
            <p class="text-lg mb-2">Digital-агентство «Атвинта» с 2011 года объединяет сильных специалистов, чтобы делать сложные веб-проекты.</p>
            <p class="text-lg mb-2">Наши сотрудники увлечены своей профессией и умеют работать в команде. Поэтому у нас получаются проекты, которые собирают награды и признание профессионального сообщества, а Агентство входит в ТОП-50 digital-агентств России по версии Рейтингу Рунета и Рейтинга Tagline.</p>
            <p class="text-lg mb-2">Мы развиваемся, проектов становится все больше, команда постоянно растет. Присоединяйся и ты!</p>
        </section>

        <!-- Наши преимущества -->
        <section class="mb-12">
            <h2 class="text-3xl font-bold mb-4">Наши преимущества</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-gray-100 p-6 rounded-lg">
                    <h3 class="text-xl font-semibold mb-2">Официальное трудоустройство</h3>
                    <p>Мы предлагаем официальное трудоустройство и социальный пакет.</p>
                </div>
                <div class="bg-gray-100 p-6 rounded-lg">
                    <h3 class="text-xl font-semibold mb-2">Интересные задачи</h3>
                    <p>Работа над проектами, которые действительно интересны и важны.</p>
                </div>
                <div class="bg-gray-100 p-6 rounded-lg">
                    <h3 class="text-xl font-semibold mb-2">Профессиональный рост</h3>
                    <p>Возможности для обучения и карьерного роста внутри компании.</p>
                </div>
                <div class="bg-gray-100 p-6 rounded-lg">
                    <h3 class="text-xl font-semibold mb-2">Развитие в профессии</h3>
                    <p>Агентство частично или полностью оплатит необходимое обучение и повышение квалификации.</p>
                </div>
            </div>
        </section>

        <!-- Текущие вакансии -->
        <section class="mb-12">
            <h2 class="text-3xl font-bold mb-4">Текущие вакансии</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($vacancies as $vacancy)
                <div class="bg-gray-100 p-6 rounded-lg">
                    <h3 class="text-xl font-semibold mb-2">{{ $vacancy->post->name }}</h3>
                    <p class="mb-2">{{ $vacancy->description }}</p>
                    @if($vacancy->salary)
                    <p class="text-lg font-semibold">Зарплата: {{ $vacancy->salary }}</p>
                    @endif
                </div>
                @endforeach
            </div>
        </section>

        <!-- Форма оставления заявки -->
        <section class="py-8">
            <div class="text-center mx-auto max-w-3xl px-4">
                <h2 class="text-3xl font-bold mb-6">Хотите с нами работать? Заполните форму</h2>
                
                @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
                @endif
                
                <div class="flex justify-center items-center gap-2 mb-4">
                    <p class="text-lg text-gray-600">Или свяжитесь с нами по электронной почте:</p>
                    <a href="mailto:info@atwinta.ru" class="text-blue-500 hover:text-blue-700 text-lg font-semibold">
                        info@atwinta.ru
                    </a>
                </div>             
                <p class="text-lg text-gray-600">Мы ответим в течение 2-х часов в рабочее время</p>
            </div>       
        </section>
        
        <form action="{{ route('career.submit') }}" method="POST" enctype="multipart/form-data" class="bg-gray-100 p-6 rounded-lg">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-lg font-semibold mb-2">Имя</label>
                <input type="text" id="name" name="name" required class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div class="mb-4">
                <label for="email" class="block text-lg font-semibold mb-2">Почта</label>
                <input type="email" id="email" name="email" required class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div class="mb-4">
                <label for="phone" class="block text-lg font-semibold mb-2">Телефон</label>
                <input type="tel" id="phone" name="phone" required class="w-full px-4 py-2 border rounded-lg">
            </div>
            <!-- Поле для выбора типа работ -->
            <div class="mb-4">
                <label for="position" class="block text-lg font-semibold mb-2">Тип работ</label>
                <select id="position" name="position" required class="w-full px-4 py-2 border rounded-lg">
                    <option value="" disabled selected>Выберите тип работ</option>
                    @foreach($departments as $department)
                    <option value="{{ $department->name }}">{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label for="resume" class="block text-lg font-semibold mb-2">Прикрепить резюме</label>
                <input type="file" id="resume" name="resume" required class="w-full px-4 py-2 border rounded-lg" accept=".doc,.docx,.pdf">
            </div>
            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 w-full">Отправить</button>
        </form>
    </div>
</body>
</html>