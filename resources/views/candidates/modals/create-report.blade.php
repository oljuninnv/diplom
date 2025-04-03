<div id="report-modal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="modal-content relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Создание отчета</h3>
            <button class="modal-close text-gray-400 hover:text-gray-500">&times;</button>
        </div>
        <span id="report-candidate-name"></span>
        <form id="report-form" enctype="multipart/form-data" onsubmit="submitReportForm(event)">
            @csrf
            <div class="mb-4">
                <label for="report-file" class="block text-sm font-medium text-gray-700 mb-1">Файл отчёта</label>
                <input type="file" id="report-file" name="report" required
                       class="block w-full text-sm text-gray-500
                              file:mr-4 file:py-2 file:px-4
                              file:rounded-md file:border-0
                              file:text-sm file:font-semibold
                              file:bg-blue-50 file:text-blue-700
                              hover:file:bg-blue-100">
                <p class="mt-1 text-sm text-gray-500">Поддерживаемые форматы: PDF, DOC, DOCX, XLS, XLSX (макс. 2MB)</p>
            </div>
            <div class="mt-5 sm:mt-6 flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('report-modal').classList.add('hidden')"
                        class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:text-sm">
                    Отмена
                </button>
                <button type="submit"
                        class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:text-sm">
                    Создать отчёт
                </button>
            </div>
        </form>
    </div>
</div>