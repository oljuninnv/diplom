<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Привязка Telegram аккаунта</title>
    @vite('resources/css/app.css')
    <style>
        .telegram-btn {
            background-color: #0088cc;
            transition: background-color 0.3s;
        }
        .telegram-btn:hover {
            background-color: #0077b3;
        }
        .skip-btn {
            transition: background-color 0.3s;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white rounded-lg shadow-md overflow-hidden w-full max-w-md">
        <div class="bg-blue-500 p-4 text-white">
            <h1 class="text-xl font-bold text-center">Привязка Telegram</h1>
        </div>
        
        <div class="p-6">
            @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
                <p>{{ session('error') }}</p>
            </div>
            @endif

            <div class="mb-6">
                <p class="text-gray-700 mb-4">Для завершения авторизации рекомендуется привязать Telegram аккаунт. Это позволит:</p>
                <ul class="list-disc pl-5 space-y-2 text-gray-600 mb-6">
                    <li>Получать важные уведомления</li>
                </ul>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h2 class="font-semibold text-blue-800 mb-2">Как привязать:</h2>
                    <ol class="list-decimal list-inside space-y-2 text-blue-700">
                        <li>Нажмите кнопку "Привязать Telegram"</li>
                        <li>В открывшемся Telegram нажмите "Start"</li>
                        <li>Вернитесь на эту страницу</li>
                    </ol>
                </div>
            </div>

            <div class="flex flex-col space-y-4">
                <a href="{{ $deepLink }}" 
                   class="telegram-btn text-white font-medium py-3 px-4 rounded-lg text-center flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.14.18-.357.295-.6.295-.002 0-.003 0-.005 0l.213-3.054 5.56-5.022c.24-.213-.054-.334-.373-.121l-6.869 4.326-2.96-.924c-.64-.203-.658-.64.135-.954l11.566-4.458c.538-.196 1.006.128.832.941z"/>
                    </svg>
                    Привязать Telegram
                </a>

                <a href="{{ $skipUrl }}" 
                   class="skip-btn bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-3 px-4 rounded-lg text-center">
                    Пропустить привязку
                </a>
            </div>

            <div class="mt-6 text-center text-sm text-gray-500">
                <p>Вы можете привязать аккаунт позже в настройках профиля</p>
            </div>
        </div>
    </div>
</body>
</html>