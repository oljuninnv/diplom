<!DOCTYPE html>
<html>

<head>
    <title>
        {{ $data['action'] === 'scheduled' ? 'Созвон назначен' : ($data['action'] === 'updated' ? 'Изменение в созвоне' : 'Созвон отменен') }}
    </title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }

        .content {
            padding: 20px;
        }

        .footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #777;
            text-align: center;
        }

        .credentials {
            background-color: #f8f9fa;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            border-left: 4px solid #6c757d;
        }

        .contacts {
            background-color: #fff8e1;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            border-left: 4px solid #ffc107;
        }

        .warning {
            color: #dc3545;
            font-weight: bold;
        }

        .success {
            color: #28a745;
        }

        .info {
            color: #17a2b8;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>ATWINTA</h1>
            <p>Digital-агентство</p>
        </div>

        <div class="content">
            <h2>Уважаемый(ая) {{ $data['user']->name }},</h2>

            @if ($data['action'] === 'scheduled')
                <p class="success">Мы рады сообщить, что созвон с вами успешно назначен!</p>

                <div class="credentials">
                    <h3>Детали созвона:</h3>
                    <p><strong>Тип созвона:</strong> {{ $data['call_type'] }}</p>
                    <p><strong>Дата:</strong> {{ $data['call']->date }}</p>
                    <p><strong>Время:</strong> {{ $data['call']->time }}</p>
                    <p><strong>Ссылка для подключения:</strong> <a href="{{ $data['call']->meeting_link }}">Присоединиться</a></p>
                </div>
            @elseif($data['action'] === 'updated')
                <p class="info">В запланированный созвон внесены изменения:</p>
                <p><strong>Тип созвона:</strong> {{ $data['call_type'] }}</p>
                <p><strong>Новая дата:</strong> {{ $data['call']->date }}</p>
                <p><strong>Новое время:</strong> {{ $data['call']->time }}</p>
                <p><strong>Ссылка:</strong> <a href="{{ $data['call']->meeting_link }}">Присоединиться</a>
                </p>
            @else
                <p class="warning">Запланированный созвон был отменен:</p>
                <p><strong>Тип созвона:</strong> {{ $data['call_type'] }}</p>
                <p><strong>Дата:</strong> {{ $data['call']->date }}</p>
                <p><strong>Время:</strong> {{ $data['call']->time }}</p>
                <p>Для уточнения деталей свяжитесь с организатором.</p>
            @endif

            <div class="contacts">
                <h3>Команда сопровождения:</h3>
                <p><strong>Тьютор:</strong> {{ $data['tutor']->name }}</p>
                <p><strong>HR-менеджер:</strong> {{ $data['hrManager']->name }}</p>
            </div>

            <p>Пожалуйста, не передавайте эти данные третьим лицам.</p>
        </div>

        <div class="footer">
            <p>Это письмо было отправлено автоматически. Пожалуйста, не отвечайте на него.</p>
            <p>&copy; {{ date('Y') }} ATWINTA. Все права защищены.</p>
        </div>
    </div>
</body>

</html>
