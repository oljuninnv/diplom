@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Заголовок страницы -->
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-gray-900">Задание</h1>
            <p class="mt-2 text-lg text-gray-600">Выполните задание и отправьте ссылку на репозиторий</p>
        </div>

        <!-- Карточка с заданием -->
        <div class="bg-white shadow rounded-lg overflow-hidden mb-8">
            <div class="p-6 sm:p-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="mb-4 md:mb-0">
                        <h2 class="text-xl font-semibold text-gray-800">Название задания</h2>
                        <p class="mt-1 text-gray-600">Описание задания и требования к выполнению</p>
                        
                        <!-- Добавленная строка с датой окончания -->
                        <div class="mt-2 flex items-center text-sm text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Срок выполнения: <span class="font-medium ml-1">26.04.2025</span>
                        </div>
                    </div>
                    <div class="mt-4 md:mt-0 md:ml-4">
                        <a href="/path/to/task/file.pdf" download
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                            Скачать задание
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Форма для отправки решения -->
        <div class="bg-white shadow rounded-lg overflow-hidden disabled-form" id="submit-form">
            <div class="p-6 sm:p-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Отправить решение</h2>

                <form id="github-form">
                    @csrf

                    <div class="mb-6">
                        <label for="github-repo" class="block text-sm font-medium text-gray-700 mb-1">Ссылка на GitHub
                            репозиторий</label>
                        <input type="url" id="github-repo" name="github_repo"
                            class="github-input mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="https://github.com/username/repository"
                            pattern="^https:\/\/github\.com\/[a-zA-Z0-9_-]+\/[a-zA-Z0-9_-]+$" required>
                        <p id="github-error" class="mt-1 text-xs text-red-500 hidden">Введите корректную ссылку на
                            GitHub репозиторий (например, https://github.com/username/repository)</p>
                        <p class="mt-1 text-xs text-gray-500">Форма временно неактивна. Отправка будет доступна позже.
                        </p>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" disabled
                            class="px-6 py-2 bg-gray-400 text-white rounded-md cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Отправить
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Валидация GitHub ссылки
        const githubRepoInput = document.getElementById('github-repo');
        const githubError = document.getElementById('github-error');
        const githubForm = document.getElementById('github-form');

        function validateGitHubRepo() {
            const pattern = /^https:\/\/github\.com\/[a-zA-Z0-9_-]+\/[a-zA-Z0-9_-]+$/;
            const isValid = pattern.test(githubRepoInput.value);

            if (!isValid && githubRepoInput.value) {
                githubError.classList.remove('hidden');
                githubRepoInput.classList.add('border-red-500');
                return false;
            } else {
                githubError.classList.add('hidden');
                githubRepoInput.classList.remove('border-red-500');
                return true;
            }
        }

        githubRepoInput.addEventListener('input', validateGitHubRepo);
        githubRepoInput.addEventListener('blur', validateGitHubRepo);

        githubForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (validateGitHubRepo()) {
                // Здесь будет код для отправки формы
                alert('Форма временно неактивна!');
            }
        });

        // Активация формы (убрать класс disabled-form и атрибут disabled у кнопки)
        // function enableForm() {
        //     document.getElementById('submit-form').classList.remove('disabled-form');
        //     document.querySelector('#github-form button[type="submit"]').removeAttribute('disabled');
        // }
    </script>
@endsection