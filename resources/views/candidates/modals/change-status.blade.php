<div id="status-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Изменение статуса задания</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">Кандидат: <span id="status-candidate-name" class="font-medium"></span></p>
                            <div class="mt-4 space-y-2">
                                <div class="flex items-center">
                                    <input id="status-approved" name="status" type="radio" value="одобрено" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                                    <label for="status-approved" class="ml-3 block text-sm font-medium text-gray-700">Одобрено</label>
                                </div>
                                <div class="flex items-center">
                                    <input id="status-revision" name="status" type="radio" value="доработка" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                                    <label for="status-revision" class="ml-3 block text-sm font-medium text-gray-700">На доработку</label>
                                </div>
                                <div class="flex items-center">
                                    <input id="status-failed" name="status" type="radio" value="провалено" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                                    <label for="status-failed" class="ml-3 block text-sm font-medium text-gray-700">Провалено</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" id="confirm-status" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Сохранить
                </button>
                <button type="button" class="modal-close mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Отмена
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('status-modal');
        const closeModalButtons = document.querySelectorAll('.modal-close');
        const confirmStatusButton = document.getElementById('confirm-status');

        // Функция для закрытия модального окна
        function closeModal() {
            modal.classList.add('hidden');
        }

        // Добавляем обработчики событий для кнопок "Закрыть" и "Отмена"
        closeModalButtons.forEach(button => {
            button.addEventListener('click', closeModal);
        });

        // Добавляем обработчик события для кнопки "Сохранить"
        confirmStatusButton.addEventListener('click', function() {
            // Здесь можно добавить логику для сохранения статуса
            const selectedStatus = document.querySelector('input[name="status"]:checked');
            if (selectedStatus) {
                alert(`Статус "${selectedStatus.value}" сохранен!`);
                closeModal(); // Закрываем модальное окно после сохранения
            } else {
                alert('Пожалуйста, выберите статус.');
            }
        });
    });
</script>