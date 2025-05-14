<!DOCTYPE html>
<html>
<head>
    <title>Ваша заявка взята на рассмотрение</title>
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
            
            <p>После просмотра вашего резюме, мы решили взять вашу заявку на рассмотрение.</p>
            
            <p>Мы убедительно проссим оставаться на связи, т.к. наш hr-менеджер в скором времени свяжится с вами на счёт проведения первичного собеседования.</p>
            
            <p>Пожалуйста, не передавайте эти данные третьим лицам.</p>
        </div>
        
        <div class="footer">
            <p>Это письмо было отправлено автоматически. Пожалуйста, не отвечайте на него.</p>
            <p>&copy; {{ date('Y') }} ATWINTA. Все права защищены.</p>
        </div>
    </div>
</body>
</html>