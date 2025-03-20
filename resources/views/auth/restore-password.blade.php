<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">Изменить пароль</h1>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Пароль</label>
                <input type="password" name="password" id="password" required placeholder="Введите новый пароль"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="mb-6">
                <label for="repeat-password" class="block text-gray-700 text-sm font-bold mb-2">Повторите пароль</label>
                <input type="password" name="repeat-password" id="repeat-password" required placeholder="Повторите пароль"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="flex items-center justify-center mb-4">
                <button type="submit"
                    class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Отправить
                </button>
            </div>
        </form>
    </div>
</body>
</html>
