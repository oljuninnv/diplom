<!DOCTYPE html>
<html>
<head>
    <title>Ваша заявка одобрена</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; font-size: 12px; color: #777; }
        .credentials { background-color: #f2f2f2; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .contacts { background-color: #fff8e1; padding: 15px; margin: 15px 0; border-radius: 5px; border-left: 4px solid #ffc107; }
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
            
            <p>После рассмотрения вашей заявки мы приняли решение предоставить вам тестовое задание.</p>
            
            <div class="credentials">
                <h3>Данные для входа в систему:</h3>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Пароль:</strong> password</p>
                <p><strong>Ссылка для входа:</strong> <a href="http://127.0.0.1:8000/">http://127.0.0.1:8000/</a></p>
            </div>
            
            <div class="contacts">
                <h3>Команда сопровождения:</h3>
                <p><strong>Тьютор:</strong> {{ $tutor->name }}</p>
                <p><strong>HR-менеджер:</strong> {{ $hrManager->name }}</p>
                
                <p class="warning">Важно:</p>
                <p>Пожалуйста, беспокойте тьютора и HR-менеджера только в <strong>крайнем случае</strong>, 
                   если у вас возникли действительно серьезные трудности при выполнении задания.</p>
                <p>Частота и характер ваших обращений будут учитываться при оценке выполнения тестового задания.</p>
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