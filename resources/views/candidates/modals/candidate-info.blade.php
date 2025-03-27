<div id="candidate-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-candidate-name"></h3>
                        <div class="mt-2">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">Email:</p>
                                    <p class="text-sm font-medium text-gray-900" id="modal-candidate-email"></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Телефон:</p>
                                    <p class="text-sm font-medium text-gray-900" id="modal-candidate-phone"></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Telegram:</p>
                                    <p class="text-sm font-medium text-gray-900" id="modal-candidate-telegram"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" class="modal-close mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Закрыть
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Функция для открытия модального окна
    function openModal() {
        document.getElementById('candidate-modal').classList.remove('hidden');
    }

    // Функция для закрытия модального окна
    function closeModal() {
        document.getElementById('candidate-modal').classList.add('hidden');
    }

    // Добавляем обработчик события для кнопки "Закрыть"
    document.querySelector('.modal-close').addEventListener('click', closeModal);
</script>