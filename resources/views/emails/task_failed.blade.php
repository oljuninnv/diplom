<!DOCTYPE html>
<html>
<head>
    <title>{{ $isCandidate ? 'Ваше тестовое задание просрочено' : 'Кандидат не выполнил тестовое задание' }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .task-card { background: #f9f9f9; border-left: 4px solid #4a76a8; padding: 15px; margin: 15px 0; }
        .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; font-size: 12px; color: #777; }
        .alert { background: #fff3cd; padding: 10px; border-left: 4px solid #ffc107; margin: 15px 0; }
        .btn { display: inline-block; padding: 10px 20px; background: #4a76a8; color: white; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ATWINTA</h1>
            <p>Уведомление о просроченном задании</p>
        </div>
        
        <div class="content">
            <h2>Уважаемый(ая) {{ $user->name }},</h2>
            
            @if($isCandidate)
                <p>К сожалению, вы не выполнили тестовое задание в установленный срок.</p>
            @else
                <p>Кандидат {{ $candidate->name }} не выполнил тестовое задание в установленный срок.</p>
            @endif
            
            <div class="task-card">
                <h3>{{ $taskStatus->task->title }}</h3>
                <p><strong>Срок сдачи:</strong> {{ $taskStatus->end_date->format('d.m.Y H:i') }}</p>
                <p><strong>Текущий статус:</strong> Просрочено</p>
            </div>
            
            @if($isCandidate)
            <div class="alert">
                <p>Пожалуйста, свяжитесь с вашим куратором/hr-менеджером для уточнения дальнейших действий.</p>
            </div>
            @endif
        </div>
        
        <div class="footer">
            <p>Это письмо было отправлено автоматически. Пожалуйста, не отвечайте на него.</p>
            <p>&copy; {{ date('Y') }} ATWINTA. Все права защищены.</p>
        </div>
    </div>
</body>
</html>