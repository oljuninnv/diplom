<!DOCTYPE html>
<html>
<head>
    <title>Подтверждение получения вашей заявки</title>
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
            <h1>ATWINTA</h1>
            <p>Digital-агентство</p>
        </div>
        
        <div class="content">
            <h2>Уважаемый(ая) {{ $userData['name'] }},</h2>
            
            <p>Благодарим вас за проявленный интерес к нашей компании и отправленную заявку!</p>
            
            <p>Мы получили ваши данные:</p>
            <ul>
                <li><strong>Имя:</strong> {{ $userData['name'] }}</li>
                <li><strong>Email:</strong> {{ $userData['email'] }}</li>
                <li><strong>Телефон:</strong> {{ $userData['phone'] }}</li>
                <li><strong>Тип работ:</strong> {{ $userData['position'] }}</li>
            </ul>
            
            <p>Наш HR-специалист рассмотрит вашу заявку в ближайшее время и свяжется с вами.</p>
            
            <p>Если у вас есть срочные вопросы, вы можете написать нам на <a href="mailto:info@atwinta.ru">info@atwinta.ru</a></p>
        </div>
        
        <div class="footer">
            <p>Это письмо было отправлено автоматически. Пожалуйста, не отвечайте на него.</p>
            <p>&copy; {{ date('Y') }} ATWINTA. Все права защищены.</p>
        </div>
    </div>
</body>
</html>