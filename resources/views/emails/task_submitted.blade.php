<!DOCTYPE html>
<html>
<head>
    <title>Новое решение задания</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 12px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Новое решение задания</h1>
        </div>
        
        <div class="content">
            <table>
                <tr>
                    <th>Кандидат</th>
                    <td>{{ $userData->name }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $userData->email }}</td>
                </tr>
                <tr>
                    <th>Задание</th>
                    <td>{{ $taskData->task->title }}</td>
                </tr>
                <tr>
                    <th>Ссылка на репозиторий</th>
                    <td><a href="{{ $taskData->github_repo }}" target="_blank">{{ $taskData->github_repo }}</a></td>
                </tr>
                <tr>
                    <th>Дата отправки</th>
                    <td>{{ now()->format('d.m.Y H:i') }}</td>
                </tr>
                <tr>
                    <th>Файл задания</th>
                    <td>
                        @if(Storage::disk('public')->exists($taskData->task->task))
                            {{ $taskData->task->task }} (прикреплен к письму)
                        @else
                            Файл задания не найден
                        @endif
                    </td>
                </tr>
            </table>
            
            <p style="margin-top: 20px;">
                Статус задания был изменен на "на проверке". Пожалуйста, проверьте решение.
            </p>
        </div>
    </div>
</body>
</html>