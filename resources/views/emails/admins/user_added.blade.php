<!DOCTYPE html>
<html>
<head>
    <title>Новый пользователь добавлен в систему</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 12px; text-align: left; }
        th { background-color: #f2f2f2; }
        .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; font-size: 12px; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ATWINTA</h1>
            <h2>Пользователю назначено тестовое задание</h2>
        </div>
        
        <div class="content">
            <p>Уведомляем вас, что пользователю назначено тестовое задание.</p>
            
            <table>
                <tr>
                    <th>Поле</th>
                    <th>Значение</th>
                </tr>
                <tr>
                    <td>Имя</td>
                    <td>{{ $user->name }}</td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>{{ $user->email }}</td>
                </tr>
                <tr>
                    <td>Тестовое задание</td>
                    <td>{{ $task->title }}</td>
                </tr>
                <tr>
                    <td>Тьютор</td>
                    <td>{{ $tutor->name }}</td>
                </tr>
                <tr>
                    <td>HR-менеджер</td>
                    <td>{{ $hrManager->name }}</td>
                </tr>
                <tr>
                    <td>Дата окончания задания</td>
                    <td>{{ $endDate ?? 'Не указана' }}</td>
                </tr>
                <tr>
                    <td>Дата добавления</td>
                    <td>{{ now()->format('d.m.Y H:i') }}</td>
                </tr>
            </table>
        </div>
        
        <div class="footer">
            <p>Это письмо было отправлено автоматически.</p>
            <p>&copy; {{ date('Y') }} ATWINTA. Все права защищены.</p>
        </div>
    </div>
</body>
</html>