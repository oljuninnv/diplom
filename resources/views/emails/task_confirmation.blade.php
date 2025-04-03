<!DOCTYPE html>
<html>
<head>
    <title>Решение принято на проверку</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; font-size: 12px; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Ваше решение принято на проверку</h1>
        </div>
        
        <div class="content">
            <h2>Уважаемый(ая) {{ $userData->name }},</h2>
            
            <p>Мы получили ваше решение задания "{{ $taskData->task->title }}".</p>
            
            <p><strong>Ссылка на репозиторий:</strong> <a href="{{ $taskData->github_repo }}" target="_blank">{{ $taskData->github_repo }}</a></p>
            
            <p>Наш специалист проверит ваше решение в ближайшее время и свяжется с вами.</p>
            
            <p>Спасибо за участие!</p>
        </div>
        
        <div class="footer">
            <p>Это письмо было отправлено автоматически. Пожалуйста, не отвечайте на него.</p>
        </div>
    </div>
</body>
</html>