<div id="status-modal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="modal-content relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Изменение статуса</h3>
            <button class="modal-close text-gray-400 hover:text-gray-500">&times;</button>
        </div>
        <span id="status-candidate-name"></span>
        <form class="mt-2" id="status-form" onsubmit="event.preventDefault(); submitStatusForm();">
            <div id="status-options" class="space-y-2 mb-4">
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" class="modal-close inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Отмена
                </button>
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Сохранить
                </button>
            </div>
        </form>
    </div>
</div>