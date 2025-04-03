<div id="task-modal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="modal-content relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Информация о задании</h3>
            <button class="modal-close text-gray-400 hover:text-gray-500">&times;</button>
        </div>
        <div class="mb-4">
            <h4 id="modal-task-title" class="text-md font-semibold"></h4>
            <p>Уровень: <span id="modal-task-difficulty" class="text-sm text-gray-500"></span></p>
        </div>
        <div class="mb-4">
            <a id="modal-task-document-link" href="#" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">Скачать задание</a>
        </div>
        <div class="flex justify-end">
            <button type="button" class="modal-close inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                Закрыть
            </button>
        </div>
    </div>
</div>