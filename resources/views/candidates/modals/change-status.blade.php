<div id="status-modal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="modal-content relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Изменение статуса</h3>
            <button class="modal-close text-gray-400 hover:text-gray-500">&times;</button>
        </div>
        <span id="status-candidate-name"></span>
        <form class="mt-2" id="status-form" enctype="multipart/form-data" onsubmit="submitStatusForm(event)">
            @csrf
            <div id="status-options" class="space-y-2 mb-4">
                <!-- Статусы будут загружены здесь -->
            </div>
            
            <div class="mb-4">
                <label for="status-comment" class="block text-sm font-medium text-gray-700 mb-1">Комментарий</label>
                <textarea id="status-comment" name="comment" rows="3"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>
            
            <div class="mb-4">
                <label for="status-file" class="block text-sm font-medium text-gray-700 mb-1">Файл отчёта (опционально)</label>
                <input type="file" id="status-file" name="report"
                accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"    
                class="block w-full text-sm text-gray-500
                           file:mr-4 file:py-2 file:px-4
                           file:rounded-md file:border-0
                           file:text-sm file:font-semibold
                           file:bg-blue-50 file:text-blue-700
                           hover:file:bg-blue-100">
                <p class="mt-1 text-sm text-gray-500">Поддерживаемые форматы: Word (DOC, DOCX), PDF (макс. 10MB)</p>
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