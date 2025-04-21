<!DOCTYPE html>
<html>
<head>
    <title>Напоминание о созвоне</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; font-size: 12px; color: #777; }
        .meeting-info { background-color: #f2f2f2; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .button-container { text-align: center; margin: 15px 0; }
        .button { display: inline-block; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px; }
        .warning { color: #d32f2f; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ATWINTA</h1>
            <p>Digital-агентство</p>
        </div>
        
        <div class="content">
            <h2>Уважаемый(ая) {{ $user->name }},</h2>
            
            <p>Напоминаем вам о предстоящем созвоне, который начнется через 10 минут.</p>
            
            <div class="meeting-info">
                <h3>Детали созвона:</h3>
                <p><strong>Тип:</strong> {{ $call->type }}</p>
                <p><strong>Дата:</strong> {{ \Carbon\Carbon::parse($call->date)->format('d.m.Y') }}</p>
                <p><strong>Время:</strong> {{ \Carbon\Carbon::parse($call->time)->format('H:i') }}</p>
                <p><strong>Ссылка для подключения:</strong></p>
                <div class="button-container">
                    <a href="{{ $call->meeting_link }}" class="button">Присоединиться к созвону</a>
                </div>
            </div>
            
        </div>
        
        <div class="footer">
            <p>Это письмо было отправлено автоматически. Пожалуйста, не отвечайте на него.</p>
            <p>&copy; {{ date('Y') }} ATWINTA. Все права защищены.</p>
        </div>
    </div>
</body>
</html>